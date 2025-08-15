# Módulo de Escala de Tarefas

## Visão Geral

Este módulo foi migrado do arquivo `refatorar/modEscala_Tarefas.php` para uma implementação completa em Laravel, mantendo todas as funcionalidades originais e melhorando a experiência do usuário.

## Funcionalidades

- **Listagem de Tarefas**: Exibe todas as tarefas da escala em uma tabela responsiva
- **Inserção de Tarefas**: Permite adicionar novas tarefas com validação de senha
- **Edição de Tarefas**: Permite modificar tarefas existentes
- **Exclusão de Tarefas**: Remove tarefas com confirmação e validação de senha
- **Validação de Senha**: Todas as operações críticas requerem validação de senha do usuário

## Estrutura de Arquivos

```
app/
├── Http/Controllers/
│   └── EscTrController.php            # Controller principal
├── Services/
│   └── SGFPService.php                 # Service com funções de negócio
resources/
├── views/
│   └── esc-tr/
│       └── index.blade.php             # View principal
public/
└── js/
    └── escalatarefas/
        └── app.js                      # JavaScript específico
routes/
└── web.php                             # Rotas do módulo
```

## Rotas

| Método | URL | Nome | Descrição |
|--------|-----|------|-----------|
| GET | `/esc-tr` | `escalatarefas.index` | Página principal |
| POST | `/esc-tr/store` | `escalatarefas.store` | Inserir nova tarefa |
| POST | `/esc-tr/update` | `escalatarefas.update` | Atualizar tarefa |
| POST | `/esc-tr/destroy` | `escalatarefas.destroy` | Excluir tarefa |
| GET | `/esc-tr/data` | `escalatarefas.data` | Dados para DataTables |

## Controller

### EscTrController

O controller gerencia todas as operações relacionadas às tarefas da escala:

- **index()**: Exibe a página principal com listagem de tarefas
- **store()**: Insere uma nova tarefa
- **update()**: Atualiza uma tarefa existente
- **destroy()**: Remove uma tarefa
- **getData()**: Retorna dados em formato JSON para DataTables

## Service

### SGFPService

O service contém as funções de negócio migradas do sistema original:

- **retornaEscalaTarefas()**: Lista todas as tarefas
- **retornaEscalaTarefasJson()**: Lista tarefas em formato JSON
- **inserirEscalaTarefa()**: Insere nova tarefa
- **atualizarEscalaTarefa()**: Atualiza tarefa existente
- **excluirEscalaTarefa()**: Remove tarefa
- **validaSenha()**: Valida senha do usuário

## View

### index.blade.php

A view principal inclui:

- Tabela responsiva com DataTables
- Modal para inserção de nova tarefa
- Modal para edição de tarefas
- Modal para confirmação de exclusão
- Validação de formulários
- Mensagens de feedback com SweetAlert2

## JavaScript

### app.js

Arquivo JavaScript específico com:

- Inicialização do DataTable usando AdminLTE
- Configuração de formulários
- Funções AJAX para operações CRUD
- Validações e feedback visual
- Gerenciamento de estados dos botões
- Integração com plugins AdminLTE (DataTables, SweetAlert2)

## Banco de Dados

### Tabela ESCALA_TAREFAS

```sql
CREATE TABLE sgcr.crsa.ESCALA_TAREFAS (
    nr_ID INT IDENTITY(1,1) PRIMARY KEY,
    txtNome VARCHAR(255) NOT NULL,
    dtCriacao DATETIME DEFAULT GETDATE(),
    txtUsuario VARCHAR(100),
    dtAlteracao DATETIME NULL,
    txtUsuarioAlteracao VARCHAR(100) NULL
);
```

## Configuração do Menu

O módulo já está configurado no menu AdminLTE:

```php
[
    'text' => 'Esscala',
    'icon' => 'fas fa-cogs',
    'submenu' => [
        [
            'text' => 'Tarefas',
            'url' => 'esc-tr',
            'icon' => 'fas fa-list-ol',
        ],
        // ... outros itens
    ],
],
```

## Dependências

- **Laravel**: Framework base
- **AdminLTE**: Template de interface em português (inclui DataTables e SweetAlert2)
- **jQuery**: Manipulação do DOM e AJAX

## Segurança

- Todas as rotas são protegidas por middleware de autenticação
- **Usuário Autenticado**: O sistema utiliza `auth()->user()->cdusuario` para obter o código do usuário logado
- Validação de senha para operações críticas
- Proteção CSRF em todos os formulários
- Validação de entrada em todos os campos
- Logs de auditoria para operações importantes

## Uso

1. Acesse o menu "Esscala" > "Tarefas"
2. Use o botão "Nova Tarefa" para adicionar tarefas
3. Clique nos ícones de edição ou exclusão para modificar/remover
4. Todas as operações requerem validação de senha

## Manutenção

Para adicionar novas funcionalidades:

1. Adicione métodos no controller
2. Implemente funções no service
3. Crie rotas correspondentes
4. Atualize a view conforme necessário
5. Adicione JavaScript para interações

## Troubleshooting

### Problemas Comuns

1. **Erro de conexão com banco**: Verifique configurações de conexão
2. **Problemas de permissão**: Confirme se o usuário tem acesso à tabela
3. **Erro de validação**: Verifique se a senha está correta
4. **Problemas de JavaScript**: Verifique console do navegador

### Logs

Todos os erros são registrados em:
- `storage/logs/laravel.log`
- Logs específicos do service com contexto detalhado
