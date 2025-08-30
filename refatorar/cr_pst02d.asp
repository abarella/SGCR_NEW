<%@ Language=VBScript%>
<!--#INCLUDE FILE="includes/validasessao.inc"-->
<%
	Response.ContentType = "text/HTML"
	Response.CharSet = "ISO-8859-1"
	Server.ScriptTimeout = 10000
%>

<xml id='xoDSO'>
</xml>
<xml id='xoDSO2'>
</xml>

<html>
	<head>
		<meta http-equiv="Pragma" CONTENT="no-cache"/>
		<meta http-equiv="Cache-control" CONTENT="no-store"/>
		<meta http-equiv="Expires" CONTENT="-1"/>
		<link rel="stylesheet" href="Estilos/Estilos_Default.css"/>
        <script language="javascript" src="funcoes/funcoes.js"></script>
        <script language="javascript" src="funcoes/cr_pst02d.js"></script>
	</head>

	<body align="center" scroll="auto" leftmargin="25" topmargin="0" rightMargin="20" onload="javascript:CarregarCombos();ListarValores();">
		<script language="javascript" src="funcoes/dhtmlHistory.js"></script>
		<input type="hidden" id="txtCDusuario" value="<%=session("cdusuario")%>"/>
		<p align="right"><a href="javascript:history.go(-1);">Voltar a pagina anterior</a>&#160;&#160;</p>
		<p align="center"><img src="Figuras/cr_pst02d.gif"/></p>
        	<input type="hidden" id="txtPasta" name="txtPasta" value="<%=request.querystring("pasta")%>"/>
        	<input type="hidden" id="txtStatus" name="txtStatus" value="<%=request.querystring("status")%>"/>
		<input type="hidden" id="txtAno" name="txtAno" value="<%=request.querystring("ano")%>"/>
		
		<div id="caixamsg">
			<table>
				<tr>
					<td id="msg"></td>
					<td align="right"><img src="Figuras/Boxfechar.gif" style="cursor: 'hand';" onclick="javascript: escondeMensagem(caixamsg)"/></td>
				</tr>
			</table>
		</div>
		<p/>&nbsp;

		<table id="tbQuadro" width="50%" align="center" >
			<tr>
				<td width="30%" align="right"><font size="2"><b>Produto:</b></font></td>
				<td><font size="2"><%=request.querystring("produto")%></font></td>
				<td width="9%"><font size="2"><b>Lote:</b></td>
				<td><font size="2"><%=request.querystring("lote")%></font></td>
			</tr>
		</table>
		<br>	
		<table width="80%" align="center" class="formulario">
			<tr>
				<td class="titcpoform" width="20%">Observa��o/Coment�rio:</td>
				<td><textarea id="txtcomentario" rows="3"  cols="80"  onKeyPress="javascript:txtObservacao_onKeyPress(this);"><%=request.querystring("observacao")%></textarea></td>
			</tr>
		</table>
		<br><br>	
		<table width="80%" align="center" class="formulario">
			<tr>
				<td class="titcpoform" width="20%">Situa��o da Produ��o: </td>
				<td class="titcpoform" width="20%">Situa��o da Pasta: </td>
			<tr>
				<td><select name="select" id="cmbProducao"></select></td>
				<td><select name="select" id="cmbStatus"></select></td>
			</tr>
		</table>
		<p>&#160;</p>
		<table id="tbsenha" align="center" width="20%">
			<tr>
				<td height="20" valign="middle" align="right" width="10%"><b>Senha:</b></td>
				<td valign="bottom" align="left" width="12%"><input type="password" name="txtSenha" id="txtSenha" size="7" maxlength="6"/></td>
			</tr>
		</table>
			
		<table id="tbEnviar" width="20%" align="center">
			<tr>
				<td class="botao" onclick="javascript:Gravar();">Enviar</td>
			</tr>
		</table>
		<!--Numera��o de p�ginas-->
		<table width="100%" align="center" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td>&#160;</td>
			</tr>
			<tr>
				<td id="tdPaginas" align="left" valign="top"/>&nbsp;
			</tr>
		</table>

		<p/>&nbsp;
	</body>
</html>