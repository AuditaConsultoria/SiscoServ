<?php
	//ob_start();

	session_name('sessao');
	session_start();
	
	ini_set('display_errors', 1); 
	error_reporting(E_ALL); 
	
	require_once("../includes/include.php");
	require_once("../includes/funcoes.php");
	require_once("../includes/busca.php");

	$h = new Site();
	
	if ($h->u->Permissoes() != 1)
	{
		die();
	}	

	
	if (isset($_GET["tipo"])) {
		if ($_GET["tipo"] == "pais") {
			$ttt = new TabelaBuscaPaises($_GET["num_random"], $_GET["resultado"], $_GET["descricao"]);
			$ttt->EscreveTabela();
		}
		else if ($_GET["tipo"] == "moeda") {
			$ttt = new TabelaBuscaMoeda($_GET["num_random"], $_GET["resultado"], $_GET["descricao"]);
			$ttt->EscreveTabela();
		} 
		else if ($_GET["tipo"] == "nbs") {
			$ttt = new TabelaBuscaNBS($_GET["num_random"], $_GET["resultado"], $_GET["descricao"]);
			$ttt->EscreveTabela();
		}
		else if ($_GET["tipo"] == "enq") {
			$ttt = new TabelaBuscaEnquadramento($_GET["num_random"], $_GET["resultado"], $_GET["descricao"], $_GET["tipo_va"]);
			$ttt->EscreveTabela();
		} 
		else if ($_GET["tipo"] == "part_int") {
			$ttt = new TabelaBuscaParticipantesInterna($_GET["num_random"], $_GET["resultado"], $_GET["nome"], $_GET["endereco"], $_GET["cod_pais"], $_GET["desc_pais"], $_GET["nif"], $_GET["motivo_nif"], $_GET["nif_em_branco"]);
			$ttt->EscreveTabela();
		}
		else if ($_GET["tipo"] == "rvs" || $_GET["tipo"] == "ras") {
			$tipo = Tipo_VA::NENHUM();
			
			if ($_GET["tipo"] == "rvs") {
				$tipo = Tipo_VA::RVS();
			}
			else if ($_GET["tipo"] == "ras") {
				$tipo = Tipo_VA::RAS();
			}
			
			$ttt = new TabelaBuscaVA($tipo, $_GET["num_random"], "", "");
		
			$ttt->classe = "t_consulta";
			$ttt->botao_fechar = false;
			$ttt->acrescenta_js = true;
			
			$ttt->checkbox = true;
			$ttt->botao_editar = false;
			$ttt->botao_excluir = false;
		
			if (is_numeric($_GET["ano"])) {
				$ttt->ano = $_GET["ano"];
			}
			$ttt->EscreveTabela();
		}
	}
	
	//ob_end_flush();
?>