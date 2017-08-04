<?php
	ob_start();
	
	session_name('sessao');
	session_start();
	
	ini_set('display_errors', 1); 
	error_reporting(E_ALL); 
	
	require("includes/include.php");
	require("includes/formularios.php");
	require("includes/busca.php");

	$h = new Site();
	
	if ($h->u->Permissoes() != 1) {
		$h->AtualizaLogin();
		header("Location: " . Enderecos::Login());
	}
	
	$h->header_adicional = "\t\t<script src=\"" . Enderecos::Scripts() . "buscas.js?v=" . $_SESSION["vjs"] . "\" language=\"javascript\" type=\"text/JavaScript\"></script>\n";
	$h->header_adicional .= "\t\t<script src=\"" . Enderecos::Scripts() . "cadastros.js?v=" . $_SESSION["vjs"] . "\" language=\"javascript\" type=\"text/JavaScript\"></script>\n";
	
	$h->Cabecalho();
	
	$h->Menu();	

	
	$classe_corpo = "";
	
	$tipo_va = Tipo_VA::NENHUM();
	$texto_va = "";
	
	if (isset($_GET["tipo"])) {
		if ($_GET["tipo"] == "ras") {
			$classe_corpo = "corpo_ras";
			
			$tipo_va = Tipo_VA::RAS();
			$texto_va = "RAS";
		}
		else if ($_GET["tipo"] == "rvs") {
			$classe_corpo = "corpo_rvs";
			
			$tipo_va = Tipo_VA::RVS();
			$texto_va = "RVS";
		}
	}
	
	if ($tipo_va != Tipo_VA::NENHUM()) {	
		$t = new TabelaBuscaVA($tipo_va, "", "", "");
		
		$t->classe = "t_consulta";
		$t->botao_fechar = false;
		$t->acrescenta_js = true;
?>
		<main id="corpo" class="<?php echo($classe_corpo); ?>">
			<h2>Pesquisa de <?php echo($texto_va); ?></h2>
<?php
		$t->EscreveTabela();
?>
		</main>
<?php 
	}
	
	$h->Rodape();
	
	ob_end_flush();
?>