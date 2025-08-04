# Estrutura de Pastas do Projeto

```
app/
  Models/           # Models Eloquent (User, grupo, etc)
  Http/
    Controllers/    # Controllers principais e de autenticação
    Middleware/     # Middlewares customizados
  Providers/        # Service Providers
  Services/         # Serviços customizados
  Ldap/             # Integração LDAP
bootstrap/          # Arquivos de bootstrap do Laravel
config/             # Arquivos de configuração
public/             # Raiz pública do projeto (index.php, assets)
resources/
  views/            # Views Blade (telas)
  lang/             # Traduções
routes/             # Arquivos de rotas (web.php, console.php)
storage/             # Arquivos gerados, cache, logs
vendor/              # Dependências do Composer
tests/               # Testes unitários e de feature
```

## Descrição dos principais diretórios
- **app/**: Código-fonte principal da aplicação.
- **app/Models/**: Representação das entidades do banco de dados.
- **app/Http/Controllers/**: Lógica de controle das rotas e requisições.
- **app/Http/Middleware/**: Filtros e camadas intermediárias de requisições.
- **app/Providers/**: Inicialização de serviços e bindings do Laravel.
- **app/Services/**: Lógica de negócio reutilizável.
- **app/Ldap/**: Integração com diretórios LDAP.
- **resources/views/**: Templates Blade para renderização das páginas.
- **routes/**: Definição das rotas da aplicação.
- **tests/**: Testes automatizados.
- **public/**: Arquivos públicos e ponto de entrada da aplicação.
- **config/**: Configurações do framework e pacotes. 