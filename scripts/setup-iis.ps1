# Script para configurar o site no IIS
# Execute este script como Administrador

param(
    [string]$SiteName = "sgcr_novo",
    [string]$AppPoolName = "sgcr_novo",
    [string]$PhysicalPath = "\\flautim\g$\inetpub\wwwroot\SGCR_NEW",
    [string]$Port = "80"
)

Write-Host "=== Configuração do IIS para $SiteName ===" -ForegroundColor Green

# Verificar se está rodando como administrador
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Host "❌ Este script deve ser executado como Administrador!" -ForegroundColor Red
    exit 1
}

# Importar módulo WebAdministration
try {
    Import-Module WebAdministration
    Write-Host "✅ Módulo WebAdministration carregado" -ForegroundColor Green
} catch {
    Write-Host "❌ Erro ao carregar WebAdministration: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Verificar conectividade com o servidor flautim
Write-Host "Verificando conectividade com o servidor flautim..." -ForegroundColor Yellow
if (!(Test-Path "\\flautim\g$")) {
    Write-Host "❌ Não foi possível acessar o servidor flautim. Verifique a conectividade de rede." -ForegroundColor Red
    exit 1
}
Write-Host "✅ Conectividade com flautim OK" -ForegroundColor Green

# Criar diretório se não existir
if (!(Test-Path $PhysicalPath)) {
    Write-Host "Criando diretório: $PhysicalPath" -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $PhysicalPath -Force
}

# Verificar se o Application Pool existe, se não, criar
if (!(Test-Path "IIS:\AppPools\$AppPoolName")) {
    Write-Host "Criando Application Pool: $AppPoolName" -ForegroundColor Yellow
    New-WebAppPool -Name $AppPoolName
    
    # Configurar Application Pool
    Set-ItemProperty "IIS:\AppPools\$AppPoolName" -Name "managedRuntimeVersion" -Value "v4.0"
    Set-ItemProperty "IIS:\AppPools\$AppPoolName" -Name "processModel.identityType" -Value "ApplicationPoolIdentity"
    Set-ItemProperty "IIS:\AppPools\$AppPoolName" -Name "startMode" -Value "AlwaysRunning"
    Set-ItemProperty "IIS:\AppPools\$AppPoolName" -Name "recycling.periodicRestart.time" -Value "00:00:00"
    
    Write-Host "✅ Application Pool criado e configurado" -ForegroundColor Green
} else {
    Write-Host "✅ Application Pool já existe" -ForegroundColor Green
}

# Verificar se o site existe, se não, criar
if (!(Test-Path "IIS:\Sites\$SiteName")) {
    Write-Host "Criando site: $SiteName" -ForegroundColor Yellow
    New-Website -Name $SiteName -ApplicationPool $AppPoolName -PhysicalPath $PhysicalPath -Port $Port
    
    # Configurar site
    Set-ItemProperty "IIS:\Sites\$SiteName" -Name "bindings" -Value @{protocol="http";bindingInformation="*:$Port:"}
    
    Write-Host "✅ Site criado" -ForegroundColor Green
} else {
    Write-Host "✅ Site já existe" -ForegroundColor Green
}

# Configurar permissões
Write-Host "Configurando permissões..." -ForegroundColor Yellow
$acl = Get-Acl $PhysicalPath
$accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule("IIS_IUSRS", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.SetAccessRule($accessRule)
Set-Acl $PhysicalPath $acl

# Criar diretórios necessários para Laravel
$laravelDirs = @(
    "storage\logs",
    "storage\framework\cache",
    "storage\framework\sessions", 
    "storage\framework\views",
    "bootstrap\cache"
)

foreach ($dir in $laravelDirs) {
    $fullPath = Join-Path $PhysicalPath $dir
    if (!(Test-Path $fullPath)) {
        New-Item -ItemType Directory -Path $fullPath -Force
        Write-Host "Criado diretório: $dir" -ForegroundColor Yellow
    }
}

# Configurar permissões para storage
$storagePath = Join-Path $PhysicalPath "storage"
if (Test-Path $storagePath) {
    $storageAcl = Get-Acl $storagePath
    $storageAcl.SetAccessRule($accessRule)
    Set-Acl $storagePath $storageAcl
}

# Configurar web.config para Laravel (se não existir)
$webConfigPath = Join-Path $PhysicalPath "web.config"
if (!(Test-Path $webConfigPath)) {
    $webConfigContent = @"
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="Imported Rule 1" stopProcessing="true">
                    <match url="^(.*)/$" ignoreCase="false" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Redirect" redirectType="Permanent" url="/{R:1}" />
                </rule>
                <rule name="Imported Rule 2" stopProcessing="true">
                    <match url="^" ignoreCase="false" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php" />
                </rule>
            </rules>
        </rewrite>
        <defaultDocument>
            <files>
                <add value="index.php" />
            </files>
        </defaultDocument>
    </system.webServer>
</configuration>
"@
    Set-Content -Path $webConfigPath -Value $webConfigContent
    Write-Host "✅ web.config criado" -ForegroundColor Green
}

# Iniciar o site
try {
    Start-Website -Name $SiteName
    Write-Host "✅ Site iniciado com sucesso" -ForegroundColor Green
} catch {
    Write-Host "⚠️ Erro ao iniciar site: $($_.Exception.Message)" -ForegroundColor Yellow
}

# Verificar status
$siteStatus = Get-Website -Name $SiteName
Write-Host "`n=== Status do Site ===" -ForegroundColor Cyan
Write-Host "Nome: $($siteStatus.Name)" -ForegroundColor White
Write-Host "Status: $($siteStatus.State)" -ForegroundColor White
Write-Host "URL: http://des-sgcr_novo.ipen.br" -ForegroundColor White
Write-Host "Application Pool: $($siteStatus.ApplicationPool)" -ForegroundColor White
Write-Host "Caminho Físico: $($siteStatus.PhysicalPath)" -ForegroundColor White

Write-Host "`n✅ Configuração do IIS concluída!" -ForegroundColor Green
Write-Host "📝 Lembre-se de configurar o arquivo .env com as credenciais corretas do banco de dados" -ForegroundColor Yellow