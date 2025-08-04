# Pipeline de CI/CD para Deploy Automático no IIS

Este documento descreve o pipeline de CI/CD configurado para automatizar o deploy da aplicação Laravel no IIS após merge na branch principal.

## 📋 Pré-requisitos

### 1. GitLab Runner Configurado
- ✅ Runner registrado e funcionando
- ✅ Tags configuradas: `windows`, `iis`, `dev`
- ✅ Executor: `shell`

### 2. IIS Configurado
- ✅ IIS instalado e funcionando
- ✅ Módulo WebAdministration habilitado
- ✅ Permissões de administrador para o usuário do runner
- ✅ Acesso ao servidor `flautim` via rede

### 3. PHP e Composer
- ✅ PHP 8.2+ instalado
- ✅ Composer instalado
- ✅ Extensões PHP necessárias habilitadas

## 🚀 Configuração Inicial

### 1. Configurar o IIS
Execute o script de configuração como Administrador:

```powershell
# Navegar até o diretório do projeto
cd "C:\Users\luiz.f-basis\Sistemas\sgcr_novo"

# Executar script de configuração
.\scripts\setup-iis.ps1
```

**Parâmetros opcionais:**
```powershell
.\scripts\setup-iis.ps1 -SiteName "meu-site" -Port "8080"
```

### 2. Configurar Variáveis de Ambiente
Crie o arquivo `.env` no diretório de deploy (`\\flautim\g$\inetpub\wwwroot\SGCR_NEW`):

```env
APP_NAME="SGCR Novo"
APP_ENV=production
APP_KEY=base64:sua-chave-aqui
APP_DEBUG=false
APP_URL=http://des-sgcr_novo.ipen.br

DB_CONNECTION=sqlsrv
DB_HOST=seu-servidor-sql
DB_PORT=1433
DB_DATABASE=colibri
DB_USERNAME=crsa
DB_PASSWORD=cr9537

# Outras configurações necessárias...
```

## 🔄 Como o Pipeline Funciona

### Stages do Pipeline

1. **Build Stage**
   - Instala dependências
   - Otimiza a aplicação (cache, routes, views)
   - Gera artifacts para deploy

2. **Deploy Stage**
   - Cria backup da versão atual
   - Para o site no IIS
   - Copia novos arquivos
   - Configura permissões
   - Inicia o site
   - Verifica se está funcionando

### Triggers
- ✅ Executa automaticamente após merge na `main` ou `master`
- ✅ Não executa em tags
- ✅ Deploy manual (requer aprovação)

## 📁 Estrutura de Diretórios

```
C:\Users\luiz.f-basis\Sistemas Ipen\sgcr_novo\  # Código fonte local
\\flautim\g$\inetpub\wwwroot\SGCR_NEW\          # Diretório de deploy no servidor
C:\backups\sgcr_novo\                           # Backups automáticos
```

## 🔧 Configurações Personalizáveis

### Variáveis do Pipeline
Edite o arquivo `.gitlab-ci.yml` para personalizar:

```yaml
variables:
  PROJECT_NAME: "sgcr_novo"                                    # Nome do projeto
  IIS_SITE_NAME: "sgcr_novo"                                   # Nome do site no IIS
  IIS_APP_POOL: "sgcr_novo"                                    # Application Pool
  SOURCE_DIR: "C:\\Users\\luiz.f-basis\\Sistemas\\sgcr_novo"  # Código fonte
  DEPLOY_DIR: "\\\\flautim\\g$\\inetpub\\wwwroot\\SGCR_NEW"        # Deploy no servidor
  BACKUP_DIR: "C:\\backups\\sgcr_novo"                              # Backups
```

### Tags do Runner
Certifique-se de que o runner tenha as tags corretas:
- `windows`
- `iis` 
- `dev`

## 🛠️ Comandos Úteis

### Verificar Status do Runner
```powershell
Get-Service gitlab-runner
```

### Verificar Status do Site no IIS
```powershell
Import-Module WebAdministration
Get-Website -Name "sgcr_novo"
```

### Verificar Conectividade com Servidor
```powershell
Test-Path "\\flautim\g$"
```

### Verificar Logs do Pipeline
- Acesse o GitLab → CI/CD → Pipelines
- Clique no pipeline desejado
- Visualize os logs de cada job

## 🔄 Rollback

Se algo der errado, use o job de rollback:

1. Vá para o pipeline no GitLab
2. Clique em "Rollback" 
3. Confirme a ação

O rollback irá:
- Parar o site
- Restaurar o backup mais recente
- Iniciar o site novamente

## 📊 Monitoramento

### Logs Importantes
- **Build logs**: `C:\GitLab-Runner\builds\`
- **Aplicação logs**: `\\flautim\g$\inetpub\wwwroot\SGCR_NEW\storage\logs\`
- **IIS logs**: `\\flautim\g$\inetpub\logs\LogFiles\`

### Verificações Pós-Deploy
1. Site acessível em `http://des-sgcr_novo.ipen.br`
2. Logs sem erros críticos
3. Funcionalidades principais funcionando

## 🚨 Troubleshooting

### Problemas Comuns

#### 1. Runner não executa
```powershell
# Verificar status
Get-Service gitlab-runner

# Reiniciar se necessário
Restart-Service gitlab-runner
```

#### 2. Problemas de Conectividade com Servidor
```powershell
# Verificar conectividade
Test-Path "\\flautim\g$"

# Verificar permissões de rede
net use \\flautim\g$ /user:seu-usuario

# Mapear drive se necessário
net use Z: \\flautim\g$ /persistent:yes
```

#### 3. Permissões no IIS
```powershell
# Verificar permissões
Get-Acl "\\flautim\g$\inetpub\wwwroot\SGCR_NEW"

# Corrigir permissões
$acl = Get-Acl "\\flautim\g$\inetpub\wwwroot\SGCR_NEW"
$accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule("IIS_IUSRS", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.SetAccessRule($accessRule)
Set-Acl "\\flautim\g$\inetpub\wwwroot\SGCR_NEW" $acl
```

#### 4. Site não inicia
```powershell
# Verificar status
Get-Website -Name "sgcr_novo"

# Verificar Application Pool
Get-IISAppPool -Name "sgcr_novo"

# Reiniciar Application Pool
Restart-WebAppPool -Name "sgcr_novo"
```

#### 5. Erro de PHP
- Verificar se PHP está no PATH
- Verificar extensões necessárias
- Verificar configuração do php.ini

### Logs de Debug
Para debug detalhado, adicione ao pipeline:
```yaml
script:
  - Set-PSDebug -Trace 1
  - # seus comandos aqui
  - Set-PSDebug -Off
```

## 📞 Suporte

Em caso de problemas:
1. Verifique os logs do pipeline no GitLab
2. Consulte os logs do IIS
3. Verifique as permissões de arquivo
4. Teste manualmente os comandos do pipeline
5. Verifique a conectividade com o servidor flautim

## 🔄 Atualizações

Para atualizar o pipeline:
1. Edite o arquivo `.gitlab-ci.yml`
2. Faça commit e push
3. O pipeline será executado automaticamente na próxima vez

---

**Última atualização:** $(Get-Date -Format "dd/MM/yyyy")
**Versão do pipeline:** 1.1