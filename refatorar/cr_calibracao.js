function fnResizeCols(objDados)
{
	if (objDados.readyState == 'complete')
	{
		var objDSO = eval(objDados.dataSrc.replace('#',''));
		var objTitulo = tbtitulo;
					
		if (getQtdeRegistros(objDSO) > 0){
			setsizecols(objTitulo, objDados, '50%');
			verificaColunaProducao();
			if(Trim(tdProduto.innerText) == "FLUOR"){
			    carregarComboProducao();
			    window.setTimeout("selecionaComboProducao();",100);
			}
			objDados.style.visibility = 'visible';
		} else {
			objDados.style.visibility = 'hidden';
		}
	}
}

function chamaCalendarioHora(obj){
    txtData = obj.parentElement.children[0];
    txtData.value = CalendarioHora(txtData,txtData.value);
}

function verificaColunaProducao()
{
   if(Trim(tdProduto.innerText) != "FLUOR"){
       tbtitulo.rows(0).cells(4).style.display = 'none';
       for(j=1;j<tbdados.rows.length;j++){
            tbdados.rows(j).cells(4).style.display = 'none';
       }
   }
}

function selecionaComboProducao(){
    for(j=1;j<tbdados.rows.length;j++){
        var pst_producao = tbdados.rows(j).cells(1).children[0].value;
        tbdados.rows(j).cells(4).children[0].value = pst_producao;
    }   
}

function carregarComboProducao()
{
	var strQS = 'SP=P0020_PRODUCAO';
		strQS +='#@produto=' + tdProduto.innerText;
			
	var ObjXmlDoc = executarProc(strQS);
			
	if(getMensagemErro(ObjXmlDoc) != null){
		mostraMensagem(caixamsg, getMensagemErro(ObjXmlDoc), 'firebrick');
		return;
	}
	
	if(getQtdeRegistros(ObjXmlDoc) == 0){
		mostraMensagem(caixamsg, "NENHUMA PRODUÇÃO ENCONTRADA", 'orange');
		return;
	} else {
		escondeMensagem(caixamsg);
	}

   for(j=1;j<=tbdados.rows.length;j++){
        if(tbdados.rows(j) != null){
            /* Fonte de dados, combo, campo que vai no value, campo que vai no text*/
            carregarCombo(ObjXmlDoc, tbdados.rows(j).cells(4).children[0], 'p020_ID', 'p020_producaonr');
        }
   }
   setsizecols(tbtitulo, tbdados, '50%');
}

function linkListaAjax()
{
	var strQS = 'SP=crsa.PPST_LISTA7A';
		strQS +='#@produto=' + tdProduto.innerText; //char(10)
		strQS +='#@lote=' + tdLote.innerText; //int
    	strQS += '#GRID=cr_calibracao';
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

function confereSenha()
{   
	senha = document.getElementById("txtSenha");
	
	var strQS = 'SP=crsa.P1110_confsenha'
	    strQS += '#@p1110_usuarioid=' + document.getElementById("txtCDusuario").value;
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


function validarCampos()
{
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

function Gravar(senha)
{
	if (!validarCampos()){
		return false;
	}
	
	for (i = 1; i < tbdados.rows.length; i++){
			
			var strQS = 'SP=crsa.Ppst_SERIE2_GRAVA';
				strQS += '#@produto=' + tdProduto.innerText; //varchar(10)
				strQS += '#@lote=' + tdLote.innerText; //int
				strQS += '#@data_calibracao=' + tbdados.rows(i).cells(3).children[0].value; //datetime
				strQS += '#@pst_serie=' +  tbdados.rows(i).cells(2).innerText; //varchar(1)
				strQS += '#@pst_producao=' + tbdados.rows(i).cells(4).children[0].value; //int=null
				strQS += '#@pst_numero=' + tbdados.rows(i).cells(0).children[0].value; //int
				strQS += '#@cdusuario=' + txtCDusuario.value;
				strQS += '#@senha=' + senha;
				strQS += '#@pst_observacao=' + tbdados.rows(i).cells(5).children[0].value; //datetime
            	strQS += '#UDL=conexaoCR'
/// alert(strQS);
			var ObjXmlDoc = executarProc(strQS);

	        if(getMensagemErro(ObjXmlDoc) != null){
		        mostraMensagem(caixamsg, getMensagemErro(ObjXmlDoc), 'firebrick');
		        txtSenha.value = "";
		        return;
	        }
  	}	        

	linkListaAjax();
	txtSenha.value = "";
}