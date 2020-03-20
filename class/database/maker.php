<?php

if($_POST){
    require_once(__DIR__."/static/Connection.class.php");

    $connection = new Connection();

	if(isset($_POST["criar-tabela"])){
		criar_classe($connection, $_POST["tabela"], $_POST["classe"]);
	}elseif(isset($_POST["criar-todos"])){
		$res = $connection->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE'");
		$arr = $res->fetchAll();
		foreach($arr as $row){
			$tabela = $row["table_name"];
			$classe = ucfirst($tabela);
			criar_classe($connection, $tabela, $classe);
		}
	}
}

function criar_classe($connection, $tabela, $classe){
	// Pega todas as colunas da tabela
	$arr_coluna = [];
	$res = $connection->query("SELECT * FROM information_schema.columns WHERE table_name = '{$tabela}'");
	$arr = $res->fetchAll();
	foreach($arr as $row){
		$arr_coluna[] = $row;
    }
    
	// Comeca a escrever o arquivo
	$arquivo = "<?php\r\n\r\nrequire_once(__DIR__.\"/../static/DBTable.class.php\");\r\n\r\n";
	$arquivo .= "class {$classe} extends DBTable {\r\n\r\n";
	$arquivo .= "\tfunction __construct($"."id = null){\r\n";
    $arquivo .= "\t\t$"."this->table = \"{$tabela}\";\r\n";
	foreach($arr_coluna as $coluna){
		$arquivo .= "\t\t$"."this->columns[\"{$coluna["column_name"]}\"] = new DBColumn";
		switch($coluna["data_type"]){
			case "date":
				$arquivo .= "Date(\"{$coluna["column_name"]}\");\r\n";
				break;
			case "bigint":
			case "integer":
				$arquivo .= "Integer(\"{$coluna["column_name"]}\");\r\n";
				break;
			case "numeric":
				$arquivo .= "Decimal(\"{$coluna["column_name"]}\", {$coluna["numeric_scale"]});\r\n";
                break;
			case "character":
			case "character varying":
				$arquivo .= "String(\"{$coluna["column_name"]}\", {$coluna["character_maximum_length"]});\r\n";
                break;
            case "json":
            case "text":
            case "uuid":
				$arquivo .= "String(\"{$coluna["column_name"]}\");\r\n";
				break;
			case "time":
			case "time without time zone":
				$arquivo .= "Time(\"{$coluna["column_name"]}\");\r\n";
				break;
			case "timestamp":
			case "timestamp without time zone":
				$arquivo .= "DateTime(\"{$coluna["column_name"]}\");\r\n";
                break;
            default:
                die("Tipo não identificado: {$coluna["data_type"]}");
		}
	}
	$arquivo .= "\r\n\t\tparent::__construct($"."id);\r\n\t}\r\n";
	foreach($arr_coluna as $coluna){
		$formated = in_array($coluna["data_type"], array("date", "numeric", "timestamp", "timestamp without time zone"));
		$arquivo .= "\r\n\tfunction get{$coluna["column_name"]}(".($formated ? "$"."formated = false" : "")."){\r\n";
		$arquivo .= "\t\treturn $"."this->columns[\"{$coluna["column_name"]}\"]->getvalue(".($formated ? "$"."formated" : "").");\r\n\t}\r\n";
	}

	foreach($arr_coluna as $coluna){
		$arquivo .= "\r\n\tfunction set{$coluna["column_name"]}($"."{$coluna["column_name"]}){\r\n";
		$arquivo .= "\t\t$"."this->columns[\"{$coluna["column_name"]}\"]->setvalue($"."{$coluna["column_name"]});\r\n\t}\r\n";
	}
    $arquivo .= "\r\n}\r\n";
    
    $filename = __DIR__."/table/{$tabela}.class.php"; 
	$f = fopen($filename, "w+");
	fwrite($f, $arquivo);
	fclose($f);
	chmod($filename, 0777);
}
?>
<html>
	<head>
		<title>Criador de Classes</title>
		<style type="text/css">
			input, select {
				width: 400px;
			}

			input[type="submit"] {
				margin-top: 10px;
				width: 198px;
			}
		</style>
		<script type="text/javascript">
			function tabela_para_classe(){
				var value = document.getElementById("tabela").value;
				value = value.substr(0, 1).toUpperCase() + value.substr(1);
				document.getElementById("classe").value = value;
			}
		</script>
	</head>
	<body style="font-family:Verdana; font-size:12px">
		<form method="post">
			<label for="tabela">Nome da Tabela</label><br>
			<input type="text" id="tabela" name="tabela" onchange="tabela_para_classe()"><br>
			<label for="classe">Nome da Classe</label><br>
			<input type="text" id="classe" name="classe"><br>
			<input type="submit" name="criar-tabela" value="Criar">
			<input type="submit" name="criar-todos" value="Criar todos">
		</form>
	</body>
</html>