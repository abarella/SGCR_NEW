var verificaCk = 0;

function ListarValores()
{
	strQS ='SP=crsa.PPST_LISTA4'; 
	strQS +='#@pst_numero=' + txtPasta.value;
	strQS +='#@tipo=P';
	strQS +='#UDL=conexaoCR'

	var ObjXmlDoc = executarProc(strQS);

	if(getCampo(ObjXmlDoc,"pstprod_status") != null){
		cmbProducao.value = getCampo(ObjXmlDoc,"pstprod_status");
	}else{
		cmbProducao.value = 0;
	}

	if(getCampo(ObjXmlDoc,"pstprod_status") != null)
		cmbStatus.value = getCampo(ObjXmlDoc,"pstprod_status");

	if(getCampo(ObjXmlDoc,"pst_obsp") != null)
		txtcomentario.value = getCampo(ObjXmlDoc,"pst_obsp");
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
		 mostraMensagem(caixamsg, 'Senha não confere ou Senha Inválida.', 'orange');
		 senha.value="";
		 txtSenha.focus();
		 return false;
 	 }
 	 return true;
}

function CarregarStatus()
{
	var strQS = 'SP=crsa.PPST_STATUS';
    	strQS += '#UDL=conexaoCR'

	var ObjXmlDoc = executarProc(strQS);

	if(getMensagemErro(ObjXmlDoc) != null){
		mostraMensagem(caixamsg, getMensagemErro(ObjXmlDoc), 'firebrick');
		return;
	}

	if(getQtdeRegistros(ObjXmlDoc) == 0){
		mostraMensagem(caixamsg, "NENHUMA PASTA ENCONTRADA!", 'orange');
	} 
	
	carregarCombo(ObjXmlDoc, cmbStatus, 'pststs_codigo', 'pststs_descricao', true);	
	/*
	//Consulta abaixo utilizada para pegar o valor do código da descrição  
	//da situação da Pasta (valor que veio da cr_pst02.asp). 
	var strQS = 'SP=crsa.PPST_STATUS';
	    strQS += '#@status=' + txtStatus.value;
    	    strQS += '#UDL=conexaoCR'

	var ObjXmlDoc2 = executarProc(strQS);

	txtStatus.value = getParmRetornoProc(ObjXmlDoc2, 'codigo');
	window.setTimeout("cmbStatus.value = txtStatus.value;",100);
	*/
}

function CarregarProducao()
{
	var strQS = 'SP=crsa.PPST_PRODUCAOSTATUS';
    	strQS += '#UDL=conexaoCR'

	var ObjXmlDoc = executarProc(strQS);

	if(getMensagemErro(ObjXmlDoc) != null){
		mostraMensagem(caixamsg, getMensagemErro(ObjXmlDoc), 'firebrick');
		return;
	}

	if(getQtdeRegistros(ObjXmlDoc) == 0){
		mostraMensagem(caixamsg, "NENHUMA PRODUÇÃO ENCONTRADA!", 'orange');
	} 
	
	PreencheCombo(ObjXmlDoc, cmbProducao, 'pstprod_status', 'pstprod_descricao', 'Selecione');	
}

function CarregarCombos()
{
	CarregarStatus();
	CarregarProducao();
}

//FUNÇÃO QUE PERMITE QUE SEJA DIGITADO SOMENTE 255 CARACTERES
function txtObservacao_onKeyPress(obj)
{
    var codigo = window.event.keyCode;

    if(codigo == 13){
        window.event.returnValue = false;
    }	

	if (obj.value.length == 255){
		window.event.returnValue = false;
	}		
}

function validarCampos()
{  
	if (cmbProducao.value == "")
	{
	   alert("INFORME A SITUAÇÃO DA PRODUÇÃO.");
	   cmbProducao.focus();
	   return false;
    }
	if (cmbStatus.value == "")
	{
	   alert("INFORME A SITUAÇÃO DA PASTA.");
	   cmbStatus.focus();
	   return false;
    }
	if (txtSenha.value == "")
	{
	   alert("INFORME A SENHA.");
	   txtSenha.focus();
	   return false;
    }
	if (confereSenha() == false){
	   return; 
	}
	return true;
}

function Gravar()
{	
	if (!validarCampos()){
		return false;
	}

	var strQS =  'SP=crsa.Ppst_Ocorrencia';
		strQS += '#@pst_ano=' + txtAno.value.substr(6,4);
		strQS += '#@pst_numero=' + txtPasta.value;
		strQS += '#@pstusu_codigo=' + txtCDusuario.value;
		strQS += '#@obs=' + txtcomentario.value;
		strQS += '#@senha=' + txtSenha.value;
    	strQS += '#UDL=conexaoCR'

	var ObjXmlDoc = executarProc(strQS);

	if(getMensagemErro(ObjXmlDoc) != null){
		mostraMensagem(caixamsg, getMensagemErro(ObjXmlDoc), 'firebrick');
		return;
	}
	
	if(getParmRetornoProc(ObjXmlDoc, 'RETURN_VALUE') == 0){
		mostraMensagem(caixamsg, 'REGISTROS ALTERADOS COM SUCESSO', 'orange');
		return;
	}
	
	/* GRAVA NO HISTÓRICO */
	alimentaHistory(n);
}

function initialize()
{   
    dhtmlHistory.initialize();

	if (historyStorage.hasKey("Revisado"))
		cmbRevisado.value = historyStorage.get("Revisado");

	if (historyStorage.hasKey("Producao"))
		cmbProducao.value = historyStorage.get("Producao");

	if (historyStorage.hasKey("PAGINA"))
		linkListaAjax(historyStorage.get("PAGINA"));
}

function alimentaHistory(n)
{
	if (historyStorage.hasKey("PAGINA")) {
		historyStorage.remove("PAGINA");
	}
	historyStorage.put("PAGINA", n);
	
	if (historyStorage.hasKey("Status")) {
		historyStorage.remove("Status");
	}
	if (cmbStatus.value != "")
		historyStorage.put("Status",cmbStatus.value);

	if (historyStorage.hasKey("Producao")) {
		historyStorage.remove("Producao");
	}
	if (cmbProducao.value != "")
		historyStorage.put("Producao",cmbProducao.value);		
}