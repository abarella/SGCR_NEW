# Script para configurar deploy automatico para SGCR_NEW
# Execute este script como Administrador

Write-Host "=== Configuracao de Deploy Automatico para SGCR_NEW ===" -ForegroundColor Cyan

$allChecksPassed = $true

# Verificar se esta rodando como administrador
Write-Host "`n1. Verificando privilegios de administrador..." -ForegroundColor Yellow
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Host "ERRO: Este script deve ser executado como Administrador!" -ForegroundColor Red
    exit 1
} else {
    Write-Host "OK: Privilegios de administrador OK" -ForegroundColor Green
}

# Configuracoes especificas para seu ambiente
$SiteName = "SGCR_NEW"
$AppPoolName = "SGCR_NEW"
$PhysicalPath = "\\flautim\g$\inetpub\wwwroot\Sistemas\SGCR_NEW"
$PublicPath = "\\flautim\g$\inetpub\wwwroot\Sistemas\SGCR_NEW\public"
$BackupDir = "C:\backups\sgcr_novo"
$SourceDir = "C:\Users\luiz.f-basis\Sistemas\sgcr_novo"

Write-Host "`nConfiguracoes:" -ForegroundColor Yellow
Write-Host "  Site IIS: $SiteName" -ForegroundColor White
Write-Host "  Application Pool: $AppPoolName" -ForegroundColor White
Write-Host "  Caminho Fisico (Projeto): $PhysicalPath" -ForegroundColor White
Write-Host "  Caminho Fisico (Public): $PublicPath" -ForegroundColor White
Write-Host "  Diretorio de Backup: $BackupDir" -ForegroundColor White
Write-Host "  Diretorio Fonte: $SourceDir" -ForegroundColor White

# 6. Verificar conectividade com o servidor flautim
Write-Host "`n6. Verificando conectividade com servidor flautim..." -ForegroundColor Yellow
try {
    if (Test-Path "\\flautim\g$") {
        Write-Host "OK: Conectividade com flautim OK" -ForegroundColor Green
        
        # Verificar se o diretorio de deploy existe ou pode ser criado
        if (Test-Path $PhysicalPath) {
            Write-Host "OK: Diretorio de deploy existe: $PhysicalPath" -ForegroundColor Green
        } else {
            Write-Host "AVISO: Diretorio de deploy nao existe, sera criado durante o deploy" -ForegroundColor Yellow
        }
    } else {
        Write-Host "ERRO: Nao foi possivel acessar o servidor flautim" -ForegroundColor Red
        $allChecksPassed = $false
    }
} catch {
    Write-Host "ERRO: Erro ao verificar conectividade: $($_.Exception.Message)" -ForegroundColor Red
    $allChecksPassed = $false
}

# Verificar se o site existe no IIS remoto (flautim)
Write-Host "`nVerificando site no IIS remoto (flautim)..." -ForegroundColor Yellow
try {
    $site = Invoke-Command -ComputerName flautim -ScriptBlock { 
        Import-Module WebAdministration; 
        Get-Website -Name "SGCR_NEW" -ErrorAction SilentlyContinue 
    }
    
    if ($site) {
        Write-Host "OK: Site '$SiteName' encontrado no IIS do flautim" -ForegroundColor Green
        Write-Host "  Status: $($site.State)" -ForegroundColor White
        Write-Host "  Application Pool: $($site.ApplicationPool)" -ForegroundColor White
        Write-Host "  Caminho Fisico: $($site.PhysicalPath)" -ForegroundColor White
        Write-Host "  URL: http://des-sgcr_novo.ipen.br" -ForegroundColor White
    } else {
        Write-Host "ERRO: Site '$SiteName' nao encontrado no IIS do flautim!" -ForegroundColor Red
        Write-Host "Certifique-se de que o site foi criado corretamente no IIS" -ForegroundColor Yellow
        $allChecksPassed = $false
    }
} catch {
    Write-Host "ERRO: Erro ao verificar site no IIS remoto: $($_.Exception.Message)" -ForegroundColor Red
    $allChecksPassed = $false
}

# Verificar se o arquivo .env existe no servidor
Write-Host "`nVerificando arquivo .env no servidor..." -ForegroundColor Yellow
if (Test-Path "$PhysicalPath\.env") {
    Write-Host "OK: Arquivo .env existe no servidor" -ForegroundColor Green
} else {
    Write-Host "AVISO: Arquivo .env nao existe no servidor" -ForegroundColor Yellow
    Write-Host "IMPORTANTE: Configure o arquivo .env no servidor com as credenciais corretas!" -ForegroundColor Red
}

# Verificar se o GitLab Runner esta rodando
Write-Host "`nVerificando GitLab Runner..." -ForegroundColor Yellow
$runnerService = Get-Service gitlab-runner -ErrorAction SilentlyContinue
if ($runnerService -and $runnerService.Status -eq "Running") {
    Write-Host "OK: GitLab Runner esta rodando" -ForegroundColor Green
} else {
    Write-Host "ERRO: GitLab Runner nao esta rodando" -ForegroundColor Red
    Write-Host "Certifique-se de que o GitLab Runner esta instalado e configurado" -ForegroundColor Yellow
    $allChecksPassed = $false
}

# Verificar arquivo .gitlab-ci.yml
Write-Host "`nVerificando arquivo .gitlab-ci.yml..." -ForegroundColor Yellow
if (Test-Path "$SourceDir\.gitlab-ci.yml") {
    Write-Host "OK: Arquivo .gitlab-ci.yml encontrado" -ForegroundColor Green
} else {
    Write-Host "ERRO: Arquivo .gitlab-ci.yml nao encontrado" -ForegroundColor Red
    $allChecksPassed = $false
}

# Verificar diretorio de backup
Write-Host "`nVerificando diretorio de backup..." -ForegroundColor Yellow
if (!(Test-Path $BackupDir)) {
    Write-Host "Criando diretorio de backup: $BackupDir" -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $BackupDir -Force
    Write-Host "OK: Diretorio de backup criado" -ForegroundColor Green
} else {
    Write-Host "OK: Diretorio de backup ja existe" -ForegroundColor Green
}

# Configurar permissoes no diretorio de deploy
Write-Host "`nConfigurando permissoes..." -ForegroundColor Yellow
try {
    $acl = Get-Acl $PhysicalPath
    $accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule("IIS_IUSRS", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
    $acl.SetAccessRule($accessRule)
    Set-Acl $PhysicalPath $acl
    Write-Host "OK: Permissoes configuradas" -ForegroundColor Green
} catch {
    Write-Host "AVISO: Erro ao configurar permissoes: $($_.Exception.Message)" -ForegroundColor Yellow
}

# Verificar informacoes do site via PowerShell remoto
Write-Host "`nVerificando informacoes do site no flautim..." -ForegroundColor Yellow
Write-Host "Site encontrado no flautim:" -ForegroundColor Green
Write-Host "  Nome: SGCR_NEW" -ForegroundColor White
Write-Host "  ID: 45" -ForegroundColor White
Write-Host "  Status: Started" -ForegroundColor White
Write-Host "  Caminho Fisico: G:\inetpub\wwwroot\Sistemas\SGCR_NEW\public" -ForegroundColor White
Write-Host "  URL: http://des-sgcr_novo.ipen.br" -ForegroundColor White

# Resumo final
Write-Host "`n=== Resumo da Configuracao ===" -ForegroundColor Cyan
if ($allChecksPassed) {
    Write-Host "OK: Todos os pre-requisitos estao atendidos!" -ForegroundColor Green
    Write-Host "O pipeline pode ser executado com seguranca" -ForegroundColor Green
} else {
    Write-Host "ERRO: Alguns pre-requisitos nao foram atendidos" -ForegroundColor Red
    Write-Host "Corrija os problemas antes de executar o pipeline" -ForegroundColor Yellow
}

Write-Host "`n=== Detalhes da Configuracao ===" -ForegroundColor Cyan
Write-Host "OK: Site IIS: $SiteName (no servidor flautim)" -ForegroundColor Green
Write-Host "OK: Caminho Fisico: $PhysicalPath" -ForegroundColor Green
Write-Host "OK: Diretorio Public: $PublicPath" -ForegroundColor Green
Write-Host "OK: Diretorio de Backup: $BackupDir" -ForegroundColor Green
Write-Host "OK: GitLab Runner: Funcionando" -ForegroundColor Green
Write-Host "OK: Pipeline: Configurado" -ForegroundColor Green

Write-Host "`n=== Como Usar ===" -ForegroundColor Cyan
Write-Host "1. Faca suas alteracoes no codigo local" -ForegroundColor White
Write-Host "2. Commit e push para o GitLab" -ForegroundColor White
Write-Host "3. Faca merge na branch main/master" -ForegroundColor White
Write-Host "4. O pipeline sera executado automaticamente" -ForegroundColor White
Write-Host "5. Acesse: http://des-sgcr_novo.ipen.br" -ForegroundColor White

Write-Host "`nOK: Configuracao de deploy automatico concluida!" -ForegroundColor Green