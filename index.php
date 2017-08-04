<?php
	ob_start();
	
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
	
	$h->Cabecalho();
	
	$h->Menu();
?>
	
<?php 
	$h->Rodape();
	
	ob_end_flush();
?>