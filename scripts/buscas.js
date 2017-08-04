/*
 Abre um painel com uma tabela de busca.
 Parâmetros:
	painel: caixa onde será mostrada a tabela de busca;
	tipo: tipo de dado a ser buscado (tabela do banco
		de dados a que está relacionada a busca);
	resultado: nome do textbox que será modificado quando
		um registro for selecionado na tabela;
	descicao: nome do textbox que guardará a descrição
		quando um registro for selecionado na tabela.
*/
function AbreBusca(painel, tipo, resultado, descricao, tipo_va) {
	ImageLoader(painel);
	
	Abre(painel, 300);
	
	var num_id = Math.floor(Math.random() * 99999999) + 10000000;
	
	var funcao = function(texto) {
		CancelaImageLoader(painel);

		painel.innerHTML = texto;
				
		sorttable.makeSortable(document.getElementById("tabela_busca_pais" + num_id));
	}
	
	var endereco = caminho_funcoes + "/busca.php?tipo=" + tipo + "&num_random=" + num_id + "&resultado=" + resultado + "&descricao=" + descricao;
	if (!EUndefined(tipo_va)) {
		endereco += "&tipo_va=" + tipo_va;
	}
	
	ExecutaXMLHTTP("GET", endereco, true, funcao);
}
function AbreBuscaPart(painel, tipo, resultado, nome, enderecox, cod_pais, desc_pais, nif, motivo_nif, nif_em_branco) {
	ImageLoader(painel);
	
	Abre(painel, 300);
	
	var num_id = Math.floor(Math.random() * 99999999) + 10000000;
	
	var funcao = function(texto) {
		CancelaImageLoader(painel);

		painel.innerHTML = texto;
				
		sorttable.makeSortable(document.getElementById("tabela_busca_pais" + num_id));
	}
	
	var endereco = caminho_funcoes + "/busca.php?tipo=" + tipo + "&num_random=" + num_id + "&resultado=" + resultado + "&nome=" + nome + "&endereco=" + enderecox + "&cod_pais=" + cod_pais + "&desc_pais=" + desc_pais + "&nif=" + nif + "&motivo_nif=" + motivo_nif + "&nif_em_branco=" + nif_em_branco;
	//alert(endereco);
		
	ExecutaXMLHTTP("GET", endereco, true, funcao);
}

function AbreBuscaRegistro(painel, tipo, ano) {
	ImageLoader(painel);
	
	Abre(painel);
	
	var num_id = Math.floor(Math.random() * 99999999) + 10000000;
	
	var funcao = function(texto) {
		CancelaImageLoader(painel);

		painel.innerHTML = texto;
				
		sorttable.makeSortable(document.getElementById("tabela_busca_pais" + num_id));
	}
	
	var endereco = caminho_funcoes + "/busca.php?tipo=" + tipo + "&num_random=" + num_id + "&ano=" + ano;
	
	ExecutaXMLHTTP("GET", endereco, true, funcao);
}

function SelecionaCodigoPart(t_resultado, t_nome, t_enderecox, t_cod_pais, t_desc_pais, t_nif, t_motivo_nif, t_nif_em_branco, resultado, nome, enderecox, cod_pais, nif, motivo_nif) {
	document.getElementById(t_resultado).value = resultado;
	DescricaoLocalizado(document.getElementById(t_nome), nome);
	document.getElementById(t_enderecox).value = enderecox;
	document.getElementById(t_cod_pais).value = cod_pais;
	if (nif == "") {
		//alert(1);
		document.getElementById(t_nif_em_branco).checked = true;
		ToggleNif(true, document.getElementById('preencher_nif'), document.getElementById('motivo_nif'));
	}
	else {
		//alert(2);
		document.getElementById(t_nif_em_branco).checked = false;
		ToggleNif(false, document.getElementById('preencher_nif'), document.getElementById('motivo_nif'));
	}
	document.getElementById(t_nif).value = nif;
	document.getElementById(t_motivo_nif).value = motivo_nif;
	
	EncontraDescricao(document.getElementById(t_cod_pais), document.getElementById(t_desc_pais), 'pais');
}

function DescricaoEmBusca(textbox) {
	textbox.style.color = "#C5C5C5";
	textbox.value = "Buscando...";	
}
function DescricaoNLoc(textbox) {
	textbox.style.color = "#FF0000";
	textbox.value = "NÃO LOCALIZADO";
}
function DescricaoLocalizado(textbox, valor) {
	textbox.style.color = "#000000";
	textbox.value = valor;
}

function EncontraDescricao(codigo, textbox, tipo) {
	if (codigo.value.trim() == "") {
		textbox.value = "";
		return;
	}
	
	DescricaoEmBusca(textbox);
	
	funcao = function(texto) {
		if (texto == "*") {
			DescricaoNLoc(textbox);
		}
		else {
			DescricaoLocalizado(textbox, texto);
		}
	}
	
	ExecutaXMLHTTP("GET", caminho_funcoes + "descricao.php?codigo=" + codigo.value + "&tipo=" + tipo, true, funcao);
}
function EncontraPart(codigo, nome, enderecox, cod_pais, desc_pais, nif, motivo_nif, nif_em_branco) {
	enderecox.value = "";
	cod_pais.value = "";
	desc_pais.value = "";
	nif.value = "";
	motivo_nif.value = "";
	
	if (codigo.value.trim() == "") {
		nome.value = "";
		
		return;
	}
	
	DescricaoEmBusca(nome);
	
	funcao = function(texto) {
		if (texto == "*") {
			DescricaoNLoc(nome);
		}
		else {
			campos = texto.split("|");
			
			SelecionaCodigoPart(codigo.id, nome.id, enderecox.id, cod_pais.id, desc_pais.id, nif.id, motivo_nif.id, nif_em_branco.id, codigo.value, campos[0], campos[1], campos[2], campos[3], campos[4]);
		}
	}
	
	var endereco = caminho_funcoes + "/descricao.php?codigo=" + codigo.value + "&tipo=part_comp";
	ExecutaXMLHTTP("GET", endereco, true, funcao);
}

function ExisteDescricao(codigo, tipo) {
	if (codigo.trim() == "") {
		return false;
	}
	
	var texto = ExecutaXMLHTTP("GET", caminho_funcoes + "/descricao.php?codigo=" + codigo + "&tipo=" + tipo, false);
	
	return !(texto == "*");
}

function SelecionaTodosExport(tipo) {
	var max = document.getElementById("f_" + tipo + "_exp_total").value;
	
	if (!max.IsNumeric()) return;
	
	for (var i = 1; i <= max; i++) {
		document.getElementById("f_" + tipo + "_exp_" + i).checked = true;
	}
}
function RetornaSelecionadosExport(tipo) {
	var r = "";
	var max = document.getElementById("f_" + tipo + "_exp_total").value;
	
	if (max.IsNumeric()) {
		for (var i = 1; i <= max; i++) {
			if (document.getElementById("f_" + tipo + "_exp_" + i).checked) {
				r += document.getElementById("f_" + tipo + "_exp_" + i).value;
				if (i != max) {
					r += ",";
				}
			}
		}
	}
	
	return r;
}


function Exportar(tipo, de_onde, resultado) {
	Esconde(de_onde);
	Mostra(resultado);
	resultado.innerHTML = "Exportando...";
	
	var ids = RetornaSelecionadosExport(tipo);
	
	var t_reg = "";
	var t_pag = "";
	
	if (tipo == "ras") {
		t_reg = "RAS";
		t_pag = "RP";		
	}
	else if (tipo == "rvs") {
		t_reg = "RVS";
		t_pag = "RF";		
	}
	
	funcao = function(texto) {
		if (texto == "*") {
			resultado.innerHTML = "Erro na Exportação.";
			Mostra(de_onde);
		}
		else {
			var r = texto.split("|");
			
			if (r.length != 2) {
				resultado.innerHTML = "Erro na Exportação: " + texto;
			}
			else {
				resultado.innerHTML = "Finalizado! | <a href='" + r[0] + "'>Fazer Download do Arquivo de Lotes para Inclusão de " + t_reg + "</a> | <a href='" + r[1] + "'>Fazer Download do Arquivo de Lotes para Inclusão de " + t_pag + "</a>";
			}
		}
	}
	
	var endereco = caminho_funcoes + "cadastros.php?funcao=exportar&ids=" + ids + "&tipo=" + tipo;
	
	ExecutaXMLHTTP("GET", endereco, true, funcao);
}