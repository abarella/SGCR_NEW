# Procedures para Definição de Série

Este documento lista as procedures necessárias para o funcionamento do módulo "Definição de Série" no Laravel, baseadas nas procedures reais do sistema antigo em PHP.

## Procedures Utilizadas

### 1. Carregar Produtos
**Procedure:** `vendasPelicano.dbo.P0250_Produto_GRUPO`
```sql
EXEC vendasPelicano.dbo.P0250_Produto_GRUPO @cdusuario = ?, @resulta = 0, @mensa = ''
```
**Retorno:** XML com produtos disponíveis para o usuário
**Campos:** `prod_cod510`

### 2. Carregar Lotes
**Localização:** `GlobalService::carregarLotes()`
**Procedure:** `vendasPelicano.dbo.P0100_Calendario_Produto`
```sql
EXEC vendasPelicano.dbo.P0100_Calendario_Produto @p100prod = ?, @tipo = 1
```
**Retorno:** XML com lotes do produto
**Campos:** `p100lote`

### 3. Retornar Lista de Série
**Procedure:** `vendasPelicano.dbo.P0110_SERIE_LISTA`
```sql
SET NOCOUNT ON; 
EXEC vendasPelicano.dbo.P0110_SERIE_LISTA @produto = ?, @lote = ?, @ORDEM = ?
```
**Retorno:** XML com lista de séries
**Campos:** `p110lote`, `cli_dest_responsavel`, `uf_codigo`, `p110atv`, `p110serie`, `p110prod`, `p110dtpx`, `p110obsd`, `p110chve`

### 4. Carregar Séries Disponíveis
**Procedure:** `sgcr.crsa.PPST_SERIE`
```sql
EXEC sgcr.crsa.PPST_SERIE @produto = ?, @lote = ?
```
**Retorno:** XML com séries disponíveis
**Campos:** `pst_serie`, `numero`

### 5. Carregar Técnicos
**Procedure:** `crsa.P1110_USUARIOS_MMRD`
```sql
EXEC crsa.P1110_USUARIOS_MMRD
```
**Retorno:** XML com técnicos operadores
**Campos:** `p1110_usuarioid`, `p1110_nome`

### 6. Gravar Série Individual
**Procedure:** `vendasPelicano.dbo.P0110_SERIE_01`
```sql
DECLARE @resulta INT, @mensa VARCHAR(100);
EXEC vendasPelicano.dbo.P0110_SERIE_01 
    @p110chve = ?, 
    @serie = ?, 
    @cdusuario = ?, 
    @senha = ?, 
    @resulta = @resulta OUTPUT, 
    @mensa = @mensa OUTPUT;
SELECT @resulta AS resulta, @mensa AS mensa;
```

### 7. Gravar Série por Intervalo
**Procedure:** `vendasPelicano.dbo.P0110_SERIE`
```sql
DECLARE @resulta INT, @mensa VARCHAR(100);
EXEC vendasPelicano.dbo.P0110_SERIE 
    @produto = ?, 
    @lote = ?, 
    @serie = ?, 
    @tipo = ?, 
    @inicio = ?, 
    @fim = ?, 
    @forca = ?,
    @resulta = @resulta OUTPUT, 
    @mensa = @mensa OUTPUT,
    @cdusuario = ?, 
    @senha = ?;
SELECT @resulta AS resulta, @mensa AS mensa;
```

### 8. Validar Senha
**Localização:** `GlobalService::validaAcesso()`
**Procedure:** `SGCR.crsa.P1110_Login`
```sql
exec SGCR.crsa.P1110_Login 'usuario','senha', '','',''
```
**Retorno:** Objeto com dados do usuário se válido, string vazia se inválido
**Comportamento:** Retorna objeto do usuário ou string vazia, não boolean

## Observações Importantes

1. **XML Parsing:** Todas as procedures retornam dados em formato XML que precisam ser parseados
2. **Tratamento de Erros:** As procedures de gravação retornam códigos de resultado e mensagens
3. **Validação de Senha:** Implementada no `GlobalService` como método estático, não como procedure
4. **Carregamento de Lotes:** Implementado no `GlobalService` como método estático
5. **Parâmetros:** Algumas procedures requerem parâmetros específicos como `@tipo` e `@ORDEM`
6. **Resultado:** Procedures de gravação retornam `resulta = 0` para sucesso

## Implementação no Laravel

O `DefinicaoSerieService` implementa todas essas procedures com:
- Parsing correto de XML
- Tratamento de erros
- Logging de exceções
- Retorno de objetos estruturados
- Validação de senha via `GlobalService::validaAcesso()`
- Carregamento de lotes via `GlobalService::carregarLotes()` 