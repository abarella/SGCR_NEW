var verificaCk = 0;

function removeNull(s){
    return s!=null?s:"";
}

function usuario_pesquisa()
{
	var strQS = 'SP=crsa.P1110_USUARIOS_PESQUISA'
	    strQS += '#@p1110_usuarioid=' + txtCDusuario.value;
	    strQS += '#UDL=conexaoCR'

	var ObjXmlDoc = executarProc(strQS);

	var grupo = getCampo(ObjXmlDoc, 'p052_GrupoCD');
		grupo = parseInt(grupo);

	if(grupo == 5){
		ckdocumentacao[1].checked = true;
		ListarValores();
		tbDocumentacao.style.display = 'none';
	}else{
		if(grupo >= 2 && grupo <= 4){
			ckdocumentacao[0].checked = true;
			ListarValores();
			tbDocumentacao.style.display = 'none';		
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
		 mostraMensagem(caixamsg, 'Senha não confere ou Senha Inválida.', 'orange');
		 senha.value="";
		 txtSenha.focus();
		 return false;
 	 }
 	 return true;
}

function CarregarRevisado()
{
	var strQS = 'SP=crsa.P1110_USUARIOS'
		strQS +='#@p052_grupocd=6';
		strQS +='#@p1110_ativo=A'; 
		strQS +='#@ordem=1';				
    	strQS +='#UDL=conexaoCR'

	var ObjXmlDoc = executarProc(strQS);

	if(getMensagemErro(ObjXmlDoc) != null){
		mostraMensagem(caixamsg, getMensagemErro(ObjXmlDoc), 'firebrick');
		return;
	}
	
	if(getQtdeRegistros(ObjXmlDoc) == 0){
		mostraMensagem(caixamsg, "NENHUM USUÁRIO ENCONTRADO!", 'orange');
	} 
	
	PreencheCombo(ObjXmlDoc, cmbRevisado, 'p1110_usuarioid', 'p1110_nome', 'Selecione');	
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
	
	//Consulta abaixo utilizada para pegar o valor do código da descrição  
	//da situação da Pasta (valor que veio da cr_pst02.asp). 
	var strQS = 'SP=crsa.PPST_STATUS';
		strQS += '#@status=' + Trim(txtStatus.value);
    	strQS += '#UDL=conexaoCR'

	var ObjXmlDoc2 = executarProc(strQS);

	txtStatus.value = getParmRetornoProc(ObjXmlDoc2, 'codigo');
	//window.setTimeout("cmbStatus.value = txtStatus.value;",500);
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
	CarregarRevisado();
	CarregarStatus();
	CarregarProducao();
	usuario_pesquisa();
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

function ListarValores()
{
	strQS ='SP=crsa.PPST_LISTA4'; 
	strQS +='#@pst_numero=' + txtPasta.value;
	strQS +='#UDL=conexaoCR'
	
	if(ckdocumentacao[0].checked == true)
		strQS +='#@tipo=' + ckdocumentacao[0].value;
	if(ckdocumentacao[1].checked == true)
		strQS +='#@tipo=' + ckdocumentacao[1].value;

	var ObjXmlDoc = executarProc(strQS);

	if(ckdocumentacao[0].checked == true){
		if(getCampo(ObjXmlDoc,"docum_receb") != null){
			txtDataInicial.value = removeNull(getCampo(ObjXmlDoc,"docum_receb"));
			txtcomentario.value = removeNull(getCampo(ObjXmlDoc,"pst_obsp"));
		}else{
			txtDataInicial.value = "";
			txtcomentario.value = "";
		}
	}
	if(ckdocumentacao[1].checked == true){
		if(getCampo(ObjXmlDoc,"docum_reca") != null){
			txtDataInicial.value = removeNull(getCampo(ObjXmlDoc,"docum_reca"));
			txtcomentario.value = removeNull(getCampo(ObjXmlDoc,"pst_obsc"));
		}else{
			txtDataInicial.value = "";
			txtcomentario.value = "";
		}
	}
	if(getCampo(ObjXmlDoc,"pstprod_status") != null){
		cmbProducao.value = getCampo(ObjXmlDoc,"pstprod_status");
	}else{
		cmbProducao.value = 0;
	}
	
	if(getCampo(ObjXmlDoc,"revisadopor") != null){
		cmbRevisado.value = getCampo(ObjXmlDoc,"revisadopor");
    }else{
	    cmbRevisado.value = "";    
    }
       
	if(getCampo(ObjXmlDoc,"pststs_codigo") != null){
		cmbStatus.value = getCampo(ObjXmlDoc,"pststs_codigo");
    }else{
	    cmbStatus.value = "";    
    }
}

function validarCampos()
{  
   for(i=0;i<2;i++){
   	if(ckdocumentacao[i].checked == true){
		verificaCk = 1;
	}
   }
   if(verificaCk != 1)
    {
	   alert("INFORME O TIPO DE DOCUMENTAÇÃO.");
	   return false;
    }
   if (txtDataInicial.value == "")
	{
	   alert("INFORME A DATA.");
	   txtDataInicial.focus();
	   return false;
    }
	if (cmbRevisado.value == "")
	{
	   alert("INFORME QUEM FEZ A REVISÃO.");
	   cmbRevisado.focus();
	   return false;
    }
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
	
	for(i=0;i<2;i++){
		if(ckdocumentacao[i].checked == true){
			var tipo_doc = ckdocumentacao[i].value;
		}
    }
	
	var strQS =  'SP=crsa.Ppst_Documentacao';
		strQS += '#@pst_ano=' + txtDataInicial.value.substr(6,4);
		strQS += '#@pst_numero=' + txtPasta.value;
		strQS += '#@pst_status=' + cmbStatus.value;
		strQS += '#@pst_prodstatus=' + cmbProducao.value;
		strQS += '#@pst_de=' + tipo_doc;
		strQS += '#@pst_revisadopor=' + cmbRevisado.value;
		strQS += '#@pst_doc_data=' + txtDataInicial.value;
		strQS += '#@pst_observacao=' + txtcomentario.value;
		strQS += '#@cdusuario=' + txtCDusuario.value;
		strQS += '#@senha=' + txtSenha.value;
    	strQS += '#UDL=conexaoCR'

	var ObjXmlDoc = executarProc(strQS);

	if(getMensagemErro(ObjXmlDoc) != null){
		mostraMensagem(caixamsg, getMensagemErro(ObjXmlDoc), 'firebrick');
		return;
	}
	
	if(getParmRetornoProc(ObjXmlDoc, 'RETURN_VALUE') == 0){

		mostraMensagem(caixamsg, 'REGISTROS ALTERADOS COM SUCESSO', 'blue');
		txtSenha.value = "";

	    /* GRAVA NO HISTÓRICO */
	    alimentaHistory(1);
	    window.setTimeout("window.history.go(-1);",1500);

		return;
	}
}

function initialize()
{   
    dhtmlHistory.initialize();

	if (historyStorage.hasKey("Revisado"))
		cmbRevisado.value = historyStorage.get("Revisado");

	if (historyStorage.hasKey("Producao"))
		cmbProducao.value = historyStorage.get("Producao");

	if (historyStorage.hasKey("Status"))
		cmbStatus.value = historyStorage.get("Status");

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
		
	if (historyStorage.hasKey("Revisado")) {
		historyStorage.remove("Revisado");
	}
	if (cmbRevisado.value != "")
		historyStorage.put("Revisado",cmbRevisado.value);		

	if (historyStorage.hasKey("Producao")) {
		historyStorage.remove("Producao");
	}
	if (cmbProducao.value != "")
		historyStorage.put("Producao",cmbProducao.value);		
}