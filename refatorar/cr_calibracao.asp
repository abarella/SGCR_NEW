<%@ Language=VBScript%>
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
        <script language="javascript" src="funcoes/cr_calibracao.js"></script>
	</head>

	<body align="center" scroll="auto" leftmargin="25" topmargin="0" rightMargin="20" onLoad="javascript:setsizecols(tbtitulo,tbdados);linkListaAjax();">
		<input type="hidden" id="txtCDusuario" value="<%=session("cdusuario")%>"/>
		<br/>
		<p align="center"><img src="Figuras/cr_calibracao.gif"/></p>
        
		<div id="caixamsg">
			<table>
				<tr>
					<td id="msg"></td>
					<td align="right"><img src="Figuras/Boxfechar.gif" style="cursor: 'hand';" onClick="javascript: escondeMensagem(caixamsg)"/></td>
				</tr>
			</table>
		</div>
		
		<p/>

		<table width="50%" align="center" class="formulario">
			<tr>
				<td class="titcpoform">Produto:</td>
				<td id="tdProduto"><%=request("produto")%></td>
				<td class="titcpoform">Lote:</td>
				<td id="tdLote"><%=request("lote")%></td>
			</tr>
		</table>&#160;
		
		<div id="titulo" class="titulo"> 
		  <table id="tbtitulo">
			<tr>
			  <td style="display:none;">pst_numero</td> 
			  <td style="display:none;">pst_producao</td>
			  <td>Série</td>
			  <td>Data e Hora da Calibração</td>
			  <td>Produção</td>
			  <td>Observação</td>
			</tr>
		  </table>
		</div>
		<div id="dados" class="dados" onscroll="titulo.scrollLeft=dados.scrollLeft;"> 
  		<table id="tbdados" cellpadding="1" datasrc="#xoDSO" onreadystatechange="javascript:fnResizeCols(this);">
    			<thead>
    				<td style="display:none;"/>
    				<td style="display:none;"/>
    				<td/>
    				<td/>
 					<td/>
	   				<td/>
			</thead>
    			<tr id="trCalib" onMouseOver="javascript:overgrid(this);" onMouseOut="javascript:overgrid(this);"> 
      				<td style="display:none;"><input type="hidden" datafld="pst_numero"/></td>
      				<td style="display:none;"><input type="hidden" datafld="pst_periodo"/></td>
      				<td align="center"><span datafld="pst_serie"/></td>
      				<td id="tdCalib" align="center">
      				    <input type="text" datafld="pst_calibracao" size="16" maxlength="16" onBlur="javascript:validaDataHora(this);" onKeyPress="javascript:FormataDataHora(this);"/>&#160;
      				    <img id="imgDtaCalibracao" src="figuras/calendario.gif" style="cursor:'hand';" onClick="javascript:chamaCalendarioHora(this);"/>
      				</td>
      				<td align="center"><select id="cmbProducao"></select></td>
       				<td align="left"> <input type="text" datafld="pst_observacao" size="60" maxlength="250"/></td>
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
				<td class="botao" onClick="javascript:Gravar(txtSenha.value);">Enviar</td>
			</tr>
		</table>
	</body>
</html>