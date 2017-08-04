<?php
	/*
		Retorna uma sequência numérica aleatória.
	*/
	function NumRandom() {
		$r = rand(10000000, 99999999);
		
		return $r;
	}
	
	function FormataDataBanco($qual_data) {
		$data = explode("/", $qual_data);
		
		return $data[2] . "-" . $data[1] . "-" . $data[0];
	}
	
	function FormataDataTexto($qual_data) {
		if ($qual_data == "") return "";
	
		$data = explode("-", $qual_data);
		
		if ($data[2] == "0000" && $data[1] == "00" && $data[0] == "00") {
			return "";
		}
		
		return $data[2] . "/" . $data[1] . "/" . $data[0];
	}
?>