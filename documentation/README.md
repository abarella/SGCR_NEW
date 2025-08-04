# Documentação do Projeto Laravel - SGCR

## Visão Geral
Este projeto é uma aplicação web baseada em Laravel, utilizando o template ADMINLTE e banco de dados SQL Server. O sistema implementa autenticação, gerenciamento de usuários, grupos, assinaturas, assuntos, aplicações e integrações com LDAP.

## Estrutura de Pastas
- **app/Models/**: Models Eloquent (User, grupo, usuario_aplicacao, aplicacao, assunto, assinatura, emissor)
- **app/Http/Controllers/**: Controllers principais (GrupoController, Usuario_AplicacaoController, AplicacaoController, AssuntoController, AssinaturaController, EmissorController, HomeController, Auth/*)
- **app/Services/**: Serviços customizados (GlobalService)
- **app/Providers/**: Service Providers (AppServiceProvider, AuthServiceProvider, TelescopeServiceProvider)
- **app/Ldap/**: Integrações LDAP
- **routes/web.php**: Rotas web
- **resources/views/**: Views Blade (login, dashboard, etc)
- **tests/**: Testes unitários e de feature

## Dependências e Requisitos
- **PHP**: 8.2.15
- **Laravel**: conforme composer.json
- **Banco de Dados**: SQL Server
- **Extensões PHP**: 
  - php_pdo_sqlsrv_82_ts_x64.dll
  - php_sqlsrv_82_ts_x64.dll
- **ADMINLTE**: Template de interface

## Configuração Inicial
1. Instale as dependências com `composer install` e `npm install`.
2. Configure o arquivo `.env` para conexão com SQL Server.
3. Certifique-se de habilitar as extensões do SQL Server no `php.ini`.
4. Execute as migrations se necessário.
5. Para rodar os testes: `php artisan test`

## Principais Models
- **User**: Usuário do sistema, integra com LDAP, autenticação, e possui campos fillable: name, email, password.
- **grupo, usuario_aplicacao, aplicacao, assunto, assinatura, emissor**: Entidades de domínio do sistema.

## Principais Controllers
- **Auth/**: Login, registro, recuperação de senha, verificação de email.
- **GrupoController, Usuario_AplicacaoController, AplicacaoController, AssuntoController, AssinaturaController, EmissorController**: CRUD das entidades principais.
- **HomeController**: Dashboard inicial.
- **ImageController**: Upload de imagens.

## Rotas Principais (routes/web.php)


## Autenticação
- .
- .

## Testes
- Testes unitários e de feature em `tests/`
- Para rodar: `php artisan test`

## Observações
- O projeto utiliza o ADMINLTE para interface administrativa.
- O banco de dados padrão é SQL Server, mas pode ser adaptado para outros bancos.
- Para dúvidas sobre configuração, consulte o README original ou a documentação oficial do Laravel.

---

