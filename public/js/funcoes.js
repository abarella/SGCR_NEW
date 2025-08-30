/**
 * Arquivo de funções JavaScript para PSP-PC
 * Baseado no arquivo funcoes.js original da pasta refatorar
 */

// Função para executar procedures
function executarProc(strQS) {
    try {
        // Parsear a string de query
        var params = {};
        var parts = strQS.split('#');
        
        for (var i = 0; i < parts.length; i++) {
            var part = parts[i];
            if (part.startsWith('SP=')) {
                params.SP = part.substring(3);
            } else if (part.startsWith('@')) {
                var paramParts = part.split('=');
                if (paramParts.length === 2) {
                    params[paramParts[0]] = paramParts[1];
                }
            } else if (part.startsWith('UDL=')) {
                params.UDL = part.substring(4);
            }
        }
        
        // Fazer requisição AJAX para o endpoint correspondente
        var url = '/psp-pc/executar-procedure';
        var data = {
            procedure: params.SP,
            parameters: params
        };
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, false); // Síncrono para manter compatibilidade
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        xhr.send(JSON.stringify(data));
        
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                return response.data;
            } else {
                console.error('Erro na procedure:', response.message);
                return null;
            }
        } else {
            console.error('Erro na requisição:', xhr.status);
            return null;
        }
        
    } catch (error) {
        console.error('Erro ao executar procedure:', error);
        return null;
    }
}

// Função para obter mensagem de erro
function getMensagemErro(ObjXmlDoc) {
    if (!ObjXmlDoc) return 'Erro desconhecido';
    
    try {
        if (ObjXmlDoc.error) {
            return ObjXmlDoc.error;
        }
        
        if (ObjXmlDoc.mensagem) {
            return ObjXmlDoc.mensagem;
        }
        
        return null;
    } catch (error) {
        return 'Erro ao processar resposta';
    }
}

// Função para obter quantidade de registros
function getQtdeRegistros(ObjXmlDoc) {
    if (!ObjXmlDoc) return 0;
    
    try {
        if (ObjXmlDoc.total) {
            return parseInt(ObjXmlDoc.total);
        }
        
        if (ObjXmlDoc.data && Array.isArray(ObjXmlDoc.data)) {
            return ObjXmlDoc.data.length;
        }
        
        return 0;
    } catch (error) {
        return 0;
    }
}

// Função para obter campo específico
function getCampo(ObjXmlDoc, campo) {
    if (!ObjXmlDoc) return null;
    
    try {
        if (ObjXmlDoc.data && ObjXmlDoc.data[0]) {
            return ObjXmlDoc.data[0][campo];
        }
        
        if (ObjXmlDoc[campo]) {
            return ObjXmlDoc[campo];
        }
        
        return null;
    } catch (error) {
        return null;
    }
}

// Função para obter parâmetro de retorno da procedure
function getParmRetornoProc(ObjXmlDoc, parametro) {
    if (!ObjXmlDoc) return null;
    
    try {
        if (ObjXmlDoc.parametros && ObjXmlDoc.parametros[parametro]) {
            return ObjXmlDoc.parametros[parametro];
        }
        
        if (ObjXmlDoc[parametro]) {
            return ObjXmlDoc[parametro];
        }
        
        return null;
    } catch (error) {
        return null;
    }
}

// Função para preencher combo
function PreencheCombo(ObjXmlDoc, combo, valor, texto, primeiroItem) {
    if (!combo) return;
    
    try {
        combo.innerHTML = '';
        
        if (primeiroItem) {
            var option = document.createElement('option');
            option.value = '';
            option.textContent = primeiroItem;
            combo.appendChild(option);
        }
        
        if (ObjXmlDoc.data && Array.isArray(ObjXmlDoc.data)) {
            ObjXmlDoc.data.forEach(function(item) {
                var option = document.createElement('option');
                option.value = item[valor];
                option.textContent = item[texto];
                combo.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erro ao preencher combo:', error);
    }
}

// Função para carregar combo (alias para PreencheCombo)
function carregarCombo(ObjXmlDoc, combo, valor, texto, primeiroItem) {
    return PreencheCombo(ObjXmlDoc, combo, valor, texto, primeiroItem);
}

// Função para mostrar mensagem
function mostraMensagem(caixa, mensagem, tipo) {
    if (!caixa) return;
    
    try {
        var msg = caixa.querySelector('#msg');
        if (msg) {
            msg.textContent = mensagem;
        }
        
        // Definir classe CSS baseada no tipo
        var cssClass = '';
        switch (tipo) {
            case 'firebrick':
                cssClass = 'alert-danger';
                break;
            case 'orange':
                cssClass = 'alert-warning';
                break;
            case 'blue':
                cssClass = 'alert-info';
                break;
            default:
                cssClass = 'alert-info';
        }
        
        caixa.className = 'alert ' + cssClass;
        caixa.style.display = 'block';
        
        // Esconder mensagem após 5 segundos
        setTimeout(function() {
            escondeMensagem(caixa);
        }, 5000);
        
    } catch (error) {
        console.error('Erro ao mostrar mensagem:', error);
    }
}

// Função para esconder mensagem
function escondeMensagem(caixa) {
    if (!caixa) return;
    
    try {
        caixa.style.display = 'none';
    } catch (error) {
        console.error('Erro ao esconder mensagem:', error);
    }
}

// Função para validar senha
function confereSenha(usuario, senha) {
    try {
        var xhr = new XMLHttpRequest();
        var url = '/psp-pc/validar-senha';
        var data = {
            usuario: usuario,
            senha: senha
        };
        
        xhr.open('POST', url, false); // Síncrono para manter compatibilidade
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        xhr.send(JSON.stringify(data));
        
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            return response.success;
        }
        
        return false;
        
    } catch (error) {
        console.error('Erro ao validar senha:', error);
        return false;
    }
}

// Função para formatar data
function FormataData(campo) {
    if (!campo) return;
    
    try {
        var valor = campo.value;
        if (valor.length == 2) {
            campo.value = valor + "/";
        }
        if (valor.length == 5) {
            campo.value = valor + "/";
        }
    } catch (error) {
        console.error('Erro ao formatar data:', error);
    }
}

// Função para verificar data
function VerificaData(campo) {
    if (!campo) return false;
    
    try {
        var valor = campo.value;
        var regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
        if (!regex.test(valor)) {
            alert("Data inválida! Use o formato DD/MM/AAAA");
            campo.focus();
            return false;
        }
        return true;
    } catch (error) {
        console.error('Erro ao verificar data:', error);
        return false;
    }
}

// Função para remover valores nulos
function removeNull(s) {
    return s != null ? s : "";
}

// Função para remover espaços em branco
function Trim(str) {
    if (!str) return "";
    return str.replace(/^\s+|\s+$/g, '');
}

// Função para validar campos obrigatórios
function validarCamposObrigatorios(campos) {
    for (var i = 0; i < campos.length; i++) {
        var campo = campos[i];
        if (!campo.value || campo.value.trim() === '') {
            alert('Campo obrigatório não preenchido: ' + campo.name);
            campo.focus();
            return false;
        }
    }
    return true;
}

// Função para limpar formulário
function limparFormulario(formId) {
    try {
        var form = document.getElementById(formId);
        if (form) {
            form.reset();
        }
    } catch (error) {
        console.error('Erro ao limpar formulário:', error);
    }
}

// Função para formatar número
function formatarNumero(numero, casasDecimais) {
    try {
        if (numero === null || numero === undefined) return '';
        return parseFloat(numero).toFixed(casasDecimais || 2);
    } catch (error) {
        return numero;
    }
}

// Função para formatar moeda
function formatarMoeda(valor) {
    try {
        if (valor === null || valor === undefined) return 'R$ 0,00';
        return 'R$ ' + parseFloat(valor).toFixed(2).replace('.', ',');
    } catch (error) {
        return 'R$ 0,00';
    }
}

// Função para validar CPF
function validarCPF(cpf) {
    try {
        cpf = cpf.replace(/[^\d]/g, '');
        
        if (cpf.length !== 11) return false;
        
        // Verifica se todos os dígitos são iguais
        if (/^(\d)\1+$/.test(cpf)) return false;
        
        // Validação do primeiro dígito verificador
        var soma = 0;
        for (var i = 0; i < 9; i++) {
            soma += parseInt(cpf.charAt(i)) * (10 - i);
        }
        var resto = 11 - (soma % 11);
        var dv1 = resto < 2 ? 0 : resto;
        
        // Validação do segundo dígito verificador
        soma = 0;
        for (var i = 0; i < 10; i++) {
            soma += parseInt(cpf.charAt(i)) * (11 - i);
        }
        resto = 11 - (soma % 11);
        var dv2 = resto < 2 ? 0 : resto;
        
        return parseInt(cpf.charAt(9)) === dv1 && parseInt(cpf.charAt(10)) === dv2;
        
    } catch (error) {
        return false;
    }
}

// Função para validar CNPJ
function validarCNPJ(cnpj) {
    try {
        cnpj = cnpj.replace(/[^\d]/g, '');
        
        if (cnpj.length !== 14) return false;
        
        // Verifica se todos os dígitos são iguais
        if (/^(\d)\1+$/.test(cnpj)) return false;
        
        // Validação do primeiro dígito verificador
        var multiplicadores = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        var soma = 0;
        for (var i = 0; i < 12; i++) {
            soma += parseInt(cnpj.charAt(i)) * multiplicadores[i];
        }
        var resto = soma % 11;
        var dv1 = resto < 2 ? 0 : 11 - resto;
        
        // Validação do segundo dígito verificador
        multiplicadores = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        soma = 0;
        for (var i = 0; i < 13; i++) {
            soma += parseInt(cnpj.charAt(i)) * multiplicadores[i];
        }
        resto = soma % 11;
        var dv2 = resto < 2 ? 0 : 11 - resto;
        
        return parseInt(cnpj.charAt(12)) === dv1 && parseInt(cnpj.charAt(13)) === dv2;
        
    } catch (error) {
        return false;
    }
}

// Função para validar email
function validarEmail(email) {
    try {
        var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    } catch (error) {
        return false;
    }
}

// Função para validar telefone
function validarTelefone(telefone) {
    try {
        telefone = telefone.replace(/[^\d]/g, '');
        return telefone.length >= 10 && telefone.length <= 11;
    } catch (error) {
        return false;
    }
}

// Função para formatar telefone
function formatarTelefone(telefone) {
    try {
        telefone = telefone.replace(/[^\d]/g, '');
        
        if (telefone.length === 11) {
            return '(' + telefone.substring(0, 2) + ') ' + telefone.substring(2, 7) + '-' + telefone.substring(7);
        } else if (telefone.length === 10) {
            return '(' + telefone.substring(0, 2) + ') ' + telefone.substring(2, 6) + '-' + telefone.substring(6);
        }
        
        return telefone;
    } catch (error) {
        return telefone;
    }
}

// Função para formatar CPF
function formatarCPF(cpf) {
    try {
        cpf = cpf.replace(/[^\d]/g, '');
        if (cpf.length === 11) {
            return cpf.substring(0, 3) + '.' + cpf.substring(3, 6) + '.' + cpf.substring(6, 9) + '-' + cpf.substring(9);
        }
        return cpf;
    } catch (error) {
        return cpf;
    }
}

// Função para formatar CNPJ
function formatarCNPJ(cnpj) {
    try {
        cnpj = cnpj.replace(/[^\d]/g, '');
        if (cnpj.length === 14) {
            return cnpj.substring(0, 2) + '.' + cnpj.substring(2, 5) + '.' + cnpj.substring(5, 8) + '/' + cnpj.substring(8, 12) + '-' + cnpj.substring(12);
        }
        return cnpj;
    } catch (error) {
        return cnpj;
    }
}

// Função para formatar CEP
function formatarCEP(cep) {
    try {
        cep = cep.replace(/[^\d]/g, '');
        if (cep.length === 8) {
            return cep.substring(0, 5) + '-' + cep.substring(5);
        }
        return cep;
    } catch (error) {
        return cep;
    }
}

// Função para obter data atual formatada
function getDataAtual() {
    try {
        var hoje = new Date();
        var dia = String(hoje.getDate()).padStart(2, '0');
        var mes = String(hoje.getMonth() + 1).padStart(2, '0');
        var ano = hoje.getFullYear();
        return dia + '/' + mes + '/' + ano;
    } catch (error) {
        return '';
    }
}

// Função para obter hora atual formatada
function getHoraAtual() {
    try {
        var hoje = new Date();
        var hora = String(hoje.getHours()).padStart(2, '0');
        var minuto = String(hoje.getMinutes()).padStart(2, '0');
        var segundo = String(hoje.getSeconds()).padStart(2, '0');
        return hora + ':' + minuto + ':' + segundo;
    } catch (error) {
        return '';
    }
}

// Função para converter data para formato brasileiro
function converterDataBR(data) {
    try {
        if (!data) return '';
        
        var dataObj = new Date(data);
        if (isNaN(dataObj.getTime())) return '';
        
        var dia = String(dataObj.getDate()).padStart(2, '0');
        var mes = String(dataObj.getMonth() + 1).padStart(2, '0');
        var ano = dataObj.getFullYear();
        
        return dia + '/' + mes + '/' + ano;
    } catch (error) {
        return data;
    }
}

// Função para converter data para formato americano
function converterDataUS(data) {
    try {
        if (!data) return '';
        
        var partes = data.split('/');
        if (partes.length !== 3) return data;
        
        var dia = partes[0];
        var mes = partes[1];
        var ano = partes[2];
        
        return ano + '-' + mes + '-' + dia;
    } catch (error) {
        return data;
    }
}
