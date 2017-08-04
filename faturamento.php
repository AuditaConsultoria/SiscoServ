<?php function NovaOperacao($num) { ?>
	<li>
		<span>Operação: <b><?php echo($num); ?></b></span>
	</li>
	
	<li>
		<span>NBS:</span>
		<input type="text" />
		<input type="text" />
	</li>

	<li>
		<span>País Destino:</span>
		<input type="text" />
		<input type="text" />
	</li>
	
	<li>
		<span>Modo:</span>

		<input id="f_rvs_modo_1_<?php echo($num); ?>" name="f_rvs_modo_<?php echo($num); ?>" value="1" type="radio" />
		<label for="f_rvs_modo_1_<?php echo($num); ?>">1 - Transfronteiriço</label>

		<input id="f_rvs_modo_2_<?php echo($num); ?>" name="f_rvs_modo_<?php echo($num); ?>" value="2" type="radio" />
		<label for="f_rvs_modo_2_<?php echo($num); ?>">2 - Consumo no Exterior</label>
		
		<input id="f_rvs_modo_4_<?php echo($num); ?>" name="f_rvs_modo_<?php echo($num); ?>" value="4" type="radio" />
		<label for="f_rvs_modo_4_<?php echo($num); ?>">4 - Movimento Temporário de Pessoas Físicas</label>

	</li>
	
	<li>
		<span>Dt. Início:</span>
		<input type="text" />
		
		<span>Dt. Conclusão:</span>
		<input type="text" />
	</li>
	
	<li>
		<ul class="painel_interno">
			<li>
				<span>Valor:</span>
				<input type="text" />
			</li>
			<li>
				<span>Valor já Faturado:</span>
				<input type="text" />
			</li>
			<li>
				<span>Valor Restante a Faturar:</span>
				<input type="text" />
			</li>
		</ul>
	</li>

<?php }

function NovoFaturamento($num, $qtde) { ?>	
	<li>
		<span>Recebimento: <b><?php echo($num); ?></b></span>
	</li>
		
	<li>
		<span>Número do Documento:</span>
		<input type="text" />
	</li>
	
	<li>
		<span>Data do Recebimento:</span>
		<input type="text" />
	</li>
		
	<?php for ($i = 1; $i <= $qtde; $i++) { ?>
		<ul class = "painel_interno">
			<li>
				<span>Operação: <?php echo($i); ?></span>
			</li>
			<li>
				<span>Valor do Recebimento:</span>
				<input type="text" />
			</li>
		</ul>
	<?php } ?>
	</li>
<?php }

	if (isset($_GET["funcao"]))	{
		if ($_GET["funcao"] == "nova_operacao") {
			NovaOperacao($_GET["num"]);
		}
	
		if ($_GET["funcao"] == "novo_faturamento") {
			NovoFaturamento($_GET["num"], $_GET["qtde"]);
		}
	
	}

?>

