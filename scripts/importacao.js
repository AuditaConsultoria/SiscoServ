function ListaArquivos(inputfile, retorno, botao_importar) {
	var txt = "";
	
	if ('files' in inputfile) {
		if (inputfile.files.length == 0) {
			txt = "Nenhum arquivo selecionado.";
			botao_importar.style.display = "none";
		}
		else {
			txt = "<ul>";
			botao_importar.style.display = "block";
			
			for (var i = 0; i < inputfile.files.length; i++) {
				txt += "<li><span>" + (i + 1) + "</span>";

				var file = inputfile.files[i];
				
				if ('name' in file) {
					txt += "<span>" + file.name + "</span>";
				}
				if ('size' in file) {
					var size = file.size;
					var unidade = "bytes";
					
					if (size >= 1024) {
						size /= 1024;
						unidade = "KB";
					}
					if (size >= 1024) {
						size /= 1024;
						unidade = "MB";
					}
					
					txt += "<span>" + size.toFixed(2) + " " + unidade + "</span>";
				}
			}
			
			txt += "</ul>"
		}
	}
	
	retorno.innerHTML = txt;
}

function ProgressoUpload(qual_iframe, up_id) {
	function set () {
		qual_iframe.src = "upload_frame.php?up_id=" + up_id;
	}
	setTimeout(set);
}

function CarregaArquivo(arquivo, campo_status) {
	
}