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
	
	function UploadArquivos() {
		/*foreach($_FILES as $name => $file) {
			foreach($file as $property => $keys) {
				foreach($keys as $key => $value) {
					echo($value) . "<br />";
				}
			}
		}
		return; */
		
		
		if (isset($_FILES["f_txts"]))
		{
			date_default_timezone_set("Brazil/East"); //Definindo timezone padrão
			
			$name = $_FILES["f_txts"]["name"];
			$tmp_name = $_FILES["f_txts"]["tmp_name"];
			
			$qtde = count($tmp_name);
		  
			for ($i = 0; $i < $qtde; $i++) {
				echo ("Movendo arquivo " . ($i + 1) . " de " . $qtde . "<br / >");			  
	 
				$new_name = date("Y.m.d-H.i.s") . "__" . $_FILES["f_txts"]["name"][$i];
				$dir = Enderecos::ImportacaoFisico(); //Diretório para uploads
				
				echo ($_FILES["f_txts"]["tmp_name"][$i] . " --> " . $dir . $new_name . "<br />");
		 
				move_uploaded_file($_FILES["f_txts"]["tmp_name"][$i], $dir . $new_name); //Fazer upload do arquivo
			}
		}
	   
		echo ("OK");
	}
	
	
	function ImportaEFD($arquivo) {
		set_time_limit(0);
		
		$sql = "INSERT INTO " . $_SESSION["banco"] . ".up_status VALUES (0, '" . addslashes($arquivo) . "', 'Abrindo arquivo...')";
		Conexao::Executa($sql);
		
		$sql = "INSERT INTO " . $_SESSION["banco"] . ".ux_uploads VALUES (0, '" . addslashes($arquivo) . "', 0, Now(), NULL)";
		Conexao::Executa($sql);
		
		$sql = "SELECT @@IDENTITY as id;";
		$rs = Conexao::Executa($sql);
		
		$row = $rs->fetch_assoc();
		
		$id_upload = $row["id"];
		
		$rs->close();
		
		$file = fopen(Enderecos::ImportacaoFisico() . $arquivo, "r");
		
		$sql = "TRUNCATE TABLE " . $_SESSION["banco"] . ".temp_0150";
		Conexao::Executa($sql);
		
		//echo("<b>" . $arquivo . "</b><br /><br />");
		
		$i = 1;
		
		while(!feof($file)) {
			$IND_OPER = 2;
			$COD_SIT = 5;
			$SER = 6;
			$SUB = 7;
			$NUM_DOC = 8;
			$DT_DOC = 10;
			$DT_EXE_SERV = 11;
			$VL_DOC = 12;
			
			$linha = fgets($file);
			
			if (substr($linha, 0, 6) == "|0140|") {
				$campos_filial = explode("|", $linha);
				$filial = $campos_filial[4];
				
				$sql = "UPDATE " . $_SESSION["banco"] . ".up_status SET status = 'Verificando 0150 (filial " . addslashes($filial) . ")...' WHERE nome_arquivo = '" . addslashes($arquivo) . "'";
				Conexao::Executa($sql);
			}
						
			if (substr($linha, 0, 6) == "|0150|") {
				$campos = explode("|", $linha);
				
				$sql = "INSERT INTO " . $_SESSION["banco"] . ".temp_0150 VALUES (0, '" . addslashes($filial) . "', ";
				
				for ($c = 1; $c <= 13; $c++) {
					$sql .= "'" . addslashes($campos[$c]) . "', ";
				}
				
				$sql .= $i . ")";
				
				Conexao::Executa($sql);
		
				/* if (!$rs)
				{
					$r = "Erro no banco de dados: " . mysql_error() . "<br />" . $sql . "<br /><br />" .
						"Favor, entrar em contato com os administradores do sistema.";
					echo($r);
				}  */
			} 
			
			
			if (substr($linha, 0, 6) == "|A010|") {
				$campos_filial = explode("|", $linha);
				$filial = $campos_filial[2];
				
				$sql = "UPDATE " . $_SESSION["banco"] . ".up_status SET status = 'Verificando A100 (filial " . addslashes($filial) . ")...' WHERE nome_arquivo = '" . addslashes($arquivo) . "'";
				Conexao::Executa($sql);
			}
			
			if (substr($linha, 0, 6) == "|A100|") {
				$campos = explode("|", $linha);
				
				//IND_OPER = "1", NF de emissão própria, serviço prestado;
				//COD_SIT = "00", documento não cancelado.
				if ($campos[$IND_OPER] == "1" && $campos[$COD_SIT] == "00") { 				
					//$sql = "SELECT * FROM " . $_SESSION["banco"] . ".temp_0150 WHERE FILIAL = '" . addslashes($filial) . "' AND COD_PART = '" . addslashes($campos[4]) . "'";
					$sql = "SELECT * FROM " . $_SESSION["banco"] . ".temp_0150 WHERE COD_PART = '" . addslashes($campos[4]) . "'";
					$rs = Conexao::Executa($sql);
					
					$xxx = $sql;
					
					if ($rs->num_rows > 0) {
						$row = $rs->fetch_assoc();
						
						$cod_pais = $row["COD_PAIS"];
					
						$sql = "SELECT * FROM tx_pais WHERE cod_longo = '" . addslashes($cod_pais) . "'";
						$rsx = Conexao::Executa($sql);
						
						if ($rsx->num_rows > 0) {
							$rowx = $rsx->fetch_assoc();
							$cod_pais = $rowx["codigo"];
						}
						
						$rsx->close();
					
						if ($cod_pais != "" && $cod_pais != "01058" && $cod_pais != "1058" && $cod_pais != "105") {
							/* echo("LINHA: " . $i . "<br />COD_PART: " . $campos[4] . "<br />");
							echo("COD_PAIS: " . $cod_pais . "<br />");
							echo("SQL: " . $xxx . "<br />");
							echo("num_rows: " . $rs->num_rows . "<br />"); */
							
							//$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_participantes WHERE FILIAL = '" . addslashes($filial) . "' AND COD_0150 = '" . addslashes($campos[4]) . "'";
							$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_participantes WHERE COD_0150 = '" . addslashes($campos[4]) . "'";
							$rsx = Conexao::Executa($sql);
							
							if ($rsx->num_rows > 0) {
								$rowx = $rsx->fetch_assoc();
								$id_part = $rowx["ID"];
								//echo("1<br />" . $sql . "<br />");
							}
							else
							{
								$rsx->close();
								
								$sql = "INSERT INTO " . $_SESSION["banco"] . ".zx_participantes VALUES (0, '" . 
									addslashes($row["NOME"]) . "', '" .
									addslashes($row["END"] . " " . $row["NUM"] . " " . $row["COMPL"] . " " . $row["BAIRRO"]) . "', '" .
									$cod_pais . "', '', '', '" .
									addslashes($row["COD_PART"]) . "', '" . addslashes($filial) . "', " . $id_upload . ");";
									
								Conexao::Executa($sql);
									
								$sql = "SELECT @@IDENTITY as id;";
								$rsx = Conexao::Executa($sql);
								
								$rowx = $rsx->fetch_assoc();
								
								$id_part = $rowx["id"];
								//echo("2<br />");
							}
							
							$rsx->close();
													
							$cod_moeda = "";
							
							/* $sql = "SELECT * FROM " . $_SESSION["banco"] . ".tx_moeda WHERE cod_pais = '" . $cod_pais . "'";
							$rsx = Conexao::Executa($sql);
							
							if ($rsx->num_rows > 0) {
								$rowx = $rsx->fetch_assoc();
								$cod_moeda = $rowx["codigo"];
							}

							$rsx->close();
							*/							
							
							$sql = "INSERT INTO " . $_SESSION["banco"] . ".temp_venda_rvs VALUES (0, '', " . $id_part . ", '";
							$sql .= addslashes($row["NOME"]) . "', '" .
								addslashes($row["END"] . " " . $row["NUM"] . " " . $row["COMPL"] . " " . $row["BAIRRO"]) . "', '" .
								$cod_pais . "', '', '" . 
								"', '" . 
								$cod_moeda . "', '0', ''," . 
								"Now(), Now(), 0, 0, 0, 0, '" .
								addslashes($campos[$SER]) . "', '" .
								addslashes($campos[$SUB]) . "', " .
								addslashes($campos[$NUM_DOC]) . ", '" .
								substr($campos[$DT_DOC], 4, 4) . "-" . substr($campos[$DT_DOC], 2, 2) . "-" . substr($campos[$DT_DOC], 0, 2) . "', '" . 
								substr($campos[$DT_EXE_SERV], 4, 4) . "-" . substr($campos[$DT_EXE_SERV], 2, 2) . "-" . substr($campos[$DT_EXE_SERV], 0, 2) . "', " . 
								str_replace("," , ".", $campos[$VL_DOC]) . ", 0, " . $id_upload . 
								")";
															
							Conexao::Executa($sql);

							
							$sql = "SELECT @@IDENTITY as id;";
							$rsx = Conexao::Executa($sql);
							
							$rowx = $rsx->fetch_assoc();
							$id_rvs = $rowx["id"];
							$rsx->close();
							
							
							$sql = "INSERT INTO " . $_SESSION["banco"] . ".temp_venda_rvs_operacao VALUES (0, " . $id_rvs . ", '', '120015910', '" . $cod_pais . "', '4', '" . 
								substr($campos[$DT_DOC], 4, 4) . "-" . substr($campos[$DT_DOC], 2, 2) . "-" . substr($campos[$DT_DOC], 0, 2) . "', '" . 
								substr($campos[$DT_EXE_SERV], 4, 4) . "-" . substr($campos[$DT_EXE_SERV], 2, 2) . "-" . substr($campos[$DT_EXE_SERV], 0, 2) . "', 0, " . 
								"'', '', Now(), Now(), 0, 0, 0, 0, 0, " . $id_upload . ")";
								//str_replace("," , ".", $campos[$VL_DOC]) . ", " . 
							
							Conexao::Executa($sql);
					
							/* if (!$rs)
							{
								$r = "Erro no banco de dados: " . mysql_error() . "<br />" . $sql . "<br /><br />" .
									"Favor, entrar em contato com os administradores do sistema.";
								echo($r);
							} */
							
							//echo("<br /><br /><br />");
							
						}
					}
					
					if ($rs) {
						$rs->close();
					}
				}
			}
		
			$i++;
		}
		
		$sql = "UPDATE " . $_SESSION["banco"] . ".up_status SET status = 'FIM' WHERE nome_arquivo = '" . addslashes($arquivo) . "'";
		Conexao::Executa($sql);
	
		fclose($file);
		unlink(Enderecos::ImportacaoFisico() . $arquivo);
		
		//echo("OK");
		echo("[ID_UPLOAD]" . $id_upload);
	}
	
	function PreparaImportacao() {
		$sql = "TRUNCATE TABLE " . $_SESSION["banco"] . ".temp_0150";
		Conexao::Executa($sql);
		
		$sql = "TRUNCATE TABLE " . $_SESSION["banco"] . ".up_status";
		Conexao::Executa($sql);

		$sql = "TRUNCATE TABLE " . $_SESSION["banco"] . ".temp_aquis_ras";
		Conexao::Executa($sql);

		$sql = "TRUNCATE TABLE " . $_SESSION["banco"] . ".temp_aquis_ras_operacao";
		Conexao::Executa($sql);
		
		$sql = "TRUNCATE TABLE " . $_SESSION["banco"] . ".temp_venda_rvs";
		Conexao::Executa($sql);
		
		$sql = "TRUNCATE TABLE " . $_SESSION["banco"] . ".temp_venda_rvs_operacao";
		Conexao::Executa($sql);
		
		echo("OK");
	}
	
	function RetornaStatus($arquivo) {
		$sql = "SELECT * FROM " . $_SESSION["banco"] . ".up_status WHERE nome_arquivo = '" . addslashes($arquivo) . "'";
		$rs = Conexao::Executa($sql);
		
		if ($rs->num_rows > 0) {
			$row = $rs->fetch_assoc();
			
			echo($row["status"]);
		}
		else {
			echo("Arquivo não encontrado.");
		}
		
		$rs->close();
	}
	
	
	function VerificaDuplicadas() {
		$sql = "SELECT a.* FROM " . $_SESSION["banco"] . ".temp_venda_rvs AS a INNER JOIN " . $_SESSION["banco"] . ".zx_venda_rvs AS b ON a.id_upload = b.id_upload AND a.SER = b.SER AND a.SUB = b.SUB AND a.NUM_DOC = b.NUM_DOC AND a.DT_DOC = b.DT_DOC";
		$rs = Conexao::Executa($sql);
		
		if ($rs->num_rows == 0) {
			echo("OK");
		}
		else {
			$i = 0;
			$max = 20;
			
			if ($rs->num_rows < $max) $max = $rs->num_rows;
			
			echo($rs->num_rows . "|" . $max);
?>
<table>
	<tr>
		<th>
			SÉRIE
		</th>
		<th>
			NÚMERO
		</th>
		<th>
			DT. DOC.
		</th>
		<th>
			VL. DOC.
		</th>
	</tr>
<?php
			while ($row = $rs->fetch_assoc()) {
				$i++;
				
				if ($i > $max) {
					break;
				}
?>
	<tr>
		<td>
			<?php echo($row["SER"]); ?>
		</td>
		<td>
			<?php echo($row["NUM_DOC"]); ?>
		</td>
		<td>
			<?php echo($row["DT_DOC"]); ?>
		</td>
		<td>
			<?php echo($row["VL_DOC"]); ?>
		</td>
	</tr>
<?php
			}
?>
</table>
<?php
		}
	}

	function SalvarNotas($subst, $id_upload) {
		$qtde_de_notas = 0;	
		
		$id_upload = trim($id_upload);
		if (substr($id_upload, -1) == ",") {
			$id_upload = substr($id_upload, 0 , -1);
		}
	
		if ($subst == "true") {
			$sql = "SELECT COUNT(*) AS QTDE FROM " . $_SESSION["banco"] . ".temp_venda_rvs";
			$rs = Conexao::Executa($sql);
			
			if ($row = $rs->fetch_assoc()) {
				$qtde_de_notas = $row["QTDE"];
			}
			
			$rs->close();
			
			$sql = "UPDATE " . $_SESSION["banco"] . ".temp_venda_rvs AS a INNER JOIN " . $_SESSION["banco"] . ".zx_venda_rvs AS b " . 
			"ON a.id_upload = b.id_upload AND a.SER = b.SER AND a.SUB = b.SUB AND a.NUM_DOC = b.NUM_DOC AND a.DT_DOC = b.DT_DOC " . 
			"SET b.ID_Participante = a.ID_Participante, " .
			"b.NomeAdquirente = a.NomeAdquirente, " .
			"b.EnderecoAdquirente = a.EnderecoAdquirente, " .
			"b.CodigoPaisAdquirente = a.CodigoPaisAdquirente, " .
			"b.Nif = a.Nif, " .
			"b.InfoComplementar = a.InfoComplementar, " .
			"b.CodigoMoeda = a.CodigoMoeda, " .
			"b.data_alteracao = Now(), " .
			"a.NumeroRVSEmpresa = b.ID, " .
			"a.excluir = 1, b.excluir = 1";
			Conexao::Executa($sql);
			
			$sql = "UPDATE " . $_SESSION["banco"] . ".temp_venda_rvs AS a INNER JOIN " . $_SESSION["banco"] . ".temp_venda_rvs_operacao AS b ON a.ID = b.ID_RVS SET b.excluir = 1, b.NumeroServAdqEmpresa = a.NumeroRVSEmpresa WHERE a.excluir  = 1";
			Conexao::Executa($sql);
			
			$sql = "UPDATE " . $_SESSION["banco"] . ".temp_venda_rvs_operacao SET ID_RVS = NumeroServAdqEmpresa WHERE excluir  = 1";
			Conexao::Executa($sql);
			
			$sql = "UPDATE " . $_SESSION["banco"] . ".zx_venda_rvs AS a INNER JOIN " . $_SESSION["banco"] . ".zx_venda_rvs_operacao AS b ON a.ID = b.ID_RVS SET b.excluir = 1 WHERE a.excluir  = 1";
			Conexao::Executa($sql);
			
			$sql = "DELETE FROM " . $_SESSION["banco"] . ".zx_venda_rvs_operacao WHERE excluir = 1";
			Conexao::Executa($sql);
			
			$sql = "INSERT INTO " . $_SESSION["banco"] . ".zx_venda_rvs_operacao SELECT 0 AS ID, ID_RVS, '' AS NumeroServAdqEmpresa, CodigoNbs, CodigoPaisDestino, ModoPrestacao, DataInicio, DataConclusao, Valor, NumeroRE, NumeroDI, Now(), Now(), 0, 0, 0, 0, 0, id_upload  FROM " . $_SESSION["banco"] . ".temp_venda_rvs_operacao WHERE excluir = 1";
			Conexao::Executa($sql);
			
			$sql = "DELETE FROM " . $_SESSION["banco"] . ".temp_venda_rvs WHERE excluir = 1";
			Conexao::Executa($sql);
			
			$sql = "DELETE FROM " . $_SESSION["banco"] . ".temp_venda_rvs_operacao WHERE excluir = 1";
			Conexao::Executa($sql);
		}
		else {
			$sql = "UPDATE " . $_SESSION["banco"] . ".temp_venda_rvs AS a INNER JOIN " . $_SESSION["banco"] . ".zx_venda_rvs AS b ON a.id_upload = b.id_upload AND a.SER = b.SER AND a.SUB = b.SUB AND a.NUM_DOC = b.NUM_DOC AND a.DT_DOC = b.DT_DOC SET a.excluir = 1";
			Conexao::Executa($sql);
			
			$sql = "UPDATE " . $_SESSION["banco"] . ".temp_venda_rvs AS a INNER JOIN " . $_SESSION["banco"] . ".temp_venda_rvs_operacao AS b ON a.ID = b.ID_RVS SET b.excluir = 1 WHERE a.excluir  = 1";
			Conexao::Executa($sql);
			
			$sql = "DELETE FROM " . $_SESSION["banco"] . ".temp_venda_rvs WHERE excluir = 1";
			Conexao::Executa($sql);
			
			$sql = "DELETE FROM " . $_SESSION["banco"] . ".temp_venda_rvs_operacao WHERE excluir = 1";
			Conexao::Executa($sql);
			
			$sql = "SELECT COUNT(*) AS QTDE FROM " . $_SESSION["banco"] . ".temp_venda_rvs";
			$rs = Conexao::Executa($sql);
			
			if ($row = $rs->fetch_assoc()) {
				$qtde_de_notas = $row["QTDE"];
			}
			
			$rs->close();
		}
			
		$sql = "INSERT INTO " . $_SESSION["banco"] . ".zx_venda_rvs SELECT 0 AS ID, NumeroRVSEmpresa, ID_Participante, NomeAdquirente, EnderecoAdquirente, CodigoPaisAdquirente, NIF, InfoComplementar, CodigoMoeda, TipoVinculacao, MotivoNif, Now(), Now(), 0, 0, 0, 0, " .
		"SER, SUB, NUM_DOC, DT_DOC, DT_EXE_SERV, VL_DOC, 0, id_upload FROM " . $_SESSION["banco"] . ".temp_venda_rvs";
		Conexao::Executa($sql);
		
		$sql = "UPDATE " . $_SESSION["banco"] . ".temp_venda_rvs AS a INNER JOIN " . $_SESSION["banco"] . ".zx_venda_rvs AS b " . 
		"ON a.SER = b.SER AND a.SUB = b.SUB AND a.NUM_DOC = b.NUM_DOC AND a.DT_DOC = b.DT_DOC " . 
		"SET a.NumeroRVSEmpresa = b.ID ";
		Conexao::Executa($sql);
		
		$sql = "UPDATE " . $_SESSION["banco"] . ".temp_venda_rvs AS a INNER JOIN " . $_SESSION["banco"] . ".temp_venda_rvs_operacao AS b ON a.ID = b.ID_RVS SET b.NumeroServAdqEmpresa = a.NumeroRVSEmpresa";
		Conexao::Executa($sql);
		
		$sql = "UPDATE " . $_SESSION["banco"] . ".temp_venda_rvs_operacao SET ID_RVS = NumeroServAdqEmpresa";
		Conexao::Executa($sql);
		
		$sql = "INSERT INTO " . $_SESSION["banco"] . ".zx_venda_rvs_operacao SELECT 0 AS ID, ID_RVS, '' AS NumeroServAdqEmpresa, CodigoNbs, CodigoPaisDestino, ModoPrestacao, DataInicio, DataConclusao, Valor, NumeroRE, NumeroDI, Now(), Now(), 0, 0, 0, 0, 0, id_upload  FROM " . $_SESSION["banco"] . ".temp_venda_rvs_operacao";
		Conexao::Executa($sql);
		
		$sql = "INSERT INTO " . $_SESSION["banco"] . ".zx_venda_faturamento SELECT 0 AS ID, A.ID AS ID_RVS, A.NUM_DOC AS NumeroFatura, B.DataInicio AS DataFatura, NULL AS VinculacaoNumRE, NULL AS VinculacaoNumDI, 0 AS Vinculacao, " .
			"NOW() AS data_inclusao, NOW() AS data_alteracao, 0 AS exportado, 0 AS importado, 0 AS modificado_apos_exportacao, 0 AS modificado_apos_importacao, A.id_upload " .
			"FROM " . $_SESSION["banco"] . ".zx_venda_rvs AS A INNER JOIN " . $_SESSION["banco"] . ".zx_venda_rvs_operacao AS B ON A.ID = B.ID_RVS AND A.id_upload = B.id_upload " .
			"WHERE A.id_upload IN (" . $id_upload . ")";
		Conexao::Executa($sql);
		
		$sql = "INSERT INTO " . $_SESSION["banco"] . ".zx_venda_faturamento_item SELECT 0 AS ID, B.ID AS ID_Faturamento, A.ID AS ID_Operacao, Valor AS ValorFaturado, 0 AS ValorMantidoExt, " .
			"NOW() AS data_inclusao, NOW() AS data_alteracao, 0 AS exportado, 0 AS importado, 0 AS modificado_apos_exportacao, 0 AS modificado_apos_importacao " .
			"FROM " . $_SESSION["banco"] . ".zx_venda_rvs_operacao AS A INNER JOIN " . $_SESSION["banco"] . ".zx_venda_faturamento AS B ON A.ID_RVS = B.ID_RVS AND A.id_upload = B.id_upload " .
			"WHERE B.id_upload IN (" . $id_upload . ")";
		Conexao::Executa($sql);
		
		echo($qtde_de_notas . " NFs salvas no banco de dados.");
	}
	
	if (isset($_GET["funcao"])) {

		if ($_GET["funcao"] == "upload_efd") {
			UploadArquivos();
		}
		if ($_GET["funcao"] == "arquivos_efd") {
			ImportaEFD($_GET["arquivo"]);
		}
		if ($_GET["funcao"] == "prepara") {
			PreparaImportacao();
		}
		if ($_GET["funcao"] == "status") {
			RetornaStatus($_GET["arquivo"]);
		}
		if ($_GET["funcao"] == "dupl") {
			VerificaDuplicadas();
		}
		if ($_GET["funcao"] == "salvar") {
			SalvarNotas($_GET["substituir"], $_GET["id_upload"]);
		}
	}
	
	//ob_end_flush();
?>