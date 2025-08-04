# SGCR NOVO - Sistema de Gestão de Controle de Radiofármacos

## 📋 Visão Geral

O SGCR NOVO é um sistema web desenvolvido em Laravel para gestão de controle de radiofármacos do IPEN (Instituto de Pesquisas Energéticas e Nucleares). O sistema gerencia todo o ciclo de vida dos radiofármacos, desde a produção até a distribuição.

## 🏗️ Infraestrutura

### Tecnologias Utilizadas

- **Backend**: Laravel 11.x (PHP 8.2+)
- **Frontend**: AdminLTE 3.11 (Bootstrap 4)
- **Banco de Dados**: SQL Server (SQLSrv)
- **Autenticação**: Laravel UI + 2FA (Google2FA)
- **Debugging**: Laravel Telescope 5.10
- **Editor**: Summernote 0.8.8
- **Localização**: Laravel PT-BR Localization

### Requisitos do Sistema

- PHP 8.2 ou superior
- Composer 2.x
- SQL Server
- Extensões PHP: sqlsrv, pdo_sqlsrv, mbstring, xml, ctype, json, bcmath

## 🚀 Instalação e Configuração

### 1. Clone o Repositório
```bash
git clone [URL_DO_REPOSITORIO]
cd SGCR_NOVO
```

### 2. Instale as Dependências
```bash
composer install
npm install
```

### 3. Configure o Ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure o Banco de Dados
Edite o arquivo `.env`:
```env
DB_CONNECTION=sqlsrv
DB_HOST=seu_servidor_sql
DB_PORT=1433
DB_DATABASE=sgcr_novo
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 5. Execute as Migrações
```bash
php artisan migrate
```

### 6. Publique os Assets do AdminLTE
```bash
php artisan adminlte:install
php artisan adminlte:install --only=plugins
```

### 7. Configure o Telescope (Opcional)
```bash
php artisan telescope:install
php artisan migrate
```

## 📁 Estrutura de Pastas

```
SGCR_NOVO/
├── app/
│   ├── Console/Commands/          # Comandos Artisan
│   ├── Http/
│   │   ├── Controllers/          # Controladores da aplicação
│   │   │   ├── Auth/            # Controladores de autenticação
│   │   │   └── UserController/   # Controladores de usuário
│   │   └── Middleware/           # Middlewares customizados
│   ├── Ldap/                     # Integração LDAP
│   ├── Models/                    # Modelos Eloquent
│   ├── Providers/                 # Service Providers
│   └── Services/                  # Serviços da aplicação
├── config/                        # Arquivos de configuração
├── database/
│   ├── factories/                 # Factories para testes
│   ├── migrations/                # Migrações do banco
│   └── seeders/                   # Seeders
├── documentation/                 # Documentação do projeto
├── lang/                          # Arquivos de localização
├── public/                        # Arquivos públicos
│   └── vendor/                    # Assets de terceiros
├── resources/
│   ├── css/                       # Arquivos CSS
│   ├── js/                        # Arquivos JavaScript
│   ├── lang/                      # Traduções
│   ├── sass/                      # Arquivos SASS
│   └── views/                     # Views Blade
├── routes/                        # Definição de rotas
├── scripts/                       # Scripts de automação
├── storage/                       # Arquivos de armazenamento
└── tests/                         # Testes automatizados
```

## 🎨 Componentes e Configurações

### AdminLTE Configuration

O sistema utiliza o AdminLTE como interface principal, configurado em `config/adminlte.php`:

- **Título**: SGCR - PRODUÇÃO
- **Logo**: IPEN SGCR com imagem personalizada
- **Tema**: Sidebar light primary
- **Layout**: Responsivo com sidebar colapsável

### Plugins Configurados

- **DataTables**: Para tabelas interativas
- **Select2**: Para campos de seleção avançada
- **DateRangePicker**: Para seleção de intervalos de data
- **SweetAlert2**: Para notificações elegantes
- **Toastr**: Para notificações toast
- **Summernote**: Editor de texto rico
- **FontAwesome**: Ícones
- **Moment.js**: Manipulação de datas
- **jQuery UI**: Componentes interativos

## 🧭 Navegação do Menu

### 1. Produção RL
- **Folha de Produção** (`prl-fp`): Gestão de folhas de produção
- **Altera Estoque** (`prl-ae`): Modificação de estoque

### 2. Folha de Prod.-Embalado (`fpe`)
- Gestão de produtos embalados

### 3. Transferências
- **Transf. Material** (`trf-tm`): Transferência de materiais
- **Transf. Material-Doação** (`trf-td`): Transferências para doação
- **Consulta** (`trf-co`): Consulta de transferências
- **Altera Estoque Inicial** (`trf-ai`): Modificação de estoque inicial

### 4. Definição & Verificação
- **Definição de Série** (`dfv-ds`): Configuração de séries
- **Lista de Verificação** (`dfv-lv`): Listas de verificação
- **Registro de Limpeza** (`dfv-rl`): Registro de limpeza
- **Dados de Produção** (`dfv-dp`): Dados de produção
- **Dados de Produção - Intervalo** (`dfv-di`): Dados por intervalo

### 5. Folha de Produção MM (`fpm`)
- Gestão de folhas de produção MM

### 6. Planej. Produção RP (`ppr`)
- Planejamento de produção RP

### 7. Distribuição RP
- **Gálio** (`drp-ga`): Distribuição de Gálio
- **Gerador** (`drp-ge`): Distribuição de Gerador
- **Iodo** (`drp-io`): Distribuição de Iodo
- **Tálio** (`drp-tl`): Distribuição de Tálio

### 8. Pastas de Produção
- **Altera Datas** (`psp-ad`): Modificação de datas
- **R.D. & M.M.** (`psp-rm`): R.D. e M.M.
- **Reagentes Liofilizados** (`psp-rl`): Gestão de reagentes

- **Pastas Não Concluídas** (`psp-pc`): Pastas pendentes
- **Pastas Não Impressas** (`psp-pi`): Pastas não impressas

### 9. Embalagem
- **Folha de Embalagem** (`emb-fe`): Gestão de embalagem
- **Registra Lote & Castelo** (`emb-ls`): Registro de lotes
- **Recebimento de Blindagens** (`emb-rb`): Recebimento
- **Recebimento de Blindagens - Autorização** (`emb-ra`): Autorização
- **Retorno do Monobloco** (`emb-rm`): Retorno
- **Gelo Seco** (`emb-gs`): Gestão de gelo seco
- **Registra Anel & Disco** (`emb-ad`): Registro de anéis

### 10. Reemissão Documentos
- **Alterar Dose de Superfície** (`rdc-ds`): Modificação de dose
- **Guia de Monitoração** (`rdc-gm`): Guias
- **Folha de Dados** (`rdc-fd`): Folhas de dados
- **Ficha de Emergência** (`rdc-fe`): Fichas
- **Boleto** (`rdc-bl`): Boletos
- **Conjunto Completo** (`rdc-cc`): Conjuntos

### 11. Blindagem X Pasta (`bxp`)
- Relacionamento entre blindagem e pasta

## 🔧 Controllers Principais

- `HomeController`: Dashboard principal
- `PspAdController`: Gestão de alteração de datas
- `bxpController`: Gestão de blindagem x pasta
- `Auth/LoginController`: Autenticação
- `TwoFactorController`: Autenticação 2FA

## 🔐 Autenticação e Segurança

### Autenticação 2FA
O sistema implementa autenticação de dois fatores usando Google2FA:
- Geração de QR Code para dispositivos móveis
- Backup codes para recuperação
- Integração com Laravel UI

### Middleware de Autorização
- Verificação de permissões por Gate
- Controle de acesso baseado em roles
- Proteção de rotas sensíveis

## 🐛 Debugging e Monitoramento

### Laravel Telescope
Configurado para monitoramento em desenvolvimento:
- Logs de requisições
- Queries SQL
- Exceções
- Jobs em fila
- Cache hits/misses

**Acesso**: `/telescope` (apenas em ambiente local)

## 📊 Banco de Dados

### Conexão
- Driver: SQLSrv
- Configuração em `config/database.php`
- Migrações organizadas por funcionalidade

### Modelos Principais
- `User`: Usuários do sistema
- `Aplicacao`: Aplicações
- `Assinatura`: Assinaturas
- `Assunto`: Assuntos
- `Grupo`: Grupos de usuários

## 🚀 Deploy e Produção

### Scripts de Automação
Localizados em `scripts/`:
- `check-prerequisites-fixed.ps1`: Verificação de pré-requisitos
- `configure-deploy.ps1`: Configuração de deploy
- `setup-iis.ps1`: Configuração IIS

### Variáveis de Ambiente
```env
APP_ENV=production
APP_DEBUG=false
TELESCOPE_ENABLED=false
```

## 📝 Desenvolvimento

### Comandos Úteis
```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Gerar assets
npm run dev
npm run build

# Executar testes
php artisan test

# Verificar status das migrações
php artisan migrate:status
```

### Padrões de Código
- PSR-4 para autoloading
- Laravel Pint para formatação
- Documentação em português
- Comentários em português

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📄 Licença

Este projeto é propriedade do IPEN e está sob licença interna.

## 📞 Suporte

Para suporte técnico, entre em contato com a equipe de desenvolvimento do IPEN.

---

**Desenvolvido para o IPEN - Instituto de Pesquisas Energéticas e Nucleares**