function LimpaErro(form)
{
	var caixa;
	
	for (var i = 0; i < form.length; i++)
	{
		if (form.elements[i].className == "campo_form_erro")
		{
			form.elements[i].className = "campo_form";
		}
	}
}

/*
 Cria um novo item no caso de cadastros que
 contêm múltiplos items.
 Esta função suporta até dois níveis.
 
 Parâmetros:
	painel: local onde será adicionado o novo item ao final;
	nome_funcao: nome da funcao a ser passada por querystring na
		requisição por AJAX;
	tipo: tipo de cadastro ('ras' ou 'rvs');
	campo_qtde: campo do formulário (de preferência um
		input = "hidden") que guarda a quantidade de
		itens atualmente carregados;
	id_novo_painel: prefixo do atributo id do objeto
		HTML referente ao novo painel. O número do item
		será acrescentado ao final da string.
	num_sup: Número do item superior (usar somente em caso
		de dois níveis -- exemplo, quando for para itens
		que estejam dentro de "Operações", já que estas,
		por sua vez, são items de "RAS" ou "RVS").
*/
function NovoItem(painel, nome_funcao, tipo, campo_qtde, classe_painel, id_novo_painel, num_sup, nf = -1) {
	campo_qtde.value++;
	
	var novo_painel = document.createElement("fieldset");

	novo_painel.setAttribute("class", classe_painel);
	novo_painel.setAttribute("id", id_novo_painel + campo_qtde.value);
	
	painel.appendChild(novo_painel);
	
	ImageLoader(novo_painel);
	
	//Abre(novo_painel, 300);
		
	funcao = function(texto) {
		CancelaImageLoader(novo_painel);

		novo_painel.innerHTML = texto;
		PainelRestaurar(painel, nome_funcao + "_restaurar", tipo, campo_qtde, classe_painel, num_sup);
	}
	
	var endereco = caminho_funcoes + "cadastros.php?funcao=" + nome_funcao + "&tipo=" + tipo + "&num=" + campo_qtde.value + "&nf=" + nf;
	if (!EUndefined(num_sup)) {
		endereco += "&num_sup=" + num_sup;
	}
	
	ExecutaXMLHTTP("GET", endereco, true, funcao);
}
function PainelRestaurar(painel, nome_funcao, tipo, campo_qtde, classe_painel, num_sup) {
	var novo_painel = document.createElement("div");

	novo_painel.setAttribute("class", classe_painel);
	novo_painel.setAttribute("style", "display: none;");
	
	painel.appendChild(novo_painel);
	
	funcao = function(texto) {
		retorno = texto.split("|");
		
		novo_painel.setAttribute("id", retorno[0]);
		novo_painel.innerHTML = retorno[1];
	}
	
	var endereco = caminho_funcoes + "cadastros.php?funcao=" + nome_funcao + "&tipo=" + tipo + "&num=" + campo_qtde.value;
	if (!EUndefined(num_sup)) {
		endereco += "&num_sup=" + num_sup;
	}
	
	ExecutaXMLHTTP("GET", endereco, true, funcao);
}


function ExcluirItem(painel_orig, painel_restaura, campo_excluido) {
	campo_excluido.value = "1";
	
	/* funcao = function() {
		Abre(painel_restaura, funcao_depois, 50, 300);
	}
	
	Fecha(painel_orig, funcao, 50); */
	
	Mostra(painel_restaura);
	Esconde(painel_orig);
}
function RestaurarItem(painel_orig, painel_restaura, campo_excluido) {
	campo_excluido.value = "0";
	
	/* funcao = function() {
		Abre(painel_orig, funcao_depois, 50, 300);
	}
	
	Fecha(painel_restaura, funcao, 50); */
	Mostra(painel_orig);
	Esconde(painel_restaura);
}

function ToggleNif(em_branco, painel_preenche, painel_n_preenche) {
	if (em_branco) {
		painel_preenche.style.display = "none";
		painel_n_preenche.style.display = "block";
	}
	else {
		painel_preenche.style.display = "block";
		painel_n_preenche.style.display = "none";		
	}
}

function CalculaValorRestante(valor_total, id_caixas_a_somar, resultado1, resultado2, id_campo_excluido) {
	var i = 1;
	var soma = 0;
	
	while (true) {
		if (!(document.getElementById(id_caixas_a_somar + i) === null)) {
			if (document.getElementById(id_campo_excluido + i).value == "0") { //Verifica se não está excluido
				if (!document.getElementById(id_caixas_a_somar + i).value.IsNumeric()) {
					document.getElementById(id_caixas_a_somar + i).value = "0";
				}
				
				soma += parseFloat(document.getElementById(id_caixas_a_somar + i).value.replace(",", "."));
			}
		}
		else {
			break;
		}
		
		i++;
	}
	
	resultado1.value = soma.toString().replace(".", ",");
	resultado2.value = (parseFloat(valor_total.value.replace(",", ".")) - soma).toString().replace(".", ",");
}

function Grava(form, tipo, botao_salvar, local_msg_erro, local_id) {
	var gravar_texto_anterior = botao_salvar.value;

	botao_salvar.value = "Gravando...";
	botao_salvar.className = "botao_form_disabled";
	botao_salvar.disabled = true;
	
	Esconde(local_msg_erro);

	var r = true;
	var msg_erro = "";
	var focus_em;
	
	LimpaErro(form);
	
	var t_participante = "";
	
	if (tipo == "ras" || tipo == "rvs") {
		if (tipo == "ras") {
			t_participante = "vendedor";
		}
		else if (tipo == "rvs") {
			t_participante = "adquirente";
		}
	}
	
	if (tipo == "participante") {
		if (document.getElementById("f_part_nome").value.trim() == "") {
			if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_nome");
			document.getElementById("f_part_nome").className = "campo_form_erro";
		
			msg_erro += (msg_erro != "" ? "<br />" : "") +
				"É necessário digitar o nome do participante.";
			r = false;
		}
		if (document.getElementById("f_part_endereco").value.trim() == "") {
			if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_endereco");
			document.getElementById("f_part_endereco").className = "campo_form_erro";
			
			msg_erro += (msg_erro != "" ? "<br />" : "") +
				"É necessário digitar o endereço do participante.";
			r = false;
		}
		
		if (document.getElementById("f_part_pais_cod").value.trim() == "") {
			if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_pais_cod");
			document.getElementById("f_part_pais_cod").className = "campo_form_erro";
		
			msg_erro += (msg_erro != "" ? "<br />" : "") +
				"É necessário digitar o código do país do participante.";
			r = false;
		}
		else if (!ExisteDescricao(document.getElementById("f_part_pais_cod").value, "pais")) {
			if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_pais_cod");
			document.getElementById("f_part_pais_cod").className = "campo_form_erro";
		
			msg_erro += (msg_erro != "" ? "<br />" : "") +
				"Código do país inválido.";
			r = false;
		}
		
		if (document.getElementById("f_part_sem_nif").checked) {
			var cod_pais = document.getElementById("f_part_pais_cod").value.trim();
			
			if (cod_pais == '63' || cod_pais == '169' || cod_pais == '493'  || cod_pais == '580') {
				if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_sem_nif");
				document.getElementById("f_part_sem_nif").className = "campo_form_erro";
				
				msg_erro += (msg_erro != "" ? "<br />" : "") +
					"O país " + document.getElementById("f_part_pais_desc").value + " exige o preenchimento do NIF.";
				r = false;				
			}
			else if (document.getElementById("f_part_motivo_nif").selectedIndex == -1) {
				if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_motivo_nif");
				document.getElementById("f_part_motivo_nif").className = "campo_form_erro";
			
				msg_erro += (msg_erro != "" ? "<br />" : "") +
					"É necessário informar o motivo de não preenchimento do NIF.";
				r = false;				
			}
		}
		else {
			if (document.getElementById("f_part_nif").value.trim() == "") {
				if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_nif");
				document.getElementById("f_part_nif").className = "campo_form_erro";
			
				msg_erro += (msg_erro != "" ? "<br />" : "") +
					"É necessário preencher o NIF do participante.";
				r = false;
			}
		}
	}
	else if (tipo == "ras" || tipo == "rvs") {
		if (document.getElementById("f_part_cod").value.trim() == "") {
			if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_cod");
			document.getElementById("f_part_cod").className = "campo_form_erro";
		
			msg_erro += (msg_erro != "" ? "<br />" : "") +
				"É necessário digitar o código do " + t_participante + ".";
			r = false;
		}
		else if (!ExisteDescricao(document.getElementById("f_part_cod").value, "participante")) {
			if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_cod");
			document.getElementById("f_part_cod").className = "campo_form_erro";
		
			msg_erro += (msg_erro != "" ? "<br />" : "") +
				"Código do " + t_participante + " inválido.";
			r = false;
		}
		
		if (document.getElementById("f_" + tipo + "_moeda_cod").value.trim() == "") {
			if (EUndefined(focus_em)) focus_em = document.getElementById("f_" + tipo + "_moeda_cod");
			document.getElementById("f_" + tipo + "_moeda_cod").className = "campo_form_erro";
		
			msg_erro += (msg_erro != "" ? "<br />" : "") +
				"É necessário digitar o código da moeda.";
			r = false;
		}
		else if (!ExisteDescricao(document.getElementById("f_" + tipo + "_moeda_cod").value, "moeda")) {
			if (EUndefined(focus_em)) focus_em = document.getElementById("f_" + tipo + "_moeda_cod");
			document.getElementById("f_" + tipo + "_moeda_cod").className = "campo_form_erro";
		
			msg_erro += (msg_erro != "" ? "<br />" : "") +
				"Código da moeda inválido.";
			r = false;
		}
		
		if (document.getElementById("f_part_sem_nif").checked) {
			var cod_pais = document.getElementById("f_part_pais_cod").value.trim();
			
			if (cod_pais == '63' || cod_pais == '169' || cod_pais == '493'  || cod_pais == '580') {
				if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_sem_nif");
				document.getElementById("f_part_sem_nif").className = "campo_form_erro";
				
				msg_erro += (msg_erro != "" ? "<br />" : "") +
					"O país " + document.getElementById("f_part_pais_desc").value + " exige o preenchimento do NIF.";
				r = false;				
			}
			else if (document.getElementById("f_part_motivo_nif").selectedIndex == -1) {
				if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_motivo_nif");
				document.getElementById("f_part_motivo_nif").className = "campo_form_erro";
			
				msg_erro += (msg_erro != "" ? "<br />" : "") +
					"É necessário informar o motivo de não preenchimento do NIF.";
				r = false;				
			}
		}
		else {
			if (document.getElementById("f_part_nif").value.trim() == "") {
				if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_nif");
				document.getElementById("f_part_nif").className = "campo_form_erro";
			
				msg_erro += (msg_erro != "" ? "<br />" : "") +
					"É necessário preencher o NIF do participante.";
				r = false;
			}
		}
		
		var painel, num; 
		var nome_foco = new Array("");
		
		for (var i = 0; i < document.getElementById("f_" + tipo + "_painel_operacoes").childNodes.length; i++) {
			painel = document.getElementById("f_" + tipo + "_painel_operacoes").childNodes[i];
			
			//document.getElementById("f_rvs_info_compl").value += painel.id + "<br />";
			
			if (painel.nodeType == 1) {
				if (painel.id.left(9) == "f_" + tipo + "_op_") {
					num = painel.id.substring(9);
					
					//Se for maior que 0, significa que a operação foi incluída.
					if (painel.getElementsByTagName("input").length > 0) {
						Esconde(document.getElementById("f_" + tipo + "_msg_erro_op_" + num));
						
						if (document.getElementById("f_" + tipo + "_op_excluido_" + num).value == "0") {
							if (!ValidaOperacao(num, tipo, nome_foco)) {
								if (EUndefined(focus_em) && nome_foco[0] != "") {
									focus_em = document.getElementById(nome_foco[0]);
								}
							
								r = false;
							}
						}
					}
				}
			}
		}
	}
			
	if (!r) {
		local_msg_erro.className = "f_part_msg_erro";
		local_msg_erro.innerHTML = msg_erro;
		Mostra(local_msg_erro);
		if (!EUndefined(focus_em)) focus_em.focus();

	
		botao_salvar.disabled = false;
		botao_salvar.className = "botao_form";
		botao_salvar.value = gravar_texto_anterior;
		
		return r;
	}
	
	var xmlhttp = AbreXMLHTTP();
	var texto;
	var params = "";
	
	var endereco_grava = caminho_funcoes;
	
	if (tipo == "participante") {
		endereco_grava += "cadastros.php?funcao=grava_participante";
	}
	else if (tipo == "ras") {
		endereco_grava += "cadastros.php?funcao=grava_ras";
	}
	else if (tipo == "rvs") {
		endereco_grava += "cadastros.php?funcao=grava_rvs";
	}
	
	for (var i = 0; i < form.length; i++) {
		if (form.elements[i].id == "f_part_sem_nif") {
			if (form.elements[i].checked) {
				params += (i != 0 ? "&" : "") +
					form.elements[i].id + "=" + form.elements[i].value;
			}
		}
		else if (form.elements[i].name.indexOf("f_rvs_op_modo_prestacao") != -1) {
			if (form.elements[i].checked) {
				params += (i != 0 ? "&" : "") +
					form.elements[i].name + "=" + form.elements[i].value;
			}
		}
		else {
			params += (i != 0 ? "&" : "") +
				form.elements[i].name + "=" + form.elements[i].value;
		}
	}
	
	xmlhttp.open("POST", endereco_grava, false);
	
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			texto = xmlhttp.responseText;

			texto = texto.replace(/\+/g, " ");
			texto = unescape(texto);
		}
	}
		
	xmlhttp.send(params);
	
	if (!texto.IsNumeric()) {
		local_msg_erro.className = "texto_msg_erro";
		local_msg_erro.innerHTML = "Erro na gravação!<br />" + texto;
		Mostra(local_msg_erro);
	}
	else {
		if (tipo == "ras" || tipo == "rvs") {
			r = true;
			
			var endereco = window.location.href;
			
			if (endereco.indexOf("?") != -1) {
				endereco = endereco.substring(0, endereco.indexOf("?"));
			}
			
			window.location = endereco + "?tipo=" + tipo + "&id=" + texto;
		}
		else {
			r = true;
	
			local_msg_erro.className = "texto_msg";
			local_msg_erro.innerHTML = "Gravado com sucesso!";
			Mostra(local_msg_erro);
			
			if (!EUndefined(local_id)) {
				if (local_id.value == "")
				{
					local_id.value = texto;
					Mostra(local_id);
					Mostra(local_id);
				}
			}
			
			var intervalo = window.setTimeout(
				function() {
					Esconde(local_msg_erro);
				}
			, 3000);
		}
	}
	
	botao_salvar.disabled = false;
	botao_salvar.className = "botao_form";
	botao_salvar.value = gravar_texto_anterior;
	
	return r;
}

function ValidaOperacao(num, tipo, focus_em) {
	var r = true;
	var msg_erro = "";

	if (document.getElementById("f_" + tipo + "_op_cod_nbs_" + num).value.trim() == "") {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_cod_nbs_" + num);
		document.getElementById("f_" + tipo + "_op_cod_nbs_" + num).className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"É necessário informar o NBS.";
		r = false;
	}
	else if (!ExisteDescricao(document.getElementById("f_" + tipo + "_op_cod_nbs_" + num).value, "nbs")) {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_cod_nbs_" + num);
		document.getElementById("f_" + tipo + "_op_cod_nbs_" + num).className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"Código do NBS não encontrado.";
		r = false;
	}
	
	if (document.getElementById("f_" + tipo + "_op_cod_pais_" + num).value.trim() == "") {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_cod_pais_" + num);
		document.getElementById("f_" + tipo + "_op_cod_pais_" + num).className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"É necessário informar o País de Destino.";
		r = false;
	}
	else if (!ExisteDescricao(document.getElementById("f_" + tipo + "_op_cod_pais_" + num).value, "pais")) {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_cod_pais_" + num);
		document.getElementById("f_" + tipo + "_op_cod_pais_" + num).className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"País de destino não encontrado.";
		r = false;
	}
	
	if (!(document.getElementById("f_" + tipo + "_op_modo_prestacao_" + num + "_1").checked ||
	document.getElementById("f_" + tipo + "_op_modo_prestacao_" + num + "_2").checked ||
	document.getElementById("f_" + tipo + "_op_modo_prestacao_" + num + "_4").checked)) {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_modo_prestacao_" + num + "_1");
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"É necessário informar o Modo de Prestação.";
		r = false;
	}
	
	var inicio_valido = true, conclusao_valido = true;
	
	if (!(document.getElementById("f_" + tipo + "_op_dt_inicio_" + num).value.IsDate())) {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_dt_inicio_" + num);
		document.getElementById("f_" + tipo + "_op_dt_inicio_" + num).className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"Data de Início Inválida.";
		r = false;
		inicio_valido = false;
	}
	if (!(document.getElementById("f_" + tipo + "_op_dt_conclusao_" + num).value.IsDate())) {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_dt_conclusao_" + num);
		document.getElementById("f_" + tipo + "_op_dt_conclusao_" + num).className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"Data de Conclusão Inválida.";
		r = false;
		conclusao_valido = false;
	}
	
	if (inicio_valido && conclusao_valido) {
		var aux = document.getElementById("f_" + tipo + "_op_dt_inicio_" + num).value.split("/");
		var d1 = new Date(aux[2], aux[1], aux[0]);
		
		aux = document.getElementById("f_" + tipo + "_op_dt_conclusao_" + num).value.split("/");
		var d2 = new Date(aux[2], aux[1], aux[0]);
		
		if (d1 > d2) {
			if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_dt_inicio_" + num + "_1");
			document.getElementById("f_" + tipo + "_op_dt_inicio_" + num).className = "campo_form_erro";
		
			msg_erro += (msg_erro != "" ? "<br />" : "") +
				"Data de Início não pode ser posterior à Conclusão.";
			r = false;
		}
	} 
	
	if (!(document.getElementById("f_" + tipo + "_op_valor_" + num).value.IsNumeric())) {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_valor_" + num);
		document.getElementById("f_" + tipo + "_op_valor_" + num).className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"Valor da Operação inválido.";
		r = false;
	}
	if (document.getElementById("f_" + tipo + "_op_valor_restante_" + num).value.IsNumeric(true)) {
		if (parseFloat(document.getElementById("f_" + tipo + "_op_valor_restante_" + num).value) < 0) {
			if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_valor_restante_" + num);
			document.getElementById("f_" + tipo + "_op_valor_restante_" + num).className = "campo_form_erro";
		
			msg_erro += (msg_erro != "" ? "<br />" : "") +
				"Valor dos Recebimentos não pode exceder o Valor da Operação.";
			r = false;
		}
	}
	
	if (!r) {
		document.getElementById("f_" + tipo + "_msg_erro_op_" + num).className = "texto_msg_erro";
		document.getElementById("f_" + tipo + "_msg_erro_op_" + num).innerHTML = msg_erro;
		Mostra(document.getElementById("f_" + tipo + "_msg_erro_op_" + num));
	}
	else {
		Esconde(document.getElementById("f_" + tipo + "_msg_erro_op_" + num));
	}
	
	var painel, numx;
	var tam = ("f_" + tipo + "_op_" + num + "_enq_").length;
	
	for (var i = 0; i < document.getElementById("f_" + tipo + "_op_painel_enq_" + num).childNodes.length; i++) {
		painel = document.getElementById("f_" + tipo + "_op_painel_enq_" + num).childNodes[i];
		
		if (painel.nodeType == 1) {		
			if (painel.id.left(tam) == "f_" + tipo + "_op_" + num + "_enq_") {
				numx = painel.id.substring(tam);
				
				//Se for maior que 0, significa que o enquadramento foi incluído.
				if (painel.getElementsByTagName("input").length > 0) {
					Esconde(document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_enq_" + numx));
				
					if (document.getElementById("f_" + tipo + "_op_" + num + "_enq_excluido_" + numx).value == "0") {
						if (!ValidaEnquadramento(num, numx, tipo, focus_em)) {
							r = false;
						}
					}
				}
			}
		}
	}
	
	tam = ("f_" + tipo + "_op_" + num + "_r_").length;
	
	for (var i = 0; i < document.getElementById("f_" + tipo + "_op_painel_r_" + num).childNodes.length; i++) {
		painel = document.getElementById("f_" + tipo + "_op_painel_r_" + num).childNodes[i];
		
		if (painel.nodeType == 1) {		
			if (painel.id.left(tam) == "f_" + tipo + "_op_" + num + "_r_") {
				numx = painel.id.substring(tam);
				
				//Se for maior que 0, significa que o enquadramento foi incluído.
				if (painel.getElementsByTagName("input").length > 0) {
					Esconde(document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_r_" + numx));
				
					if (document.getElementById("f_" + tipo + "_op_" + num + "_r_excluido_" + numx).value == "0") {
						if (!ValidaRecebimento(num, numx, tipo, focus_em)) {
							r = false;
						}
					}
				}
			}
		}
	}
	
	
	tam = ("f_" + tipo + "_op_" + num + "_re_").length;
	
	for (var i = 0; i < document.getElementById("f_" + tipo + "_op_painel_re_" + num).childNodes.length; i++) {
		painel = document.getElementById("f_" + tipo + "_op_painel_re_" + num).childNodes[i];
		
		if (painel.nodeType == 1) {		
			if (painel.id.left(tam) == "f_" + tipo + "_op_" + num + "_re_") {
				numx = painel.id.substring(tam);
				
				//Se for maior que 0, significa que o enquadramento foi incluído.
				if (painel.getElementsByTagName("input").length > 0) {
					Esconde(document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_re_" + numx));
				
					if (document.getElementById("f_" + tipo + "_op_" + num + "_re_excluido_" + numx).value == "0") {
						if (!ValidaRE(num, numx, tipo, focus_em)) {
							r = false;
						}
					}
				}
			}
		}
	}
	
	tam = ("f_" + tipo + "_op_" + num + "_di_").length;
	
	for (var i = 0; i < document.getElementById("f_" + tipo + "_op_painel_di_" + num).childNodes.length; i++) {
		painel = document.getElementById("f_" + tipo + "_op_painel_di_" + num).childNodes[i];
		
		if (painel.nodeType == 1) {		
			if (painel.id.left(tam) == "f_" + tipo + "_op_" + num + "_di_") {
				numx = painel.id.substring(tam);
				
				//Se for maior que 0, significa que o enquadramento foi incluído.
				if (painel.getElementsByTagName("input").length > 0) {
					Esconde(document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_di_" + numx));
				
					if (document.getElementById("f_" + tipo + "_op_" + num + "_di_excluido_" + numx).value == "0") {
						if (!ValidaDI(num, numx, tipo, focus_em)) {
							r = false;
						}
					}
				}
			}
		}
	}
	
	return r;
}

function ValidaEnquadramento(num, numx, tipo, focus_em) {
	var r = true;
	var msg_erro = "";

	if (document.getElementById("f_" + tipo + "_op_" + num + "_enq_cod_" + numx).value.trim() == "") {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_" + num + "_enq_cod_" + numx);
		document.getElementById("f_" + tipo + "_op_" + num + "_enq_cod_" + numx).className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"É necessário informar o Código do Enquadramento.";
		r = false;
	}
	else if (!ExisteDescricao(document.getElementById("f_" + tipo + "_op_" + num + "_enq_cod_" + numx).value, "enq")) {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_" + num + "_enq_cod_" + numx);
		document.getElementById("f_" + tipo + "_op_" + num + "_enq_cod_" + numx).className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"Código do Enquadramento não encontrado.";
		r = false;
	}
	
	var rc_valido = true;
	
	if (document.getElementById("f_" + tipo + "_op_" + num + "_enq_num_rc_" + numx).value.trim() != "") {
		if (document.getElementById("f_" + tipo + "_op_" + num + "_enq_num_rc_" + numx).value.length != 9) {
			rc_valido = false;
		}
		else if (
			(!document.getElementById("f_" + tipo + "_op_" + num + "_enq_num_rc_" + numx).value.substring(0, 2).IsNumeric()) ||
			(document.getElementById("f_" + tipo + "_op_" + num + "_enq_num_rc_" + numx).value.substring(2, 3) != "/") ||
			(!document.getElementById("f_" + tipo + "_op_" + num + "_enq_num_rc_" + numx).value.substring(3).IsNumeric())
		) {
			rc_valido = false;
		}
		
		if (!rc_valido) {
			if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_" + num + "_enq_num_rc_" + numx);
			document.getElementById("f_" + tipo + "_op_" + num + "_enq_num_rc_" + numx).className = "campo_form_erro";
		
			msg_erro += (msg_erro != "" ? "<br />" : "") +
				"Número do RC Inválido.";
			r = false;
		}
	}
	
	if (!r) {
		document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_enq_" + numx).className = "texto_msg_erro";
		document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_enq_" + numx).innerHTML = msg_erro;
		Mostra(document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_enq_" + numx));
	}
	
	return r;
}


function ValidaRE(num, numx, tipo, focus_em) {
	var r = true;
	var msg_erro = "";

	
	var re_valida = true;
		
	if (document.getElementById("f_" + tipo + "_op_" + num + "_re_num_" + numx).value.length != 13) {
		re_valida = false;
	}
	else if (
		(!document.getElementById("f_" + tipo + "_op_" + num + "_re_num_" + numx).value.substring(0, 2).IsNumeric()) ||
		(document.getElementById("f_" + tipo + "_op_" + num + "_re_num_" + numx).value.substring(2, 3) != "/") ||
		(!document.getElementById("f_" + tipo + "_op_" + num + "_re_num_" + numx).value.substring(3, 13).IsNumeric())
	) {
		re_valida = false;
	}
	
	if (!re_valida) {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_" + num + "_re_num_" + numx);
		document.getElementById("f_" + tipo + "_op_" + num + "_re_num_" + numx).className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"Número da RE Inválido.";
		r = false;
	}
	
	if (!r) {
		document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_re_" + numx).className = "texto_msg_erro";
		document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_re_" + numx).innerHTML = msg_erro;
		Mostra(document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_re_" + numx));
	}
	
	return r;
}

function ValidaDI(num, numx, tipo, focus_em) {
	var r = true;
	var msg_erro = "";

	
	var di_valida = true;
		
	if (document.getElementById("f_" + tipo + "_op_" + num + "_di_num_" + numx).value.length != 12) {
		di_valida = false;
	}
	else if (
		(!document.getElementById("f_" + tipo + "_op_" + num + "_di_num_" + numx).value.substring(0, 2).IsNumeric()) ||
		(document.getElementById("f_" + tipo + "_op_" + num + "_di_num_" + numx).value.substring(2, 3) != "/") ||
		(!document.getElementById("f_" + tipo + "_op_" + num + "_di_num_" + numx).value.substring(3, 9).IsNumeric()) ||
		(document.getElementById("f_" + tipo + "_op_" + num + "_di_num_" + numx).value.substring(10, 11) != "-") ||
		(!document.getElementById("f_" + tipo + "_op_" + num + "_di_num_" + numx).value.substring(11, 12).IsNumeric())
	) {
		di_valida = false;
	}
	
	if (!di_valida) {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_" + num + "_di_num_" + numx);
		document.getElementById("f_" + tipo + "_op_" + num + "_di_num_" + numx).className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"Número da DI Inválido.";
		r = false;
	}
	
	if (!r) {
		document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_di_" + numx).className = "texto_msg_erro";
		document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_di_" + numx).innerHTML = msg_erro;
		Mostra(document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_di_" + numx));
	}
	
	return r;
}


function ValidaRecebimento(num, numx, tipo, focus_em) {
	var r = true;
	var msg_erro = "";
	
	var t_data = "";
	
	if (tipo == "ras") {
		t_data = "Pagamento";
	}
	else if (tipo == "rvs") {
		t_data = "Faturamento";
	}

	
	
	if (!(document.getElementById("f_" + tipo + "_op_" + num + "_r_data_" + numx).value.IsDate())) {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_" + num + "_r_data_" + numx);
		document.getElementById("f_" + tipo + "_op_" + num + "_r_data_" + numx).className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"Data de " + t_data + " Inválida.";
		r = false;
		inicio_valido = false;
	}
	
	if (!(document.getElementById("f_" + tipo + "_op_" + num + "_valor_" + numx).value.IsNumeric())) {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_" + num + "_valor_" + numx);
		document.getElementById("f_" + tipo + "_op_" + num + "_valor_" + numx).className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"Valor do " + t_data + " inválido.";
		r = false;
	} 
	if (!(document.getElementById("f_" + tipo + "_op_" + num + "_valor_ext_" + numx).value.IsNumeric())) {
		if (focus_em[0] == "") focus_em[0] = ("f_" + tipo + "_op_" + num + "_valor_ext_" + numx);
		document.getElementById("f_" + tipo + "_op_" + num + "_valor_ext_" + numx).className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"Valor Mantido no Exterior inválido.";
		r = false;
	} 
	
	if (!r) {
		document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_r_" + numx).className = "texto_msg_erro";
		document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_r_" + numx).innerHTML = msg_erro;
		Mostra(document.getElementById("f_" + tipo + "_msg_erro_op_" + num + "_r_" + numx));
	}
	
	return r;
}

/*
function Grava_Antigo(form, tipo)
{
	var gravar_texto_anterior = document.getElementById("f_part_salvar").value;

	document.getElementById("f_part_salvar").value = "Gravando...";
	document.getElementById("f_part_salvar").className = "botao_form_disabled";
	document.getElementById("f_part_salvar").disabled = true;
	
	Esconde(document.getElementById("f_part_msg_erro"));

	var r = true;
	var msg_erro = "";
	var focus_em;
	
	LimpaErro(form);
	
	if (document.getElementById("f_part_nome").value.trim() == "")
	{
		if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_nome");
		document.getElementById("f_part_nome").className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"É necessário digitar o nome do participante.";
		r = false;
	}
	if (document.getElementById("f_part_endereco").value.trim() == "")
	{
		if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_endereco");
		document.getElementById("f_part_endereco").className = "campo_form_erro";
		
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"É necessário digitar o endereço do participante.";
		r = false;
	}
	
	if (document.getElementById("f_part_pais_cod").value.trim() == "")
	{
		if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_pais_cod");
		document.getElementById("f_part_pais_cod").className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"É necessário digitar o código do país do participante.";
		r = false;
	}
	else if (!ExisteDescricao(document.getElementById("f_part_pais_cod").value, "pais"))
	{
		if (EUndefined(focus_em)) focus_em = document.getElementById("f_part_pais_cod");
		document.getElementById("f_part_pais_cod").className = "campo_form_erro";
	
		msg_erro += (msg_erro != "" ? "<br />" : "") +
			"Código do país inválido.";
		r = false;
	}
			
	if (!r)
	{
		document.getElementById("f_part_msg_erro").className = "f_part_msg_erro";
		document.getElementById("f_part_msg_erro").innerHTML = msg_erro;
		Mostra(document.getElementById("f_part_msg_erro"));
		if (!EUndefined(focus_em)) focus_em.focus();

	
		document.getElementById("f_part_salvar").disabled = false;
		document.getElementById("f_part_salvar").className = "botao_form";
		document.getElementById("f_part_salvar").value = gravar_texto_anterior;
		
		return r;
	}
	
	var xmlhttp = AbreXMLHTTP();
	var texto;
	var params = "";
	
	for (var i = 0; i < form.length; i++)
	{
		params += (i != 0 ? "&" : "") +
			form.elements[i].id + "=" + form.elements[i].value;
	}
	
	xmlhttp.open("POST", caminho_funcoes + "cadastros.php?funcao=grava_participante", false);
	
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	
	xmlhttp.onreadystatechange = function() 
	{
		if (xmlhttp.readyState == 4)
		{
			texto = xmlhttp.responseText;

			texto = texto.replace(/\+/g, " ");
			texto = unescape(texto);
		}
	}
		
	xmlhttp.send(params);
	
	if (!texto.IsNumeric())
	{
		document.getElementById("f_part_msg_erro").className = "texto_msg_erro";
		document.getElementById("f_part_msg_erro").innerHTML = "Erro na gravação!<br />" + texto;
		Mostra(document.getElementById("f_part_msg_erro"));
	}
	else
	{
		r = true;
	
		document.getElementById("f_part_msg_erro").className = "texto_msg";
		document.getElementById("f_part_msg_erro").innerHTML = "Gravado com sucesso!";
		Mostra(document.getElementById("f_part_msg_erro"));
		
		if (document.getElementById("f_part_cod").value == "")
		{
			document.getElementById("f_part_cod").value = texto;
			Mostra(document.getElementById("f_part_cod"));
			Mostra(document.getElementById("f_t_part_cod"));
		}
		
		var intervalo = window.setTimeout(
			function() {
				Esconde(document.getElementById("f_part_msg_erro"));
			}
		, 3000);
	}
	
	document.getElementById("f_part_salvar").disabled = false;
	document.getElementById("f_part_salvar").className = "botao_form";
	document.getElementById("f_part_salvar").value = gravar_texto_anterior;
	
	return r;
} */

function Excluir(id, tipo) {
	var t_tipo = "";
	var funcao;
	
	if (tipo == "rvs") {
		t_tipo = "RVS";
	}
	else if (tipo == "ras") {
		t_tipo = "RAS";
	}
	
	var r = confirm("Deseja excluir o " + t_tipo + " número " + id + "?");
	
	if (r) {
		funcao = function(texto) {
			if (texto == "OK") {
				window.location.reload();
			}
			else {
				document.getElementById("tb_msg_erro").innerHTML = "Erro na exclusão: " + texto;
				Mostra(document.getElementById("tb_msg_erro"));
				//document.write("Erro na exclusão: " + texto);
			}
		}
		
		var endereco = caminho_funcoes + "/cadastros.php?funcao=excluir&tipo=" + tipo + "&id=" + id;
		
		ExecutaXMLHTTP("GET", endereco, true, funcao);
	}
}