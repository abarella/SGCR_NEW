# Guia de Uso do Sistema

## Rodando o Projeto Localmente
1. Clone o repositório.
2. Instale as dependências:
   ```
   composer install
   npm install
   ```
3. Copie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente, especialmente a conexão com o SQL Server.
4. Gere a chave da aplicação:
   ```
   php artisan key:generate
   ```
5. Execute as migrations (se necessário):
   ```
   php artisan migrate
   ```
6. Inicie o servidor de desenvolvimento:
   ```
   php artisan serve
   ```
7. Acesse `http://localhost:8000` no navegador.

## Executando Testes
- Para rodar todos os testes:
  ```
  php artisan test
  ```

## Funcionalidades Principais
- **Login/Logout**: Autenticação padrão Laravel com integração LDAP.
- **CRUD de entidades**: Utilize as rotas `/emissor`, `/grupo`, `/assinatura`, `/assunto`, `/aplicacao`, `/usuario_aplicacao` para gerenciar os dados.
- **Upload de Imagem**: Rota `/image/upload`.
- **Dashboard**: Após login, acesso ao painel principal em `/home`.

## Dicas para Desenvolvedores
- Os controllers seguem o padrão resource do Laravel.
- Os models estão em `app/Models` e utilizam Eloquent.
- Para adicionar novas entidades, crie o model, migration, controller e resource route correspondente.
- Testes podem ser adicionados em `tests/Unit` (unitários) e `tests/Feature` (funcionais).

## Observações
- Certifique-se de habilitar as extensões do SQL Server no PHP.
- O template ADMINLTE é utilizado para a interface administrativa.
- Para dúvidas, consulte a documentação oficial do Laravel ou os arquivos em `documentation/`. 