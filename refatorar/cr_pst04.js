var ordem = 1;

//Função que faz a troca de ordem e indica a pagina que estava antes de clicar no link
function trocaOrdem(novaOrdem)
{   
	ordem = novaOrdem;
	
	if (historyStorage.hasKey("PAGINA"))
	{
		var Pagina = historyStorage.get("PAGINA")
	}
	else
	{
		var Pagina = 1;
	}
	linkListaAjax(Pagina);
}

function carregarProdutos()
{   
	var strQS = 'SP=P0250_PRODUTO_SELECIONA'
	    strQS += '#@categoria=';

	var ObjXmlDoc = executarProc(strQS);
		
	if(getMensagemErro(ObjXmlDoc) != null){
		mostraMensagem(caixamsg, getMensagemErro(ObjXmlDoc), 'firebrick');
		return;
	}
	
	if(getQtdeRegistros(ObjXmlDoc) == 0){
		mostraMensagem(caixamsg, "NENHUM PRODUTO ENCONTRADO", 'orange');
	} else {
		escondeMensagem(caixamsg);
	}

	carregarCombo(ObjXmlDoc, cmbProduto, 'prod_cod510', 'nome_comercial');
}

function detalhes(obj, pagina)
{

    if (pagina == 'cr_pst02d') {
	
        var strpagina= Trim(pagina) + ".asp?pasta=" + Trim(obj.parentElement.parentElement.cells(1).innerText) + 
            "&produto=" + Trim(obj.parentElement.parentElement.cells(2).innerText) +		 
            "&lote=" + Trim(obj.parentElement.parentElement.cells(4).innerText) +
            "&status=" + Trim(obj.parentElement.parentElement.cells[13].children[2].value) +
            "&prodstatus=" + Trim(obj.parentElement.parentElement.cells[13].children[3].value) +
            "&ano=" + Trim(obj.parentElement.parentElement.cells(8).innerText);

	     window.location = strpagina;				  
	} else {	
        var strpagina= Trim(pagina) + ".asp?pasta=" + Trim(obj.parentElement.parentElement.cells(1).innerText) + 
            "&produto=" + Trim(obj.parentElement.parentElement.cells(2).innerText) +		 
            "&lote=" + Trim(obj.parentElement.parentElement.cells(4).innerText);

		if (Trim(pagina) != 'cr_pst02a') {		 	 
            strpagina += "&data=" + Trim(obj.parentElement.parentElement.cells(8).innerText) +
                "&observacao=" + Trim(obj.parentElement.parentElement.cells[13].children[1].value);
		}
		if (Trim(pagina) == 'cr_pst02b') {		 	 
            strpagina += "&status=" + Trim(obj.parentElement.parentElement.cells[13].children[2].value) +
                "&prodstatus=" + Trim(obj.parentElement.parentElement.cells[13].children[3].value);
		}
		
		window.location = strpagina;	 
	}
}

function fnResizeCols(objDados)
{
	if (objDados.readyState == 'complete')
	{
		var objDSO = eval(objDados.dataSrc.replace('#',''));
		var objTitulo = tbtitulo
					
		if (getQtdeRegistros(objDSO) > 0){
			setsizecols(objTitulo, objDados, '50%');
			dados.fireEvent('onscroll')
			objDados.style.visibility = 'visible';
		} else {
			objDados.style.visibility = 'hidden';
		}
	}
}
	
function linkListaAjax(n)
{
	var strQS = 'SP=crsa.PPST_LISTA_NAOCONCLUIDOS'
	    strQS += '#@produto=' + cmbProduto.value
	    strQS += '#@ordem=' + ordem
		strQS += '#@pst_numero=' + txtPstNumero.value;	    
	    strQS += '#PAGINA=' + n
	    strQS += '#GRID=cr_pst04_grid'
	    strQS += '#QTDEREGSPAG=25'
	    strQS += '#UDL=conexaoCR';

	var ObjXmlDoc = executarProc(strQS)

	if(getMensagemErro(ObjXmlDoc) != null){
		mostraMensagem(caixamsg, getMensagemErro(ObjXmlDoc), 'firebrick');
		return;
	}

	if(getQtdeRegistros(ObjXmlDoc) == 0){
		mostraMensagem(caixamsg, "NENHUM REGISTRO ENCONTRADO", 'orange');
	} else {
		escondeMensagem(caixamsg)
	}

    if(getCampo(ObjXmlDoc, "inicio") != null){
	    txtPstInicio.value = getCampo(ObjXmlDoc, "inicio");
    }else{
	    txtPstInicio.value = "";    
    }	    
	if(getCampo(ObjXmlDoc, "termino") != null){
	    txtPstTermino.value = getCampo(ObjXmlDoc, "termino");
    }else{
	    txtPstTermino.value = "";    
    }	    
	
   	tdPaginas.innerHTML = retornaHTMLPaginacao(ObjXmlDoc);
   	carregarGrid(ObjXmlDoc, xoDSO)
    
	/* GRAVA NO HISTÓRICO */
	alimentaHistory(n);
}

function chamaImpressaoPastas(inicio,termino)
{
	if(inicio > 0 || termino > 0){

		if(inicio == 0)
			inicio = termino;

		if(termino == 0)
			termino = inicio;


		var str = "relatorios/Pastasimpressao.asp?inicio=" + inicio + "&termino=" + termino;
		window.location = str;

	    var strQS = 'SP=crsa.PPST_IMPRESSAO';
	        strQS += '#@inicio=' + inicio;
	        strQS += '#@termino=' + termino;
	        strQS += '#UDL=conexaoCR';

	    var ObjXmlDoc = executarProc(strQS)

	    if(getMensagemErro(ObjXmlDoc) != null){
		    mostraMensagem(caixamsg, getMensagemErro(ObjXmlDoc), 'firebrick');
		    return;
	    }

	}
}

function alimentaHistory(n)
{
	if (historyStorage.hasKey("PRODUTO")) {
		historyStorage.remove("PRODUTO");
	}
	
	historyStorage.put("PRODUTO", cmbProduto.value);

	if (historyStorage.hasKey("PAGINA")) {
		historyStorage.remove("PAGINA");
	}
	historyStorage.put("PAGINA", n);
}

function initialize()
{
   	carregarProdutos();
	dhtmlHistory.initialize();
   	
	if (dhtmlHistory.isFirstLoad()) {
    	linkListaAjax(1);
	}
		
	dhtmlHistory.addListener(historyChange);
}

function historyChange(newLocation, historyData)
{
	if (historyStorage.hasKey("PRODUTO"))
		cmbProduto.value = historyStorage.get("PRODUTO");

	if (historyStorage.hasKey("PAGINA"))
		linkListaAjax(historyStorage.get("PAGINA"));
}
