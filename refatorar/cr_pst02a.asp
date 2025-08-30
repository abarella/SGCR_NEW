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
        	<script language="javascript" src="funcoes/cr_pst02a.js"></script>
		<style>
			.formulario_rl_est
			{
				BACKGROUND-COLOR: #FFFFFF;
				FONT-SIZE: 8pt;
			}
			.formulario_rl_est TD
			{
				BORDER-BOTTOM: #E4EAF1 solid 1 pt;
			}
		</style>
	</head>

	<body align="center" scroll="auto" leftmargin="25" topmargin="0" rightMargin="20" onLoad="javascript:setsizecols(tbtitulo,tbdados); setsizecols(tbtitulo2,tbdados2); initialize(<%=request.querystring("pasta")%>);">
		<script language="javascript" src="funcoes/dhtmlHistory.js"></script>
		<input type="hidden" id="txtCDusuario" value="<%=session("cdusuario")%>"/>
		<p align="right"><a href="javascript:history.go(-1);">Voltar a pagina anterior</a>&#160;&#160;</p>
		<p align="center"><img src="Figuras/cr_pst02a.gif"/></p>

		<div id="caixamsg">
			<table>
				<tr>
					<td id="msg"></td>
					<td align="right"><img src="Figuras/Boxfechar.gif" style="cursor: 'hand';" onClick="javascript: escondeMensagem(caixamsg)"/></td>
				</tr>
			</table>
		</div>
		<p/>
        
		<table id="tbQuadro" width="80%" align="left" class="formulario">
			<tr>
    				<td width="12%" height="21" align="right" class="titcpoform"><div align="left"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Produto</font><b>:</b></div></td>
				<td width="46%" align="left"><%=request.querystring("produto")%></td>
    				<td width="10%" align="right" class="titcpoform"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Lote:</font></td>
				<td width="32%" align="left"><%=request.querystring("lote")%></td>
			</tr>
		</table>
		<p>&#160;</p>
				
		
		
<div id="titulo" class="titulo"> 
  <table id="tbtitulo">
    <tr> 
      <td height="15">Data</td>
      <td>Usuario</td>
      <td>Ocorrência</td>
    </tr>
  </table>
</div>				
				
				
		
<div id="dados" class="dados3" onscroll="titulo.scrollLeft=dados.scrollLeft;"> 
  <table id="tbdados" cellpadding="1" datasrc="#xoDSO" onreadystatechange="javascript:fnResizeCols(this,tbtitulo);" class="formulario_rl_est">
    <thead>
    	<td/><!--Data-->
    	<td/><!--Usuario-->
    	<td/><!--Ocorrência-->
    </thead>
    <tr onMouseOver="javascript:overgrid(this);" onMouseOut="javascript:overgrid(this);"> 
      <td align="left"><span datafld="data"></span>&#160;</td><!--Data-->
      <td align="left"><span datafld="nome"></span>&#160;</td><!--Usuario-->
      <td align="left"><span datafld="ocorrencia"></span>&#160;</td><!--Ocorrência-->
    </tr>
  </table>
</div>
&#160;
				
				
		
<div id="titulo2" class="titulo"> 
  <table id="tbtitulo2">
    <tr> 
      <td height="15">Data</td>
      <td>Usuario</td>
      <td>Registros</td>
      <td>Observação</td>
    </tr>
  </table>
</div>				
				
				
		
<div id="dados2" class="dados3" onscroll="titulo2.scrollLeft=dados2.scrollLeft;"> 
  <table id="tbdados2" cellpadding="1" datasrc="#xoDSO2" onreadystatechange="javascript:fnResizeCols(this,tbtitulo2);" class="formulario_rl_est">
    <thead>
    	<td/><!--Data-->
    	<td/><!--Usuario-->
    	<td/><!--Registros-->
    	<td/><!--Registros-->
    </thead>
    <tr onMouseOver="javascript:overgrid(this);" onMouseOut="javascript:overgrid(this);"> 
      <td align="left"><span datafld="data"></span>&#160;</td><!--Data-->
      <td align="left"><span datafld="nome"></span>&#160;</td><!--Usuario-->
      <td align="left"><span datafld="ocorrencia"></span>&#160;</td><!--Registros-->
      <td align="left"><span datafld="pstfase_obs"></span>&#160;</td><!--Registros-->
    </tr>
  </table>
</div>
		&#160;&#160;
		
		<table width="20%" align="center">
			<tr>
				<td class="botao" onClick="javascript:history.go(-1);">Retornar</td>
			</tr>
		</table>

					
		<!--Numeração de páginas-->
		<table width="100%" align="center" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td>&#160;</td>
			</tr>
			<tr>
				<td id="tdPaginas" align="left" valign="top"/>
			</tr>
		</table>

		<p/>
	</body>
</html>