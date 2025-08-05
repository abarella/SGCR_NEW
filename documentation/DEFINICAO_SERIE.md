# Definição de Série - Documentação

## Visão Geral

O módulo de **Definição de Série** permite gerenciar séries de produtos radiofarmacêuticos no sistema SGCR. Este módulo foi migrado do sistema PHP puro para Laravel, mantendo todas as funcionalidades originais.

## Estrutura do Módulo

### Arquivos Criados

#### Controllers
- `app/Http/Controllers/DefinicaoSerie/DefinicaoSerieController.php`

#### Services
- `app/Services/DefinicaoSerieService.php`

#### Models
- `app/Models/DefinicaoSerie.php`

#### Views
- `resources/views/definicaoserie/index.blade.php`
- `resources/views/definicaoserie/intervalo.blade.php`
- `resources/views/definicaoserie/intervalo-lote.blade.php`

#### Migrations
- `database/migrations/2025_01_30_000000_create_definicao_serie_table.php`

#### Seeders
- `database/seeders/DefinicaoSerieSeeder.php`

## Funcionalidades

### 1. Página Principal (`/dfv-ds`)
- Seleção de produto e lote
- Listagem de itens com suas séries
- Definição individual de série
- Links para definição por intervalo

### 2. Definição por Intervalo de Atividade (`/dfv-ds/intervalo`)
- Define série para um intervalo de atividades
- Filtros: "Para todos" ou "Somente os sem série"
- Validação de senha

### 3. Definição por Intervalo de Lote/Número (`/dfv-ds/intervalo-lote`)
- Define série para um intervalo de lotes/números
- Filtros: "Para todos" ou "Somente os sem série"
- Validação de senha

## Rotas

| Método | URL | Descrição |
|--------|-----|-----------|
| GET | `/dfv-ds` | Página principal |
| POST | `/dfv-ds/carregar-lotes` | Carrega lotes por produto (AJAX) |
| POST | `/dfv-ds/pesquisar-lista-serie` | Pesquisa lista de série (AJAX) |
| POST | `/dfv-ds/definir-serie` | Define série individual |
| GET | `/dfv-ds/intervalo` | Página de intervalo de atividade |
| POST | `/dfv-ds/definir-serie-intervalo` | Define série por intervalo de atividade |
| GET | `/dfv-ds/intervalo-lote` | Página de intervalo de lote |
| POST | `/dfv-ds/definir-serie-intervalo-lote` | Define série por intervalo de lote |
| POST | `/dfv-ds/buscar-serie` | Busca série existente |

## Banco de Dados

### Tabela `p110`
```sql
CREATE TABLE p110 (
    chve BIGINT PRIMARY KEY IDENTITY(1,1),
    produto VARCHAR(10),
    lote VARCHAR(20),
    numero INT,
    medico VARCHAR(100),
    uf VARCHAR(2),
    atividade DECIMAL(10,2),
    serie VARCHAR(50),
    producao DATETIME,
    calibracao DATETIME,
    observacao TEXT,
    usuario_alteracao VARCHAR(50),
    data_alteracao DATETIME
);
```

## Procedures Necessárias

### 1. `sp_gravar_serie`
```sql
CREATE PROCEDURE sp_gravar_serie
    @p110chve BIGINT,
    @p110serie VARCHAR(50),
    @cdusuario INT
AS
BEGIN
    UPDATE p110 
    SET serie = @p110serie,
        usuario_alteracao = @cdusuario,
        data_alteracao = GETDATE()
    WHERE chve = @p110chve;
END
```

### 2. `sp_gravar_serie_atividade`
```sql
CREATE PROCEDURE sp_gravar_serie_atividade
    @produto VARCHAR(10),
    @lote VARCHAR(20),
    @serie VARCHAR(50),
    @cdusuario INT,
    @senha VARCHAR(50),
    @tipo INT,
    @inicio INT,
    @fim INT,
    @forca CHAR(1)
AS
BEGIN
    -- Validação de senha
    IF NOT EXISTS (SELECT 1 FROM usuarios WHERE cdusuario = @cdusuario AND senha = @senha AND ativo = 'S')
    BEGIN
        RAISERROR('Senha inválida!', 16, 1);
        RETURN;
    END

    -- Atualização baseada no tipo (1=atividade, 2=lote)
    IF @tipo = 1
    BEGIN
        -- Por atividade
        UPDATE p110 
        SET serie = @serie,
            usuario_alteracao = @cdusuario,
            data_alteracao = GETDATE()
        WHERE produto = @produto 
        AND lote = @lote 
        AND numero BETWEEN @inicio AND @fim
        AND (@forca = 'S' OR (@forca = 'N' AND serie IS NULL));
    END
    ELSE
    BEGIN
        -- Por lote/número
        UPDATE p110 
        SET serie = @serie,
            usuario_alteracao = @cdusuario,
            data_alteracao = GETDATE()
        WHERE produto = @produto 
        AND lote = @lote 
        AND numero BETWEEN @inicio AND @fim
        AND (@forca = 'S' OR (@forca = 'N' AND serie IS NULL));
    END
END
```

## Configuração

### 1. Executar Migration
```bash
php artisan migrate
```

### 2. Executar Seeder (opcional)
```bash
php artisan db:seed --class=DefinicaoSerieSeeder
```

### 3. Verificar Menu
O módulo já está configurado no menu AdminLTE com o prefixo `dfv-ds`.

## Validações

### Campos Obrigatórios
- Produto
- Lote
- Série
- Senha do usuário
- Técnico operador (nas páginas de intervalo)

### Validações Específicas
- Atividade/Lote inicial não pode ser maior que o final
- Senha deve ser válida
- Filtro deve ser selecionado

## Segurança

- Todas as operações requerem validação de senha
- Logs de erro são registrados
- Validação de dados no frontend e backend
- Proteção CSRF em todas as rotas

## Integração com Sistema Existente

### GlobalService
Funções adicionadas ao `GlobalService`:
- `validaAcesso()` - Valida senha do usuário

### Padrão de Nomenclatura
- Prefixo de rotas: `dfv-ds` (Definição & Verificação - Definição de Série)
- Namespace: `App\Http\Controllers\DefinicaoSerie`
- Views: `resources/views/definicaoserie/`

## Troubleshooting

### Problemas Comuns

1. **Erro de conexão com banco**
   - Verificar configuração do `.env`
   - Verificar drivers SQL Server

2. **Procedures não encontradas**
   - Executar scripts SQL das procedures
   - Verificar permissões no banco

3. **Erro de validação de senha**
   - Verificar se o usuário existe na tabela `usuarios`
   - Verificar se o campo `ativo = 'S'`

4. **AJAX não funcionando**
   - Verificar token CSRF
   - Verificar console do navegador para erros JavaScript

## Próximos Passos

1. Testar todas as funcionalidades
2. Ajustar procedures conforme necessidade
3. Implementar logs mais detalhados
4. Adicionar testes unitários
5. Documentar procedures específicas do ambiente 