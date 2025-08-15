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
        <script language="javascript" src="funcoes/cr_pst03.js"></script>
	</head>

	<body align="center" scroll="auto" leftmargin="25" topmargin="0" rightMargin="20" onload="javascript:setsizecols(tbtitulo,tbdados); ">
		<input type="hidden" id="txtCDusuario" value="<%=session("cdusuario")%>"/>
		<p align="right"><a href="inicio.asp">Voltar ao menu</a>&#160;&#160;</p>
		<p align="center"><img src="Figuras/cr_pst03.gif"/></p>
        
		<div id="caixamsg">
			<table>
				<tr>
					<td id="msg"></td>
					<td align="right"><img src="Figuras/Boxfechar.gif" style="cursor: 'hand';" onclick="javascript: escondeMensagem(caixamsg)"/></td>
				</tr>
			</table>
		</div>
		
		<p/>

		<table width="100%" align="center" class="formulario">
			<tr>
				<td class="titcpoform">Área:</td>
				<td>
				    <select name="select" id="cmbarea">
					    <option value="1">Radioisotopos Primarios</option>
          			    <option value="3" selected>Moléculas Marcadas</option>
    				</select>
    			</td>
				<td class="titcpoform">Lote: </td>
				<td><input type="text" id="txtlote" size="4" maxlength="3" onKeyPress="somentenumerico(this)"/></td>
				<td><img width="21" height="21" src="figuras/localizar_medio.gif" style="cursor:hand;" alt="Localizar" onclick="javascript: linkListaAjax(cmbarea.value, txtlote.value);"/> </td>
			</tr>
		</table>&#160;
		
		<div id="titulo" class="titulo"> 
		  <table id="tbtitulo">
			<tr> 
			  <td>Produtos</td>
			  <td>Número de Produções</td>
			  <td>Calibração</td>
			  <td>Partidas</td>
			  <td>Séries Autorizadas</td>
			</tr>
		  </table>
		</div>
		
		
		<div id="dados" class="dados" onscroll="titulo.scrollLeft=dados.scrollLeft;"> 
  		<table id="tbdados" cellpadding="1" datasrc="#xoDSO" onreadystatechange="javascript:fnResizeCols(this);">
    			<thead>
    				<td/><!--Produtos-->
    				<td/><!--Número de Produções-->
    				<td/><!--Calibração-->
    				<td/><!--Partidas-->
    				<td/><!--Séries Autorizadas-->
			</thead>
    			<tr onmouseover="javascript:overgrid(this);" onmouseout="javascript:overgrid(this);"> 
      				<td align="left"><span datafld="prod_cod510"/></td><!--Produtos-->
      				<td align="center">
      				    <input type="text" size="5" maxlength="4" datafld="Num_Producoes" onchange="javascript: VerificaValor(this);" onKeyPress="javascript: somentenumerico(this);"/>&#160;
      				    <img name="alterar" datafld="imgAlterar" style="cursor:hand;" border="0" alt="Alterar" onclick="javascript:chamaCalibracao(this);"/>
      				</td><!--Número de Produções-->
      				<td align="center"><span datafld="p100dtcl"/></td>
      				<td align="right"><span datafld="partidas"/></td>
      				<td align="center"><span datafld="pst_serie"/></td><!--Séries Autorizadas-->
    			</tr>
  		</table>
		</div>

		<!--Numeração de páginas-->
		<table width="100%" align="center" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td>&#160;</td>
			</tr>
			<tr>
				<td id="tdPaginas" align="left" valign="top"/>&nbsp;
			</tr>
		</table>

		<p/>&nbsp;


		<table id="tbsenha" align="center" width="20%">
			<tr>
				<td class="titcpoform" valign="center" align="left" width="10%">Senha:</td>
				<td valign="bottom" align="left" width="12%"><input type="password" name="txtSenha" id="txtSenha" size="7" maxlength="6"></input></td>
			</tr>
		</table>
			
		<table width="20%" align="center">
			<tr>
				<td class="botao" onclick="javascript:Gravar(txtSenha.value,txtlote.value);">Enviar</td>
			</tr>
		</table>
	</body>
</html>