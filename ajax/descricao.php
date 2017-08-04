<?php
	//ob_start();

	session_name('sessao');
	session_start();
	
	ini_set('display_errors', 1); 
	error_reporting(E_ALL); 
	
	require_once("../includes/include.php");

	$h = new Site();
	
	if ($h->u->Permissoes() != 1)
	{
		die();
	}	

	
	if (isset($_GET["tipo"]) && isset($_GET["codigo"])) {
		$r = "";
		
		if ($_GET["tipo"] == "participante") {
			$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_participantes WHERE ID = '" . addslashes($_GET["codigo"]) . "'";
			$rs = Conexao::Executa($sql);
						
			if ($rs->num_rows == 0) {
				$r = "*";
			}
			else {
				$row = $rs->fetch_assoc();
				
				$r = $row["Nome"];
			}
			
			$rs->close();
		}
		else if ($_GET["tipo"] == "part_comp") {
			$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_participantes WHERE ID = '" . addslashes($_GET["codigo"]) . "'";
			$rs = Conexao::Executa($sql);
						
			if ($rs->num_rows == 0) {
				$r = "*";
			}
			else {
				$row = $rs->fetch_assoc();
				
				$r = $row["Nome"] . "|" . $row["Endereco"] . "|" . $row["Pais"] . "|" . $row["NIF"] . "|" . $row["MotivoNif"];
			}
			
			$rs->close();
		}
		else if ($_GET["tipo"] == "pais") {
			$sql = "SELECT * FROM tx_pais WHERE codigo = '" . addslashes($_GET["codigo"]) . "'";
			$rs = Conexao::Executa($sql);
						
			if ($rs->num_rows == 0) {
				$r = "*";
			}
			else {
				$row = $rs->fetch_assoc();
				
				$r = $row["nome"];
			}
			
			$rs->close();
		}
		else if ($_GET["tipo"] == "nbs") {
			$sql = "SELECT * FROM tx_nbs WHERE codigo = '" . addslashes($_GET["codigo"]) . "'";
			$rs = Conexao::Executa($sql);
						
			if ($rs->num_rows == 0) {
				$r = "*";
			}
			else {
				$row = $rs->fetch_assoc();
				
				$r = $row["descricao"];
			}
			
			$rs->close();
		}
		else if ($_GET["tipo"] == "moeda") {
			$sql = "SELECT * FROM tx_moeda WHERE codigo = '" . addslashes($_GET["codigo"]) . "'";
			$rs = Conexao::Executa($sql);
						
			if ($rs->num_rows == 0) {
				$r = "*";
			}
			else {
				$row = $rs->fetch_assoc();
				
				$r = $row["descricao"];
			}
			
			$rs->close();
		}
		else if ($_GET["tipo"] == "enq") {
			$sql = "SELECT * FROM tx_enquadramento WHERE codigo = '" . addslashes($_GET["codigo"]) . "'";
			
			if (isset($_GET["modulo"])) {
				if ($_GET["modulo"] == "A") {
					$sql .= " AND modulo_aquisicao == '1'";
				}
				else if ($_GET["modulo"] == "V") {
					$sql .= " AND modulo_venda == '1'";
				}
			}
			$rs = Conexao::Executa($sql);
						
			if ($rs->num_rows == 0) {
				$r = "*";
			}
			else {
				$row = $rs->fetch_assoc();
				
				$r = $row["descricao"];
			}
			
			$rs->close();
		}
		
		echo($r);
	}
	
	//ob_end_flush();
?>