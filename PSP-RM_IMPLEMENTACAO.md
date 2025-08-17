# Implementação PSP-RM (R.D. & M.M.)

## Visão Geral

Esta implementação refatora a funcionalidade legada de R.D. & M.M. (Radioisotopos e Moléculas Marcadas) do sistema ASP para uma arquitetura MVC moderna com Laravel.

## Arquivos Legados Referenciados

- `cr_pst03.asp` - Página principal de listagem de produtos
- `cr_pst03.js` - JavaScript para funcionalidades da página principal
- `cr_calibracao.asp` - Modal de calibração
- `cr_calibracao.js` - JavaScript para funcionalidades de calibração
- `cr_pst03_grid.xsl` - Template XSL para grid de dados

## Estrutura MVC Implementada

### 1. Controller
**Arquivo:** `app/Http/Controllers/PspRmController.php`

**Funcionalidades:**
- `index()` - Página principal
- `listarProdutos()` - Lista produtos por categoria e lote
- `atualizarProducoes()` - Atualiza número de produções
- `abrirCalibracao()` - Abre modal de calibração
- `atualizarCalibracao()` - Atualiza dados de calibração

### 2. Service
**Arquivo:** `app/Services/PspRmService.php`

**Funcionalidades:**
- `validarSenha()` - Valida senha do usuário
- `listarProdutos()` - Lista produtos usando procedure `P0250_Produto_RDMM`
- `atualizarProducoes()` - Atualiza produções usando procedure `PPST_RR_MM`
- `obterDadosCalibracao()` - Obtém dados de calibração usando procedure `PPST_LISTA7A`
- `atualizarCalibracao()` - Atualiza calibração (procedure `PPST_AtualizarCalibracao` - precisa ser implementada)

### 3. Model
**Arquivo:** `app/Models/ProdutoRdmm.php`

**Campos:**
- `prod_cod510` - Código do produto
- `categoria` - Categoria (1=Radioisotopos Primarios, 3=Moléculas Marcadas)
- `lote` - Número do lote
- `num_producoes` - Número de produções
- `data_calibracao` - Data de calibração
- `partidas` - Número de partidas
- `pst_serie` - Série autorizada
- `atividade`, `concentracao`, `volume` - Dados de calibração
- `observacoes` - Observações
- `usuario_id` - ID do usuário
- `data_atualizacao` - Data da última atualização

### 4. Views
**Arquivo:** `resources/views/psp-rm/index.blade.php`

**Componentes:**
- Formulário de busca por categoria e lote
- Grid de produtos com campos editáveis
- Modal de calibração
- Modal de confirmação de senha

### 5. JavaScript
**Arquivo:** `public/js/psp-rm.js`

**Funcionalidades:**
- Busca de produtos via AJAX
- Validação de campos
- Abertura de modal de calibração
- Atualização de produções
- Atualização de calibração
- Sistema de alertas

## Procedures SQL Server Utilizadas

### Procedures Existentes:
1. **`P0250_Produto_RDMM`** - Lista produtos por categoria e lote
2. **`PPST_RR_MM`** - Atualiza número de produções
3. **`PPST_LISTA7A`** - Lista dados de calibração
4. **`P1110_CONFSENHA`** - Valida senha do usuário

### Procedures que Precisam ser Implementadas:
1. **`PPST_AtualizarCalibracao`** - Atualiza dados de calibração

## Rotas Implementadas

```php
Route::prefix('psp-rm')->group(function () {
    Route::get('/', [PspRmController::class, 'index'])->name('psp-rm.index');
    Route::get('/listar-produtos', [PspRmController::class, 'listarProdutos'])->name('psp-rm.listar-produtos');
    Route::post('/atualizar-producoes', [PspRmController::class, 'atualizarProducoes'])->name('psp-rm.atualizar-producoes');
    Route::get('/abrir-calibracao', [PspRmController::class, 'abrirCalibracao'])->name('psp-rm.abrir-calibracao');
    Route::post('/atualizar-calibracao', [PspRmController::class, 'atualizarCalibracao'])->name('psp-rm.atualizar-calibracao');
});
```

## Funcionalidades Implementadas

### 1. Listagem de Produtos
- Busca por categoria (Radioisotopos Primarios ou Moléculas Marcadas)
- Busca por lote
- Exibição em grid com campos editáveis

### 2. Atualização de Produções
- Edição inline do campo "Número de Produções"
- Validação de senha antes da atualização
- Uso da procedure `PPST_RR_MM`

### 3. Sistema de Calibração
- Modal para edição de dados de calibração
- Campos: Série, Data/Hora da Calibração, Produção, Observação
- Validação de senha antes da atualização

### 4. Validação de Segurança
- Middleware de autenticação
- Validação de senha para operações críticas
- Uso da procedure `P1110_CONFSENHA`

## Dependências

- Laravel Framework
- AdminLTE (já configurado no projeto)
- Bootstrap (já configurado no projeto)
- jQuery (já configurado no projeto)

## Como Usar

1. Acesse a URL `/psp-rm` no menu AdminLTE
2. Selecione a categoria (Radioisotopos Primarios ou Moléculas Marcadas)
3. Digite o número do lote
4. Clique em buscar para listar os produtos
5. Edite o campo "Número de Produções" e pressione Enter
6. Digite sua senha para confirmar a alteração
7. Clique no ícone de engrenagem para abrir o modal de calibração
8. Preencha os dados de calibração e salve

## Observações Importantes

1. **Tabelas:** As tabelas já existem no SQL Server e são referenciadas pelas procedures
2. **Procedures:** A maioria das procedures já existe, exceto `PPST_AtualizarCalibracao`
3. **Segurança:** Todas as operações críticas requerem validação de senha
4. **Compatibilidade:** A implementação mantém a mesma funcionalidade do sistema legado
5. **Interface:** Interface moderna usando AdminLTE e Bootstrap

## Próximos Passos

1. Implementar a procedure `PPST_AtualizarCalibracao` no SQL Server
2. Testar todas as funcionalidades em ambiente de desenvolvimento
3. Validar com usuários finais
4. Implementar testes automatizados
5. Documentar procedimentos de manutenção

## Suporte

Para dúvidas ou problemas, consulte:
- Logs do Laravel em `storage/logs/laravel.log`
- Console do navegador para erros JavaScript
- Logs do SQL Server para erros de procedures
