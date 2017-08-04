<?php
	ob_start();
	
	session_name('sessao');
	session_start();
	
	ini_set('display_errors', 1); 
	error_reporting(E_ALL); 
	
	require("includes/include.php");

	$h = new Site();
	
	if ($h->u->Logado() == 1)
	{
		header("Location: " . Enderecos::Site());
/*		echo ("ENDEREÇOS: " . Enderecos::Site());
	 echo("LOGADO: " . $h->u->Logado() . "<br />");
	echo("IDUser: " . $h->u->IDUser() . "<br />");
	echo("IDLogin: " . $h->u->IDLogin() . "<br />");
	echo("NomeBanco: " . $h->u->NomeBanco() . "<br />");
	echo("Nome: " . $h->u->Nome() . "<br />"); */
	}
	
	$msg = "";
	
	if (isset($_POST["form_login_post"])) {
		if ($_POST["form_login_post"] == "post") {
			$username = $_POST["form_login_user"];
			$senha = $_POST["form_login_senha"];
			
			if ($username == "") {
				$msg .= ($msg != "" ? "<br />" : "") .
				"Digite o nome de usuário.";
			}
			if ($senha == "") {
				$msg .= ($msg != "" ? "<br />" : "") .
				"Digite a senha.";
			}

			if ($msg == "") {
				if ($h->u->Login($h->s->ID(), $username, $senha, false)) {
					$h->AtualizaLogin();
					
					header("Location: " . Enderecos::Site());
				}
				else {
					$msg = $h->u->Erro();
				}
			}
		}
	}
	
	//$h->header_adicional = "<script src=\"" . Enderecos::Scripts() . "cadastros.js?v=" . $_SESSION["vjs"] . "\" type=\"text/JavaScript\"></script>\n";
	
	$h->Cabecalho();

?>
			<main>
				<form id="form_login" name="form_login" method="post" action="<?php echo(Enderecos::CompletoAmp()) ?>">
					<fieldset class="painel_formulario">
						<input type="hidden" id="form_login_post" name="form_login_post" value="post" />
						<label for="form_login_user" class="t_campo_form">Login:
							<input id="form_login_user" name="form_login_user" class="campo_form" type="text" maxlength="20" required="required" value="<?php echo((isset($_POST["form_login_user"]) ? htmlspecialchars($_POST["form_login_user"], ENT_COMPAT) : "")); ?>" />
						</label>
						<label for="form_login_senha" class="t_campo_form">Senha:
							<input id="form_login_senha" name="form_login_senha" class="campo_form" type="password" required="required" maxlength="50"/>
						</label>

						<?php if ($msg != "") { ?>
						<span id="form_login_msg_texto" class="texto_msg_erro"><?php echo($msg); ?></span>
						<?php } ?>
						
						<input id="form_login_salvar" name="form_login_salvar" class="botao_form" onclick="javascript: return ValidaLogin(document.getElementById('form_login'), document.getElementById('form_login_user'), document.getElementById('form_login_senha'));" type="submit" value="Entrar" />
					</fieldset>
				</form>
			</main>

<?php
	
	$h->Rodape();
	
	ob_end_flush();
?>