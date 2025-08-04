# Script para verificar pre-requisitos do deploy
# Execute este script antes de executar o pipeline

param(
    [string]$ProjectName = "sgcr_novo",
    [string]$SourceDir = "C:\Users\luiz.f-basis\Sistemas\sgcr_novo",
    [string]$DeployDir = "\\flautim\g$\inetpub\wwwroot\Sistemas\SGCR_NEW",
    [string]$BackupDir = "C:\backups\sgcr_novo"
)

Write-Host "=== Verificacao de Pre-requisitos para Deploy ===" -ForegroundColor Cyan

$allChecksPassed = $true

# 1. Verificar se esta rodando como administrador
Write-Host "`n1. Verificando privilegios de administrador..." -ForegroundColor Yellow
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Host "ERRO: Este script deve ser executado como Administrador!" -ForegroundColor Red
    $allChecksPassed = $false
} else {
    Write-Host "OK: Privilegios de administrador OK" -ForegroundColor Green
}

# 2. Verificar se o GitLab Runner esta rodando
Write-Host "`n2. Verificando GitLab Runner..." -ForegroundColor Yellow
$runnerService = Get-Service gitlab-runner -ErrorAction SilentlyContinue
if ($runnerService -and $runnerService.Status -eq "Running") {
    Write-Host "OK: GitLab Runner esta rodando" -ForegroundColor Green
} else {
    Write-Host "ERRO: GitLab Runner nao esta rodando" -ForegroundColor Red
    $allChecksPassed = $false
}

# 3. Verificar se PHP esta instalado
Write-Host "`n3. Verificando PHP..." -ForegroundColor Yellow
try {
    $phpVersion = php --version 2>$null
    if ($phpVersion) {
        Write-Host "OK: PHP instalado: $($phpVersion[0])" -ForegroundColor Green
    } else {
        Write-Host "ERRO: PHP nao encontrado no PATH" -ForegroundColor Red
        $allChecksPassed = $false
    }
} catch {
    Write-Host "ERRO: Erro ao verificar PHP: $($_.Exception.Message)" -ForegroundColor Red
    $allChecksPassed = $false
}

# 4. Verificar se Composer esta instalado
Write-Host "`n4. Verificando Composer..." -ForegroundColor Yellow
try {
    $composerVersion = composer --version 2>$null
    if ($composerVersion) {
        Write-Host "OK: Composer instalado: $($composerVersion[0])" -ForegroundColor Green
    } else {
        Write-Host "ERRO: Composer nao encontrado no PATH" -ForegroundColor Red
        $allChecksPassed = $false
    }
} catch {
    Write-Host "ERRO: Erro ao verificar Composer: $($_.Exception.Message)" -ForegroundColor Red
    $allChecksPassed = $false
}

# 5. Verificar se o diretorio fonte existe
Write-Host "`n5. Verificando diretorio fonte..." -ForegroundColor Yellow
if (Test-Path $SourceDir) {
    Write-Host "OK: Diretorio fonte existe: $SourceDir" -ForegroundColor Green
    
    # Verificar se e um projeto Laravel
    if (Test-Path "$SourceDir\artisan") {
        Write-Host "OK: Projeto Laravel detectado" -ForegroundColor Green
    } else {
        Write-Host "AVISO: Diretorio nao parece ser um projeto Laravel (artisan nao encontrado)" -ForegroundColor Yellow
    }
} else {
    Write-Host "ERRO: Diretorio fonte nao existe: $SourceDir" -ForegroundColor Red
    $allChecksPassed = $false
}

# 6. Verificar conectividade com o servidor flautim
Write-Host "`n6. Verificando conectividade com servidor flautim..." -ForegroundColor Yellow
try {
    if (Test-Path "\\flautim\g$") {
        Write-Host "OK: Conectividade com flautim OK" -ForegroundColor Green
        
        # Verificar se o diretorio de deploy existe ou pode ser criado
        if (Test-Path $DeployDir) {
            Write-Host "OK: Diretorio de deploy existe: $DeployDir" -ForegroundColor Green
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

# 7. Verificar se o modulo WebAdministration esta disponivel
Write-Host "`n7. Verificando modulo WebAdministration..." -ForegroundColor Yellow
try {
    Import-Module WebAdministration -ErrorAction Stop
    Write-Host "OK: Modulo WebAdministration carregado" -ForegroundColor Green
    
    # Verificar se o site existe no IIS
    $site = Get-Website -Name $ProjectName -ErrorAction SilentlyContinue
    if ($site) {
        Write-Host "OK: Site '$ProjectName' existe no IIS" -ForegroundColor Green
        Write-Host "   Status: $($site.State)" -ForegroundColor White
        Write-Host "   Application Pool: $($site.ApplicationPool)" -ForegroundColor White
    } else {
        Write-Host "AVISO: Site '$ProjectName' nao existe no IIS (sera criado pelo script de setup)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "ERRO: Erro ao carregar WebAdministration: $($_.Exception.Message)" -ForegroundColor Red
    $allChecksPassed = $false
}

# 8. Verificar diretorio de backup
Write-Host "`n8. Verificando diretorio de backup..." -ForegroundColor Yellow
if (Test-Path $BackupDir) {
    Write-Host "OK: Diretorio de backup existe: $BackupDir" -ForegroundColor Green
} else {
    Write-Host "AVISO: Diretorio de backup nao existe, sera criado durante o deploy" -ForegroundColor Yellow
}

# 9. Verificar arquivo .env
Write-Host "`n9. Verificando arquivo .env..." -ForegroundColor Yellow
if (Test-Path "$SourceDir\.env") {
    Write-Host "OK: Arquivo .env existe no projeto" -ForegroundColor Green
} else {
    Write-Host "AVISO: Arquivo .env nao existe, sera criado durante o build" -ForegroundColor Yellow
}

# 10. Verificar dependencias do projeto
Write-Host "`n10. Verificando dependencias do projeto..." -ForegroundColor Yellow
if (Test-Path "$SourceDir\composer.json") {
    Write-Host "OK: composer.json encontrado" -ForegroundColor Green
} else {
    Write-Host "ERRO: composer.json nao encontrado" -ForegroundColor Red
    $allChecksPassed = $false
}

# Resumo final
Write-Host "`n=== Resumo da Verificacao ===" -ForegroundColor Cyan
if ($allChecksPassed) {
    Write-Host "OK: Todos os pre-requisitos estao atendidos!" -ForegroundColor Green
    Write-Host "O pipeline pode ser executado com seguranca" -ForegroundColor Green
} else {
    Write-Host "ERRO: Alguns pre-requisitos nao foram atendidos" -ForegroundColor Red
    Write-Host "Corrija os problemas antes de executar o pipeline" -ForegroundColor Yellow
}

Write-Host "`n=== Proximos Passos ===" -ForegroundColor Cyan
if ($allChecksPassed) {
    Write-Host "1. Execute o script de configuracao do IIS:" -ForegroundColor White
    Write-Host "   .\scripts\configure-deploy.ps1" -ForegroundColor Gray
    Write-Host "2. Configure o arquivo .env no servidor se necessario" -ForegroundColor White
    Write-Host "3. Execute o pipeline no GitLab" -ForegroundColor White
} else {
    Write-Host "1. Corrija os problemas identificados acima" -ForegroundColor White
    Write-Host "2. Execute este script novamente para verificar" -ForegroundColor White
    Write-Host "3. Apos correcao, execute o script de configuracao do IIS" -ForegroundColor White
}

Write-Host "`nPara mais informacoes, consulte: documentation/CI-CD-PIPELINE.md" -ForegroundColor Gray