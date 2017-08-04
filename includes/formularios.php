<?php
	class Modo_Formulario {
		private static $t_nenhum = -1;
		
		private static $t_inclusao = 0;
		private static $t_alteracao = 1;
		private static $t_consulta = 2;
		
		public static function Inclusao() {
			return self::$t_inclusao;
		}
		
		public static function Alteracao() {
			return self::$t_alteracao;
		}
		
		public static function Consulta() {
			return self::$t_consulta;
		}
		
		public static function NENHUM() {
			return self::$t_nenhum;
		}
	}	

	/* Constantes de definições de tipos de formulários */
	class Tipo_VA {
		
		private static $t_nenhum = -1;
		
		private static $t_ras = 0;
		private static $t_rvs = 1;
		
		public static function RAS() {
			return self::$t_ras;
		}
		
		public static function RVS() {
			return self::$t_rvs;
		}
		
		public static function NENHUM() {
			return self::$t_nenhum;
		}
	}
	
	class Tipo_DIRE {
		
		private static $t_nenhum = -1;
		
		private static $t_di = 0;
		private static $t_re = 1;
		
		public static function DI() {
			return self::$t_di;
		}
		
		public static function RE() {
			return self::$t_re;
		}
		
		public static function NENHUM() {
			return self::$t_nenhum;
		}
	}
	
	class Formulario {
		public $modo;
		
		public $id;
		
		public function __construct($id = -1) {
			if (!is_numeric($id)) {
				$id = -1;
			}
			
			if ($id == -1) {
				$this->modo = Modo_Formulario::Inclusao();
			}
			else {
				$this->modo = Modo_Formulario::Alteracao();
			}
			
			$this->id = $id;
		}
	}
	class Formulario_Multiplo extends Formulario {
		public $num;
		public $cria_fieldset;
		
		public function __construct($num, $id = -1) {
			parent::__construct($id);
			
			$this->num = $num;
			$this->cria_fieldset = true;
		}

	}
	
	function EncontraDescricaoPais($codigo) {
		$r = "*";
		
		$sql = "SELECT * FROM tx_pais WHERE codigo = '" . addslashes($codigo) . "'";
		$rs = Conexao::Executa($sql);
		
		if ($rs->num_rows != 0) {
			$row = $rs->fetch_assoc();
			$r = $row["nome"];
		}
		
		$rs->close();
		
		return $r;
	}
	function EncontraDescricaoMoeda($codigo) {
		$r = "*";
		
		$sql = "SELECT * FROM tx_moeda WHERE codigo = '" . addslashes($codigo) . "'";
		$rs = Conexao::Executa($sql);
		
		if ($rs->num_rows != 0) {
			$row = $rs->fetch_assoc();
			$r = $row["descricao"];
		}
		
		$rs->close();
		
		return $r;
	}
	function EncontraDescricaoNBS($codigo) {
		$r = "*";
		
		$sql = "SELECT * FROM tx_nbs WHERE codigo = '" . addslashes($codigo) . "'";
		$rs = Conexao::Executa($sql);
		
		if ($rs->num_rows != 0) {
			$row = $rs->fetch_assoc();
			$r = $row["descricao"];
		}
		
		$rs->close();
		
		return $r;
	}
	function EncontraDescricaoEnquadramento($codigo) {
		$r = "*";
		
		$sql = "SELECT * FROM tx_enquadramento WHERE codigo = '" . addslashes($codigo) . "'";
		$rs = Conexao::Executa($sql);
		
		if ($rs->num_rows != 0) {
			$row = $rs->fetch_assoc();
			$r = $row["descricao"];
		}
		
		$rs->close();
		
		return $r;
	}
	
	class Formulario_Participante extends Formulario {
		public $nome;
		public $endereco;
		public $cod_pais;
		public $desc_pais;
		public $nif;
		public $motivo_nif;
		public $nif_n_preenchido;
		
		public $t_codigo;
		
		public function __construct($id = -1) {
			parent::__construct($id);
			
			$this->t_codigo = "Código";
			
			if ($this->modo == Modo_Formulario::Alteracao()) {
				$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_participantes WHERE ID = " . $this->id;
				$rs = Conexao::Executa($sql);
				
				if ($rs->num_rows == 0) {
					$this->modo = Modo_Formulario::Inclusao();
				}
				else {
					$row = $rs->fetch_assoc();
					
					$this->nome = $row["Nome"];
					$this->endereco = $row["Endereco"];
					$this->cod_pais = $row["Pais"];
					$this->nif = $row["NIF"];
					$this->motivo_nif = $row["MotivoNif"];
					
					if ($this->motivo_nif == "1" || $this->motivo_nif == "2") {
						$this->nif_n_preenchido = true;
					}
					else {
						$this->nif_n_preenchido = false;
					}
					
					
					$desc = EncontraDescricaoPais($this->cod_pais);
					
					if ($desc == "*") {
						$this->desc_pais = "";
					}
					else {
						$this->desc_pais = $desc;
					}
				}
				
				$rs->close();
			}
			
			if ($this->modo == Modo_Formulario::Inclusao()) {
				$this->nome = "";
				$this->endereco = "";
				$this->cod_pais = "";
				$this->desc_pais = "";
				$this->nif = "";
				$this->motivo_nif = "";
				$this->nif_n_preenchido = false;
			}
		}

		
		public function EscreveFormulario() {			
			if ($this->modo != Modo_Formulario::Consulta()) {
?>
			<form id="grava_participante" name="grava_participante" method="post" action="<?php echo(Enderecos::CompletoAmp()); ?>">
<?php 
			}
			
			$funcao_js_cod_part = "EncontraPart(this, document.getElementById('f_part_nome'), document.getElementById('f_part_endereco'), document.getElementById('f_part_pais_cod'), document.getElementById('f_part_pais_desc'), document.getElementById('f_part_nif'), document.getElementById('f_part_motivo_nif'), document.getElementById('f_part_sem_nif'))";
?>
				<fieldset class="painel_formulario_part<?php echo($this->modo == Modo_Formulario::Consulta()? "_consulta" : ""); ?>">
					<legend><?php echo($this->t_codigo); ?>:</legend>
					
					<input type="hidden" name="f_part_post" id="f_part_post" value="post">

					<label class="nome_campo" id="f_t_part_cod" for="f_part_cod"<?php echo($this->modo == Modo_Formulario::Inclusao() ? " style=\"display: none;\"" : ""); ?>>Código:</label>
					<input id="f_part_cod" name="f_part_cod" <?php echo($this->modo != Modo_Formulario::Consulta()? "readonly=\"readonly\" " : "onchange=\"javascript: " . $funcao_js_cod_part . ";\" "); ?>type="text"<?php echo($this->modo == Modo_Formulario::Inclusao() ? " style=\"display: none;\"" : ""); ?><?php echo($this->id != -1 ? " value=\"" . htmlspecialchars($this->id, ENT_COMPAT) . "\"" : ""); ?> />
<?php 
			if ($this->modo == Modo_Formulario::Consulta()) {
?>
					<input type="button" value="..." onclick="javascript: AbreBuscaPart(document.getElementById('busca_part'), 'part_int', 'f_part_cod', 'f_part_nome', 'f_part_endereco', 'f_part_pais_cod', 'f_part_pais_desc', 'f_part_nif', 'f_part_motivo_nif', 'f_part_sem_nif'); this.onclick = function() { Abre(document.getElementById('busca_part'), 300); }" />

					<section id="busca_part" class="painel_interno_busca">
					</section>
<?php 
			}
?>

					<label class="nome_campo" for="f_part_nome">Nome:</label>
					<input id="f_part_nome" name="f_part_nome" <?php echo($this->modo == Modo_Formulario::Consulta()? "readonly=\"readonly\" " : ""); ?>maxlength="150" type="text"<?php echo($this->nome != "" ? " value=\"" . htmlspecialchars($this->nome, ENT_COMPAT) . "\"" : ""); ?> />
					
					<label class="nome_campo" for="f_part_endereco">Endereço:</label>
					<input id="f_part_endereco" name="f_part_endereco" <?php echo($this->modo == Modo_Formulario::Consulta()? "readonly=\"readonly\" " : ""); ?>maxlength="150" type="text"<?php echo($this->endereco != "" ? " value=\"" . htmlspecialchars($this->endereco, ENT_COMPAT) . "\"" : ""); ?> />
					
					<label class="nome_campo" for="f_part_pais_cod">País:</label>
					<input id="f_part_pais_cod" name="f_part_pais_cod" type="text" <?php echo($this->modo == Modo_Formulario::Consulta()? "readonly=\"readonly\" " : ""); ?>maxlength="5" <?php echo($this->modo != Modo_Formulario::Consulta()? "onchange=\"javascript: EncontraDescricao(this, document.getElementById('f_part_pais_desc'), 'pais');\" " : ""); ?><?php echo($this->cod_pais != "" ? " value=\"" . htmlspecialchars($this->cod_pais, ENT_COMPAT) . "\"" : ""); ?> />
<?php 
			if ($this->modo != Modo_Formulario::Consulta()) {
?>
					<input type="button" value="..." onclick="javascript: AbreBusca(document.getElementById('busca_pais'), 'pais', 'f_part_pais_cod', 'f_part_pais_desc'); this.onclick = function() { Abre(document.getElementById('busca_pais'), 300); }" />
<?php
			}
?>
					<input id="f_part_pais_desc" name="f_part_pais_desc" class="f_desc" readonly="readonly" type="text"<?php echo($this->desc_pais != "" ? " value=\"" . htmlspecialchars($this->desc_pais, ENT_COMPAT) . "\"" : ""); ?> />
					
<?php 
			if ($this->modo != Modo_Formulario::Consulta()) {
?>
					<section id="busca_pais" class="painel_interno_busca">
					</section>
<?php
			}
?>
					
					<section id="preencher_nif" <?php echo($this->nif_n_preenchido ? "style=\"display: none;\" " : ""); ?>>
						<label class="nome_campo" for="f_part_nif">NIF:</label>
						<input id="f_part_nif" name="f_part_nif" maxlength="40" type="text"<?php echo($this->nif != "" ? " value=\"" . htmlspecialchars($this->nif, ENT_COMPAT) . "\"" : ""); ?> />
					</section>
					
					<section id="motivo_nif" <?php echo(!$this->nif_n_preenchido ? "style=\"display: none;\" " : ""); ?>>
						<label class="nome_campo" for="f_part_nif">Motivo de não preenchimento do NIF:</label>
						
						<select id="f_part_motivo_nif" name="f_part_motivo_nif" <?php echo($this->modo == Modo_Formulario::Consulta()? "readonly=\"readonly\" " : ""); ?>>
							<option value="1" <?php echo($this->motivo_nif == "1" ? " selected=\"selected\"" : ""); ?>>1 – Residente ou domiciliado no exterior dispensado do NIF</option>
							<option value="2" <?php echo($this->motivo_nif == "2" ? " selected=\"selected\"" : ""); ?>>2 – País não exige NIF</option>
						</select> 
					</section>
<?php 
			//if ($this->modo != Modo_Formulario::Consulta()) {
?>
					<input id="f_part_sem_nif" name="f_part_sem_nif" type="checkbox" value="on"<?php echo($this->nif_n_preenchido ? " checked=\"checked\"" : ""); ?> onclick="javascript: ToggleNif(this.checked, document.getElementById('preencher_nif'), document.getElementById('motivo_nif'));"><label for="f_part_sem_nif">Nif em branco</label>
<?php
			//}
?>					
					
<?php 
			if ($this->modo != Modo_Formulario::Consulta()) {
?>
					<span id="f_part_msg_erro"></span>
					
					<input id="f_part_salvar" name="f_part_salvar" class="f_part_salvar" type="button" onclick="javascript: Grava(document.getElementById('grava_participante'), 'participante', document.getElementById('f_part_salvar'), document.getElementById('f_part_msg_erro'), document.getElementById('f_part_cod')); return false;" value="Salvar" />
<?php
			}
?>
				</fieldset>
<?php 
			if ($this->modo != Modo_Formulario::Consulta()) {
?>
			</form>
<?php
			}
		}
		
		public function Grava() {
			$r = "*";
		
			if (!isset($_POST["f_part_post"])) return $r;
			if ($_POST["f_part_post"] != "post") return $r;
		
			if (trim($_POST["f_part_nome"]) == "") return $r;
			if (trim($_POST["f_part_endereco"]) == "") return $r;
			if (EncontraDescricaoPais($_POST["f_part_pais_cod"]) == "*") return $r;
			
			$nif = "";
			$motivo_nif = "";
			
			if ($_POST["f_part_sem_nif"] == "on") {
				if ($_POST["f_part_motivo_nif"] != "1" && $_POST["f_part_motivo_nif"] != "2") return $r;
				
				$motivo_nif = $_POST["f_part_motivo_nif"];
			}
			else {
				if (trim($_POST["f_part_nif"]) == "") return $r;
				
				$nif = trim($_POST["f_part_nif"]);
			}
			
			if (trim($_POST["f_part_cod"]) != "")
			{
				if (!is_numeric($_POST["f_part_cod"])) return $r;
			
				$sql = "UPDATE " . $_SESSION["banco"] . ".zx_participantes " . 
					"SET Nome  = '" . addslashes($_POST["f_part_nome"]) . "', " .
					"Endereco = '" . addslashes($_POST["f_part_endereco"]) . "', " . 
					"Pais = '" . addslashes($_POST["f_part_pais_cod"]) . "', " . 
					"NIF = '" . addslashes($nif) . "', " . 
					"MotivoNif = '" . addslashes($motivo_nif) . "' " . 
					"WHERE ID = " . $_POST["f_part_cod"];
				
				Conexao::Executa($sql);
				
				$r = $_POST["f_part_cod"];
			}
			else
			{	
				$sql = "INSERT INTO " . $_SESSION["banco"] . ".zx_participantes (Nome, Endereco, Pais, NIF, MotivoNif) " . 
					"VALUES ('" . addslashes($_POST["f_part_nome"]) . "', '" .
					addslashes($_POST["f_part_endereco"]) . "', '" . 
					addslashes($_POST["f_part_pais_cod"]) . "', '" . 
					addslashes($nif) . "', '" . addslashes($motivo_nif) . "')";
					
				Conexao::Executa($sql);
				
				$r = Conexao::$cn->insert_id;
			}
			
			return $r;
		}
	}

	class Formulario_Venda_Aquisicao_Base extends Formulario {
		public $tipo;
		
		public $t_tipo;
		public $t_min;
		
		public $t_part;
		
		public $nome_tabela_bd;
		public $nome_tabela_bd_ext;
		
		public $t_tipo_ext;
		public $t_tipo_ext_min;
		public $t_tipo_ext_xml;
		
		public function __construct($tipo = -1, $id = -1) {
			parent::__construct($id);
			
			
			if ($tipo == -1) {
				$this->tipo = Tipo_VA::NENHUM();
				
				$this->t_tipo = "";
				$this->t_tipo_min = "";
				
				$this->t_tipo_ext = "";
				$this->t_tipo_ext_min = "";
				$this->t_tipo_ext_xml = "";
			}
			else {
				$this->tipo = $tipo;
				
				if ($this->tipo == Tipo_VA::RVS()) {
					$this->t_tipo = "RVS";
					$this->t_tipo_min = "rvs";
					
					$this->t_part = "Adquirente";
					
					$this->nome_tabela_bd = "venda_rvs";
					$this->nome_tabela_bd_ext = "venda_faturamento";
					
					$this->t_tipo_ext = "Faturamento";
					$this->t_tipo_ext_min = "faturamento";
					$this->t_tipo_ext_xml = "Fatura";
				}
				else if ($this->tipo == Tipo_VA::RAS()) {
					$this->t_tipo = "RAS";
					$this->t_tipo_min = "ras";
					
					$this->t_part = "Vendedor";
					
					$this->nome_tabela_bd = "aquis_ras";
					$this->nome_tabela_bd_ext = "aquis_pagamento";
					
					$this->t_tipo_ext = "Pagamento";
					$this->t_tipo_ext_min = "pagamento";
					$this->t_tipo_ext_xml = "Pagamento";
				}
			}
		}
	}
	
	class Formulario_Venda_Aquisicao extends Formulario_Venda_Aquisicao_Base {	
		public $descricao;
		
		public $cod_part;
		public $part_nome;
		public $part_end;
		public $part_cod_pais;
		public $part_nome_pais;
		public $part_nif;
		public $part_motivo_nif;
		
		public $info_compl;
		
		public $tipo_vinc;
		
		public $cod_moeda;
		public $desc_moeda;
		
		public $nf;
		
		
		public function __construct($tipo = -1, $id = -1, $carrega_dados = true) {
			parent::__construct($tipo, $id);

			//Se for somente para exclusão, não precisa carregar dados, só preciso do ID.
			if (!$carrega_dados) return;
			
			$this->cod_part = -1;
			
			
			if ($this->modo == Modo_Formulario::Alteracao()) {
				$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . " WHERE ID = " . $this->id;
				$rs = Conexao::Executa($sql);
				
				if ($rs->num_rows == 0) {
					$this->modo = Modo_Formulario::Inclusao();
				}
				else {
					$row = $rs->fetch_assoc();
					
					$this->descricao = "";
					
					$this->cod_part = $row["ID_Participante"];
					$this->part_nome = $row["Nome" . $this->t_part];
					$this->part_end = $row["Endereco" . $this->t_part];
					$this->part_cod_pais = $row["CodigoPais" . $this->t_part];
					$this->part_nif = $row["NIF"];
					
					$this->info_compl = $row["InfoComplementar"];
					
					$this->cod_moeda = $row["CodigoMoeda"];
					$this->tipo_vinc = $row["TipoVinculacao"];
					
					$this->nf = $row["NUM_DOC"];
					
					$desc = EncontraDescricaoPais($this->cod_pais);
					
					if ($desc == "*") {
						$this->part_nome_pais = "";
					}
					else {
						$this->part_nome_pais = $desc;
					}
					
					$desc = EncontraDescricaoMoeda($this->cod_moeda);
					
					if ($desc == "*") {
						$this->desc_moeda = "";
					}
					else {
						$this->desc_moeda = $desc;
					}
				}
				
				$rs->close();
			}
			
			if ($this->modo == Modo_Formulario::Inclusao()) {
				$this->descricao = "";
				
				$this->cod_part = "";
				$this->part_nome = "";
				$this->part_end = "";
				$this->part_cod_pais = "";
				$this->part_nome_pais = "";
				$this->part_nif = "";
				$this->part_motivo_nif = "";
				
				$this->info_compl = "";
				$this->tipo_vinc = "";
				
				$this->cod_moeda = "";
				$this->desc_moeda = "";
				
				$this->nf = -1;
			}
		}

		public function EscreveFormulario() {
			$f_part = new Formulario_Participante($this->cod_part);
			$f_part->t_codigo = $this->t_part;
			$f_part->modo = Modo_Formulario::Consulta();
?>
			<form id="grava_<?php echo($this->t_tipo_min); ?>" name="grava_<?php echo($this->t_tipo_min); ?>" method="post" action="<?php echo(Enderecos::CompletoAmp()); ?>">
				<fieldset class="painel_formulario_<?php echo($this->t_tipo_min); ?>">
					<input type="hidden" name="f_<?php echo($this->t_tipo_min); ?>_post" id="f_<?php echo($this->t_tipo_min); ?>_post" value="post">
					
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_id"<?php echo($this->modo == Modo_Formulario::Inclusao() ? " style=\"display: none;\"" : ""); ?>>Número <?php echo($this->t_tipo); ?>:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_id" name="f_<?php echo($this->t_tipo_min); ?>_id" <?php echo($this->modo != Modo_Formulario::Consulta()? "readonly=\"readonly\" " : ""); ?>type="text"<?php echo($this->modo == Modo_Formulario::Inclusao() ? " style=\"display: none;\"" : ""); ?><?php echo($this->id != -1 ? " value=\"" . htmlspecialchars($this->id, ENT_COMPAT) . "\"" : ""); ?> />
					
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_num_doc">Descrição:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_descricao" name="f_<?php echo($this->t_tipo_min); ?>_descricao" class="f_desc" type="text"<?php echo($this->descricao != "" ? " value=\"" . htmlspecialchars($this->descricao, ENT_COMPAT) . "\"" : ""); ?> />
					
<?php 
					$f_part->EscreveFormulario();
?>

					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_info_compl">Info Complementar:</label>
					<textarea id="f_<?php echo($this->t_tipo_min); ?>_info_compl" name="f_<?php echo($this->t_tipo_min); ?>_info_compl"><?php echo($this->info_compl != "" ? htmlspecialchars($this->info_compl, ENT_COMPAT) : ""); ?></textarea>
				
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_moeda_cod">Cód. Moeda:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_moeda_cod" name="f_<?php echo($this->t_tipo_min); ?>_moeda_cod" type="text" maxlength="5" onchange="javascript: EncontraDescricao(this, document.getElementById('f_<?php echo($this->t_tipo_min); ?>_moeda_desc'), 'moeda');"<?php echo($this->cod_moeda != "" ? " value=\"" . $this->cod_moeda . "\"" : ""); ?> />
					<input type="button" value="..." onclick="javascript: AbreBusca(document.getElementById('busca_moeda'), 'moeda', 'f_<?php echo($this->t_tipo_min); ?>_moeda_cod', 'f_<?php echo($this->t_tipo_min); ?>_moeda_desc'); this.onclick = function() { Abre(document.getElementById('busca_moeda'), 300); }" />
					<input id="f_<?php echo($this->t_tipo_min); ?>_moeda_desc" class="f_desc" name="f_<?php echo($this->t_tipo_min); ?>_moeda_desc" readonly="readonly" type="text"<?php echo($this->desc_moeda != "" ? " value=\"" . $this->desc_moeda . "\"" : ""); ?> />

					<label id="f_t_vinculacao" class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_tipo_vinc">
						<?php if($this->tipo == Tipo_VA::RVS()) { ?>Vinculação do adquirente residente ou domiciliado no Brasil ao vendedor do serviço residente ou domiciliado no exterior:<?php } ?>
						<?php if($this->tipo == Tipo_VA::RAS()) { ?>Vinculação do vendedor residente ou domiciliado no Brasil ao adquirente do serviço residente ou domiciliado no exterior:<?php } ?>
					</label>
					<select id="f_<?php echo($this->t_tipo_min); ?>_tipo_vinc" name="f_<?php echo($this->t_tipo_min); ?>_tipo_vinc">
						<option value="0"<?php echo($this->tipo_vinc == "0" ? " selected=\"selected\"" : ""); ?>>0 – Não há vinculação</option>
						<option value="1"<?php echo($this->tipo_vinc == "1" ? " selected=\"selected\"" : ""); ?>>1 – Filial</option>
						<option value="2"<?php echo($this->tipo_vinc == "2" ? " selected=\"selected\"" : ""); ?>>2 – Sucursal</option>
						<option value="3"<?php echo($this->tipo_vinc == "3" ? " selected=\"selected\"" : ""); ?>>3 – Controlada</option>
						<option value="4"<?php echo($this->tipo_vinc == "4" ? " selected=\"selected\"" : ""); ?>>4 – Outros</option>
					</select>
					
					<section id="busca_moeda" class="painel_interno_busca">
					</section>
					
					<section id="f_<?php echo($this->t_tipo_min); ?>_painel_operacoes" class="painel_operacoes">
						<h4>Operações:</h4>
<?php 
					$i = 1;

					if ($this->modo == Modo_Formulario::Inclusao()) {
						$f_op = new Formulario_Operacoes(1, $this->tipo, $this->nf);
						
						$f_op->EscreveFormulario();						
					}
					else {
						$sql = "SELECT ID FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao WHERE ID_" . $this->t_tipo . " = " . $this->id;
						$rs = Conexao::Executa($sql);
						
						if ($rs->num_rows > 0) {
							while($row = $rs->fetch_assoc()) {
								$f_op = new Formulario_Operacoes($i, $this->tipo, $this->nf, $row["ID"]);
								
								$f_op->EscreveFormulario();
								
								$i++;
							}
							
							$rs->close();
						}
						
						$i--;
					}
?>
					</section>
					<div class="painel_formulario_<?php echo($this->t_tipo_min); ?>_consulta">
						<input type="hidden" id="f_<?php echo($this->t_tipo_min); ?>_qtde_op" name="f_<?php echo($this->t_tipo_min); ?>_qtde_op" value="<?php echo($i); ?>" />
						<a href="#" onclick="javascript: NovoItem(document.getElementById('f_<?php echo($this->t_tipo_min); ?>_painel_operacoes'), 'painel_operacao', '<?php echo($this->t_tipo_min); ?>', document.getElementById('f_<?php echo($this->t_tipo_min); ?>_qtde_op'), 'painel_formulario_<?php echo($this->t_tipo_min); ?>_consulta', 'f_<?php echo($this->t_tipo_min); ?>_op_'); return false;" id="f_<?php echo($this->t_tipo_min); ?>_novaoperacao" >Nova Operação...</a>
					</div>
					
<?php 
			if ($this->modo != Modo_Formulario::Consulta()) {
?>
					<span id="f_<?php echo($this->t_tipo_min); ?>_msg_erro"></span>
					
					<input id="f_<?php echo($this->t_tipo_min); ?>_salvar" name="f_<?php echo($this->t_tipo_min); ?>_salvar" class="f_part_salvar" type="button" onclick="javascript: Grava(document.getElementById('grava_<?php echo($this->t_tipo_min); ?>'), '<?php echo($this->t_tipo_min); ?>', document.getElementById('f_<?php echo($this->t_tipo_min); ?>_salvar'), document.getElementById('f_<?php echo($this->t_tipo_min); ?>_msg_erro'), document.getElementById('f_<?php echo($this->t_tipo_min); ?>_id')); return false;" value="Salvar" />
<?php
			}
?>

				<!--
				<li style="float: left;">
					<span class="nome_campo">Operações:<span>
					
					<ul id="form_rvs_painel_operacoes">
						<li id="lugar_operacaoxxx1"><a href="#" onclick="javascript: NovaOperacao(this.parentNode, document.getElementById('form_rvs_painel_operacoes')); return false;">Incluir Nova</a></li>
					</ul>
					
					<section id="painel_operacoes">
					</section>
				</li>
				
				<li style="float: left;">
					<span class="nome_campo">Recebimentos:<span>
					
					<ul id="form_rvs_painel_faturamentos">
						<li id="lugar_faturamento1"><a href="#" onclick="javascript: NovaOperacao(this.parentNode, document.getElementById('form_rvs_painel_faturamentos'), '2'); return false;">Incluir Novo</a></li>
					</ul>
										
					<section id="painel_faturamentos">
					</section>
				</li>
				
				<li style="clear: both;">
					<span class="nome_campo">Vinculação à Exportação de Bens:<span>
					<span><a href="#" onclick="javascript: return false;">Incluir Nova</a></span>
					
					<section id="painel_vinculacao">
					</section>
				</li> -->
				</fieldset>
			</form>
<?php
		}
		
		public function Grava() {
			$r = "*";
		
			if (!isset($_POST["f_" . $this->t_tipo_min . "_post"])) return $r;
			if ($_POST["f_" . $this->t_tipo_min . "_post"] != "post") return $r;
		
			if (trim($_POST["f_part_cod"]) == "") return $r;
			if (trim($_POST["f_" . $this->t_tipo_min . "_moeda_cod"]) == "") return $r;
			if (EncontraDescricaoMoeda($_POST["f_" . $this->t_tipo_min . "_moeda_cod"]) == "*") return $r;
			
			$id_op = 1;
			
			while (isset($_POST["f_" . $this->t_tipo_min . "_op_id_" . $id_op])) {
				$campo_id_op = "f_" . $this->t_tipo_min . "_op_id_" . $id_op;
				$campo_exc_op = "f_" . $this->t_tipo_min . "_op_excluido_" . $id_op;
				
				if ($_POST[$campo_exc_op] != "1") {
					//VALIDA OPERAÇÕES
				}
				
				$id_op++;
			}
			
			if (trim($_POST["f_" . $this->t_tipo_min . "_id"]) != "")
			{
				if (!is_numeric($_POST["f_" . $this->t_tipo_min . "_id"])) return $r;
			
				$sql = "UPDATE " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . " " . 
					"SET ID_Participante  = '" . addslashes($_POST["f_part_cod"]) . "', " .
					"Nome" . $this->t_part . " = '" . addslashes($_POST["f_part_nome"]) . "', " . 
					"Endereco" . $this->t_part . " = '" . addslashes($_POST["f_part_endereco"]) . "', " . 
					"CodigoPais" . $this->t_part . " = '" . addslashes($_POST["f_part_pais_cod"]) . "', " . 
					"NIF = '" . addslashes($_POST["f_part_nif"]) . "', " . 
					"MotivoNif = '" . addslashes($_POST["f_part_motivo_nif"]) . "', " . 
					"InfoComplementar = '" . addslashes($_POST["f_" . $this->t_tipo_min . "_info_compl"]) . "', " . 
					"CodigoMoeda = '" . addslashes($_POST["f_" . $this->t_tipo_min . "_moeda_cod"]) . "', " . 
					"TipoVinculacao = '" . addslashes($_POST["f_" . $this->t_tipo_min . "_tipo_vinc"]) . "', " . 
					"data_alteracao = NOW() " . 
					"WHERE ID = " . $_POST["f_" . $this->t_tipo_min . "_id"];
				
				Conexao::Executa($sql);

				$this->id = $_POST["f_" . $this->t_tipo_min . "_id"];
				$r = $this->id;
			}
			else
			{	
				$sql = "INSERT INTO " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "  " . 
					"VALUES (0, '', '" . addslashes($_POST["f_part_cod"]) . "', '" .
					addslashes($_POST["f_part_nome"]) . "', '" . 
					addslashes($_POST["f_part_endereco"]) . "', '" . 
					addslashes($_POST["f_part_pais_cod"]) . "', '" . 
					addslashes($_POST["f_part_nif"]) . "', '" .
					addslashes($_POST["f_" . $this->t_tipo_min . "_info_compl"]) . "', '" .
					addslashes($_POST["f_" . $this->t_tipo_min . "_moeda_cod"]) . "', '" .
					addslashes($_POST["f_" . $this->t_tipo_min . "_tipo_vinc"]) . "', '" .
					addslashes($_POST["f_part_motivo_nif"]) . "', " .
					"NOW(), NOW(), 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL)";
					
				Conexao::Executa($sql);
				
				$this->id = Conexao::$cn->insert_id;
			}
			
			$nif = "";
			$motivo_nif = "";
			
			if ($_POST["f_part_sem_nif"] == "on") {
				if ($_POST["f_part_motivo_nif"] != "1" && $_POST["f_part_motivo_nif"] != "2") return $r;
				
				$motivo_nif = $_POST["f_part_motivo_nif"];
			}
			else {
				if (trim($_POST["f_part_nif"]) == "") return $r;
				
				$nif = trim($_POST["f_part_nif"]);
			}
			
			if (trim($_POST["f_part_cod"]) != "")
			{
				if (!is_numeric($_POST["f_part_cod"])) return $r;
			
				$sql = "UPDATE " . $_SESSION["banco"] . ".zx_participantes SET " . 
					"NIF = '" . addslashes($nif) . "', " . 
					"MotivoNif = '" . addslashes($motivo_nif) . "' " . 
					"WHERE ID = " . $_POST["f_part_cod"];
				
				Conexao::Executa($sql);
			}

			$id_op = 1;
			$id_gravacao_op = -1;

			//GRAVA OPERAÇÕES
			$r .= $_POST["f_" . $this->t_tipo_min . "_op_id_" . $id_op];
			while (isset($_POST["f_" . $this->t_tipo_min . "_op_id_" . $id_op])) {
				$campo_id_op = "f_" . $this->t_tipo_min . "_op_id_" . $id_op;
				$campo_exc_op = "f_" . $this->t_tipo_min . "_op_excluido_" . $id_op;
								
				if ($_POST[$campo_exc_op] == "1") {
					if (trim($_POST[$campo_id_op]) != "-1" && is_numeric($_POST[$campo_id_op])) {
						$sql = "DELETE FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao WHERE ID = " . $_POST[$campo_id_op];
						Conexao::Executa($sql);
						
						$sql = "DELETE FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_enquadramento WHERE ID_OPERACAO = " . $_POST[$campo_id_op];
						Conexao::Executa($sql);
					}
				}
				else {
					if (trim($_POST[$campo_id_op]) != "-1")
					{
						if (!is_numeric($_POST[$campo_id_op])) return $r;
					
						$sql = "UPDATE " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao " . 
							"SET CodigoNbs  = '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_cod_nbs_" . $id_op]) . "', " .
							"ModoPrestacao = '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_modo_prestacao_" . $id_op]) . "', " . 
							"Valor = " . str_replace(",", ".", $_POST["f_" . $this->t_tipo_min . "_op_valor_" . $id_op]) . ", " . 
							"CodigoPaisDestino = '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_cod_pais_" . $id_op]) . "', " . 
							"DataInicio = '" . FormataDataBanco($_POST["f_" . $this->t_tipo_min . "_op_dt_inicio_" . $id_op]) . "', " . 
							"DataConclusao = '" . FormataDataBanco($_POST["f_" . $this->t_tipo_min . "_op_dt_conclusao_" . $id_op]) . "', " . 
							"NumeroRE = '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_num_re_" . $id_op]) . "', " . 
							"NumeroDI = '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_num_di_" . $id_op]) . "', " . 
							"data_alteracao = NOW() " . 
							"WHERE ID = " . $_POST[$campo_id_op];
						
						Conexao::Executa($sql);

						$id_gravacao_op = $_POST[$campo_id_op];
					}
					else
					{	
						$sql = "INSERT INTO " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao  " . 
							"VALUES (0, " . $this->id . ", '', '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_cod_nbs_" . $id_op]) . "', '" .
							addslashes($_POST["f_" . $this->t_tipo_min . "_op_cod_pais_" . $id_op]) . "', '" . 
							addslashes($_POST["f_" . $this->t_tipo_min . "_op_modo_prestacao_" . $id_op]) . "', '" . 
							FormataDataBanco($_POST["f_" . $this->t_tipo_min . "_op_dt_inicio_" . $id_op]) . "', '" . 
							FormataDataBanco($_POST["f_" . $this->t_tipo_min . "_op_dt_conclusao_" . $id_op]) . "', " .
							str_replace(",", ".", $_POST["f_" . $this->t_tipo_min . "_op_valor_" . $id_op]) . ", '" .
							addslashes($_POST["f_" . $this->t_tipo_min . "_op_num_re_" . $id_op]) . "', '" .
							addslashes($_POST["f_" . $this->t_tipo_min . "_op_num_di_" . $id_op]) . "', " .
							"NOW(), NOW(), 0, 0, 0, 0, 0, NULL)";
							
						Conexao::Executa($sql);
						
						$id_gravacao_op = Conexao::$cn->insert_id;
					}
				
				
					//GRAVA ENQUADRAMENTOS
					$id_op_enq = 1;
					$id_gravacao_op_enq = -1;
					
					while (isset($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_enq_id_" . $id_op_enq])) {
						$campo_id_op_enq = "f_" . $this->t_tipo_min . "_op_" . $id_op . "_enq_id_" . $id_op_enq;
						$campo_exc_op_enq = "f_" . $this->t_tipo_min . "_op_" . $id_op . "_enq_excluido_" . $id_op_enq;
						
						if ($_POST[$campo_exc_op_enq] == "1") {
							if (trim($_POST[$campo_id_op_enq]) != "-1" && is_numeric($_POST[$campo_id_op_enq])) {
								$sql = "DELETE FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_enquadramento WHERE ID = " . $_POST[$campo_id_op_enq];
								Conexao::Executa($sql);
							}
						}
						else {
							if (trim($_POST[$campo_id_op_enq]) != "-1")
							{
								if (!is_numeric($_POST[$campo_id_op_enq])) return $r;
							
								$sql = "UPDATE " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_enquadramento " . 
									"SET CodigoEnquadramento  = '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_enq_cod_" . $id_op_enq]) . "', " .
									"NumeroRc = '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_enq_num_rc_" . $id_op_enq]) . "', " . 
									"data_alteracao = NOW() " . 
									"WHERE ID = " . $_POST[$campo_id_op_enq];
								
								Conexao::Executa($sql);

								$id_gravacao_op_enq = $_POST[$campo_id_op_enq];
							}
							else
							{	
								$sql = "INSERT INTO " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_enquadramento  " . 
									"VALUES (0, " . $this->id . ", " . $id_gravacao_op . ", '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_enq_cod_" . $id_op_enq]) . "', '" .
									addslashes($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_enq_num_rc_" . $id_op_enq]) . "', " . 
									"NOW(), NOW(), 0, 0, 0, 0)";
									
								Conexao::Executa($sql);
								
								$id_gravacao_op_enq = Conexao::$cn->insert_id;
							}
						}
						
						$id_op_enq++;
					}
					
					//GRAVA RECEBIMENTOS
					$id_op_r = 1;
					$id_gravacao_op_r = -1;
					
					$campo_op_r_id = "";
					$campo_op_r_id_r = "";
					$campo_op_r_data = "";
					$campo_op_r_num_doc = "";
					$campo_op_r_valor = "";
					$campo_op_r_valor_ext = "";
					
					if ($this->tipo == Tipo_VA::RAS()) {
						$campo_op_r_id = "ID_RVA";
						$campo_op_r_id_r = "ID_Pagamento";
						$campo_op_r_data = "DataPagamento";
						$campo_op_r_num_doc = "NumeroPagamento";
						$campo_op_r_valor = "ValorPago";
						$campo_op_r_valor_ext = "ValorPagoRecMantidoExt";
					}
					else if ($this->tipo == Tipo_VA::RVS()) {
						$campo_op_r_id = "ID_RVS";
						$campo_op_r_id_r = "ID_Faturamento";
						$campo_op_r_data = "DataFatura";
						$campo_op_r_num_doc = "NumeroFatura";
						$campo_op_r_valor = "ValorFaturado";
						$campo_op_r_valor_ext = "ValorMantidoExt";
					}
					
					while (isset($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_r_id_" . $id_op_r])) {
						$campo_id_op_r = "f_" . $this->t_tipo_min . "_op_" . $id_op . "_r_id_" . $id_op_r;
						$campo_exc_op_r = "f_" . $this->t_tipo_min . "_op_" . $id_op . "_r_excluido_" . $id_op_r;
						
						if ($_POST[$campo_exc_op_r] == "1") {
							if (trim($_POST[$campo_id_op_r]) != "-1" && is_numeric($_POST[$campo_id_op_r])) {
								$sql = "DELETE FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . " WHERE ID = " . $_POST[$campo_id_op_r];
								Conexao::Executa($sql);
								
								$sql = "DELETE FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . "_item WHERE " . $campo_op_r_id_r . " = " . $_POST[$campo_id_op_r];
								Conexao::Executa($sql);
							}
						}
						else {
							if (trim($_POST[$campo_id_op_r]) != "-1")
							{
								if (!is_numeric($_POST[$campo_id_op_r])) return $r;
							
								$sql = "UPDATE " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . " " . 
									"SET " . $campo_op_r_num_doc . " = '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_r_num_doc_" . $id_op_r]) . "', " .
									$campo_op_r_data . " = '" . FormataDataBanco($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_r_data_" . $id_op_r]) . "', " . 
									"data_alteracao = NOW() " . 
									"WHERE ID = " . $_POST[$campo_id_op_r];
								Conexao::Executa($sql);
								
								$sql = "UPDATE " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . "_item " . 
									"SET " . $campo_op_r_valor . " = " . str_replace(",", ".", $_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_valor_" . $id_op_r]) . ", " .
									$campo_op_r_valor_ext . " = " . str_replace(",", ".", $_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_valor_ext_" . $id_op_r]) . ", " . 
									"data_alteracao = NOW() " . 
									"WHERE " . $campo_op_r_id_r . " = " . $_POST[$campo_id_op_r];
								Conexao::Executa($sql);

								$id_gravacao_op_r = $_POST[$campo_id_op_r];
							}
							else
							{	
								$sql = "INSERT INTO " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . "  " . 
									"VALUES (0, " . $this->id . ", '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_r_num_doc_" . $id_op_r]) . "', '" .
									FormataDataBanco($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_r_data_" . $id_op_r]) . "', " . 
									"NULL, NULL, NULL, NOW(), NOW(), 0, 0, 0, 0, NULL)";
									
								Conexao::Executa($sql);
								
								$id_gravacao_op_r = Conexao::$cn->insert_id;
									
								$sql = "INSERT INTO " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . "_item  " . 
									"VALUES (0, " . $id_gravacao_op_r . ", " . $id_gravacao_op . ", " . str_replace(",", ".", $_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_valor_" . $id_op_r]) . ", " .
									str_replace(",", ".", $_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_valor_ext_" . $id_op_r]) . ", " . 
									"NOW(), NOW(), 0, 0, 0, 0)";
									
								Conexao::Executa($sql);
							}
						}
						
						$id_op_r++;
					}
					
					
					
					//GRAVA RE
					$id_op_re = 1;
					$id_gravacao_op_re = -1;
					
					while (isset($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_re_id_" . $id_op_re])) {
						$campo_id_op_re = "f_" . $this->t_tipo_min . "_op_" . $id_op . "_re_id_" . $id_op_re;
						$campo_exc_op_re = "f_" . $this->t_tipo_min . "_op_" . $id_op . "_re_excluido_" . $id_op_re;
						
						if ($_POST[$campo_exc_op_re] == "1") {
							if (trim($_POST[$campo_id_op_re]) != "-1" && is_numeric($_POST[$campo_id_op_re])) {
								$sql = "DELETE FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_vinculacao WHERE ID = " . $_POST[$campo_id_op_re];
								Conexao::Executa($sql);
							}
						}
						else {
							if (trim($_POST[$campo_id_op_re]) != "-1")
							{
								if (!is_numeric($_POST[$campo_id_op_re])) return $r;
							
								$sql = "UPDATE " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_vinculacao " . 
									"SET NumeroRE  = '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_re_num_" . $id_op_re]) . "', " .
									"data_alteracao = NOW() " . 
									"WHERE ID = " . $_POST[$campo_id_op_re];
								
								Conexao::Executa($sql);

								$id_gravacao_op_re = $_POST[$campo_id_op_re];
							}
							else
							{	
								$sql = "INSERT INTO " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_vinculacao  " . 
									"VALUES (0, " . $this->id . ", " . $id_gravacao_op . ", NULL, '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_re_num_" . $id_op_re]) . "', " .
									"NOW(), NOW(), 0, 0, 0, 0)";
									
								Conexao::Executa($sql);
								
								$id_gravacao_op_re = Conexao::$cn->insert_id;
							}
						}
						
						$id_op_re++;
					}
					
					
					//GRAVA DI
					$id_op_di = 1;
					$id_gravacao_op_di = -1;
					
					while (isset($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_di_id_" . $id_op_di])) {
						$campo_id_op_di = "f_" . $this->t_tipo_min . "_op_" . $id_op . "_di_id_" . $id_op_di;
						$campo_exc_op_di = "f_" . $this->t_tipo_min . "_op_" . $id_op . "_di_excluido_" . $id_op_di;
						
						if ($_POST[$campo_exc_op_di] == "1") {
							if (trim($_POST[$campo_id_op_di]) != "-1" && is_numeric($_POST[$campo_id_op_di])) {
								$sql = "DELETE FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_vinculacao WHERE ID = " . $_POST[$campo_id_op_di];
								Conexao::Executa($sql);
							}
						}
						else {
							if (trim($_POST[$campo_id_op_di]) != "-1")
							{
								if (!is_numeric($_POST[$campo_id_op_di])) return $r;
							
								$sql = "UPDATE " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_vinculacao " . 
									"SET NumeroDI  = '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_di_num_" . $id_op_di]) . "', " .
									"data_alteracao = NOW() " . 
									"WHERE ID = " . $_POST[$campo_id_op_di];
								
								Conexao::Executa($sql);

								$id_gravacao_op_di = $_POST[$campo_id_op_di];
							}
							else
							{	
								$sql = "INSERT INTO " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_vinculacao  " . 
									"VALUES (0, " . $this->id . ", " . $id_gravacao_op . ", '" . addslashes($_POST["f_" . $this->t_tipo_min . "_op_" . $id_op . "_di_num_" . $id_op_di]) . "', NULL, " .
									"NOW(), NOW(), 0, 0, 0, 0)";
									
								Conexao::Executa($sql);
								
								$id_gravacao_op_di = Conexao::$cn->insert_id;
							}
						}
						
						$id_op_di++;
					}
				}
				
				$id_op++;
			}

			$r = $this->id;
			return $r;
		}
		
		public function Excluir() {
			$campo_op_r_id = "";
			$campo_op_r_id_r = "";
			
			if ($this->tipo == Tipo_VA::RAS()) {
				$campo_op_r_id = "ID_RVA";
				$campo_op_r_id_r = "ID_Pagamento";
			}
			else if ($this->tipo == Tipo_VA::RVS()) {
				$campo_op_r_id = "ID_RVS";
				$campo_op_r_id_r = "ID_Faturamento";
			}
					
			$sql = "INSERT INTO " . $_SESSION["banco"] . ".ex_" . $this->nome_tabela_bd_ext . "_item SELECT * FROM " . 
				$_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . "_item WHERE " . $campo_op_r_id_r . 
				" IN (SELECT ID FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . " WHERE " . $campo_op_r_id . " = " . $this->id . ")";
			Conexao::Executa($sql);
			
			$sql = "INSERT INTO " . $_SESSION["banco"] . ".ex_" . $this->nome_tabela_bd_ext . " SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext .
				" WHERE " . $campo_op_r_id . " = " . $this->id;
			Conexao::Executa($sql);
			
			$sql = "INSERT INTO " . $_SESSION["banco"] . ".ex_" . $this->nome_tabela_bd . "_operacao_vinculacao SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_vinculacao WHERE ". $campo_op_r_id . " = " . $this->id;
			Conexao::Executa($sql);
			
			$sql = "INSERT INTO " . $_SESSION["banco"] . ".ex_" . $this->nome_tabela_bd . "_operacao_enquadramento SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_enquadramento WHERE ". $campo_op_r_id . " = " . $this->id;
			Conexao::Executa($sql);
			
			$sql = "INSERT INTO " . $_SESSION["banco"] . ".ex_" . $this->nome_tabela_bd . "_operacao SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao WHERE ". $campo_op_r_id . " = " . $this->id;
			Conexao::Executa($sql);
			
			$sql = "INSERT INTO " . $_SESSION["banco"] . ".ex_" . $this->nome_tabela_bd . " SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . " WHERE ID = " . $this->id;
			Conexao::Executa($sql);
			
			
			$sql = "DELETE FROM " . 
				$_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . "_item WHERE " . $campo_op_r_id_r . 
				" IN (SELECT ID FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . " WHERE " . $campo_op_r_id . " = " . $this->id . ")";
			Conexao::Executa($sql);
			
			$sql = "DELETE FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext .
				" WHERE " . $campo_op_r_id . " = " . $this->id;
			Conexao::Executa($sql);
			
			$sql = "DELETE FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_vinculacao WHERE ". $campo_op_r_id . " = " . $this->id;
			Conexao::Executa($sql);
			
			$sql = "DELETE FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_enquadramento WHERE ". $campo_op_r_id . " = " . $this->id;
			Conexao::Executa($sql);
			
			$sql = "DELETE FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao WHERE ". $campo_op_r_id . " = " . $this->id;
			Conexao::Executa($sql);
			
			$sql = "DELETE FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . " WHERE ID = " . $this->id;
			Conexao::Executa($sql);
			
			echo("OK");
		}
		
		
		public function FormExportacao() {
			$sql = "SELECT YEAR(DataInicio) AS Ano FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao GROUP BY YEAR(DataInicio) ORDER BY YEAR(DataInicio) DESC";
			$rs = Conexao::Executa($sql);
?>
				<span id="f_t_botao_<?php echo($this->t_tipo_min); ?>">
					<a href="#" onclick="javascript: Exportar('<?php echo($this->t_tipo_min); ?>', document.getElementById('f_t_botao_<?php echo($this->t_tipo_min); ?>'), document.getElementById('f_t_result_<?php echo($this->t_tipo_min); ?>')); return false;">Exportar Selecionados</a>
				</span>
				<span id="f_t_result_<?php echo($this->t_tipo_min); ?>" style="display: none;">
				</span>

				<label id="f_t_ano_export_<?php echo($this->t_tipo_min); ?>" class="nome_campo" for="f_ano_export_<?php echo($this->t_tipo_min); ?>">Ano:
				</label>
				<select id="f_ano_export_<?php echo($this->t_tipo_min); ?>" name="f_ano_export_<?php echo($this->t_tipo_min); ?>" onchange="javascript: AbreBuscaRegistro(document.getElementById('busca_export_<?php echo($this->t_tipo_min) ?>'), '<?php echo($this->t_tipo_min); ?>', this.value);">
<?php
			while($row = $rs->fetch_assoc()) {
?>
					<option value="<?php echo($row["Ano"]); ?>"><?php echo($row["Ano"]); ?></option>
<?php
			}
			
			$rs->close();
?>
				</select>
				
				<section id="busca_export_<?php echo($this->t_tipo_min) ?>" class="painel_interno_busca">
				</section>
				
				<script type="text/javascript">
				<!--
					AbreBuscaRegistro(document.getElementById('busca_export_<?php echo($this->t_tipo_min) ?>'), '<?php echo($this->t_tipo_min); ?>', document.getElementById('f_ano_export_<?php echo($this->t_tipo_min); ?>').value);
				//-->
				</script>
<?php			
			

		}
		
		
		public function Exportar($ids) {
			$cada_id = explode(",", $ids);
			$ids_select = "";
			
			foreach($cada_id as $idd) {
				if (is_numeric($idd)) {
					$ids_select .= ($ids_select != "" ? "," : "") . $idd;
				}
			}
			
			if ($ids_select == "") {
				echo("*");
				return;
			}
					
			$zip = new ZipArchive();
			
			//$now = DateTime::createFromFormat('U.u', microtime(true));
			//$nome_so_zip = "Lote_" . $this->t_tipo . "_" . $now->format("Y-m-d_H.i.s.u") . ".zip";
			//$nome_so_zip = "Lote_" . $this->t_tipo . "_" . date("Y-m-d_H.i.s.u") . ".zip";
			$nome_so_zip = $this->t_tipo . date("YmdHis") . ".zip";
			$nome_zip = Enderecos::ExportacaoFisico() . $nome_so_zip;
			$endereco_zip = Enderecos::Site() . "baixa_arquivo.php?nome=" . $nome_so_zip;

			if ($zip->open($nome_zip, ZIPARCHIVE::CREATE) !== TRUE) {
				echo("*");
				return;
			}
				
			//$sql = "Select * From zx_aquis_ras Where importado = 0";
			$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . " WHERE ID IN (" . $ids_select . ")";
			$rs = Conexao::Executa($sql);
			
			$t_reg = "";
			
			if ($this->tipo == Tipo_VA::RVS()) {
				$t_reg = "registro";
			}
			else if ($this->tipo == Tipo_VA::RAS()) {
				$t_reg = "RAS";
			}
			
			for ($i = 0; $i < $rs->num_rows; $i++) {
				$row = $rs->fetch_assoc();
				
				//$nome_sem_caminho = $this->t_tipo . "_INC_" . date("Y-m-d") . "_" . $row["ID"] . ".xml";
				//$nome_sem_caminho = $this->t_tipo . "_I_" . date("Y-m-d") . "_" . $row["ID"] . ".xml";
				$nome_sem_caminho = $this->t_tipo . $row["ID"] . ".xml";
				$nome_arquivo = Enderecos::ExportacaoFisico() . $nome_sem_caminho;
				$f = fopen($nome_arquivo, "w");
				
				/* echo(Enderecos::ExportacaoFisico() . $nome_sem_caminho);
				die(); */
				
				fwrite($f, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
				fwrite($f, "<Incluir" . $this->t_tipo . " xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"incluir_" . $t_reg . ".xsd\">\n");
				fwrite($f, "\t<Numero" . $this->t_tipo . "Empresa>" . $row["ID"] . "</Numero" . $this->t_tipo . "Empresa>\n");
				
				$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_participantes WHERE ID >= " . $row["ID_Participante"];
				$rsx = Conexao::Executa($sql);
				
				if ($rsx->num_rows != 0) {
					$rowx = $rsx->fetch_assoc();
					
					fwrite($f, "\t<Nome" . $this->t_part . ">" . htmlspecialchars($rowx["Nome"], ENT_COMPAT) . "</Nome" . $this->t_part . ">\n");
					fwrite($f, "\t<Endereco" . $this->t_part . ">" . htmlspecialchars($rowx["Endereco"], ENT_COMPAT) . "</Endereco" . $this->t_part . ">\n");
					fwrite($f, "\t<CodigoPais" . $this->t_part . ">" . htmlspecialchars($rowx["Pais"], ENT_COMPAT) . "</CodigoPais" . $this->t_part . ">\n");
					fwrite($f, "\t<IdentificadorFiscal>\n");
					if (trim($rowx["NIF"]) == "") {
						fwrite($f, "\t\t<MotivoNaoPreenchimentoNif>" . htmlspecialchars($rowx["MotivoNif"], ENT_COMPAT) . "</MotivoNaoPreenchimentoNif>\n");
					}
					else {
						fwrite($f, "\t\t<Nif>" . htmlspecialchars($rowx["NIF"], ENT_COMPAT) . "</Nif>\n");
					}
					fwrite($f, "\t</IdentificadorFiscal>\n");
				}
				else {
					fwrite($f, "\t<Nome" . $this->t_part . ">" . htmlspecialchars($row["Nome" . $this->t_part], ENT_COMPAT) . "</Nome" . $this->t_part . ">\n");
					fwrite($f, "\t<Endereco" . $this->t_part . ">" . htmlspecialchars($row["Endereco" . $this->t_part], ENT_COMPAT) . "</Endereco" . $this->t_part . ">\n");
					fwrite($f, "\t<CodigoPais" . $this->t_part . ">" . htmlspecialchars($row["CodigoPais" . $this->t_part], ENT_COMPAT) . "</CodigoPais" . $this->t_part . ">\n");
					fwrite($f, "\t<IdentificadorFiscal>\n");
					if (trim($row["NIF"]) == "") {
						fwrite($f, "\t\t<MotivoNaoPreenchimentoNif>" . htmlspecialchars($row["MotivoNif"], ENT_COMPAT) . "</MotivoNaoPreenchimentoNif>\n");
					}
					else {
						fwrite($f, "\t\t<Nif>" . htmlspecialchars($row["NIF"], ENT_COMPAT) . "</Nif>\n");
					}
					fwrite($f, "\t</IdentificadorFiscal>\n");
				}
				
				$rsx->close();
				
				fwrite($f, "\t<TipoVinculacao>" . htmlspecialchars($row["TipoVinculacao"], ENT_COMPAT) . "</TipoVinculacao>\n");
				
									
				$sql = "Select * From " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao Where ID_" . $this->t_tipo . " = " .$row["ID"];
				$rsx = Conexao::Executa($sql);
				
				for ($j = 0; $j < $rsx->num_rows; $j++) {
					$rowx = $rsx->fetch_assoc();
					
					fwrite($f, "\t<Operacao>\n");
					
					fwrite($f, "\t\t<NumeroOperacaoEmpresa>" . $rowx["ID"] . "</NumeroOperacaoEmpresa>\n");
					fwrite($f, "\t\t<CodigoNbs>" . htmlspecialchars($rowx["CodigoNbs"], ENT_COMPAT) . "</CodigoNbs>\n");
					fwrite($f, "\t\t<CodigoPaisDestino>" . htmlspecialchars($rowx["CodigoPaisDestino"], ENT_COMPAT) . "</CodigoPaisDestino>\n");
					fwrite($f, "\t\t<ModoPrestacao>" . htmlspecialchars($rowx["ModoPrestacao"], ENT_COMPAT) . "</ModoPrestacao>\n");
					fwrite($f, "\t\t<DataInicio>" . ($rowx["DataInicio"]) . "</DataInicio>\n");
					fwrite($f, "\t\t<DataConclusao>" . ($rowx["DataConclusao"]) . "</DataConclusao>\n");
					fwrite($f, "\t\t<Valor>" . $rowx["Valor"] . "</Valor>\n");
					
					$sql = "Select * From " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_enquadramento Where ID_OPERACAO = " . $rowx["ID"];
					$rsy = Conexao::Executa($sql);
					
					for ($k = 0; $k < $rsy->num_rows; $k++) {
						$rowy = $rsy->fetch_assoc();
						
						fwrite($f, "\t\t<Enquadramento>\n");
						
						fwrite($f, "\t\t\t<CodigoEnquadramento>" . htmlspecialchars($rowy["CodigoEnquadramento"], ENT_COMPAT) . "</CodigoEnquadramento>\n");
						fwrite($f, "\t\t\t<NumeroRc>" . htmlspecialchars($rowy["NumeroRc"], ENT_COMPAT) . "</NumeroRc>\n");
						
						fwrite($f, "\t\t</Enquadramento>\n");
					}
					
					$rsy->close();
					
					$sql = "Select * From " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_vinculacao Where ID_OPERACAO = " . $rowx["ID"] . " AND IFNULL(NumeroRE, '') <> ''";
					$rsy = Conexao::Executa($sql);
					
					for ($k = 0; $k < $rsy->num_rows; $k++) {
						$rowy = $rsy->fetch_assoc();
						
						fwrite($f, "\t\t<VinculacaoNumRE>" . htmlspecialchars($rowy["NumeroRE"], ENT_COMPAT) . "</VinculacaoNumRE>\n");
					}
					
					$rsy->close();
					
					
					$sql = "Select * From " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_vinculacao Where ID_OPERACAO = " . $rowx["ID"] . " AND IFNULL(NumeroDI, '') <> ''";
					$rsy = Conexao::Executa($sql);
					
					for ($k = 0; $k < $rsy->num_rows; $k++) {
						$rowy = $rsy->fetch_assoc();
						
						fwrite($f, "\t\t<VinculacaoNumDI>" . htmlspecialchars($rowy["NumeroDI"], ENT_COMPAT) . "</VinculacaoNumDI>\n");
					}
					
					$rsy->close();
					
					fwrite($f, "\t</Operacao>\n");
				}
				
				$rsx->close();
				
				fwrite($f, "\t<InfoComplementar>" . htmlspecialchars($row["InfoComplementar"], ENT_COMPAT) . "</InfoComplementar>\n");
				fwrite($f, "\t<CodigoMoeda>" . htmlspecialchars($row["CodigoMoeda"], ENT_COMPAT) . "</CodigoMoeda>\n");
				
				fwrite($f, "</Incluir" . $this->t_tipo . ">\n");
				
				fclose($f);
				
				$zip->addFile($nome_arquivo, $nome_sem_caminho);
			}
			
			$rs->close();
			$zip->close();
			
			echo($endereco_zip . "|");
						
			$zip = new ZipArchive();
			
			//$now = DateTime::createFromFormat('U.u', microtime(true));
			//$nome_so_zip = "Lote_" . $this->t_tipo_ext . "s_" . $now->format("Y-m-d_H.i.s.u") . ".zip";
			//$nome_so_zip = "Lote_" . $this->t_tipo_ext . "s_" . date("Y-m-d_H.i.s.u") . ".zip";
			//$nome_so_zip = strtoupper(substr($this->t_tipo_ext, 0, 3)) . "_" . date("Y-m-d_H.i.s") . ".zip";
			$nome_so_zip = strtoupper(substr($this->t_tipo_ext, 0, 3)) . date("YmdHis") . ".zip";
			$nome_zip = Enderecos::ExportacaoFisico() . $nome_so_zip;
			//$endereco_zip = Enderecos::Exportacao() . $nome_so_zip;
			$endereco_zip = Enderecos::Site() . "baixa_arquivo.php?nome=" . $nome_so_zip;

			if ($zip->open($nome_zip, ZIPARCHIVE::CREATE) !== TRUE) {
				echo("*");
				return;
			}
				
			$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . " WHERE ID_" . $this->t_tipo . " IN (" . $ids_select . ")";
			$rs = Conexao::Executa($sql);
			
			$t_inc = "";
			$t_valor = "";
			$t_valor_ext = "";
			$t_valor_bd = "";
			
			if ($this->tipo == Tipo_VA::RVS()) {
				$t_valor = "ValorFaturado";
				$t_valor_ext = "ValorMantidoExterior";
				$t_valor_bd = "ValorMantidoExt";
				$t_inc = "incluir";
			}
			else if ($this->tipo == Tipo_VA::RAS()) {
				$t_valor = "ValorPago";
				$t_valor_ext = "ValorPagoRecMantidoExt";
				$t_valor_bd = "ValorPagoRecMantidoExt";
				$t_inc = "inclusao";
			}
			
			for ($i = 0; $i < $rs->num_rows; $i++) {
				$row = $rs->fetch_assoc();
				
				//$nome_sem_caminho = strtoupper(substr($this->t_tipo_ext, 0, 3)) . "_INC_" . date("Y-m-d") . "_" . $row["ID"] . ".xml";
				//$nome_sem_caminho = strtoupper(substr($this->t_tipo_ext, 0, 3)) . "_I_" . date("Y-m-d") . "_" . $row["ID"] . ".xml";
				$nome_sem_caminho = strtoupper(substr($this->t_tipo_ext, 0, 3)) . $row["ID"] . ".xml";
				$nome_arquivo = Enderecos::ExportacaoFisico() . $nome_sem_caminho;
				$f = fopen($nome_arquivo, "w");
				
				fwrite($f, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
				fwrite($f, "<Inclusao" . $this->t_tipo_ext_xml . " xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"" . $t_inc . "_" . $this->t_tipo_ext_min . ".xsd\">\n");
				fwrite($f, "\t<Numero" . $this->t_tipo . "Empresa>" . $row["ID_" . $this->t_tipo] . "</Numero" . $this->t_tipo . "Empresa>\n");
				fwrite($f, "\t<Id" . $this->t_tipo_ext_xml . "Empresa>" . $row["ID"] . "</Id" . $this->t_tipo_ext_xml . "Empresa>\n");
				fwrite($f, "\t<Numero" . $this->t_tipo_ext_xml . ">" . htmlspecialchars($row["Numero" . $this->t_tipo_ext_xml], ENT_COMPAT) . "</Numero" . $this->t_tipo_ext_xml . ">\n");
				fwrite($f, "\t<Data" . $this->t_tipo_ext_xml . ">" . ($row["Data" . $this->t_tipo_ext_xml]) . "</Data" . $this->t_tipo_ext_xml . ">\n");
					
				$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . "_item WHERE ID_" . $this->t_tipo_ext . " = " . $row["ID"];
				$rsx = Conexao::Executa($sql);
				
				for ($j = 0; $j < $rsx->num_rows; $j++) {
					$rowx = $rsx->fetch_assoc();
					
					fwrite($f, "\t<Item" . $this->t_tipo_ext_xml . ">\n");
					
					fwrite($f, "\t\t<IdItem" . $this->t_tipo_ext_xml . "Empresa>" . $rowx["ID"] . "</IdItem" . $this->t_tipo_ext_xml . "Empresa>\n");
					fwrite($f, "\t\t<NumeroOperacaoEmpresa>" . $rowx["ID_Operacao"] . "</NumeroOperacaoEmpresa>\n");
					fwrite($f, "\t\t<" . $t_valor . ">" . $rowx[$t_valor] . "</" . $t_valor . ">\n");
					fwrite($f, "\t\t<" . $t_valor_ext . ">" . $rowx[$t_valor_bd] . "</" . $t_valor_ext . ">\n");
									
					fwrite($f, "\t</Item" . $this->t_tipo_ext_xml . ">\n");
				}
				
				$rsx->close();
				
				fwrite($f, "</Inclusao" . $this->t_tipo_ext_xml . ">\n");
				
				fclose($f);
				
				$zip->addFile($nome_arquivo, $nome_sem_caminho);
			}
			
			$rs->close();
			$zip->close();
			
			echo($endereco_zip);
		}
	}
	
	
	class FormularioVA_Multiplo extends Formulario_Venda_Aquisicao_Base {
		public $num;
		public $cria_fieldset;
		
		public $id_fieldset;
		public $id_painel_restaura;
		public $id_esta_excluido;
		
		public $classe_painel; //Classe CSS.
		
		public $executar_apos_excluir;
		public $executar_apos_restaurar;
		
		public function __construct($num, $tipo = -1, $id = -1) {
			parent::__construct($tipo, $id);
			
			$this->num = $num;
			$this->cria_fieldset = true;
			
			$this->executar_apos_excluir = "";
			$this->executar_apos_restaurar = "";
		}
		
		public function BotaoExcluir() { ?>
				<a class="t_botao_excluir" href="#" onclick="javascript: ExcluirItem(document.getElementById('<?php echo($this->id_fieldset); ?>'), document.getElementById('<?php echo($this->id_painel_restaura); ?>'), document.getElementById('<?php echo($this->id_esta_excluido); ?>')); <?php echo($this->executar_apos_excluir); ?>return false;">Excluir</a>
<?php			
		}
		public function PainelRestaurar() { 
			if ($this->cria_fieldset) {
		?>
				<div id="<?php echo($this->id_painel_restaura); ?>" class="<?php echo($this->classe_painel); ?>" style="display: none;">
<?php
			}
			else {
					echo($this->id_painel_restaura . "|");
			}
?>
					<a class="t_botao_excluir" href="#" onclick="javascript: RestaurarItem(document.getElementById('<?php echo($this->id_fieldset); ?>'), document.getElementById('<?php echo($this->id_painel_restaura); ?>'), document.getElementById('<?php echo($this->id_esta_excluido); ?>')); <?php echo($this->executar_apos_restaurar); ?>return false;">Desfazer Excluir</a>
<?php 		if ($this->cria_fieldset) {
		?>
				</div>
<?php						
			}
		}

	}
	
	class Formulario_Operacoes extends FormularioVA_Multiplo {
		public $cod_pais;
		public $desc_pais;
		
		public $cod_nbs;
		public $desc_nbs;
		
		public $modo_prestacao;
		public $dt_inicio;
		public $dt_conclusao;
		
		public $valor;
		
		public $valor_faturado;
		public $valor_restante;
		
		public $nf;
		
		public $t_id_esta_excluido; //Sem número no final.
		public $funcao_calculo;
		
		public function __construct($num, $tipo = -1, $nf = -1, $id = -1) {
			parent::__construct($num, $tipo, $id);
			
			
			if ($this->modo == Modo_Formulario::Alteracao()) {
				$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao WHERE ID = " . $this->id;
				$rs = Conexao::Executa($sql);
				
				
				if ($rs->num_rows == 0) {
					$this->modo = Modo_Formulario::Inclusao();
				}
				else {
					$row = $rs->fetch_assoc();
					
					$this->modo_prestacao = $row["ModoPrestacao"];
					
					$this->dt_inicio = FormataDataTexto($row["DataInicio"]);
					$this->dt_conclusao = FormataDataTexto($row["DataConclusao"]);
					
					$this->valor = $row["Valor"];
					
					$this->cod_pais = $row["CodigoPaisDestino"];
					$this->cod_nbs = $row["CodigoNbs"];
					
					$desc = EncontraDescricaoPais($this->cod_pais);
					
					if ($desc == "*") {
						$this->desc_pais = "";
					}
					else {
						$this->desc_pais = $desc;
					}
					
					$desc = EncontraDescricaoNBS($this->cod_nbs);
					
					if ($desc == "*") {
						$this->desc_nbs = "";
					}
					else {
						$this->desc_nbs = $desc;
					}
					
					$rs->close();
					
					if ($this->tipo == Tipo_VA::RAS()) {
						$sql = "SELECT SUM(ValorPago) AS Soma FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . "_item WHERE ID_Operacao = " . $this->id;

					}
					else if ($this->tipo == Tipo_VA::RVS()) {
						$sql = "SELECT SUM(ValorFaturado) As Soma FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . "_item WHERE ID_Operacao = " . $this->id;
					}
					
					$rs = Conexao::Executa($sql);
					
					if ($rs->num_rows > 0) {
						$row = $rs->fetch_assoc();
						
						$this->valor_faturado = $row["Soma"];
						
						$this->valor_restante = $this->valor - $this->valor_faturado;
												
						$this->valor_faturado = str_replace(".", ",", $this->valor_faturado);
						$this->valor_restante = str_replace(".", ",", $this->valor_restante);
					}
					else {
						$this->valor_faturado = "0";
						$this->valor_restante = "0";
					}
					
					$this->valor = str_replace(".", ",", $this->valor);
				}
				
				
				if ($rs) {
					$rs->close();
				}
			}
			
			if ($this->modo == Modo_Formulario::Inclusao()) {
				$this->descricao = "";
				
				$this->cod_pais = "";
				$this->desc_pais = "";
				
				$this->cod_nbs = "";
				$this->desc_nbs = "";
				
				$this->modo_prestacao = "";
				$this->dt_inicio = "";
				$this->dt_conclusao = "";
				
				$this->valor = "";
			}
			
			//Número da NF no EFD.
			$this->nf = $nf;
			
			$this->id_fieldset = "f_" . $this->t_tipo_min . "_op_" . $this->num;
			$this->id_painel_restaura = "f_" . $this->t_tipo_min . "_op_restaura_" . $this->num;
			$this->id_esta_excluido = "f_"  . $this->t_tipo_min . "_op_excluido_" . $this->num;
					
			$this->classe_painel = "painel_formulario_" . $this->t_tipo_min . "_consulta"; 
			
			$this->t_id_esta_excluido = "f_" . $this->t_tipo_min . "_op_" . $this->num . "_r_excluido_";
			$this->funcao_calculo = "CalculaValorRestante(document.getElementById('f_" . $this->t_tipo_min . "_op_valor_" . $this->num . "'), 'f_" . $this->t_tipo_min . "_op_" . $this->num . "_valor_', document.getElementById('f_" . $this->t_tipo_min . "_op_valor_faturado_" . $this->num. "'), document.getElementById('f_" . $this->t_tipo_min . "_op_valor_restante_" . $this->num . "'), '" . $this->t_id_esta_excluido . "');";
		}
		
		public function EscreveFormulario() {
			if ($this->cria_fieldset) {
?>
				<fieldset id="<?php echo($this->id_fieldset); ?>" class="<?php echo($this->classe_painel); ?>">
<?php 
					$this->PainelRestaurar();
			}
					$this->BotaoExcluir();
?>
					<input id="<?php echo($this->id_esta_excluido); ?>" name="<?php echo($this->id_esta_excluido); ?>" type="hidden" value="0" />
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_id_<?php echo($this->num); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_id_<?php echo($this->num); ?>" type="hidden" value="<?php echo($this->id); ?>" />
					
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_cod_nbs_<?php echo($this->num); ?>">NBS:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_cod_nbs_<?php echo($this->num); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_cod_nbs_<?php echo($this->num); ?>" type="text" maxlength="9" onchange="javascript: EncontraDescricao(this, document.getElementById('f_<?php echo($this->t_tipo_min); ?>_op_desc_nbs_<?php echo($this->num); ?>'), 'nbs');"<?php echo($this->cod_nbs != "" ? " value=\"" . $this->cod_nbs . "\"" : ""); ?> />
					<input type="button" value="..." onclick="javascript: AbreBusca(document.getElementById('busca_nbs_op_<?php echo($this->num); ?>'), 'nbs', 'f_<?php echo($this->t_tipo_min); ?>_op_cod_nbs_<?php echo($this->num); ?>', 'f_<?php echo($this->t_tipo_min); ?>_op_desc_nbs_<?php echo($this->num); ?>'); this.onclick = function() { Abre(document.getElementById('busca_nbs_op_<?php echo($this->num); ?>'), 300); }" />
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_desc_nbs_<?php echo($this->num); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_desc_nbs_<?php echo($this->num); ?>" class="f_desc" readonly="readonly" type="text"<?php echo($this->desc_nbs != "" ? " value=\"" . $this->desc_nbs . "\"" : ""); ?> />
					
					<section id="busca_nbs_op_<?php echo($this->num); ?>" class="painel_interno_busca">
					</section>

					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_cod_pais_<?php echo($this->num); ?>">País Destino:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_cod_pais_<?php echo($this->num); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_cod_pais_<?php echo($this->num); ?>" type="text" maxlength="5" onchange="javascript: EncontraDescricao(this, document.getElementById('f_<?php echo($this->t_tipo_min); ?>_op_desc_pais_<?php echo($this->num); ?>'), 'pais');"<?php echo($this->cod_pais != "" ? " value=\"" . $this->cod_pais . "\"" : ""); ?> />
					<input type="button" value="..." onclick="javascript: AbreBusca(document.getElementById('busca_pais_op_<?php echo($this->num); ?>'), 'pais', 'f_<?php echo($this->t_tipo_min); ?>_op_cod_pais_<?php echo($this->num); ?>', 'f_<?php echo($this->t_tipo_min); ?>_op_desc_pais_<?php echo($this->num); ?>'); this.onclick = function() { Abre(document.getElementById('busca_pais_op_<?php echo($this->num); ?>'), 300); }" />
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_desc_pais_<?php echo($this->num); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_desc_pais_<?php echo($this->num); ?>" class="f_desc" readonly="readonly" type="text"<?php echo($this->desc_pais != "" ? " value=\"" . $this->desc_pais . "\"" : ""); ?> />
					
					<section id="busca_pais_op_<?php echo($this->num); ?>" class="painel_interno_busca">
					</section>
					
					<fieldset class="caixa_radio">
						<legend>Modo:</legend>
						
						<input type="radio" <?php echo($this->modo_prestacao == "1" ? "checked=\"checked\" " : ""); ?>name="f_<?php echo($this->t_tipo_min); ?>_op_modo_prestacao_<?php echo($this->num); ?>" id="f_<?php echo($this->t_tipo_min); ?>_op_modo_prestacao_<?php echo($this->num); ?>_1" value="1" class="opcao_radio">
						<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_modo_prestacao_<?php echo($this->num); ?>_1">1 - Transfronteiriço</label>
						
						<input type="radio" <?php echo($this->modo_prestacao == "2" ? "checked=\"checked\" " : ""); ?>name="f_<?php echo($this->t_tipo_min); ?>_op_modo_prestacao_<?php echo($this->num); ?>" id="f_<?php echo($this->t_tipo_min); ?>_op_modo_prestacao_<?php echo($this->num); ?>_2" value="2" class="opcao_radio">
						<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_modo_prestacao_<?php echo($this->num); ?>_2">2 - Consumo no Exterior</label>
						
						<input type="radio" <?php echo($this->modo_prestacao == "4" ? "checked=\"checked\" " : ""); ?>name="f_<?php echo($this->t_tipo_min); ?>_op_modo_prestacao_<?php echo($this->num); ?>" id="f_<?php echo($this->t_tipo_min); ?>_op_modo_prestacao_<?php echo($this->num); ?>_4" value="4" class="opcao_radio">
						<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_modo_prestacao_<?php echo($this->num); ?>_4">4 - Movimento Temporário de Pessoas Físicas</label>
					</fieldset>
					
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_dt_inicio_<?php echo($this->num); ?>">Dt. Início:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_dt_inicio_<?php echo($this->num); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_dt_inicio_<?php echo($this->num); ?>" type="text" onkeydown="javascript: return SomenteNumeros(event);" onkeyup="MascaraData(this, event);"<?php echo($this->dt_inicio != "" ? " value=\"" . $this->dt_inicio . "\"" : ""); ?> />
					
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_dt_conclusao_<?php echo($this->num); ?>">Dt. Conclusão:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_dt_conclusao_<?php echo($this->num); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_dt_conclusao_<?php echo($this->num); ?>" type="text" onkeydown="javascript: return SomenteNumeros(event);" onkeyup="MascaraData(this, event);"<?php echo($this->dt_conclusao != "" ? " value=\"" . $this->dt_conclusao . "\"" : ""); ?> />
					
					<section id="f_<?php echo($this->t_tipo_min); ?>_op_painel_enq_<?php echo($this->num); ?>" class="painel_operacoes_n2">
						<h4>Enquadramentos:</h4>
<?php 
					$i = 1;

					if ($this->modo == Modo_Formulario::Inclusao()) {
						$f_r = new Formulario_Enquadramentos($this->num, 1, $this->tipo);
						
						$f_r->EscreveFormulario();						
					}
					else {
						$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_enquadramento WHERE ID_Operacao = " . $this->id;
						$rs = Conexao::Executa($sql);
												
						if ($rs->num_rows > 0) {
							while($row = $rs->fetch_assoc()) {
								$f_r = new Formulario_Enquadramentos($this->num, $i, $this->tipo, $row["ID"]);
								
								$f_r->EscreveFormulario();
								
								$i++;
							}
							
							$rs->close();
						}
						
						$i--;
					}
?>
					</section>
					
					<div class="painel_formulario_<?php echo($this->t_tipo_min); ?>_consulta_n2">
						<input type="hidden" id="f_<?php echo($this->t_tipo_min); ?>_qtde_op_<?php echo($this->num); ?>_enq" name="f_<?php echo($this->t_tipo_min); ?>_qtde_op_<?php echo($this->num); ?>_enq" value="<?php echo($i); ?>" />
						<a href="#" onclick="javascript: NovoItem(document.getElementById('f_<?php echo($this->t_tipo_min); ?>_op_painel_enq_<?php echo($this->num); ?>'), 'painel_enquadramento', '<?php echo($this->t_tipo_min); ?>', document.getElementById('f_<?php echo($this->t_tipo_min); ?>_qtde_op_<?php echo($this->num); ?>_enq'), 'painel_formulario_<?php echo($this->t_tipo_min); ?>_consulta_n2', 'f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_enq_', <?php echo($this->num); ?>); return false;" id="f_<?php echo($this->t_tipo_min); ?>_novo_enq" >Novo Enquadramento...</a>
					</div>
					
				
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_valor_<?php echo($this->num); ?>">Valor:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_valor_<?php echo($this->num); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_valor_<?php echo($this->num); ?>" type="text" onkeyup="javascript: <?php echo($this->funcao_calculo); ?>" onkeydown="javascript: return SomenteNumeros(event, this, true);"<?php echo($this->valor != "" ? " value=\"" . $this->valor . "\"" : ""); ?> />
					
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_valor_<?php echo($this->num); ?>">Valor já Faturado:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_valor_faturado_<?php echo($this->num); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_valor_faturado_<?php echo($this->num); ?>" type="text" readonly="readonly"<?php echo($this->valor_faturado != "" ? " value=\"" . $this->valor_faturado . "\"" : ""); ?> />
					
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_valor_<?php echo($this->num); ?>">Valor Restante a Faturar:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_valor_restante_<?php echo($this->num); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_valor_restante_<?php echo($this->num); ?>" type="text" readonly="readonly"<?php echo($this->valor_restante != "" ? " value=\"" . $this->valor_restante . "\"" : ""); ?> />
					
					<section id="f_<?php echo($this->t_tipo_min); ?>_op_painel_r_<?php echo($this->num); ?>" class="painel_operacoes_n2">
						<h4>Recebimentos:</h4>
<?php 
					$i = 1;
					
					$t_id_r = "";

					if ($this->tipo == Tipo_VA::RAS()) {
						$t_id_r = "ID_Pagamento";
					}
					else if ($this->tipo == Tipo_VA::RVS()) {
						$t_id_r = "ID_Faturamento";
					}

					if ($this->modo == Modo_Formulario::Inclusao()) {
						$f_r = new Formulario_Recebimentos($this->num, 1, $this->tipo, $nf);
						
						$f_r->EscreveFormulario();						
					}
					else {
						$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . "_item WHERE ID_Operacao = " . $this->id;
						$rs = Conexao::Executa($sql);
												
						if ($rs->num_rows > 0) {
							while($row = $rs->fetch_assoc()) {
								$f_r = new Formulario_Recebimentos($this->num, $i, $this->tipo, $nf, $row[$t_id_r]);
								
								$f_r->EscreveFormulario();
								
								$i++;
							}
							
							$rs->close();
						}
						
						$i--;
					}
?>
					</section>
<?php
?>
					<div class="painel_formulario_<?php echo($this->t_tipo_min); ?>_consulta_n2">
						<input type="hidden" id="f_<?php echo($this->t_tipo_min); ?>_qtde_op_<?php echo($this->num); ?>_r" name="f_<?php echo($this->t_tipo_min); ?>_qtde_op_<?php echo($this->num); ?>_r" value="<?php echo($i); ?>" />
						<a href="#" onclick="javascript: NovoItem(document.getElementById('f_<?php echo($this->t_tipo_min); ?>_op_painel_r_<?php echo($this->num); ?>'), 'painel_recebimento', '<?php echo($this->t_tipo_min); ?>', document.getElementById('f_<?php echo($this->t_tipo_min); ?>_qtde_op_<?php echo($this->num); ?>_r'), 'painel_formulario_<?php echo($this->t_tipo_min); ?>_consulta_n2', 'f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_r_', <?php echo($this->num); ?>, <?php echo($this->nf); ?>); return false;" id="f_<?php echo($this->t_tipo_min); ?>_novo_r" >Novo <?php echo($this->t_tipo_ext); ?>...</a>
					</div>
					
					
					<section id="f_<?php echo($this->t_tipo_min); ?>_op_painel_re_<?php echo($this->num); ?>" class="painel_operacoes_n2">
						<h4>Registros de Exportação:</h4>
<?php 
					$i = 1;

					if ($this->modo == Modo_Formulario::Inclusao()) {
						$f_r = new Formulario_OP_Vinculacao($this->num, 1, Tipo_DIRE::RE(), $this->tipo);
						
						$f_r->EscreveFormulario();						
					}
					else {
						$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_vinculacao WHERE ID_Operacao = " . $this->id . " AND NOT NumeroRE IS NULL";
						$rs = Conexao::Executa($sql);
												
						if ($rs->num_rows > 0) {
							while($row = $rs->fetch_assoc()) {
								$f_r = new Formulario_OP_Vinculacao($this->num, $i, Tipo_DIRE::RE(), $this->tipo, $row["ID"]);
								
								$f_r->EscreveFormulario();
								
								$i++;
							}
							
							$rs->close();
						}
						
						$i--;
					}
?>
					</section>
					
					<div class="painel_formulario_<?php echo($this->t_tipo_min); ?>_consulta_n2">
						<input type="hidden" id="f_<?php echo($this->t_tipo_min); ?>_qtde_op_<?php echo($this->num); ?>_re" name="f_<?php echo($this->t_tipo_min); ?>_qtde_op_<?php echo($this->num); ?>_re" value="<?php echo($i); ?>" />
						<a href="#" onclick="javascript: NovoItem(document.getElementById('f_<?php echo($this->t_tipo_min); ?>_op_painel_re_<?php echo($this->num); ?>'), 'painel_re', '<?php echo($this->t_tipo_min); ?>', document.getElementById('f_<?php echo($this->t_tipo_min); ?>_qtde_op_<?php echo($this->num); ?>_re'), 'painel_formulario_<?php echo($this->t_tipo_min); ?>_consulta_n2', 'f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_re_', <?php echo($this->num); ?>); return false;" id="f_<?php echo($this->t_tipo_min); ?>_novo_re" >Novo RE...</a>
					</div>
					
					<section id="f_<?php echo($this->t_tipo_min); ?>_op_painel_di_<?php echo($this->num); ?>" class="painel_operacoes_n2">
						<h4>Declarações de Importação:</h4>
<?php 
					$i = 1;

					if ($this->modo == Modo_Formulario::Inclusao()) {
						$f_r = new Formulario_OP_Vinculacao($this->num, 1, Tipo_DIRE::DI(), $this->tipo);
						
						$f_r->EscreveFormulario();						
					}
					else {
						$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_vinculacao WHERE ID_Operacao = " . $this->id . " AND NOT NumeroDI IS NULL";
						$rs = Conexao::Executa($sql);
												
						if ($rs->num_rows > 0) {
							while($row = $rs->fetch_assoc()) {
								$f_r = new Formulario_OP_Vinculacao($this->num, $i, Tipo_DIRE::DI(), $this->tipo, $row["ID"]);
								
								$f_r->EscreveFormulario();
								
								$i++;
							}
							
							$rs->close();
						}
						
						$i--;
					}
?>
					</section>
					
					<div class="painel_formulario_<?php echo($this->t_tipo_min); ?>_consulta_n2">
						<input type="hidden" id="f_<?php echo($this->t_tipo_min); ?>_qtde_op_<?php echo($this->num); ?>_di" name="f_<?php echo($this->t_tipo_min); ?>_qtde_op_<?php echo($this->num); ?>_di" value="<?php echo($i); ?>" />
						<a href="#" onclick="javascript: NovoItem(document.getElementById('f_<?php echo($this->t_tipo_min); ?>_op_painel_di_<?php echo($this->num); ?>'), 'painel_di', '<?php echo($this->t_tipo_min); ?>', document.getElementById('f_<?php echo($this->t_tipo_min); ?>_qtde_op_<?php echo($this->num); ?>_di'), 'painel_formulario_<?php echo($this->t_tipo_min); ?>_consulta_n2', 'f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_di_', <?php echo($this->num); ?>); return false;" id="f_<?php echo($this->t_tipo_min); ?>_novo_re" >Nova DI...</a>
					</div>
					
					<span id="f_<?php echo($this->t_tipo_min); ?>_msg_erro_op_<?php echo($this->num); ?>"></span>
<?php
			if ($this->cria_fieldset) {
?>
				</fieldset>
<?php
			}
		}
	}
	
	
	class Formulario_Recebimentos extends FormularioVA_Multiplo {
		public $num_r;
		
		public $data;
		public $num_doc;
		
		public $valor;
		public $valor_ext;
		public $vinculacao;
		
		public $t_id_esta_excluido; //Sem número no final.
		public $funcao_calculo;
		
		public $nf;
		
		public function __construct($num, $num_r, $tipo = -1, $nf = -1, $id = -1) {
			parent::__construct($num, $tipo, $id);
			
			$this->num_r = $num_r;
			
			//Número da NF no EFD.
			$this->nf = $nf;
			
			$t_id = "";

			if ($this->tipo == Tipo_VA::RAS()) {
				$t_id = "ID_Pagamento";
			}
			else if ($this->tipo == Tipo_VA::RVS()) {
				$t_id = "ID_Faturamento";
			}
			
			if ($this->modo == Modo_Formulario::Alteracao()) {
				$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . "_item WHERE " . $t_id . " = " . $this->id;
				$rs = Conexao::Executa($sql);
				
				if ($rs->num_rows == 0) {
					$this->modo = Modo_Formulario::Inclusao();
				}
				else {
					$row = $rs->fetch_assoc();
					
					$this->vinculacao = $row["Vinculacao"];			
									
					if ($this->tipo == Tipo_VA::RAS()) {
						$this->valor = $row["ValorPago"];
						$this->valor = str_replace(".", ",", $this->valor);
						
						$this->valor_ext = $row["ValorPagoRecMantidoExt"];
						$this->valor_ext = str_replace(".", ",", $this->valor_ext);
					}
					else if ($this->tipo == Tipo_VA::RVS()) {
						$this->valor = $row["ValorFaturado"];
						$this->valor = str_replace(".", ",", $this->valor);
						
						$this->valor_ext = $row["ValorMantidoExt"];
						$this->valor_ext = str_replace(".", ",", $this->valor_ext);
					}
					
					$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd_ext . " WHERE ID = " . $row[$t_id];
					
					$rs->close();
					$rs = Conexao::Executa($sql);
					
					if ($rs->num_rows == 0) {
						$this->data = "";
						$this->num_doc = "";
					}
					else {
						$row = $rs->fetch_assoc();
						
						if ($this->tipo == Tipo_VA::RAS()) {
							$this->data = FormataDataTexto($row["DataPagamento"]);
							$this->num_doc = $row["NumeroPagamento"];
						}
						else if ($this->tipo == Tipo_VA::RVS()) {
							$this->data = FormataDataTexto($row["DataFatura"]);
							$this->num_doc = $row["NumeroFatura"];
						}
					}
				}
				
				if ($rs) {
					$rs->close();
				}
			}
			
			if ($this->modo == Modo_Formulario::Inclusao()) {
				$this->data = "";
				
				if ($this->nf != -1) {
					$this->num_doc = $this->nf;
				}
				else {
					$this->num_doc = "";
				}
				
				$this->valor = "";
				$this->valor_ext = "";
				
				$this->vinculacao = "";
			}
						
			$this->t_id_esta_excluido = "f_" . $this->t_tipo_min . "_op_" . $this->num . "_r_excluido_";
			$this->id_esta_excluido = $this->t_id_esta_excluido . $this->num_r;
			$this->id_painel_restaura = "f_" . $this->t_tipo_min . "_op_restaura_" . $this->num . "_r_" . $this->num_r;
			$this->id_fieldset = "f_" . $this->t_tipo_min . "_op_" . $this->num . "_r_" . $this->num_r;
			
			$this->classe_painel = "painel_formulario_" . $this->t_tipo_min . "_consulta_n2"; 
			
			$this->funcao_calculo = "CalculaValorRestante(document.getElementById('f_" . $this->t_tipo_min . "_op_valor_" . $this->num . "'), 'f_" . $this->t_tipo_min . "_op_" . $this->num . "_valor_', document.getElementById('f_" . $this->t_tipo_min . "_op_valor_faturado_" . $this->num. "'), document.getElementById('f_" . $this->t_tipo_min . "_op_valor_restante_" . $this->num . "'), '" . $this->t_id_esta_excluido . "');";
			$this->executar_apos_restaurar = $this->funcao_calculo;
			$this->executar_apos_excluir = $this->funcao_calculo;
		}
		
		
		public function EscreveFormulario() {
			if ($this->cria_fieldset) {
				$this->PainelRestaurar();
?>
				<fieldset id="<?php echo($this->id_fieldset); ?>" class="<?php echo($this->classe_painel); ?>">
<?php 
			}
			$this->BotaoExcluir();
?>
					<input id="<?php echo($this->id_esta_excluido); ?>" name="<?php echo($this->id_esta_excluido); ?>" type="hidden" value="0" />
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_r_id_<?php echo($this->num_r); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_r_id_<?php echo($this->num_r); ?>" type="hidden" value="<?php echo($this->id); ?>" />

					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_r_data_<?php echo($this->num_r); ?>">Data do <?php echo($this->t_tipo_ext); ?>:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_r_data_<?php echo($this->num_r); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_r_data_<?php echo($this->num_r); ?>" type="text" onkeydown="javascript: return SomenteNumeros(event);" onkeyup="MascaraData(this, event);"<?php echo($this->data != "" ? " value=\"" . $this->data . "\"" : ""); ?> />
					
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_r_num_doc_<?php echo($this->num_r); ?>">Número do Documento:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_r_num_doc_<?php echo($this->num_r); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_r_num_doc_<?php echo($this->num_r); ?>" type="text" <?php echo($this->num_doc != "" ? " value=\"" . $this->num_doc . "\"" : ""); ?> />
					
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_valor_<?php echo($this->num_r); ?>">Valor:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_valor_<?php echo($this->num_r); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_valor_<?php echo($this->num_r); ?>" type="text" onkeyup="javascript: <?php echo($this->funcao_calculo); ?>" onkeydown="javascript: return SomenteNumeros(event, this, true);"<?php echo($this->valor != "" ? " value=\"" . $this->valor . "\"" : ""); ?> />
					
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_valor_ext_<?php echo($this->num_r); ?>">Valor Mantido no Exterior:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_valor_ext_<?php echo($this->num_r); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_valor_ext_<?php echo($this->num_r); ?>" type="text" onkeydown="javascript: return SomenteNumeros(event, this, true);"<?php echo($this->valor_ext != "" ? " value=\"" . $this->valor_ext . "\"" : ""); ?> />
					
					<span id="f_<?php echo($this->t_tipo_min); ?>_msg_erro_op_<?php echo($this->num); ?>_r_<?php echo($this->num_r); ?>"></span>
<?php
			if ($this->cria_fieldset) {
?>
				</fieldset>
<?php
			}
		}
	}
	
	
	class Formulario_Enquadramentos extends FormularioVA_Multiplo {
		public $num_r;
		
		public $cod_enq;
		public $desc_enq;
		
		public $num_rc;
		
		public function __construct($num, $num_r, $tipo = -1, $id = -1) {
			parent::__construct($num, $tipo, $id);
			
			$this->num_r = $num_r;
			
			
			if ($this->modo == Modo_Formulario::Alteracao()) {
				$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_enquadramento WHERE ID = " . $this->id;
				$rs = Conexao::Executa($sql);
				
				if ($rs->num_rows == 0) {
					$this->modo = Modo_Formulario::Inclusao();
				}
				else {
					$row = $rs->fetch_assoc();
					
					$this->num_rc = $row["NumeroRc"];					
					
					$this->cod_enq = $row["CodigoEnquadramento"];	
					
					$desc = EncontraDescricaoEnquadramento($this->cod_enq);
					
					if ($desc == "*") {
						$this->desc_enq = "";
					}
					else {
						$this->desc_enq = $desc;
					}
				}
				
				if ($rs) {
					$rs->close();
				}
			}
			
			if ($this->modo == Modo_Formulario::Inclusao()) {
				$this->num_rc = "";
				
				$this->cod_enq = "";
				$this->desc_enq = "";
			}
			
			$this->t_id_esta_excluido = "f_" . $this->t_tipo_min . "_op_" . $this->num . "_enq_excluido_";
			$this->id_esta_excluido = $this->t_id_esta_excluido . $this->num_r;
			
			$this->id_painel_restaura = "f_" . $this->t_tipo_min . "_op_restaura_" . $this->num . "_enq_" . $this->num_r;
			$this->id_fieldset = "f_" . $this->t_tipo_min . "_op_" . $this->num . "_enq_" . $this->num_r;
			
			$this->classe_painel = "painel_formulario_" . $this->t_tipo_min . "_consulta_n2"; 
		}
		
		
		public function EscreveFormulario() {
			if ($this->cria_fieldset) {
				$this->PainelRestaurar();
?>
				<fieldset id="<?php echo($this->id_fieldset); ?>" class="<?php echo($this->classe_painel); ?>">
<?php 
			}
			$this->BotaoExcluir();
?>
					<input id="<?php echo($this->id_esta_excluido); ?>" name="<?php echo($this->id_esta_excluido); ?>" type="hidden" value="0" />
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_enq_id_<?php echo($this->num_r); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_enq_id_<?php echo($this->num_r); ?>" type="hidden" value="<?php echo($this->id); ?>" />

					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_enq_cod_<?php echo($this->num_r); ?>">Cód. Enquad.:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_enq_cod_<?php echo($this->num_r); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_enq_cod_<?php echo($this->num_r); ?>" type="text" maxlength="3" onchange="javascript: EncontraDescricao(this, document.getElementById('f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_enq_desc_<?php echo($this->num_r); ?>'), 'enq');"<?php echo($this->cod_enq != "" ? " value=\"" . $this->cod_enq . "\"" : ""); ?> />
					<input type="button" value="..." onclick="javascript: AbreBusca(document.getElementById('busca_enq_op_<?php echo($this->num); ?>_<?php echo($this->num_r); ?>'), 'enq', 'f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_enq_cod_<?php echo($this->num_r); ?>', 'f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_enq_desc_<?php echo($this->num_r); ?>', <?php echo($this->tipo); ?>); this.onclick = function() { Abre(document.getElementById('busca_enq_op_<?php echo($this->num); ?>_<?php echo($this->num_r); ?>'), 300); }" />
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_enq_desc_<?php echo($this->num_r); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_enq_desc_<?php echo($this->num_r); ?>" class="f_desc" readonly="readonly" type="text"<?php echo($this->desc_enq != "" ? " value=\"" . $this->desc_enq . "\"" : ""); ?> />
					
					<section id="busca_enq_op_<?php echo($this->num); ?>_<?php echo($this->num_r); ?>" class="painel_interno_busca">
					</section>
					
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_enq_num_rc_<?php echo($this->num_r); ?>">Número RC:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_enq_num_rc_<?php echo($this->num_r); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num); ?>_enq_num_rc_<?php echo($this->num_r); ?>" type="text" maxlength="9" onkeydown="javascript: return SomenteNumeros(event);" onkeyup="MascaraNumeroRC(this, event);"<?php echo($this->num_rc != "" ? " value=\"" . $this->num_rc . "\"" : ""); ?> />
					
					<span id="f_<?php echo($this->t_tipo_min); ?>_msg_erro_op_<?php echo($this->num); ?>_enq_<?php echo($this->num_r); ?>"></span>

<?php
			if ($this->cria_fieldset) {
?>
				</fieldset>
<?php
			}
		}
	}
	
	
	class Formulario_OP_Vinculacao extends FormularioVA_Multiplo {
		public $num_r;
		
		public $tipo_dire;
		
		public $di;
		public $re;
				
		public $t_campos;
				
		public function __construct($num, $num_r, $tipo_dire, $tipo = -1, $id = -1) {
			parent::__construct($num, $tipo, $id);
			
			$this->num_r = $num_r;
			
			$this->tipo_dire = $tipo_dire;
			
			if ($this->tipo_dire == Tipo_DIRE::DI()) {
				$this->t_campos = "_di_";
			}
			else if ($this->tipo_dire == Tipo_DIRE::RE()) {
				$this->t_campos = "_re_";
			}
			
			if ($this->modo == Modo_Formulario::Alteracao()) {
				$sql = "SELECT * FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd . "_operacao_vinculacao WHERE ID = " . $this->id;
				$rs = Conexao::Executa($sql);
				
				if ($rs->num_rows == 0) {
					$this->modo = Modo_Formulario::Inclusao();
				}
				else {
					$row = $rs->fetch_assoc();
					
					$this->di = $row["NumeroDI"];
					$this->re = $row["NumeroRE"];
				}
				
				if ($rs) {
					$rs->close();
				}
			}
			
			if ($this->modo == Modo_Formulario::Inclusao()) {
				$this->di = "";
				$this->re = "";
			}
			
			$this->t_id_esta_excluido = "f_" . $this->t_tipo_min . "_op_" . $this->num . $this->t_campos . "excluido_";
			$this->id_esta_excluido = $this->t_id_esta_excluido . $this->num_r;
			
			$this->id_painel_restaura = "f_" . $this->t_tipo_min . "_op_restaura_" . $this->num . $this->t_campos . $this->num_r;
			$this->id_fieldset = "f_" . $this->t_tipo_min . "_op_" . $this->num . $this->t_campos . $this->num_r;
			
			$this->classe_painel = "painel_formulario_" . $this->t_tipo_min . "_consulta_n2"; 
		}
		
		
		public function EscreveFormulario() {
			if ($this->cria_fieldset) {
				$this->PainelRestaurar();
?>
				<fieldset id="<?php echo($this->id_fieldset); ?>" class="<?php echo($this->classe_painel); ?>">
<?php 
			}
			$this->BotaoExcluir();
?>
					<input id="<?php echo($this->id_esta_excluido); ?>" name="<?php echo($this->id_esta_excluido); ?>" type="hidden" value="0" />
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num . $this->t_campos); ?>id_<?php echo($this->num_r); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num . $this->t_campos); ?>id_<?php echo($this->num_r); ?>" type="hidden" value="<?php echo($this->id); ?>" />
					
					<section id="busca<?php echo($this->t_campos); ?>op_<?php echo($this->num); ?>_<?php echo($this->num_r); ?>" class="painel_interno_busca">
					</section>
<?php 
			if ($this->tipo_dire == Tipo_DIRE::DI()) {
?>
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num . $this->t_campos); ?>num_<?php echo($this->num_r); ?>">Número DI:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num . $this->t_campos); ?>num_<?php echo($this->num_r); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num . $this->t_campos); ?>num_<?php echo($this->num_r); ?>" type="text" maxlength="12" onkeydown="javascript: return SomenteNumeros(event);" onkeyup="MascaraNumeroDI(this, event);"<?php echo($this->di != "" ? " value=\"" . $this->di . "\"" : ""); ?> />
<?php 
			}
			else if ($this->tipo_dire == Tipo_DIRE::RE()) {
?>
					<label class="nome_campo" for="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num . $this->t_campos); ?>num_<?php echo($this->num_r); ?>">Número RE:</label>
					<input id="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num . $this->t_campos); ?>num_<?php echo($this->num_r); ?>" name="f_<?php echo($this->t_tipo_min); ?>_op_<?php echo($this->num . $this->t_campos); ?>num_<?php echo($this->num_r); ?>" type="text" maxlength="13" onkeydown="javascript: return SomenteNumeros(event);" onkeyup="MascaraNumeroRE(this, event);"<?php echo($this->re != "" ? " value=\"" . $this->re . "\"" : ""); ?> />
<?php 
			}
?>
					
					<span id="f_<?php echo($this->t_tipo_min); ?>_msg_erro_op_<?php echo($this->num . $this->t_campos . $this->num_r); ?>"></span>

<?php
			if ($this->cria_fieldset) {
?>
				</fieldset>
<?php
			}
		}
	}
?>