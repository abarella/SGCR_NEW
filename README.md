# SGCR_NEW

Sistema de Gerenciamento e Controle de Registros

## Estrutura do Projeto

- **Backend:** Laravel (PHP)
- **Frontend:** Blade + AdminLTE
- **Banco de Dados:** SQLite (padrão), compatível com outros via configuração
- **Autenticação:** Laravel Auth
- **Gerenciamento de Usuários**
- **Controle de Permissões**
- **Dashboard**
- **Módulos Principais:**
  
  - PSP-AD (Pedidos)
  - PSP-PS
  - BXP
  - FPE
  - PRL-AE
  - PRL-FP
  - Usuário Aplicação

## Estrutura de Pastas

```
app/
  Console/
  Http/
    Controllers/
  Ldap/
  Models/
  Providers/
  Services/
resources/
  views/
    home.blade.php
    psp-ad/
    psp-ps/
    bxp/
    prl-ae/
    prl-fp/
    fpe/
    usuario_aplicacao.blade.php
routes/
  web.php
database/
  migrations/
  seeders/
public/
  build/
  css/
  js/
```

## Estrutura do Menu Principal

- **Dashboard**
- **PSP-AD**
- **PSP-PS**
- **BXP**
- **FPE**
- **PRL-AE**
- **PRL-FP**
- **Usuários**

## Funcionalidades

- Cadastro, edição e exclusão de registros em todos os módulos
- Listagem com filtros e paginação
- Exportação de dados
- Controle de acesso por usuário
- Integração com DataTables (AdminLTE)
- Autenticação e autorização
- Dashboard com indicadores

## Instalação

1. Instale as dependências:
   ```
   composer install
   npm install
   npm run build
   ```
2. Configure o `.env` conforme seu ambiente.
3. Execute as migrations:
   ```
   php artisan migrate
   ```
4. Inicie o servidor:
   ```
   php artisan serve
   ```

## Observações

- O menu pode ser customizado em `resources/views/layouts/`.
- Para adicionar novos módulos, crie o Controller, Model e View correspondentes.
- O sistema utiliza componentes do AdminLTE para tabelas, formulários e layout.
