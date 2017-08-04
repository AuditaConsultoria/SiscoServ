//caminho_funcoes = "http://hetakuso.net/siscoserv_novo/ajax/";
//caminho_imagens = "http://hetakuso.net/siscoserv_novo/imagens/";
caminho_funcoes = "/siscoserv/ajax/";
caminho_imagens = "/siscoserv/imagens/";

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g, "");
}
String.prototype.ltrim = function() {
	return this.replace(/^\s+/, "");
}
String.prototype.rtrim = function() {
	return this.replace(/\s+$/, "");
}

String.prototype.reverse = function() {
	return this.split("").reverse().join("");
}

String.prototype.left = function(n) {
	if (n <= 0)
	    return "";
	else if (n > String(this).length)
	    return this;
	else
	    return String(this).substring(0, n);
}
String.prototype.right = function(n) {
    if (n <= 0)
       return "";
    else if (n > String(this).length)
       return this;
    else {
       var iLen = String(this).length;
       return String(this).substring(iLen, iLen - n);
    }
}

function EUndefined(o_que) {
	return (typeof o_que === "undefined");
}

function AbreXMLHTTP() {
	var xmlhttp;

	try {
		xmlhttp = new XMLHttpRequest();
	}
	catch(e1) {
		try {
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e2) {
			try {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e3) {
				xmlhttp = false;
			}
		}
	}
	return xmlhttp;
}

function ExecutaXMLHTTP(metodo, endereco, assincrono, funcao, uid) {
	var texto = "";
	
	var xmlhttp = AbreXMLHTTP();
	
	if (!EUndefined(uid)) {
		xmlhttp.uniqueid = uid;
	}
	//console.log(xmlhttp.uniqueid);

	xmlhttp.open(metodo, endereco, assincrono);
	xmlhttp.onreadystatechange = function() 
	{
		if (xmlhttp.readyState == 4)
		{
			texto = xmlhttp.responseText;

			texto = texto.replace(/\+/g, " ");
			texto = unescape(texto);
			
			//console.log(texto);
			
			if (!EUndefined(funcao)) {
				funcao(texto);
			}
		}
	}

	xmlhttp.send(null);
	
	return texto;
}
function ExecutaXMLHTTPExistente(xhr, metodo, endereco, assincrono, funcao, uid) {
	var texto = "";
		
	if (!EUndefined(uid)) {
		xhr.uniqueid = uid;
	}

	xhr.open(metodo, endereco, assincrono);
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4)
		{
			texto = xhr.responseText;

			texto = texto.replace(/\+/g, " ");
			texto = unescape(texto);
			
			if (!EUndefined(funcao)) {
				funcao(texto);
			}
		}
	}

	xhr.send(null);
	
	return texto;
}


String.prototype.IsNumeric = function(negativos = false) {
	if (this.length == 0) {
		return false;
	}

	var validos = "0123456789,";
	if (negativos) { validos += "-"; }
	
	var r = true;
	var c;

	for (i = 0; i < this.length && r == true; i++) { 
		c = this.charAt(i); 
		if (validos.indexOf(c) == -1) {
			r = false;
			break;
		}
	}

	return r;
}
String.prototype.IsDate = function() {
	var ardt = new Array;
	var ExpReg = new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
	
	ardt = this.split("/");
	var r = true;
	
	//if (this.trim() == "") return false;
	
	if (this.search(ExpReg) == -1) {
		r = false;
	}
	else if (((ardt[1] == 4) || (ardt[1] == 6) || (ardt[1] == 9) || (ardt[1] == 11)) && (ardt[0] > 30)) {
		r = false;
	}
	else if (ardt[1] == 2) {
		if ((ardt[0] > 28) && ((ardt[2] % 4) != 0)) r = false;
		if ((ardt[0] > 29) && ((ardt[2] % 4) == 0)) r = false;
	}
	
	return r;
}


function SomenteNumeros(e, obj, virgula) {
	if (EUndefined(virgula)) virgula = false;

	var key;

	if (window.event) {    
		key = window.event.keyCode;
	}
	else {     
		key = e.which; 
	}

	var permitidos = new Array();

	permitidos.push(8); //BackSpace	
	permitidos.push(9); //TAB
	permitidos.push(46); //Delete
	for (var i = 48; i <= 57; i++) {
		permitidos.push(i); //0 a 9
	}
	for (var i = 96; i <= 105; i++) {
		permitidos.push(i); //0 a 9 no teclado num�rico
	}
	for (var i = 33; i <= 40; i++) {
		permitidos.push(i); //Setas do teclado + HOME, END, PGUP, PGDOWN
	}
	permitidos.push(44); //n�o sei o que �
	permitidos.push(13); //Enter
	permitidos.push(21); //ESC
	permitidos.push(16); //Shift
	permitidos.push(17); //Ctrl
	permitidos.push(18); //Alt
	permitidos.push(45); //Insert
	for (var i = 112; i <= 123; i++) {
		permitidos.push(i); //F1 a F12
	}
	
	/*permitidos.push(110); //V�rgula do teclado num�rico
	permitidos.push(188); //V�rgula */

	for (var j = 0; j < permitidos.length; j ++) {
		if (key == permitidos[j]) {
			return true;
		}
	}
	
	if (virgula && (key == 110 || key == 188)) {
		if (obj.value.indexOf(",") == -1) {
			return true;
		}
	}

	return false;
}

function MascaraData(obj, e) {
	if (obj.readOnly) {
		return;
	}

	var key;

	if (window.event) {    
		key = window.event.keyCode;
	}
	else {     
		key = e.which; 
	}

	var permitidos = new Array();

	permitidos.push(8);
	permitidos.push(46);
	for (var i = 48; i <= 57; i++) {
		permitidos.push(i); //0 a 9
	}
	for (var i = 96; i <= 105; i++) {
		permitidos.push(i); //0 a 9 no teclado num�rico
	}

	var achou = false;

	for (var j = 0; j < permitidos.length; j ++) {
		if (key == permitidos[j]) {
			achou = true;
			break;
		}
	}

	if (!achou) {
		return;
	}

	var local_cursor = obj.selectionStart;
	var tam_atual = obj.value.length;

	obj.value = obj.value.replace("/", "");
	obj.value = obj.value.replace("/", "");

	if (obj.value.length <= 2) {
		return;
	}
/*	else if (obj.value.length == 2) {
		obj.value += "/";
	} */
	else if (obj.value.length <= 4) {
		obj.value = obj.value.substring(0, 2) + "/" + obj.value.substring(2);

		/* if (obj.value.length == 5) {
			obj.value += "/";
		} */
	}
	else {
		obj.value = obj.value.substring(0, 2) + "/" + obj.value.substring(2, 4) + "/" + obj.value.substring(4, 8);
	}
	
	if (obj.value.length - tam_atual > 0) {
		local_cursor += (obj.value.length - tam_atual);
	}
	
	obj.selectionStart = local_cursor;
	obj.selectionEnd = local_cursor;
}


function MascaraNumeroRC(obj, e) {
	if (obj.readOnly) {
		return;
	}

	var key;

	if (window.event) {    
		key = window.event.keyCode;
	}
	else {     
		key = e.which; 
	}

	var permitidos = new Array();

	permitidos.push(8);
	permitidos.push(46);
	for (var i = 48; i <= 57; i++) {
		permitidos.push(i); //0 a 9
	}
	for (var i = 96; i <= 105; i++) {
		permitidos.push(i); //0 a 9 no teclado num�rico
	}

	var achou = false;

	for (var j = 0; j < permitidos.length; j ++) {
		if (key == permitidos[j]) {
			achou = true;
			break;
		}
	}

	if (!achou) {
		return;
	}

	var local_cursor = obj.selectionStart;
	var tam_atual = obj.value.length;

	obj.value = obj.value.replace("/", "");

	if (obj.value.length <= 2) {
		return;
	}
	else {
		obj.value = obj.value.substring(0, 2) + "/" + obj.value.substring(2);
	}
	
	if (obj.value.length - tam_atual > 0) {
		local_cursor += (obj.value.length - tam_atual);
	}
	
	obj.selectionStart = local_cursor;
	obj.selectionEnd = local_cursor;
}


function MascaraNumeroDI(obj, e) {
	if (obj.readOnly) {
		return;
	}

	var key;

	if (window.event) {    
		key = window.event.keyCode;
	}
	else {     
		key = e.which; 
	}

	var permitidos = new Array();

	permitidos.push(8);
	permitidos.push(46);
	for (var i = 48; i <= 57; i++) {
		permitidos.push(i); //0 a 9
	}
	for (var i = 96; i <= 105; i++) {
		permitidos.push(i); //0 a 9 no teclado num�rico
	}

	var achou = false;

	for (var j = 0; j < permitidos.length; j ++) {
		if (key == permitidos[j]) {
			achou = true;
			break;
		}
	}

	if (!achou) {
		return;
	}

	var local_cursor = obj.selectionStart;
	var tam_atual = obj.value.length;

	obj.value = obj.value.replace("/", "");
	obj.value = obj.value.replace("-", "");

	if (obj.value.length <= 2) {
		return;
	}
/*	else if (obj.value.length == 2) {
		obj.value += "/";
	} */
	else if (obj.value.length <= 9) {
		obj.value = obj.value.substring(0, 2) + "/" + obj.value.substring(2);

		/* if (obj.value.length == 5)
		{
			obj.value += "/";
		} */
	}
	else {
		obj.value = obj.value.substring(0, 2) + "/" + obj.value.substring(2, 9) + "-" + obj.value.substring(9, 10);
	}
	
	if (obj.value.length - tam_atual > 0) {
		local_cursor += (obj.value.length - tam_atual);
	}
	
	obj.selectionStart = local_cursor;
	obj.selectionEnd = local_cursor;
}


function MascaraNumeroRE(obj, e) {
	if (obj.readOnly) {
		return;
	}

	var key;

	if (window.event) {    
		key = window.event.keyCode;
	}
	else {     
		key = e.which; 
	}

	var permitidos = new Array();

	permitidos.push(8);
	permitidos.push(46);
	for (var i = 48; i <= 57; i++) {
		permitidos.push(i); //0 a 9
	}
	for (var i = 96; i <= 105; i++) {
		permitidos.push(i); //0 a 9 no teclado num�rico
	}

	var achou = false;

	for (var j = 0; j < permitidos.length; j ++) {
		if (key == permitidos[j]) {
			achou = true;
			break;
		}
	}

	if (!achou) {
		return;
	}

	var local_cursor = obj.selectionStart;
	var tam_atual = obj.value.length;

	obj.value = obj.value.replace("/", "");

	if (obj.value.length <= 2) {
		return;
	}
/*	else if (obj.value.length == 2) {
		obj.value += "/";
	} */
	else {
		obj.value = obj.value.substring(0, 2) + "/" + obj.value.substring(2);

		/* if (obj.value.length == 5) {
			obj.value += "/";
		} */
	}
	
	if (obj.value.length - tam_atual > 0) {
		local_cursor += (obj.value.length - tam_atual);
	}
	
	obj.selectionStart = local_cursor;
	obj.selectionEnd = local_cursor;
}



/*
	Mostra um painel de uma vez.
*/
function Mostra(painel)
{
	painel.style.display = "block";
	painel.style.overflow = "visible";
	
	painel.style.height = "";
}

/*
	Esconde um painel de uma vez.
*/
function Esconde(painel)
{
	painel.style.display = "none";
}

/*
	painel: objeto a ser aberto;
	qual_tam: tamanho final que o objeto deve ter;
	quando_termina: fun��o a ser chamada quando terminar de abrir;
	demora: tempo que levar� para ser aberto
		(padr�o = 200 milissegundos).
*/
function Abre(painel, qual_tam, quando_termina, demora)
{
	//demora = (typeof demora === "undefined") ? 200 : demora;
	demora = (EUndefined(demora)) ? 200 : demora;
	
	//var overflow_atual = painel.style.overflow;
	//painel.style.overflow = "hidden";
	
	painel.style.display = "block";
	
	if (painel.offsetHeight >= qual_tam) {
		return;
	}
	
	//var tam = 0;
	var tam = painel.offsetHeight;
	var valor_acrescimo = qual_tam / (demora / 10); 
	
	 var intervalo = window.setInterval(
		function() {
		
			tam += valor_acrescimo;
			painel.style.height = parseInt(tam) + "px";
			
			//alert(tam);
			
			if (tam >= qual_tam)
			{
				clearInterval(intervalo);
				
				if (!EUndefined(quando_termina))
				{
					quando_termina();
				}
				// painel.style.overflow = overflow_atual;
			}
		}
	, (demora / 10));
}

/*
	painel: objeto a ser fechado;
	quando_termina: fun��o a ser chamada quando terminar de fechar;
	demora: tempo que levar� para ser fechado
		(padr�o = 200 milissegundos).
*/
function Fecha(painel, quando_termina, demora)
{
	//demora = (typeof demora === "undefined") ? 200 : demora;
	demora = (EUndefined(demora)) ? 200 : demora;
	
	//var overflow_atual = painel.style.overflow;
	//painel.style.overflow = "hidden";
	
	var tam = painel.offsetHeight;
	var valor_decrescimo = painel.offsetHeight / (demora / 10); 
	
	 var intervalo = window.setInterval(
		function() {
		
			tam -= valor_decrescimo;
			if (tam < 0) tam = 0;
			
			painel.style.height = parseInt(tam) + "px";
			//alert(painel.style.height);
			
			if (tam <= 0)
			{
				clearInterval(intervalo);
				painel.style.display = "none";
				
				if (!EUndefined(quando_termina))
				{
					quando_termina();
				}
				//painel.style.overflow = overflow_atual;
			}
		}
	, (demora / 10));
}


function ImageLoader(painel)
{
	painel.style.backgroundImage = "url('" + caminho_imagens + "ajax-loader.gif')";
	painel.style.backgroundRepeat = "no-repeat";
	painel.style.backgroundPosition = "center center";
}
function CancelaImageLoader(painel)
{
	painel.style.backgroundImage = "none";
}



/*
 Fun��o para filtrar a tabela, exibindo somente as linhas em que
 o texto buscado seja encontrado na segunda coluna.
 Mostra todas as linhas se for buscado um texto em branco.
 
 Par�metros:
	tabela: objeto HTML <table> a ser filtrado;
	texto: String a ser buscada na segunda coluna;
	ultima: <input type="hidden"> que deve guardar o �ltimo
		texto buscado (para evitar refazer a filtragem que
		j� se encontra vis�vel);
	num_filtro: campo onde ser� retornado o n�mero de
		registros exibidos ap�s a filtragem.
*/
function FiltraTabela(tabela, texto, ultima, num_filtro) {

	if (texto != ultima.value) {
		ultima.value = texto;
		
		linhas = tabela.getElementsByTagName("tr");
		qtde = 0;
		
		for (i = 1; i < linhas.length; i++) {
			if (texto != "") {							
				colunas = linhas[i].getElementsByTagName("td");
				
				if (colunas.length > 1) {
					if (colunas[1].innerText.toUpperCase().indexOf(texto.toUpperCase()) != -1) {
						linhas[i].style.display = "table-row";
						qtde++;
					}
					else {
						linhas[i].style.display = "none";
					}
				}
			}
			else {
				linhas[i].style.display = "table-row";
			}
		}
		
		if (!EUndefined(num_filtro)) {
			if (texto != "") {
				num_filtro.innerHTML = qtde;
			}
			else {
				num_filtro.innerHTML = linhas.length - 1;
			}
		}
	}
}