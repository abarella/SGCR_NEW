<%@ Language=VBScript%>
<!--#INCLUDE FILE="includes/validasessao.inc"-->
<%
	Response.ContentType = "text/HTML"
	Response.CharSet = "ISO-8859-1"
	Server.ScriptTimeout = 10000
%>

<xml id='xoDSO'>
</xml>

<html>
	<head>
		<meta http-equiv="Pragma" CONTENT="no-cache"/>
		<meta http-equiv="Cache-control" CONTENT="no-store"/>
		<meta http-equiv="Expires" CONTENT="-1"/>
		<link rel="stylesheet" href="Estilos/Estilos_Default.css"/>
        	<script language="javascript" src="funcoes/funcoes.js"></script>
        	<script language="javascript" src="funcoes/cr_pst04.js"></script>
 	</head>

	<body align="center" scroll="auto" leftmargin="25" topmargin="0" rightMargin="20" onload="javascript:initialize();">
		<script language="javascript" src="funcoes/dhtmlHistory.js"></script>
		<input type="hidden" id="txtCDusuario" value="<%=session("cdusuario")%>"/>
		<p align="right"><a href="inicio.asp">Voltar ao menu</a>&#160;&#160;</p>
		<p align="center"><img src="Figuras/cr_pst04.gif"/></p>

		<div id="caixamsg">
			<table>
				<tr>
					<td id="msg"></td>
					<td align="right"><img src="Figuras/Boxfechar.gif" style="cursor: 'hand';" onclick="javascript: escondeMensagem(caixamsg)"/></td>
				</tr>
			</table>
		</div>

		<table align="left" class="formulario">
			<tr>
				<td class="titcpoform">Produto</td>
				<td>
					<select id="cmbProduto" onchange="javascript:linkListaAjax(1);">
					</select>
				</td>
				<td width="15%" nowrap="true" class="titcpoform">Localizar Pasta:</td>
				<td><input type="text" id="txtPstNumero" maxlength="10" size="10" onkeypress="javascript:somentenumerico(this);" style="text-align:right;" onchange="javascript:linkListaAjax(1);"/></td>
			</tr>
		</table>
		
		<p>&nbsp;</p>
		
        <div id="titulo" class="titulo"> 
          <table id="tbtitulo">
            <tr> 
              <td>Funções</td>
              <td style="cursor:pointer" onclick="javascript: trocaOrdem(0);">Nr. Pasta</td>
              <td style="display:none">Produto</td>
              <td style="cursor:pointer" onclick="javascript: trocaOrdem(1);">Produto</td>
              <td>Lote</td>
              <td>Lote Novo</td>
              <td>Ano</td>
              <td>Autorizado em</td>
              <td style="cursor:pointer" onclick="javascript: trocaOrdem(2);">Documentação Produção</td>
              <td>Produção-Revisado por:</td>
              <td style="cursor:pointer" onclick="javascript: trocaOrdem(3);">Documentação Controle</td>
              <td>Controle-Revisado por:</td>
              <td>Status</td>
            </tr>
          </table>
        </div>
		
        <div id="dados" class="dados" onscroll="titulo.scrollLeft=dados.scrollLeft;"> 
          <table id="tbdados" cellpadding="1" datasrc="#xoDSO" onreadystatechange="javascript: fnResizeCols(this);" >
            <thead>
                <td/><!--Funções-->
                <td/><!--Nr. Pasta-->
                <td style="display:none"/><!--Produto-->
                <td/><!--Produto-->
                <td/><!--Lote-->
                <td/><!--Lote Novo-->
                <td/><!--Ano-->
                <td/><!--Autorizado em-->
                <td/><!--Documentação Produção-->
                <td/><!---->
	            <td/><!--Documentação Controle-->
	            <td/><!---->
                <td/>
            </thead>
            
            <tr onmouseover="javascript:overgrid(this);" onmouseout="javascript:overgrid(this);" > 
              <td align="center">
               
			        
               	        &#160;<img src="figuras/alterar.gif" style="cursor:hand;" alt="Registrar documentação " onclick="javascript: detalhes(this,'cr_pst02b');"/>
                    
			       
                    <!--<img src="figuras/alterar.gif" style="cursor:hand;" alt="Alterar previsão de entrega" onclick="javascript: detalhes(this,'cr_pst02c');"/>--> 
                    
               	    &#160;<img src="figuras/settings.gif" style="cursor:hand;" alt="Registrar ocorrências" onclick="javascript: detalhes(this,'cr_pst02d');"/>
                
		        <img width="21" height="21" src="figuras/localizar_medio.gif" style="cursor:hand;" alt="Localizar" onclick="javascript: detalhes(this,'cr_pst02a');"/>
              </td>
              <td align="right"><span datafld="pst_numero"></span>&nbsp;&nbsp;</td><!--Nr. Pasta-->
              <td style="display:none"><span datafld="pst_produto510"/></td><!--Produto-->
              <td align="left"><span datafld="nome_comercial"/></td><!--Produto-->
              <td align="left"><span datafld="Lote"/></td> <!--Lote-->
              <td align="left"><span datafld="pst_ano_lote"/></td> <!--Lote-->
              <td align="left"><span datafld="pst_ano"/></td> <!--Ano-->
              <td align="center"><span datafld="pst_registro"/></td><!--Autorizado em-->
              <td align="center"><span datafld="pst_previsaoproducao"/></td><!--Documentação Produto-->
              <!---->      
	          <td align="left"><span style="font:12pt Wingdings;" datafld="checked"></span>&nbsp;<span style="font: 6pt Verdana;" datafld="pessoaData"></span></td>
	          <td align="center"><span datafld="pst_previsaocontrole"/></td><!--Documentação Controle-->
	          <!---->
              <td align="left"><span style="font:12pt Wingdings;" datafld="checked2"></span>&nbsp;<span style="font: 6pt Verdana;" datafld="pessoaData2"></span></td>
              <td align="left"><span datafld="status"/></td><!--Status-->
              <td style="display: none;">
                <input type="hidden" datafld="pst_revisadopor"/>
                <input type="hidden" datafld="pst_observacao"/> 
                <input type="hidden" datafld="status"/>
                <input type="hidden" datafld="status_producao"/> 
                <input type="hidden" datafld="producao_revisadopor"/>
                <input type="hidden" datafld="pst_producaodoc"/> 
                <input type="hidden" datafld="pst_revisadoporc"/>
                <input type="hidden" datafld="controle_revisadopor"/> 
                <input type="hidden" datafld="pst_controledoc"/>
              </td>
            </tr>
          </table>
        </div>
	<!--Numeração de páginas-->
	<table width="100%" align="center" cellspacing="0" cellpadding="0" border="0" >
	    <tr>
		    <td>&#160;</td>
	    </tr>
	    <tr>
		    <td id="tdPaginas" align="left" valign="top"/>
	    </tr>
	</table>
	<div align="right"/>
	    	<table class="formulario">
		  <tr> 	
	    	    <td class="titcpoform">Impressão de Pastas:&#160;</td>
		    <td>		
			&#160;<input type="text" id="txtPstInicio"  size="6" maxlength="10" onkeypress="javascript:somentenumerico(this);" datafld="inicio" style="text-align:right;"/>
			&#160;&#160;a&#160;&#160;
			&#160;<input type="text" id="txtPstTermino"  size="6" maxlength="10" onkeypress="javascript:somentenumerico(this);" datafld="termino" style="text-align:right;"/>	
			&#160;<img src="figuras/impressora.gif" id="imgImpressora" style="cursor:hand;" onClick="javascript:chamaImpressaoPastas(txtPstInicio.value,txtPstTermino.value);"/>
		    </td>
		  </tr>
	    	</table>
	</div>
	</body>
</html>