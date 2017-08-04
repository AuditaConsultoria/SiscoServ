<?php

	/* Classe que armazenará constantes com endereços de acessos. */
	class Enderecos
	{
		/* Aqui será gravado o endereço do servidor. */
		private static $servername;
		
		/* Aqui será gravado o endereço da página principal. 
		 Geralmente terá o mesmo valor do $servername, exceto
		 quando em servidores locais. */
		private static $site;
		
		/* Aqui será gravado o endereço completo da página atual. */
		private static $paginaatual;
		/* Aqui será gravado o endereço completo da página atual, incluido querystrings. */
		private static $completo;
		/* Aqui será gravado o endereço completo da página atual, incluido querystrings.
		 Mas todos os & serão trocados pelo seu código HTML &amp; para seguir
		 o padrão da W3C. Usar este para escrever no HTML. */
		private static $completo_amp;
		
		private static $server_local;

		/* Diretório onde estarão os arquivos de estilos CSS. */
		private static $css;
		/* Diretório onde estarão os arquivos de funções JavaScript. */
		private static $scripts;
		/* Diretório onde ficarão armazenados os arquivos de imagens. */
		private static $images;
		/* Endereço da página de Login. */
		private static $login;
		/* Endereço da pasta onde ficam os arquivos de exportação. */
		private static $exportacao;
		/* Endereço físico da pasta onde ficam os arquivos de exportação. */
		private static $exportacao_fisico;
		
		/* Endereço da pasta onde ficam os arquivos de importação. */
		private static $importacao;
		/* Endereço físico da pasta onde ficam os arquivos de importacao. */
		private static $importacao_fisico;
		
		/* Objeto da classe ClassesCSS. Armazena nomes de classes CSS. */
		public $classes_css;
		
		private static $localhost;

		public function __construct()
		{
			//$this->server_local = "80";
			self::$server_local = "81";
			
			$ip = "";
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			
			/* Preencho os endereços dependendo do host. */
			if ($_SERVER["SERVER_NAME"] == "localhost") {
				self::$localhost = true;
			
				/* Endereço no localhost. */
				if (self::$server_local == "81"){
					self::$servername = "http://localhost:8081/";
					self::$site = "http://localhost:8081/siscoserv_novo/";
				}
				else {
					self::$servername = "http://localhost:8081/";
					self::$site = "http://localhost:8081/siscoserv_novo/";
				}
			}
			else {
				self::$localhost = false;
				
				//echo($ip);
			
				/* Endereço na web. */
				self::$servername = "http://www.auditaconsultoria.com.br/";
				self::$site = "http://www.auditaconsultoria.com.br/siscoserv/";
			}

			/* Tiro a barra do final do $servername. */
			self::$paginaatual = substr(self::$servername, 0, -1);
			/* $_SERVER["PHP_SELF"] guarda o nome do arquivo .php aberto no momento.
			 Conectando com o $servername, tenho o endereço completo da página,
			 sem as querystrings. */
			self::$paginaatual .= $_SERVER["PHP_SELF"];
			
			/* Gravarei, então, o endereço todo, incluindo a querystring
			 dentro de $completo. */
			self::$completo = self::$paginaatual;
			if ($_SERVER["QUERY_STRING"] != "") /* Se houver querystring... */
			{
				self::$completo .= "?" . $_SERVER["QUERY_STRING"];
			}
			
			/* Troco os & por &amp; */
			self::$completo_amp = str_replace("&", "&amp;", self::$completo);
			
			/* Preencho os demais diretórios. */
			self::$css = self::$site . "estilos/";
			self::$scripts = self::$site . "scripts/";
			self::$images = self::$site . "imagens/";
			self::$exportacao = self::$site . "export/";
			self::$importacao = self::$site . "uploads/";
			
			if ($_SERVER["SERVER_NAME"] == "localhost")
			{
				self::$exportacao_fisico = "C:\\wamp\\www\\SISCOSERV_NOVO\\export\\";
				self::$importacao_fisico = "C:\\wamp\\www\\SISCOSERV_NOVO\\uploads\\";
			}
			else
			{
				//echo(realpath("info.php"));
				//self::$exportacao_fisico = "/u/web/cong39/www/siscoserv/export/";
				self::$exportacao_fisico = "/home/storage/8/6e/f0/hetakuso1/public_html/siscoserv_novo/export/";
				self::$importacao_fisico = "/home/storage/8/6e/f0/hetakuso1/public_html/siscoserv_novo/uploads/";
			}			
			
			
			self::$login = self::$site . "login.php";
			
			/* Isto aqui será usado para escrever o arquivo .css.
			 Como não quero adicionar nenhum include no .css (nem este),
			 usarei, variáveis de sessão exclusivas para ele. */
			$_SESSION["css_imagens"] = self::$images;
			$_SESSION["css_funcoes"] = self::$site . "funcoes.php";
			$_SESSION["css_funcoes_exporta"] = self::$site . "funcoes_export.php";
			$_SESSION["css_abre_arquivo"] = self::$site . "baixa_arquivo.php";
			
			/* Inicializarei o objeto de classes para CSS, para que seu constructor
			seja chamada, e assim, consigamos acessar suas propriedades. */
			// $this->classes = new ClassesCSS();
		}

		/* Endereço do servidor. */
		public static function ServerName()
		{
			return self::$servername;
		}
		/* Endereço do site. Deve ser o mesmo do ServerName(), 
		 exceto quando no localhost. */
		public static function Site()
		{
			return self::$site;
		}
		/* Endereço da página atual. */
		public static function PaginaAtual()
		{
			return self::$paginaatual;
		}
		/* Endereço completo da página atual,
		 incluindo a querystring. */
		public static function Completo()
		{
			return self::$completo;
		}
		/* Endereço completo da página atual, incluindo a
		 querystring, mas com &amp; no lugar de &. 
		 Quando for necessário escrever o endereço no HTML,
		 deve-se usar este método, pois o padrão XHTML não
		 aceita & sozinho no meio do código. */
		public static function CompletoAMP()
		{
			return self::$completo_amp;
		}
		
		/* Diretório para CSS. */
		public static function CSS()
		{
			return self::$css;
		}
		/* Diretório para JavaScript. */
		public static function Scripts()
		{
			return self::$scripts;
		}
		/* Diretório para imagens. */
		public static function Images()
		{
			return self::$images;
		}
		
		public static function Login()
		{
			return self::$login;
		}
		public static function Exportacao()
		{
			return self::$exportacao;
		}
		public static function ExportacaoFisico()
		{
			return self::$exportacao_fisico;
		}
		public static function Importacao()
		{
			return self::$importacao;
		}
		public static function ImportacaoFisico()
		{
			return self::$importacao_fisico;
		}
		
		public static function ServerLocal()
		{
			return self::$server_local;
		}
		
		public static function LocalHost()
		{
			return self::$localhost;
		}
	}

?>