<?php
	ob_start();
	
	$up_id = uniqid(); 
	
	session_name('sessao');
	session_start();
	
	ini_set('display_errors', 1); 
	error_reporting(E_ALL); 
	
	require("includes/include.php");

	$h = new Site();
	
	if ($h->u->VerificaQueryStringLogoff()) {
		header("Location: " . Enderecos::Site());
	}
	
	 /* echo("LOGADO: " . $h->u->Logado() . "<br />");
	echo("IDUser: " . $h->u->IDUser() . "<br />");
	echo("IDLogin: " . $h->u->IDLogin() . "<br />");
	echo("NomeBanco: " . $h->u->NomeBanco() . "<br />");
	echo("Nome: " . $h->u->Nome() . "<br />");
	
	//$_SESSION["xxxxxx"] = "inferno";
	echo($_SESSION["xxxxxx"]);  
	
	echo("PERMISSÕES: " . $h->u->Nome()); */
	
	if ($h->u->Permissoes() != 1)
	{
		$h->AtualizaLogin();
		header("Location: " . Enderecos::Login());
	}
	
	$upload_feito = false;
	
	$destinos = array();
	
	if (isset($_POST)) {
		if (isset($_FILES["f_txts"]))
		{
			date_default_timezone_set("Brazil/East"); //Definindo timezone padrão
			
			$name = $_FILES["f_txts"]["name"];
			$tmp_name = $_FILES["f_txts"]["tmp_name"];
			
			$qtde = count($tmp_name);
		  
			for ($i = 0; $i < $qtde; $i++) {
				//echo ("Movendo arquivo " . ($i + 1) . " de " . $qtde . "<br / >");			  
	 
				$new_name = date("Y.m.d-H.i.s") . "__" . $_FILES["f_txts"]["name"][$i];
				$dir = Enderecos::ImportacaoFisico(); //Diretório para uploads
				
				//echo ($_FILES["f_txts"]["tmp_name"][$i] . " --> " . $dir . $new_name . "<br />");
				$destinos[] = $new_name;
		 
				move_uploaded_file($_FILES["f_txts"]["tmp_name"][$i], $dir . $new_name); //Fazer upload do arquivo
			}
			
			$upload_feito = true;
			
			$h->onload = "javascript: PreparaImportacao();";
		}
	}
	
	$h->header_adicional .= "\t\t<script src=\"" . Enderecos::Scripts() . "importacao.js?v=" . $_SESSION["vjs"] . "\" type=\"text/JavaScript\"></script>\n";
	
	$h->Cabecalho();
	
	$h->Menu();
?>
		<main id="corpo" class="corpo_import">
			<h2>Importar dados do EFD das Contribuições</h2>
			
<?php if (!$upload_feito) { ?>
			
			<form id="importa_efd" name="importa_efd" method="post" action="<?php echo(Enderecos::CompletoAmp()); ?>" enctype="multipart/form-data">
				<fieldset class="painel_formulario_import">
					<label class="nome_campo" id="f_t_txts" for="f_txts">Selecione um ou mais arquivos do EFD das Contribuições:</label>
					<section id="painel_arquivos">
						<h4>Arquivos Selecionados</h4>
						
						<div id="lista_arquivos">Nenhum arquivo selecionado.</div>
					</section>
					<input type="hidden" name="APC_UPLOAD_PROGRESS" id="progress_key" value="<?php echo $up_id; ?>" />
					<input id="f_txts" name="f_txts[]" type="file" multiple="multiple" onchange="javascript: ListaArquivos(this, document.getElementById('lista_arquivos'), document.getElementById('f_importar'));" />					
					
					<!-- <input id="f_importar" name="f_importar" class="f_part_salvar" style="" type="submit" onclick="javascript: this.disabled = true; ProgressoUpload(document.getElementById('upload_frame'), '<?php echo($up_id); ?>');" value="Importar TXTs" /> -->
					<input id="f_importar" name="f_importar" class="f_part_salvar" style="" type="submit"  value="Importar TXTs" />
				</fieldset>
				
				<iframe id="upload_frame" name="upload_frame" frameborder="0" border="0" src="" style="display: none;" scrolling="no" scrollbar="no" > </iframe>
			</form>
<?php }
else {
?>			<h3>Uploads feitos:</h3>
			<table>
<?php
		$i = 0;

		foreach ($destinos as $destino) { ?>
				<tr>
					<th>Arquivo</th>
					<th>Status</th>
					<th>&nbsp;</th>
				</tr>
				<tr>
					<td><?php echo($name[$i]); ?><td>
					<td id="a_status_<?php echo($i + 1) ?>">Aguardando...</td>
					<td id="a_erro_<?php echo($i + 1) ?>">&nbsp;</td>
				</tr>
<?php		
			$i++;
		}
?>
			</table>
			
			<input type="hidden" name="f_status" id="f_status" value="OK" />
			<input type="hidden" name="id_upload" id="id_upload" value="" />
			
			<span id="status_principal"></span>

			<div id="importacao_info_retorno">
			</div>
			<div id="importacao_retorno">
			</div>
			<div id="importacao_retorno_opcoes" style="display: none;">
				<span>O que deseja fazer com essas notas?</span>
				<input id="bt_substituir" onclick="javascript: DesabilitaBotoes(); SalvarNotas(true);" type="button" value="Substituir as existentes" />
				<input id="bt_ignorar" onclick="javascript: DesabilitaBotoes(); SalvarNotas(false);" type="button" value="Ignorar as duplicidades, mantendo as NFs já existentes" />
				<input id="bt_cancelar" onclick="javascript: DesabilitaBotoes(); window.location='<?php echo(Enderecos::CompletoAmp()); ?>';" type="button" value="Cancelar a importação" />
			</div>
			
			<script type="text/javascript">
			<!--
				var arquivos = [
<?php
		$i = 0;
		foreach ($destinos as $destino) { 
			echo("\t\t\t\t\t\"" . $destino . "\"\n");
			$i++;
		}
?>
				];
				
				var qtde_arquivos = <?php echo($i); ?>;
				var arquivo_atual = 0;
				
				var verifica_status;
								
				var xxx = 0;

				function ImportaDados() {
					var funcao = function(texto) {
						//if (texto == "OK") {
						if (texto.substr(0, 11) == "[ID_UPLOAD]") {
							document.getElementById("a_status_" + arquivo_atual).innerHTML = "Concluído.";
							clearTimeout(verifica_status);
						}
						else {
							document.getElementById("a_status_" + arquivo_atual).innerHTML = "Concluído com erro.";
							document.getElementById("a_erro_" + arquivo_atual).innerHTML = texto;
						}
						
						var local_id = texto.indexOf("[ID_UPLOAD]");
						var qual_id = "NULL";
						
						if (local_id != -1) {
							qual_id = texto.substr(local_id + 11);
							document.getElementById("id_upload").value += qual_id + ",";
						}
						
						document.getElementById("f_status").value = "OK";
						ImportaProximo();
					}
					
					var funcao_status = function(texto) {
						//document.getElementById("a_erro_" + arquivo_atual).innerHTML = xxx;
						if (texto != "FIM") {
							document.getElementById("a_status_" + arquivo_atual).innerHTML = texto;
						}
					}
					
					verifica_status = setInterval(
						function() {
							xxx++;
							
							var enderecox = caminho_funcoes + "upload.php?funcao=status&arquivo=" + arquivos[arquivo_atual - 1];					
							ExecutaXMLHTTP("GET", enderecox, true, funcao_status, "xxx" + xxx);							
						}, 2000
					);
					
					document.getElementById("f_status").value = "Processando";
					
					var endereco = caminho_funcoes + "upload.php?funcao=arquivos_efd&arquivo=" + arquivos[arquivo_atual - 1];					
					ExecutaXMLHTTP("GET", endereco, true, funcao, "yyy");
				}
				
				function ImportaProximo() {
					arquivo_atual++;
					
					if (arquivo_atual <= qtde_arquivos) {
						ImportaDados();
					}
					else {
						document.getElementById("f_status").value = "Fim";
						VerificaDuplicidade();
					}
				}
				
				function PreparaImportacao() {
					document.getElementById("status_principal").innerHTML = "Preparando importação...";
					
					var funcao = function(texto) {
						if (texto == "OK") {
							document.getElementById("f_status").value = "OK";
							document.getElementById("status_principal").innerHTML = "Importando arquivos...";
							ImportaProximo();
						}
						else {
							document.getElementById("status_principal").innerHTML = texto;
						}
					}
					
					document.getElementById("f_status").value = "Iniciando";

					var endereco = caminho_funcoes + "upload.php?funcao=prepara";					
					ExecutaXMLHTTP("GET", endereco, true, funcao, "zzz");
				}
				
				function VerificaDuplicidade() {
					document.getElementById("status_principal").innerHTML = "Verificando Notas...";
					
					var funcao = function(texto) {
						if (texto == "OK") {
							SalvarNotas(false);
						}
						else {
							var retorno = texto.split("|");
							
							if (retorno[0] == retorno[1]) {
								document.getElementById("importacao_info_retorno").innerHTML = "As seguintes " + retorno[0] + " NFs já foram incluídas anteriormente.";
							}
							else {
								document.getElementById("importacao_info_retorno").innerHTML = retorno[0] + " NFs já foram incluídas anteriormente. Segue uma amostra de " + retorno[1] + "delas";
							}
							
							document.getElementById("importacao_retorno").innerHTML = retorno[2];
							document.getElementById("importacao_retorno_opcoes").style.display = "block";
						}
					}
					
					var endereco = caminho_funcoes + "upload.php?funcao=dupl";
					ExecutaXMLHTTP("GET", endereco, true, funcao);
				}
				
				function DesabilitaBotoes() {
					document.getElementById("bt_substituir").disabled = true;
					document.getElementById("bt_ignorar").disabled = true;
					document.getElementById("bt_cancelar").disabled = true;
				}
				
				function SalvarNotas(substituir) {
					document.getElementById("status_principal").innerHTML = "Salvando Notas...";
					
					var funcao = function(texto) {
						if (texto.IsNumeric()) {
							document.getElementById("status_principal").innerHTML = texto + " notas incluídas com sucesso.";
						}
						else {
							document.getElementById("status_principal").innerHTML = texto;
						}
					}
					
					var endereco = caminho_funcoes + "upload.php?funcao=salvar&substituir=" + (substituir ? "true" : "false") + "&id_upload=" + document.getElementById("id_upload").value;
					ExecutaXMLHTTP("GET", endereco, true, funcao);
				}
			//-->
			</script>
<?php
	}
 ?>
 		</main>
<?php 
	$h->Rodape();
	
	ob_end_flush();
?>