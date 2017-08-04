<?php	
	ob_start();
	
	session_name('sessao');
	session_start();
	
	ini_set('display_errors', 1); 
	error_reporting(E_ALL); 
	
	require("includes/include.php");
	
	$h = new Site();
	
	/* if ($h->u->Permissoes() != 1) {
		$h->AtualizaLogin();
		header("Location: " . Enderecos::Login());
	} */
	
	$file = Enderecos::ExportacaoFisico() . $_GET["nome"];
	
	if ($h->u->Permissoes() == 1) {	
		if (file_exists($file)) {
			header("Content-Description: File Transfer");
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=" . basename($file));
			header("Content-Transfer-Encoding: binary");
			header("Expires: 0");
			header("Cache-Control: must-revalidate");
			header("Pragma: public");
			header("Content-Length: " . filesize($file));
			
			ob_clean();
			flush();
			
			readfile($file);
			
			exit;
		}
	}
	else {
		echo("Acesso negado.");
	}
?>
