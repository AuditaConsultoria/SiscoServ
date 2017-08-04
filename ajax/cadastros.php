<?php
	//ob_start();

	session_name('sessao');
	session_start();
	
	ini_set('display_errors', 1); 
	error_reporting(E_ALL); 
	
	require_once("../includes/funcoes.php");
	require_once("../includes/include.php");
	require_once("../includes/formularios.php");

	$h = new Site();
	
	if ($h->u->Permissoes() != 1)
	{
		die();
	}
	
	if (isset($_GET["funcao"])) {
		if ($_GET["funcao"] == "grava_participante") {
			$f = new Formulario_Participante();
			echo($f->Grava());
		}
		else if ($_GET["funcao"] == "grava_ras") {
			$f = new Formulario_Venda_Aquisicao(Tipo_VA::RAS());
			echo($f->Grava());
		}
		else if ($_GET["funcao"] == "grava_rvs") {
			$f = new Formulario_Venda_Aquisicao(Tipo_VA::RVS());
			echo($f->Grava());
		}
		else {
			$tipo_ras_rvs = Tipo_VA::NENHUM();
			if (isset($_GET["tipo"])) {
				if ($_GET["tipo"] == "ras") {
					$tipo_ras_rvs = Tipo_VA::RAS();
				}
				else if ($_GET["tipo"] == "rvs") {
					$tipo_ras_rvs = Tipo_VA::RVS();
				}
			}
			
			$num = -1;
			if (isset($_GET["num"])) {
				if (is_numeric($_GET["num"])) {
					$num = $_GET["num"];
				}
			}
			
			$num_sup = -1;
			if (isset($_GET["num_sup"])) {
				if (is_numeric($_GET["num_sup"])) {
					$num_sup = $_GET["num_sup"];
				}
			}
			
			$id = -1;
			if (isset($_GET["id"])) {
				if (is_numeric($_GET["id"])) {
					$id = $_GET["id"];
				}
			}
			
			$ids = "";
			if (isset($_GET["ids"])) {
				$ids = $_GET["ids"];
			}
			
			$nf = -1;
			if (isset($_GET["nf"])) {
				$nf = htmlspecialchars($_GET["nf"]);
			}
			
			if ($_GET["funcao"] == "painel_operacao") {
				if ($tipo_ras_rvs != Tipo_VA::NENHUM() && $num != -1) {
					$f = new Formulario_Operacoes($num, $tipo_ras_rvs);
					$f->cria_fieldset = false;
					$f->EscreveFormulario();
				}
			}
			else if ($_GET["funcao"] == "painel_recebimento") {
				if ($tipo_ras_rvs != Tipo_VA::NENHUM() && $num != -1 && $num_sup != -1) {
					$f = new Formulario_Recebimentos($num_sup, $num, $tipo_ras_rvs, $nf);
					$f->cria_fieldset = false;
					$f->EscreveFormulario();
				}
			}
			else if ($_GET["funcao"] == "painel_enquadramento") {
				if ($tipo_ras_rvs != Tipo_VA::NENHUM() && $num != -1 && $num_sup != -1) {
					$f = new Formulario_Enquadramentos($num_sup, $num, $tipo_ras_rvs);
					$f->cria_fieldset = false;
					$f->EscreveFormulario();
				}
			}
			else if ($_GET["funcao"] == "painel_re") {
				if ($tipo_ras_rvs != Tipo_VA::NENHUM() && $num != -1 && $num_sup != -1) {
					$f = new Formulario_OP_Vinculacao($num_sup, $num, Tipo_DIRE::RE(), $tipo_ras_rvs);
					$f->cria_fieldset = false;
					$f->EscreveFormulario();
				}
			}
			else if ($_GET["funcao"] == "painel_di") {
				if ($tipo_ras_rvs != Tipo_VA::NENHUM() && $num != -1 && $num_sup != -1) {
					$f = new Formulario_OP_Vinculacao($num_sup, $num, Tipo_DIRE::DI(), $tipo_ras_rvs);
					$f->cria_fieldset = false;
					$f->EscreveFormulario();
				}
			}
			else if ($_GET["funcao"] == "painel_operacao_restaurar") {
				if ($tipo_ras_rvs != Tipo_VA::NENHUM() && $num != -1) {
					$f = new Formulario_Operacoes($num, $tipo_ras_rvs);
					$f->cria_fieldset = false;
					$f->PainelRestaurar();
				}
			}
			else if ($_GET["funcao"] == "painel_recebimento_restaurar") {
				if ($tipo_ras_rvs != Tipo_VA::NENHUM() && $num != -1 && $num_sup != -1) {
					$f = new Formulario_Recebimentos($num_sup, $num, $tipo_ras_rvs);
					$f->cria_fieldset = false;
					$f->PainelRestaurar();
				}
			}
			else if ($_GET["funcao"] == "painel_enquadramento_restaurar") {
				if ($tipo_ras_rvs != Tipo_VA::NENHUM() && $num != -1 && $num_sup != -1) {
					$f = new Formulario_Enquadramentos($num_sup, $num, $tipo_ras_rvs);
					$f->cria_fieldset = false;
					$f->PainelRestaurar();
				}
			}
			else if ($_GET["funcao"] == "painel_re_restaurar") {
				if ($tipo_ras_rvs != Tipo_VA::NENHUM() && $num != -1 && $num_sup != -1) {
					$f = new Formulario_OP_Vinculacao($num_sup, $num, Tipo_DIRE::RE(), $tipo_ras_rvs);
					$f->cria_fieldset = false;
					$f->PainelRestaurar();
				}
			}
			else if ($_GET["funcao"] == "painel_di_restaurar") {
				if ($tipo_ras_rvs != Tipo_VA::NENHUM() && $num != -1 && $num_sup != -1) {
					$f = new Formulario_OP_Vinculacao($num_sup, $num, Tipo_DIRE::DI(), $tipo_ras_rvs);
					$f->cria_fieldset = false;
					$f->PainelRestaurar();
				}
			}
			else if ($_GET["funcao"] == "excluir") {
				if ($tipo_ras_rvs != Tipo_VA::NENHUM() && $id != -1) {
					$f = new Formulario_Venda_Aquisicao($tipo_ras_rvs, $id, false);
					$f->Excluir();
				}
			}
			else if ($_GET["funcao"] == "exportar") {
				if ($tipo_ras_rvs != Tipo_VA::NENHUM()) {
					$f = new Formulario_Venda_Aquisicao($tipo_ras_rvs);
					$f->Exportar($ids);
				}
			}
		}
	}
	
	//ob_end_flush();
?>