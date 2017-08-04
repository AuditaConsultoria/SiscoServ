<?php

	require_once("funcoes.php");
	require_once("formularios.php");
	
	/*
	   Classe abstrata para tabelas de busca.
	*/
	class TabelaBusca {
		protected $prefixo_campo;
		protected $prefixo_tabela;
		protected $prefixo_filtro;
		protected $prefixo_botao;
		
		protected $total_registros;
		
		protected $nome_tabela;
		
		protected $resultado;
		protected $descricao;
		
		public $classe;
		
		public $botao_editar;
		public $botao_excluir;
		public $checkbox;
		
		public $botao_fechar;
		public $acrescenta_js;
		
		/* Número aleatório a ser adicionado aos nomes dos objetos,
		 para evitar repetições, caso a função seja usada duas vezes
		 na mesma página. */
		public $id_nome;
		
		public function __construct($num_random, $resultado, $descricao) {
			$this->prefixo_campo = "";
			$this->prefixo_tabela = "";
			$this->prefixo_filtro = "";
			$this->prefixo_botao = "";
			
			$this->total_registros = 0;
			
			$this->id_nome = $num_random;
			$this->resultado = $resultado;
			$this->descricao = $descricao;
			
			$this->botao_editar = true;
			$this->botao_excluir = false;
			
			$this->checkbox = false;
			
			$this->botao_fechar = true;
			$this->acrescenta_js = false;
			
			$this->classe = "t_busca";
		}
		
		/*
			Abre a tabela.
		*/
		protected function EscreveHeader($campos, $tam_campos) {
			$nome_campo_ultima = $this->prefixo_campo . "_ultima" . $this->id_nome;
			$nome_campo_busca = $this->prefixo_campo . $this->id_nome;
			
			$nome_botao = $this->prefixo_botao . $this->id_nome;
			
			$nome_campo_filtro = $this->prefixo_filtro . $this->id_nome;
			
			if ($this->botao_fechar) {
?>
			<input type="button" name="<?php echo($nome_botao); ?>" id="<?php echo($nome_botao); ?>" value="x" onclick="Fecha(document.getElementById('<?php echo($this->nome_tabela); ?>').parentNode);" />
<?php } ?>
			<input type="hidden" name="<?php echo($nome_campo_ultima); ?>" id="<?php echo($nome_campo_ultima); ?>" />
			<label for="<?php echo($nome_campo_busca); ?>">Filtrar:</label>
			<input type="text" name="<?php echo($nome_campo_busca); ?>" id="<?php echo($nome_campo_busca); ?>" onkeyup="javascript: FiltraTabela(document.getElementById('<?php echo($this->nome_tabela); ?>'), this.value, document.getElementById('<?php echo($nome_campo_ultima); ?>'), document.getElementById('<?php echo($nome_campo_filtro); ?>'));" />
			<span>Exibindo <span id="<?php echo($nome_campo_filtro); ?>"><?php echo($this->total_registros); ?></span> de <span><?php echo($this->total_registros); ?></span> registros.</span>
			<span id="tb_msg_erro" style="display: none;"></span>
			<table id="<?php echo($this->nome_tabela); ?>" class="<?php echo($this->classe); ?>">
				<thead class="<?php echo($this->classe); ?>_header">
					<tr>
<?php 
			if($this->checkbox) {
?>
						<th style="width: 72px;">&nbsp;</th>
<?php 		
			}
			
			$i = 0;
			
			foreach ($campos as $campo) {
?>
						<th style="width: <?php echo($tam_campos[$i]); ?>;"><?php echo($campo->name); ?></th>
<?php 		
				$i++;
			}
			
			if ($this->botao_editar) {
?>
						<th style="width: 72px;">&nbsp;</th>
<?php
			}
			if ($this->botao_excluir) {
?>
						<th style="width: 72px;">&nbsp;</th>
<?php
			}
?>
					</tr>
				</thead>
				<tbody class="<?php echo($this->classe); ?>_corpo">
<?php
		}
		
		/*
			Fecha a tabela.
		*/
		protected function EscreveFooter() {
?>
				</tbody>
			</table>
<?php
			if ($this->acrescenta_js) {
?>
			<script type="text/JavaScript">
			<!--
				sorttable.makeSortable(document.getElementById("<?php echo($this->nome_tabela); ?>"));
			//-->
			</script>
<?php
				
			}
		}
	}

	class TabelaBuscaPaises extends TabelaBusca {

		public function __construct($num_random, $resultado, $descricao) {
			parent::__construct($num_random, $resultado, $descricao);
			
			$this->prefixo_campo = "campo_busca_pais";
			$this->prefixo_tabela = "tabela_busca_pais";
			$this->prefixo_filtro = "filtro_busca_pais";
			$this->prefixo_botao = "botao_busca_pais";
			
			$this->nome_tabela = $this->prefixo_tabela . $this->id_nome;
		}
		
		public function EscreveTabela() {
			//$this->id_nome = $_GET["num_random"];
			
			$sql = "SELECT codigo AS Código, nome AS Nome FROM tx_pais ORDER BY nome";
			$rs = Conexao::Executa($sql);
			
			$this->total_registros = $rs->num_rows;
				
			$campos = $rs->fetch_fields();
			$tam_campos = array("83px", "265px");
	
			parent::EscreveHeader($campos, $tam_campos);
			

			while ($row = $rs->fetch_assoc()) {
?>
					<tr>
<?php 
				$i = 0;
				foreach ($row as $campo) {
?>
						<td style="width: <?php echo($tam_campos[$i]); ?>;"><?php echo(htmlspecialchars($campo, ENT_COMPAT)); ?></td>
<?php
					$i++;
				}
?>
						<td style="widht: 72px; font-size: 0.5em;"><a href="#" onclick="javascript: document.getElementById('<?php echo($this->resultado); ?>').value = '<?php echo(htmlspecialchars($row["Código"], ENT_COMPAT)); ?>'; DescricaoLocalizado(document.getElementById('<?php echo($this->descricao); ?>'), '<?php echo(htmlspecialchars($row["Nome"], ENT_COMPAT)); ?>'); Fecha(document.getElementById('<?php echo($this->nome_tabela); ?>').parentNode); return false;">Selecionar</a></td>
					</tr>
				
<?php
	}
			
			$rs->close();
			
			parent::EscreveFooter();
		}
	}
	
	
	class TabelaBuscaMoeda extends TabelaBusca {

		public function __construct($num_random, $resultado, $descricao) {
			parent::__construct($num_random, $resultado, $descricao);
			
			$this->prefixo_campo = "campo_busca_moeda";
			$this->prefixo_tabela = "tabela_busca_moeda";
			$this->prefixo_filtro = "filtro_busca_moeda";
			$this->prefixo_botao = "botao_busca_moeda";
			
			$this->nome_tabela = $this->prefixo_tabela . $this->id_nome;
		}
		
		public function EscreveTabela() {
			//$this->id_nome = $_GET["num_random"];
			
			$sql = "SELECT tm.codigo AS Código, tm.descricao AS Nome, tp.nome AS pais FROM tx_moeda AS tm LEFT JOIN tx_pais AS tp ON tm.cod_pais = tp.codigo ORDER BY tm.descricao";
			$rs = Conexao::Executa($sql);
			
			$this->total_registros = $rs->num_rows;
				
			$campos = $rs->fetch_fields();
			$tam_campos = array("83px", "132px", "132px");
	
			parent::EscreveHeader($campos, $tam_campos);
			

			while ($row = $rs->fetch_assoc()) {
?>
					<tr>
<?php 
				$i = 0;
				foreach ($row as $campo) {
?>
						<td style="width: <?php echo($tam_campos[$i]); ?>;"><?php echo(htmlspecialchars($campo, ENT_COMPAT)); ?></td>
<?php
					$i++;
				}
?>
						<td style="widht: 72px; font-size: 0.5em;"><a href="#" onclick="javascript: document.getElementById('<?php echo($this->resultado); ?>').value = '<?php echo(htmlspecialchars($row["Código"], ENT_COMPAT)); ?>'; DescricaoLocalizado(document.getElementById('<?php echo($this->descricao); ?>'), '<?php echo(htmlspecialchars($row["Nome"], ENT_COMPAT)); ?>'); Fecha(document.getElementById('<?php echo($this->nome_tabela); ?>').parentNode); return false;">Selecionar</a></td>
					</tr>
				
<?php
	}
			
			$rs->close();
			
			parent::EscreveFooter();
		}
	}
	
	class TabelaBuscaNBS extends TabelaBusca {

		public function __construct($num_random, $resultado, $descricao) {
			parent::__construct($num_random, $resultado, $descricao);
			
			$this->prefixo_campo = "campo_busca_nbs";
			$this->prefixo_tabela = "tabela_busca_nbs";
			$this->prefixo_filtro = "filtro_busca_nbs";
			$this->prefixo_botao = "botao_busca_nbs";
			
			$this->nome_tabela = $this->prefixo_tabela . $this->id_nome;
		}
		
		public function EscreveTabela() {
			//$this->id_nome = $_GET["num_random"];
			
			$sql = "SELECT tm.codigo AS Código, tm.descricao AS Nome FROM tx_nbs AS tm WHERE LENGTH(codigo) = 9 ORDER BY tm.codigo";
			$rs = Conexao::Executa($sql);
			
			$this->total_registros = $rs->num_rows;
				
			$campos = $rs->fetch_fields();
			$tam_campos = array("83px", "265px");
	
			parent::EscreveHeader($campos, $tam_campos);
			

			while ($row = $rs->fetch_assoc()) {
?>
					<tr>
<?php 
				$i = 0;
				foreach ($row as $campo) {
?>
						<td style="width: <?php echo($tam_campos[$i]); ?>;"><?php echo(htmlspecialchars($campo, ENT_COMPAT)); ?></td>
<?php
					$i++;
				}
?>
						<td style="widht: 72px; font-size: 0.5em;"><a href="#" onclick="javascript: document.getElementById('<?php echo($this->resultado); ?>').value = '<?php echo(htmlspecialchars($row["Código"], ENT_COMPAT)); ?>'; DescricaoLocalizado(document.getElementById('<?php echo($this->descricao); ?>'), '<?php echo(htmlspecialchars($row["Nome"], ENT_COMPAT)); ?>'); Fecha(document.getElementById('<?php echo($this->nome_tabela); ?>').parentNode); return false;">Selecionar</a></td>
					</tr>
				
<?php
	}
			
			$rs->close();
			
			parent::EscreveFooter();
		}
	}
	
	class TabelaBuscaEnquadramento extends TabelaBusca {
		
		public $tipo;
		
		public $t_tipo;

		public function __construct($num_random, $resultado, $descricao, $tipo) {
			parent::__construct($num_random, $resultado, $descricao);
			
			$this->prefixo_campo = "campo_busca_enq";
			$this->prefixo_tabela = "tabela_busca_enq";
			$this->prefixo_filtro = "filtro_busca_enq";
			$this->prefixo_botao = "botao_busca_enq";
			
			$this->nome_tabela = $this->prefixo_tabela . $this->id_nome;
			
			if ($tipo == -1) {
				$this->tipo = Tipo_VA::NENHUM();
				
				$this->t_tipo = "";
				$this->t_tipo_min = "";
			}
			else {
				$this->tipo = $tipo;
				
				if ($this->tipo == Tipo_VA::RVS()) {
					$this->t_tipo = "venda";
				}
				else if ($this->tipo == Tipo_VA::RAS()) {
					$this->t_tipo = "aquisicao";
				}
			}
		}
		
		public function EscreveTabela() {
			//$this->id_nome = $_GET["num_random"];
			
			$sql = "SELECT tm.codigo AS Código, tm.descricao AS Nome FROM tx_enquadramento AS tm";
			
			if ($this->tipo != Tipo_VA::NENHUM()) {
				$sql .= " WHERE modulo_" . $this->t_tipo . " = '1'";
			}
			
			$sql .= " ORDER BY tm.codigo";
			$rs = Conexao::Executa($sql);
			
			$this->total_registros = $rs->num_rows;
				
			$campos = $rs->fetch_fields();
			$tam_campos = array("83px", "265px");
	
			parent::EscreveHeader($campos, $tam_campos);
			

			while ($row = $rs->fetch_assoc()) {
?>
					<tr>
<?php 
				$i = 0;
				foreach ($row as $campo) {
?>
						<td style="width: <?php echo($tam_campos[$i]); ?>;"><?php echo(htmlspecialchars($campo, ENT_COMPAT)); ?></td>
<?php
					$i++;
				}
?>
						<td style="widht: 72px; font-size: 0.5em;"><a href="#" onclick="javascript: document.getElementById('<?php echo($this->resultado); ?>').value = '<?php echo(htmlspecialchars($row["Código"], ENT_COMPAT)); ?>'; DescricaoLocalizado(document.getElementById('<?php echo($this->descricao); ?>'), '<?php echo(htmlspecialchars($row["Nome"], ENT_COMPAT)); ?>'); Fecha(document.getElementById('<?php echo($this->nome_tabela); ?>').parentNode); return false;">Selecionar</a></td>
					</tr>
				
<?php
	}
			
			$rs->close();
			
			parent::EscreveFooter();
		}
	}
	
	class TabelaBuscaParticipantesInterna extends TabelaBusca {

		public $nome;
		public $endereco;
		public $cod_pais;
		public $desc_pais;
		public $nif;
		public $motivo_nif;
		public $nif_em_branco;
	
		public function __construct($num_random, $resultado, $nome, $endereco, $cod_pais, $desc_pais, $nif, $motivo_nif, $nif_em_branco) {
			parent::__construct($num_random, $resultado, $nome);
			
			$this->prefixo_campo = "campo_busca_moeda";
			$this->prefixo_tabela = "tabela_busca_moeda";
			$this->prefixo_filtro = "filtro_busca_moeda";
			$this->prefixo_botao = "botao_busca_moeda";
			
			$this->nome = $nome;
			$this->endereco = $endereco;
			$this->cod_pais = $cod_pais;
			$this->desc_pais = $desc_pais;
			$this->nif = $nif;
			$this->motivo_nif = $motivo_nif;
			$this->nif_em_branco = $nif_em_branco;
			
			$this->nome_tabela = $this->prefixo_tabela . $this->id_nome;
		}
		
		public function EscreveTabela() {
			//$this->id_nome = $_GET["num_random"];
			
			$sql = "SELECT zp.ID AS Código, zp.Nome AS Nome, CONCAT(zp.Pais, ' - ', tp.nome) As País, Endereco, Pais, Nif, MotivoNif From " . $_SESSION["banco"] . ".zx_participantes AS zp LEFT JOIN tx_pais AS tp ON zp.Pais = tp.codigo ORDER BY zp.ID DESC";
			$rs = Conexao::Executa($sql);
						
			$this->total_registros = $rs->num_rows;
				
			$campos = $rs->fetch_fields();
			$camposx = array($campos[0], $campos[1], $campos[2]);
			$tam_campos = array("83px", "132px", "132px");
	
			parent::EscreveHeader($camposx, $tam_campos);
			

			while ($row = $rs->fetch_assoc()) {
?>
					<tr>
<?php 
				$i = 0;
				foreach ($row as $campo) {
					if ($i <= 2) {
?>
						<td style="width: <?php echo($tam_campos[$i]); ?>;"><?php echo(htmlspecialchars($campo, ENT_COMPAT)); ?></td>
<?php
					}
					$i++;
				}
				
				$funcao_js = "SelecionaCodigoPart('" . $this->resultado . "', '" . $this->nome . "', '" . $this->endereco . "', '" . $this->cod_pais . "', '" . $this->desc_pais . "', '" . $this->nif. "', '" . $this->motivo_nif . "', '" . $this->nif_em_branco . "', '" . 
					htmlspecialchars($row["Código"], ENT_COMPAT) . "', '" . htmlspecialchars($row["Nome"], ENT_COMPAT) . "', '" . htmlspecialchars($row["Endereco"], ENT_COMPAT) . "', '" . htmlspecialchars($row["Pais"], ENT_COMPAT) . "', '" . htmlspecialchars($row["Nif"], ENT_COMPAT) . "', '" . htmlspecialchars($row["MotivoNif"], ENT_COMPAT) . "')";
?>
						<td style="widht: 72px; font-size: 0.5em;"><a href="#" onclick="javascript: <?php echo($funcao_js); ?>; Fecha(document.getElementById('<?php echo($this->nome_tabela); ?>').parentNode); return false;">Selecionar</a></td>
					</tr>
				
<?php
	}
			
			$rs->close();
			
			parent::EscreveFooter();
		}
	}
	
	
	class TabelaBuscaParticipantes extends TabelaBusca {

		public function __construct($num_random, $resultado, $descricao) {
			parent::__construct($num_random, $resultado, $descricao);
			
			$this->prefixo_campo = "campo_busca_part";
			$this->prefixo_tabela = "tabela_busca_part";
			$this->prefixo_filtro = "filtro_busca_part";
			$this->prefixo_botao = "botao_busca_part";
			
			$this->nome_tabela = $this->prefixo_tabela . $this->id_nome;
		}
		
		public function EscreveTabela() {
			//$this->id_nome = $_GET["num_random"];
			
			$sql = "SELECT zp.ID AS Código, zp.Nome AS Nome, zp.Endereco As Endereço, CONCAT(zp.Pais, ' - ', tp.nome) As País, zp.NIF From " . $_SESSION["banco"] . ".zx_participantes AS zp LEFT JOIN tx_pais AS tp ON zp.Pais = tp.codigo ORDER BY zp.ID DESC";
			$rs = Conexao::Executa($sql);
			
			$this->total_registros = $rs->num_rows;
				
			$campos = $rs->fetch_fields();
			//$tam_campos = array("83px", "100px", "100px", "100px", "50px");
			$tam_campos = array("auto", "auto", "auto", "auto", "auto");
	
			parent::EscreveHeader($campos, $tam_campos);
			

			while ($row = $rs->fetch_assoc()) {
?>
					<tr>
<?php 
				$i = 0;
				foreach ($row as $campo) {
?>
						<td><?php echo(htmlspecialchars($campo, ENT_COMPAT)); ?></td>
<?php
					$i++;
				}
?>
						<td style="widht: 72px; font-size: 0.7em;"><a href="<?php echo(Enderecos::Site()) ?>participantes.php?id=<?php echo(htmlspecialchars($row["Código"], ENT_COMPAT)); ?>">Editar</a></td>
					</tr>
				
<?php
	}
			
			$rs->close();
			
			parent::EscreveFooter();
		}
	}
	
	class Tipo_Filtro_Export {
		private static $t_nenhum = -1;
		
		private static $t_sem_exportar = 0;
		private static $t_sem_retorno = 1;
		private static $t_modificado_apos_exportacao = 2;
		private static $t_modificado_apos_retorno = 3;
		
		public static function SemExportar() {
			return self::$t_sem_exportar;
		}
		
		public static function SemRetorno() {
			return self::$t_sem_retorno;
		}
		
		public static function ModificadoAposExportacao() {
			return self::$t_modificado_apos_exportacao;
		}
		
		public static function ModificadoAposRetorno() {
			return self::$t_modificado_apos_retorno;
		}
		
		public static function NENHUM() {
			return self::$t_nenhum;
		}
	}	
	
	class TabelaBuscaVA extends TabelaBusca {
		public $tipo;
		
		public $t_tipo;
		public $t_min;
		
		public $nome_tabela_bd;

		public $t_part;
		
		public $sufixo_id;
		public $tipo_filtro_export;
		
		public $ano;

		public function __construct($tipo, $num_random, $resultado, $descricao, $sufixo_id = "", $tipo_filtro_export = -1) {
			parent::__construct($num_random, $resultado, $descricao);
			
			$this->botao_excluir = true;
			
			$this->sufixo_id = $sufixo_id;
			$this->tipo_filtro_export = $tipo_filtro_export;
			
			$this->ano = 0;
			
			if ($tipo == -1) {
				$this->tipo = Tipo_VA::NENHUM();
				
				$this->t_tipo = "";
				$this->t_tipo_min = "";
			}
			else {
				$this->tipo = $tipo;
				
				if ($this->tipo == Tipo_VA::RVS()) {
					$this->t_tipo = "RVS";
					$this->t_tipo_min = "rvs";
					
					$this->nome_tabela_bd = "venda_rvs";
					
					$this->t_part = "Adquirente";
				}
				else if ($this->tipo == Tipo_VA::RAS()) {
					$this->t_tipo = "RAS";
					$this->t_tipo_min = "ras";
					
					$this->nome_tabela_bd = "aquis_ras";
					
					$this->t_part = "Vendedor";
				}
			}
			
			$this->prefixo_campo = "campo_busca_" . t_tipo_min;
			$this->prefixo_tabela = "tabela_busca_" . t_tipo_min;
			$this->prefixo_filtro = "filtro_busca_" . t_tipo_min;
			$this->prefixo_botao = "botao_busca_" . t_tipo_min;
			
			$this->nome_tabela = $this->prefixo_tabela . $this->id_nome;
		}
		
		public function EscreveTabela() {
			//$this->id_nome = $_GET["num_random"];
			
			$sql = "SELECT x.ID AS Código, '' AS Descrição, x.Nome" . $this->t_part . " AS " . $this->t_part . ", CONCAT(x.CodigoPais" . $this->t_part . ", ' - ', tp.nome) As País, x_op.Data, x_op.`Valor Op.`, CONCAT(x.CodigoMoeda, ' - ', tm.descricao) As Moeda From " . 
				$_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd ." AS x LEFT JOIN tx_pais AS tp ON x.CodigoPais" . $this->t_part . " = tp.codigo LEFT JOIN tx_moeda AS tm ON x.CodigoMoeda = tm.codigo ";
			if ($this->ano == 0 || !(is_numeric($this->ano))) {
				$sql .= "LEFT JOIN (SELECT ID_" . $this->t_tipo . ", MIN(DataInicio) AS Data, SUM(Valor) AS 'Valor Op.' FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd ."_operacao GROUP BY ID_" . $this->t_tipo . ") ";
			}
			else {
				$sql .= "INNER JOIN (SELECT ID_" . $this->t_tipo . ", MIN(DataInicio) AS Data, SUM(Valor) AS 'Valor Op.' FROM " . $_SESSION["banco"] . ".zx_" . $this->nome_tabela_bd ."_operacao WHERE YEAR(DataInicio) = " . $this->ano . " GROUP BY ID_" . $this->t_tipo . ") ";
			}
			$sql .= "AS x_op ON x.ID = x_op.ID_" . $this->t_tipo . "";
				
			if ($this->tipo_filtro_export )
			
			$sql .= " ORDER BY x.ID DESC";
			
			$rs = Conexao::Executa($sql);
			
			$this->total_registros = $rs->num_rows;
				
			$campos = $rs->fetch_fields();
			//$tam_campos = array("83px", "100px", "100px", "100px", "50px");
			/*if ($this->checkbox) {
				//Não terá os campos "Editar" e "Excluir"
				$tam_campos = array("auto", "auto", "auto", "auto", "120px", "100px", "auto", "auto", "auto");
			}
			else { */
				//Terá os campos "Editar" e "Excluir"
				$tam_campos = array("auto", "auto", "auto", "auto", "120px", "100px", "auto", "auto", "auto");
			//}
			
?>
				<a href="#" onclick="javacript: SelecionaTodosExport('<?php echo($this->t_tipo_min); ?>'); return false;">Selecionar Todos</a>
				
<?php
	
			parent::EscreveHeader($campos, $tam_campos);
			

			$linha = 0;
			while ($row = $rs->fetch_assoc()) {
				$linha++;
?>
					<tr>
<?php 
				if ($this->checkbox) {
?>
						<td style="widht: 72px;"><input id="f_<?php echo($this->t_tipo_min); ?>_exp_<?php echo($linha); ?>" name="f_<?php echo($this->t_tipo_min); ?>_exp_<?php echo($linha); ?>" checked="checked" value="<?php echo($row["Código"]) ?>" type="checkbox" /></td>
<?php
				}
				
				$i = 0;
				foreach ($row as $campo) {
					if ($i == 4) {
?>
						<td style="text-align: center;"><?php echo(htmlspecialchars(FormataDataTexto($campo), ENT_COMPAT)); ?></td>
<?php
					}
					else if ($i == 5) {
?>
						<td style="text-align: right; padding-right: 10px;"><?php echo($campo); ?></td>
<?php
					}
					else {
?>
						<td><?php echo(htmlspecialchars($campo, ENT_COMPAT)); ?></td>
<?php
					}
					$i++;
				}
				
				if (!$this->checkbox) {
?>
						<td style="widht: 72px; font-size: 0.7em;"><a href="<?php echo(Enderecos::Site()) ?>inclusao.php?tipo=<?php echo($this->t_tipo_min); ?>&amp;id=<?php echo(htmlspecialchars($row["Código"], ENT_COMPAT)); ?>">Editar</a></td>
						<td style="widht: 72px; font-size: 0.7em;"><a href="#" onclick="javascript: Excluir(parseInt('<?php echo(htmlspecialchars($row["Código"], ENT_COMPAT)); ?>'), '<?php echo($this->t_tipo_min); ?>'); return false;">Excluir</a></td>
<?php
				}
?>
					</tr>
				
<?php
	}
			
			$rs->close();
			
			parent::EscreveFooter();
?>
				<input id="f_<?php echo($this->t_tipo_min); ?>_exp_total" type="hidden" value="<?php echo($linha); ?>" />
				
<?php

		}
	}
?>