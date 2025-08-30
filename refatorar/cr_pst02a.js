var ordem = 0;
var pst_numero = "";

function fnResizeCols(objDados,objTitulo)
{
	if (objDados.readyState == 'complete')
	{
		var objDSO = eval(objDados.dataSrc.replace('#',''));
		//var objTitulo = tbtitulo
		//var objTitulo2 = tbtitulo2
		
		if (getQtdeRegistros(objDSO) > 0){
			setsizecols(objTitulo, objDados, '35%');
			//setsizecols(objTitulo2, objDados, '35%');
			objDados.style.visibility = 'visible';
		} else {
			objDados.style.visibility = 'hidden';
		}
	}
}

function initialize(pasta) {
	pst_numero = pasta;
	linkListaAjax(1, 1);
	linkListaAjax(1, 2);
}

function linkListaAjax(n, opcao)
{
	var strQS 
	
	if (opcao == 1) { 
		strQS = 'SP=crsa.PPST_LISTA3';
	} else { 	
		strQS = 'SP=crsa.PPST_LISTA2';
	}
	strQS += '#@pst_numero=' + pst_numero;
	strQS += '#PAGINA=' + n;
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
	
	if (opcao == 1) {
		carregarGrid(ObjXmlDoc, xoDSO);
	} else {	
		carregarGrid(ObjXmlDoc, xoDSO2);
	}
	
	/* GRAVA NO HISTÓRICO */
	alimentaHistory(n);
}

function alimentaHistory(n)
{
	if (historyStorage.hasKey("PAGINA")) {
		historyStorage.remove("PAGINA");
	}
	historyStorage.put("PAGINA", n);
}
