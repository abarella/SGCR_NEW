<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:output method="xml" encoding="ISO-8859-1"/>

  <!--formatar valores-->
  <xsl:decimal-format NaN="0" decimal-separator="," grouping-separator="."/>
  <xsl:param name="FormataValor" select="'###.###.##0,0000'"/>

  <!--variáveis usadas na paginação dos dados-->
  <xsl:variable name="QTDEREGSPORPAG" select="number(//RETORNO/@QTDEREGSPAG)"/>
  <xsl:variable name="QTDEREGS" select="1000"/>
  <xsl:variable name="QTDEPAGS" select="ceiling($QTDEREGS div $QTDEREGSPORPAG)"/>
  <xsl:variable name="PAGINA" select="number(//RETORNO/@PAGINA)"/>
  <xsl:variable name="REGFIM" select="$PAGINA * $QTDEREGSPORPAG"/>
  <xsl:variable name="REGINIC" select="$REGFIM - $QTDEREGSPORPAG + 1"/>
  <!---->
  <xsl:template match="/ROOT">
    <xsl:element name="ROOT">
      <xsl:for-each select="*[name()!='RETORNO' and position()&gt;=$REGINIC and position()&lt;=$REGFIM]">
        <xsl:element name="DADOS">
          <xsl:copy-of select="@*"/>
          <xsl:attribute name="partidas">
              <xsl:choose>
                <xsl:when test="normalize-space(@partidas)=0"></xsl:when>
                <xsl:otherwise>
                  <xsl:value-of select="@partidas"/>
                </xsl:otherwise>
              </xsl:choose>
          </xsl:attribute>
          <xsl:attribute name="imgAlterar">
              <xsl:choose>
                <xsl:when test="@pst_serie=''">figuras/vazio.gif</xsl:when>
                <xsl:otherwise>figuras/alterar.gif</xsl:otherwise>
              </xsl:choose>
          </xsl:attribute>          
        </xsl:element>
      </xsl:for-each>
      <xsl:apply-templates select="RETORNO"/>
    </xsl:element>
  </xsl:template>

  <xsl:template match="RETORNO">
    <xsl:element name="RETORNO">
      <xsl:copy-of select="@*"/>
      <xsl:attribute name="QTDEPAGS">
        <xsl:value-of select="$QTDEPAGS"/>
      </xsl:attribute>
    </xsl:element>
  </xsl:template>

</xsl:stylesheet>