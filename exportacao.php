<?php
	ob_start();
	
	session_name('sessao');
	session_start();
	
	ini_set('display_errors', 1); 
	error_reporting(E_ALL); 
	
	require("includes/include.php");
	require("includes/formularios.php");
	//require("includes/busca.php");

	$h = new Site();
	
	if ($h->u->Permissoes() != 1)
	{
		$h->AtualizaLogin();
		header("Location: " . Enderecos::Login());
	}
	
	$h->header_adicional = "\t\t<script src=\"" . Enderecos::Scripts() . "buscas.js?v=" . $_SESSION["vjs"] . "\" language=\"javascript\" type=\"text/JavaScript\"></script>\n";
	$h->header_adicional .= "\t\t<script src=\"" . Enderecos::Scripts() . "cadastros.js?v=" . $_SESSION["vjs"] . "\" language=\"javascript\" type=\"text/JavaScript\"></script>\n";
	
	$h->Cabecalho();
	
	$h->Menu();	

		
?>
		<main id="corpo">
			<section class="corpo_ras">
			
				<h2>Exportação -- Aquisições</h2>
			
				<section>
					
<?php
	/* $t = new TabelaBuscaVA(Tipo_VA::RAS(), "", "", "");
	
	$t->classe = "t_consulta";
	$t->botao_fechar = false;
	$t->acrescenta_js = true; */
	
	$t = new Formulario_Venda_Aquisicao(Tipo_VA::RAS());

	$t->FormExportacao();
?>
				</section>
				
			</section>
			
			<section class="corpo_rvs">
			
				<h2>Exportação -- Vendas</h2>
			
				<section>
					
<?php
	/* $t = new TabelaBuscaVA(Tipo_VA::RVS(), "", "", "");
	
	$t->classe = "t_consulta";
	$t->botao_fechar = false;
	$t->acrescenta_js = true; */

	$t = new Formulario_Venda_Aquisicao(Tipo_VA::RVS());

	$t->FormExportacao();
?>
				</section>
				
			</section>

		</main>
<?php 
	
	$h->Rodape();
	
	ob_end_flush();
?>