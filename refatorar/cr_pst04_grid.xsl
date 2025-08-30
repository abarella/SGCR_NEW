<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
    
    <!-- Template principal -->
    <xsl:template match="/">
        <xsl:apply-templates select="//row"/>
    </xsl:template>
    
    <!-- Template para cada linha de dados -->
    <xsl:template match="row">
        <xsl:if test="@pst_numero">
            <tr>
                <!-- Coluna de Funções -->
                <td>
                    <xsl:call-template name="gerarBotoesFuncoes"/>
                </td>
                
                <!-- Número da Pasta -->
                <td class="text-right">
                    <xsl:value-of select="@pst_numero"/>
                </td>
                
                <!-- Produto -->
                <td>
                    <xsl:value-of select="@nome_comercial"/>
                </td>
                
                <!-- Lote -->
                <td>
                    <xsl:value-of select="@Lote"/>
                </td>
                
                <!-- Lote Novo -->
                <td>
                    <xsl:value-of select="@pst_ano_lote"/>
                </td>
                
                <!-- Ano -->
                <td>
                    <xsl:value-of select="@pst_ano"/>
                </td>
                
                <!-- Autorizado em -->
                <td class="text-center">
                    <xsl:value-of select="@pst_registro"/>
                </td>
                
                <!-- Documentação Produção -->
                <td class="text-center">
                    <xsl:value-of select="@pst_previsaoproducao"/>
                </td>
                
                <!-- Produção-Revisado por -->
                <td>
                    <xsl:call-template name="formatarRevisado">
                        <xsl:with-param name="checked" select="@checked"/>
                        <xsl:with-param name="pessoaData" select="@pessoaData"/>
                    </xsl:call-template>
                </td>
                
                <!-- Documentação Controle -->
                <td class="text-center">
                    <xsl:value-of select="@pst_previsaocontrole"/>
                </td>
                
                <!-- Controle-Revisado por -->
                <td>
                    <xsl:call-template name="formatarRevisado">
                        <xsl:with-param name="checked" select="@checked2"/>
                        <xsl:with-param name="pessoaData" select="@pessoaData2"/>
                    </xsl:call-template>
                </td>
                
                <!-- Status -->
                <td>
                    <xsl:value-of select="@status"/>
                </td>
            </tr>
        </xsl:if>
    </xsl:template>
    
    <!-- Template para gerar botões de funções -->
    <xsl:template name="gerarBotoesFuncoes">
        <xsl:variable name="pasta" select="@pst_numero"/>
        <xsl:variable name="produto" select="@pst_produto510"/>
        <xsl:variable name="lote" select="@Lote"/>
        <xsl:variable name="data" select="@pst_registro"/>
        <xsl:variable name="observacao" select="@pst_observacao"/>
        <xsl:variable name="status" select="@status"/>
        <xsl:variable name="prodstatus" select="@status_producao"/>
        <xsl:variable name="ano" select="@pst_ano"/>
        
        <!-- Botão Documentação -->
        <button type="button" class="btn btn-sm btn-outline-primary" 
                onclick="abrirDocumentacao('{$pasta}', '{$produto}', '{$lote}', '{$data}', '{$observacao}', '{$status}', '{$prodstatus}')" 
                title="Registrar documentação">
            <i class="fas fa-edit"></i>
        </button>
        
        <!-- Botão Ocorrências -->
        <button type="button" class="btn btn-sm btn-outline-warning" 
                onclick="abrirOcorrencias('{$pasta}', '{$produto}', '{$lote}', '{$status}', '{$prodstatus}', '{$ano}')" 
                title="Registrar ocorrências">
            <i class="fas fa-cog"></i>
        </button>
        
        <!-- Botão Localizar -->
        <button type="button" class="btn btn-sm btn-outline-info" 
                onclick="localizar('{$pasta}', '{$produto}', '{$lote}')" 
                title="Localizar">
            <i class="fas fa-search"></i>
        </button>
    </xsl:template>
    
    <!-- Template para formatar dados de revisão -->
    <xsl:template name="formatarRevisado">
        <xsl:param name="checked"/>
        <xsl:param name="pessoaData"/>
        
        <xsl:choose>
            <xsl:when test="$checked = '1' or $checked = 'true'">✓</xsl:when>
            <xsl:otherwise></xsl:otherwise>
        </xsl:choose>
        <xsl:text> </xsl:text>
        <xsl:value-of select="$pessoaData"/>
    </xsl:template>
    
    <!-- Template para linha vazia quando não há dados -->
    <xsl:template name="linhaVazia">
        <tr>
            <td colspan="12" class="text-center">Nenhum registro encontrado</td>
        </tr>
    </xsl:template>
    
</xsl:stylesheet>