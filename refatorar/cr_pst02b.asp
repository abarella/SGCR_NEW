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
        <script language="javascript" src="funcoes/cr_pst02b.js"></script>
	</head>

	<body align="center" scroll="auto" leftmargin="25" topmargin="0" rightMargin="20" onload="javascript:CarregarCombos();">
		<script language="javascript" src="funcoes/dhtmlHistory.js"></script>
		<input type="hidden" id="txtCDusuario" value="<%=session("cdusuario")%>"/>
		<p align="right"><a href="javascript:history.go(-1);">Voltar a pagina anterior</a>&#160;&#160;</p>
		<p align="center"><img src="Figuras/cr_pst02b.gif"/></p>
        <input type="hidden" id="txtPasta" name="txtPasta" value="<%=request.querystring("pasta")%>"/>
        <input type="hidden" id="txtStatus" name="txtStatus" value="<%=request.querystring("status")%>"/>
        <input type="hidden" id="txtProdStatus" name="txtProdStatus" value="<%=request.querystring("prodstatus")%>"/>
		
		<div id="caixamsg">
			<table>
				<tr>
					<td id="msg"></td>
					<td align="right"><img src="Figuras/Boxfechar.gif" style="cursor: 'hand';" onclick="javascript: escondeMensagem(caixamsg)"/></td>
				</tr>
			</table>
		</div>	
<p/>&nbsp;&nbsp;&nbsp;&nbsp;
<table id="tbQuadro" width="50%" align="center" >
			<tr>
				<td width="30%" align="right"><font size="2"><b>Produto:</b></font></td>
				<td><font size="2"><%=request.querystring("produto")%></font></td>
				<td width="9%"><font size="2"><b>Lote:</b></td>
				<td><font size="2"><%=request.querystring("lote")%></font></td>
			</tr>
		</table>
		<br>	
		<table id="tbDocumentacao" width="80%" align="center" class="formulario">
			<tr >
				<td  width="15%"></td>
				<td  width="25%"><input type="radio" id="ckdocumentacao" name="ckdocumentacao" value="P" onclick="javascript:ListarValores();"/>Documentação Produção </td>
				<td width="35%"><input type="radio" id="ckdocumentacao" name="ckdocumentacao" value="C" onclick="javascript:ListarValores();"/>Documentação Controle</td>
			</tr>
		</table>&#160;
		<table width="80%" align="center" class="formulario">
			<tr>
				<td class="titcpoform" width="20%">Data Entrega:</td>
				<td><input type="text" id="txtDataInicial" maxlength="10" size="11" onkeypress="javascript: FormataData(this)" onblur="javascript: VerificaData(this)" style="text-align:right"/>&#160;<img src="figuras/calendario.gif" style="cursor: 'hand';" onclick="javascript:txtDataInicial.value = Calendario();"/></td>
			</tr>
			<tr>
				<td class="titcpoform" width="20%">Revisado por: </td>
				<td><select name="select" id="cmbRevisado"></select></td>
			</tr>
			<tr>
				<td class="titcpoform" width="20%">Observação/Comentário:</td>
				<td><textarea id="txtcomentario" rows="3"  cols="80"></textarea></td>
			</tr>
		</table>
		<BR>

		<table width="80%" align="center" class="formulario">
			<tr>
				<td class="titcpoform" width="20%">Situação da Produção: </td>
				<td class="titcpoform" width="20%">Situação da Pasta: </td>
			<tr>
				<td><select name="select" id="cmbProducao"></select></td>
				<td><select name="select" id="cmbStatus"></select></td>
			</tr>
		</table>
		<br>

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
					
		<!--Numeração de páginas-->
		<table width="100%" align="center" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td>&#160;</td>
			</tr>
			<tr>
				<td id="tdPaginas" align="left" valign="top"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</tr>
		</table>

		<p/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	</body>
</html>