var ordem = 0;
var slote;

//Função que faz a troca de ordem e indica a pagina que estava antes de clicar no link

function fnResizeCols(objDados)
{
	if (objDados.readyState == 'complete')
	{
		var objDSO = eval(objDados.dataSrc.replace('#',''));
		var objTitulo = tbtitulo
					
		if (getQtdeRegistros(objDSO) > 0){
			setsizecols(objTitulo, objDados, 'auto');
			objDados.style.visibility = 'visible';
		} else {
			objDados.style.visibility = 'hidden';
		}
	}
}


function confereSenha()
{   
	senha = document.getElementById("txtSenha");
	
	var strQS = 'SP=crsa.P1110_confsenha'
	    strQS += '#@p1110_usuarioid=' + txtCDusuario.value;
        strQS += '#@p1110_senha=' + senha.value;
	    strQS += '#UDL=conexaoCR'

	var ObjXmlDoc = executarProc(strQS);

    if (getCampo(ObjXmlDoc, "resulta")!=0)
	 {
		 //alert("Senha não confere ou Senha Inválida")
		 mostraMensagem(caixamsg, 'Senha não confere ou Senha Inválida.', 'orange');
		 senha.value="";
		 senha.focus();
		 return false;
 	 }
 	 return true;
}

function validarCampos1()
{
	if (txtlote.value == "")
	{
		alert("INFORME O LOTE!");
		   txtlote.focus();
		   return false;
	}
	return true;
}

function validarCampos2()
{
	if (txtlote.value == "")
	{
		alert("INFORME O LOTE!");
		   txtlote.focus();
		   return false;
	}
	if (txtSenha.value == "")
	{
		alert("INFORME A SENHA!");
		   txtSenha.focus();
		   return false;
	}
	if (confereSenha() == false){
	       return; 
	}
	return true;
}

function linkListaAjax(categoria, lote)
{
	if (!validarCampos1()){
		return false;
	}
	
	var strQS = 'SP=crsa.P0250_Produto_RDMM';
		strQS +='#@categoria=' + categoria;
		strQS +='#@lote=' + lote;
		strQS += '#GRID=cr_pst03_grid';
    	strQS += '#QTDEREGSPAG=1000';
    	strQS += '#UDL=conexaoCR'



	var ObjXmlDoc = executarProc(strQS);

	if(getMensagemErro(ObjXmlDoc) != null){
		mostraMensagem(caixamsg, getMensagemErro(ObjXmlDoc), 'firebrick');
		return;
	}

	if(getQtdeRegistros(ObjXmlDoc) == 0){
		mostraMensagem(caixamsg, "NENHUM REGISTRO ENCONTRADO", 'orange');
	} else {
		escondeMensagem(caixamsg)
	}
	
    tdPaginas.innerHTML = retornaHTMLPaginacao(ObjXmlDoc);
    carregarGrid(ObjXmlDoc, xoDSO);
}

function VerificaValor(text)
{
	if (Number(text.value) > 5){
		alert("Valor deve ser menor que 5");
		text.value = "";
		text.focus();
		window.event.returnValue = false;
		return;
	}		
}

function Gravar(senha, lote)
{
	if (!validarCampos2()){
		return false;
	}
	
	for (i = 1; i < tbdados.rows.length; i++){
 		if (tbdados.rows[i].cells[1].children[0].value != "" && Number(tbdados.rows[i].cells[1].children[0].value) != 0){
			var strQS = 'SP=crsa.PPST_RR_MM';
				strQS += '#@produto=' + Trim(tbdados.rows[i].cells[0].innerText);
				strQS += '#@lote=' + lote;
				strQS += '#@produto_qtde=' + Number(tbdados.rows[i].cells[1].children[0].value);
				strQS += '#@cdusuario=' + txtCDusuario.value;
				strQS += '#@senha=' + senha;
            	strQS += '#UDL=conexaoCR'

			var ObjXmlDoc = executarProc(strQS);
		}	
	}	
	if(getMensagemErro(ObjXmlDoc) != null){
		mostraMensagem(caixamsg, getMensagemErro(ObjXmlDoc), 'firebrick');
		return;
	}
	else{
	    linkListaAjax(cmbarea.value,txtlote.value);	
	}
}

function chamaCalibracao(obj)
{
    var tabela = obj.parentElement.parentElement.parentElement.parentElement; 
    var linha = obj.parentElement.parentElement.rowIndex;
    
    var produto = tabela.rows(linha).cells(0).innerText;
    var lote = txtlote.value; 
    
    if(Trim(lote) == ""){
        alert("INSIRA O LOTE!");
        return;
    }
    
    var strQS = 'cr_calibracao.asp?produto=' +  produto + '&lote=' + lote;

  	var retorno = (window.showModalDialog(strQS,'','dialogwidth: 700px; dialogheight: 500px; center=yes; status=no; help=no'));

	if (typeof(retorno) == 'string'){
		if (retorno == 'exp'){
			parent.location.href='login.asp?MSG=SESSÃO EXPIRADA';
			return;
		}else{
	        linkListaAjax(cmbarea.value,txtlote.value);
	    }
	}else{
	    linkListaAjax(cmbarea.value,txtlote.value);
	}
}