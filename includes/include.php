<?php
	header("cache-control: no-cache, must-revalidate");
	require("info.php"); 
	
	/* Aqui guardo os dados da session atual. 
	 O objeto desta classe deverá ser inicializado somente uma vez por session. Depois, disso, gravaremos o objeto
	 serializado dentro da session com as informações gravadas para acessos posteriores. */
	class Sessao {
		/* ID da session. Será o próximo ID gerado na tabela 'xm_acesso_visitas'. */
		private $id;
		
		/* Data e hora do início da session. */
		private $dt_inicio;
		/* Data e hora do último movimento do usuário. Será atualizado a cada movimentação. */
		private $dt_ultimo_acesso;
		
		/* O constructor será chamado uma única vez por session, assim que o primeiro acesso for feito. 
		 Depois, disso, gravaremos o objeto desta classe serializado dentro da session com as informações gravadas. */
		public function __construct()
		{
			/* Gravo a data de início da sessão. */
			$this->dt_inicio = time();
			/* Gravo a data de início da sessão. */
			$this->dt_ultimo_acesso = $this->dt_inicio;
			
			/* Coleto as informações de acesso do usuário, passando o User Agent do navegador seu IP para o construtor da classe UserAgent. */
			//$this->user = new UserAgent($_SERVER["HTTP_USER_AGENT"], $_SERVER["REMOTE_ADDR"]);

			/* Formato a hora de início da sessão para gravação no banco de dados. */
			$agora = date("Y-m-d : H:i:s", $this->dt_inicio);
			/* Página de entrada do usuário. */
			$pagina = Enderecos::Completo();
		}
		
		public function Movimento()
		{
			/* Hora da movimentação. */
			$this->ultimo_acesso = time();
		}

		/* Retorna o ID da sessão. */
		public function ID()
		{
			return $this->id;
		}

		/* Retorna a data e a hora em que a sessão se iniciou. */
		public function Inicio()
		{
			return $this->inicio;
		}
		/* Retorna a data e a hora do último movimento do usuário na sessão. */
		public function UltimoAcesso()
		{
			return $this->ultimo_acesso;
		}
	}
	
	/* Classe que faz conexão com o banco de dados */
	class Conexao {
		/* Este será o objeto conexão. */
		public static $cn;

		/* Abro a conexão no construtor. */
		public function __construct() {
			if ( !defined( 'E_DEPRECATED' ) )		define( 'E_DEPRECATED', 8192 );
			
			error_reporting (E_ALL & ~ E_NOTICE & ~ E_DEPRECATED);
			
			/* Conecta com o banco de dados. */
			if (Enderecos::LocalHost()) {
				if (Enderecos::ServerLocal() == "81") {
					$_SESSION["banco"] = "siscoserv_ng";
					self::$cn = new mysqli("localhost", "root", "", "siscoserv_ng");
				}
				else {
					$_SESSION["banco"] = "siscoserv_ng";
					self::$cn = new mysqli("localhost", "root", "", "siscoserv");
				}
			}
			else {
				$_SESSION["banco"] = "siscoserv_ng";
				self::$cn = new mysqli("siscoserv_ng.mysql.dbaas.com.br", "siscoserv_ng", "audita14", $_SESSION["banco"]);
				
			}
						
			/* Testa se a conexão deu certo. */
			if (self::$cn->connect_errno) {
				/* Não deu certo, mostro a mensagem de erro. */
				echo("Falha na conexão: (" . self::$cn->connect_errno . ") " . self::$cn->connect_error);
			}
			else {
				//mysqli_set_charset ("utf8", self::$cn); 
				self::$cn->set_charset("utf8");
			}
		}
		function __destruct() {
			/* Se estiver conectado... */
			if (self::$cn) {
				/* Quando o objeto se destruir, fecho a conexçao. */
				//mysqli_close(self::$cn);
				self::$cn->close();
			}
		}

		/* Executo uma query passada por parâmetro. */
		public static function Executa($sql) {
			/* Executo a query. */
			$r = self::$cn->query($sql);

			/* Se não conseguiu executar... */
			if (!$r) {
				/* Exibo a mensagem de erro. */
				echo("Erro na execução da query: (" . self::$cn->errno . ") " . self::$cn->error . "<br />" . $sql);
			} 

			/* Retorno o resultado da query. */
			return $r;
		}
	}
	
	class Login {		
		/* Flag que indica se algum usuário está logado ou não neste momento. */
		private $logado;
		
		/* ID do usuário logado. */
		private $id;

		/* ID do registro de login na tabela 'xm_acesso_logins'. */
		private $id_login;

		/* Se o usuário logou selecionando a opção 'Lembrar senha'. */
		private $lembrar;

		/* Para o usuário será redirecionado depois de logado. */
		public $redirect;

		/* Aqui serão gravadas as mensagens de erro, caso ocorram problemas em alguma tentativa de login. */
		private $erro;
		
		/* Nome do Banco de dados utilizado por este login. */
		private $nome_banco;
		
		/* Nome do usuário. */
		private $nome;
		
		/* Permissões do Usuários. */
		private $permissoes;

		public function __construct()
		{
			/* Inicializo os valores em branco. */ {
				$this->logado = 0;
				
				$this->permissoes = 0;

				$this->id = 0;

				$this->id_login = 0;

				$this->redirect = "";
			}
			
			/* Verifico o cookie de login. Caso ele tenha selecionado a opção
			 'Lembrar senha' da última vez, então, logo automaticamente. */
			if (isset($_COOKIE["login"]))
			{
				/* Flag que que irá me indicar se o login foi feito com sucesso. */
				$r = false;
				/* Desserializo o valor gravado no cookie. */
				$a = unserialize(str_replace("\\\"", "\"", $_COOKIE["login"]));

				/* Procuro no banco de dados o usuário com o ID informado no cookie. */
				$sql = "SELECT id, usuario, senha, permissoes, nome, nome_banco, ativo FROM zx_login WHERE id = '" . $a[0] . "' And excluido = 0";
				$rs = Conexao::Executa($sql);

				/* Se encontrou o usuário. */
				if ($row = $rs->fetch_row()) {
					/* Verifico se o nome e a senha salvos no cookie são consistentes. */
					if (md5($row["usuario"] == $a[1] && $row["senha"]) == $a[2])
					{
						/* Sim, então chamo o método de login, informando também o resultado
						da busca no MySql, para que ela não precise ser feita novamente. */
						$r = $this->Login(@mysql_result($rs, 0, "usuario"), @mysql_result($rs, 0, "senha"), true, $rs);
					}
				}
				
				$rs->close();

				/* Caso, tenha logado com sucesso, atualizo o cookie para a próxima vez. */
				if (!$r) {
					setcookie("login", $this->id, time() - 3600);
				}
			}
		}

		/* Método que faz o login do usuário, recebendo nome e senha por parâmetro. */
		public function Login($sid, $login, $senha, $lembrar, $rs = NULL)
		{		
			/* Se houve algum erro anterior, limpo a variável, pois será feita uma nova tentativa. */
			$this->erro = "";

			/* Caso o usuário já esteja logado... */
			if ($this->logado == 1) {
				/* Aviso que já está logado. */
				$this->erro = "Usuário já logado.";
				/* Vou embora, avisando que não houve sucesso no login. */
				return false;
			}
			
			$row = false;
			
			/* Caso o usuário esteja logando por cookie, já recebi o RecordSet com a senha já checada. Senão... */
			if ($rs == NULL) {
				/* Procuro o banco de dados algum usuário com o login informado. */
				$sql = "SELECT id, usuario, senha, nome, permissoes, nome_banco, ativo FROM zx_login WHERE usuario = '" . addslashes($login) . "' AND excluido = 0";
				$rs = Conexao::Executa($sql);

				/* Se não houver dados... */
				if ($rs->num_rows == 0) {
					/* Não encontrei o login informado. */
					$this->erro = "Usuário ou senha inválidos.";
					/* Vou embora, avisando que não houve sucesso no login. */
					
					return false;
				}
				
				/* Encontro o MD5 da senha informada. */
				$senha = md5($senha);
				
				$row = $rs->fetch_assoc();
							
				/* Verifico se o MD5 da senha informada é igual ao MD5 da senha gravada no banco de dados. */
				if ($row["senha"] != $senha) {
					/* Senha inválida. */
					$this->erro = "Usuário ou senha inválidos.";
					/* Vou embora, avisando que não houve sucesso no login. */
										
					return false;
				}
				
				$rs->close();
			}
						
			if (!$row) {
				$row = $rs->fetch_assoc();
			}
			
			/* Verifico se o usuário está ativo. */
			if ($row["ativo"] != "1")
			{
				/* echo("id: " . @mysql_result($rs, 0, "id") . "<br />" );
				echo("usuario: " . @mysql_result($rs, 0, "usuario") . "<br />" );
				echo("senha: " . @mysql_result($rs, 0, "senha") . "<br />" );
				echo("nome: " . @mysql_result($rs, 0, "nome") . "<br />" );
				echo("permissoes: " . @mysql_result($rs, 0, "permissoes") . "<br />" );
				echo("nome_banco: " . @mysql_result($rs, 0, "nome_banco") . "<br />" );
				echo("ativo: " . @mysql_result($rs, 0, "ativo") . "<br />" );
				echo("excluido: " . @mysql_result($rs, 0, "excluido") . "<br />" ); */
				
				
				/* Usuário desativado. */
				$this->erro = "Usuário desativado.";
				/* Vou embora, avisando que não houve sucesso no login. */
				return false;
			}
			
			/* Login efetuado com sucesso. Atualizo as informações 
			 do objeto com os dados do usuário logado. */
			$this->id = $row["id"];
			
			$this->permissoes = $row["permissoes"];
			$this->nome_banco = $row["nome_banco"];
			$this->nome = $row["nome"];


			/* Se selecionou a opção 'Lembrar senha'. */
			$this->lembrar = $lembrar;
			
			/* Hora atual. */
			$agora = date("Y-m-d : H:i:s", time());

			/* Insiro no banco de dados o registro deste login. */
			/* $sql = "Insert Into acesso_logins(id_session, id_usuario, ip, login, lembrar, cookie, data_login, data_logoff) Values(" .
				$sid . ", " . $this->id . ", '" . $_SERVER["REMOTE_ADDR"] . "', '" . q($login) . "', " .
				$lembrar . ", '', '" . $agora . "', NULL)";
			Conexao::Executa($sql); */
			
			/* Informo o ID do registro no banco de dados. */
			//$this->id_login = mysql_insert_id();

			/* Atualizo os dados do usuário informado a data do último login. */
			/* $sql = "Update site_usuarios Set ultimo_login = '" . $agora . "', lembrar = " . $lembrar . 
				" Where id = " . $this->id;
			Conexao::Executa($sql); */

			/* Informo que o login teve sucesso. */
			$this->logado = 1;

			/* Se selecionou a opção 'Lembrar Senha', gravo o cookie de login. */
			if ($this->lembrar) {
				/* Informações para checagem de consistência do cookie. */
				$a[0] = $this->id; $a[1] = md5($login); $a[2] = $senha;
				$cookie = serialize($a);

				/* Gravo o cookie. */
				setcookie("login", $cookie, time() + $c->validade_cookie);
			}

			//echo($this->Permissoes());
			
			/* Informo o sucesso do login como retorno do método. */
			return $this->logado;
		}

		/* Método que faz logoff do usuário logado no momento. */
		public function Logoff() {
			/* Atualizo o registro deste login informando em que hora ele terminou. */
			/* $sql = "Update acesso_logins Set = data_logoff = Now() Where id = " . $this->id_login;
			Conexao::Executa($sql); */

			/* Limpo os dados do objeto, apontando que não há mais um usuário logado. */
			$this->id = 0;

			$this->id_login = 0;

			$this->validando = false;
			$this->logado = 0;
			
			$this->nome = "";
			$this->nome_banco = "";
			
			$this->permissoes = 0;
			
			unset($_SESSION["banco"]);

			/* Limpo o cookie para que não ocorra login automatico da próxima vez. */
			setcookie("login", $this->id, time() - 3600);
		}
		
		/* Retorna string de logoff a ser adicionada ao final do link usado para logoff. */
		public function QueryStringLogoff() {
			return "action=logoff";
		}
		/* Método que verifica se o o logoff foi solicitado por querystring e, caso sim, efetua o logoff. */
		public function VerificaQueryStringLogoff() {
			if (isset($_GET["action"])) {
				if ($_GET["action"] == "logoff") {
					if ($this->Logado()) {
						$this->Logoff();
					}
					
					return true;
				}
			}
		}

		/* Método para o registro de novos usuários. */
		public function Registro() {
		}

		/* Informa se há usuário logado no momento. */
		public function Logado() {
			return $this->logado;
		}
		/* Informa se o usuário logado no momento está validando o cadastro. */
		public function Validando() {
			return $this->validando;
		}

		/* Informa o ID do usuário logado no momento. */
		public function IDUser() {
			return $this->id;
		}

		/* Informa o ID do registro deste login. */
		public function IDLogin() {
			return $this->id_login;
		}

		/* Informa se o usuário logou neste momento selecionando a opção 'Lembrar senha'. */
		public function Lembrar() {
			return $this->lembrar;
		}

		/* Retorna a mensagem do erro que ocorreu durante a tentativa de login. */
		public function Erro() {
			return $this->erro;
		}
		
		public function Permissoes() {
			return $this->permissoes;
		}
		
		public function NomeBanco() {
			return $this->nome_banco;
		}
		
		public function Nome() {
			return $this->nome;
		}
	}
	
	/* Classe principal que faz as funções básicas e escreve o código HTML de estrutura da página.  */
	class Site {
		/* Objeto da classe Conexao. */
		public $cn;

		/* Objeto da classe Sessao. */
		public $s;
		
		/* Objeto da classe Enderecos. */
		public $e;
		
		/* Objeto da classe Login. */
		public $u; //usuario logado
		
		/* Código adicional a ser colocado no head. */
		public $header_adicional;
		
		public $onload;

		public function __construct() {
			date_default_timezone_set("America/Sao_Paulo");
		
			/* Inicializo as constantes de enderços. */
			$this->e = new Enderecos();
			/* Abro a conexão com o banco de dados. */
			$this->cn = new Conexao();
			
			$this->onload = "";
			
			$_SESSION["vjs"] = "1";

			/* Procuro o objeto $s na SESSION. */
			if (!isset($_SESSION["s"])) {
				/* Se não encontrar, é porque a SESSION começou agora.
				 Então, instancio o objeto. */
				$this->s = new Sessao();
				/* E gravo na SESSION. */
				$this->AtualizaSession();
			}
			else {
				/* Encontrei o objeto na SESSION, 
				então, o recupero para dentro de $s. */
				$this->RecuperaSession();
				$this->s->Movimento();
			}
			
			
			/* Procuro o objeto $u na SESSION. */
			if (!isset($_SESSION["u"])) {
				/* Se não encontrar, é porque a SESSION começou agora.
				 Então, instancio o objeto. */
				$this->u = new Login();
				/* E gravo na SESSION. */
				$this->AtualizaLogin();		
			}
			else {
				/* Encontrei o objeto na SESSION, 
				então, o recupero para dentro de $u. */
				$this->RecuperaLogin();
				
	/* echo($_SESSION["u"]);
	echo("LOGADO: " . $this->u->Logado() . "<br />");
	echo("IDUser: " . $this->u->IDUser() . "<br />");
	echo("IDLogin: " . $this->u->IDLogin() . "<br />");
	echo("NomeBanco: " . $this->u->NomeBanco() . "<br />");
	echo("Nome: " . $this->u->Nome() . "<br />"); */
			}
			
			/* if ($this->u->NomeBanco() != "") {
				$_SESSION["banco"] = $this->u->NomeBanco();
				
				mysql_select_db ($this->u->NomeBanco(), Conexao::$a);
				mysql_set_charset ("utf8", Conexao::$a); 
			}
			else
			{
				//unset($_SESSION["banco"]);
			} */
			
			/* Não há necessidade de fazer o mesmo de cima com o objeto $e, 
			pois na classe Enderecos não há nada para ser modificado.
			Também não é necessário fazer o mesmo com $cn, pois a conexão
			com o banco de dados não fica gravada na SESSION. 
			É necessário reabri-la a cada página. */
			
			/* Desabilita Magic Quotes. */
			if (get_magic_quotes_gpc()) {
				$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
				
				while (list($key, $val) = each($process)) {
					foreach ($val as $k => $v) {
						unset($process[$key][$k]);
						if (is_array($v)) {
							$process[$key][stripslashes($k)] = $v;
							$process[] = &$process[$key][stripslashes($k)];
						} else {
							$process[$key][stripslashes($k)] = stripslashes($v);
						}
					}
				}
				unset($process);
			}
			
		}

		/* Gravo o objeto $s na SESSION. */
		public function AtualizaSession() {
			$_SESSION["s"] = serialize($this->s);
		}
		/* Recupero o objeto $s da SESSION. */
		private function RecuperaSession() {
			$this->s = unserialize($_SESSION["s"]);
		}

		/* Gravo o objeto $u na SESSION. */
		public function AtualizaLogin() {
				//echo(serialize($this->u)); //die();
			$_SESSION["u"] = serialize($this->u);
		}
		/* Recupero o objeto $u da SESSION. */
		private function RecuperaLogin() {
			$this->u = unserialize($_SESSION["u"]);
		}

		/* Cabeçalho da página. */
		public function Cabecalho($title = "") {
			if ($title == "")
			{
				$title = "Siscoserv";
			}

?>
<!doctype html>
<html>
	<head>
		<title><?php echo($title); ?></title>

		<meta charset="utf-8" />
		
		<meta name="author" content="Rafael" />
		<meta name="language" content="pt-br" />
				
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
		<link rel="shortcut icon" href="<?php echo(Enderecos::Site()); ?>" />
		
		<link href="<?php echo(Enderecos::CSS()); ?>estilos.css?v=<?php echo($_SESSION["vjs"]); ?>" type="text/css" rel="stylesheet" />
		
		<script src="<?php echo(Enderecos::Scripts()); ?>script.js?v=<?php echo($_SESSION["vjs"]); ?>" type="text/JavaScript"></script>
		
		<script src="<?php echo(Enderecos::Scripts()); ?>sorttable.js?v=<?php echo($_SESSION["vjs"]); ?>" type="text/JavaScript"></script>
		
		<?php echo($this->header_adicional); ?>
		
	</head>
	<body<?php if ($this->onload != "") echo (" onload=\"" . $this->onload . "\"");  ?>>
		<header id="cabecalho">
			<h1>Módulo Siscoserv</h1>
		</header>
		
<?php
		}

		/* Rodapé da página. */
		public function Rodape()
		{ 
?>
	</body>
</html>
<?php
		}
		
		public function InfoLogin() {
?>
			<section id="info_login">
				Logado como: <b class="destaque"><?php echo(htmlspecialchars($this->u->Nome(), ENT_COMPAT)); ?></b>. <a href="<?php echo(Enderecos::Site()); ?>index.php?<?php echo($this->u->QueryStringLogoff()); ?>">Fazer logoff</a>.
			</section>
<?php
		}
		
		/* Menu com as opções de cadastro. */
		public function Menu()
		{
			if ($this->u->Permissoes() != 1)
			{
				return;
			}		
?>
		<nav id="menu_nav">
<?php $this->InfoLogin(); ?>
			<section class="menu_ras">
				<h3 class="t_mnu_1">Aquisições - RAS e Pagamentos</h3>
				<a href="<?php echo(Enderecos::Site()); ?>inclusao.php?tipo=ras">Incluir Novo</a>
				<a href="<?php echo(Enderecos::Site()); ?>consulta.php?tipo=ras">Consultar</a>
			</section>
			<section class="menu_rvs">
				<h3 class="t_mnu_1">Vendas - RVS e Faturamentos</h3>
				<a href="<?php echo(Enderecos::Site()); ?>inclusao.php?tipo=rvs">Incluir Novo</a>
				<a href="<?php echo(Enderecos::Site()); ?>consulta.php?tipo=rvs">Consultar</a>
			</section>
			<section class="menu_participantes">
				<h3 class="t_mnu_1">Participantes</h3>
				<a href="<?php echo(Enderecos::Site()); ?>participantes.php">Incluir Novo</a>
				<a href="<?php echo(Enderecos::Site()); ?>consulta_part.php">Consultar</a>
			</section>
			<section class="menu_exportacao">
				<h3 class="t_mnu_1">Exportação</h3>
				<a href="<?php echo(Enderecos::Site()); ?>exportacao.php">Exportar XMLs</a>
				<a href="<?php echo(Enderecos::Site()); ?>retorno.php">Retorno de Exportação</a>
			</section>
			<section class="menu_efd">
				<h3 class="t_mnu_1">Importar dados do EFD das Contribuições</h3>
				<a href="<?php echo(Enderecos::Site()); ?>importa_efd.php">Importar</a>
			</section>
		</nav>
		
<?php
		}
	}
?>