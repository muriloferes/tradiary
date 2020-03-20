<?php

function array_is_associative(array $array) {
    return count(array_filter(array_keys($array), "is_string")) > 0;
}

function connection(){
    global $connection;
    if(!is_object($connection)){
        $connection = new Connection();
    }
    return $connection;
}

function current_view(){
    $filename = $_SERVER["SCRIPT_NAME"];
    $uri = $_SERVER["REQUEST_URI"];

    $arr_uri = array_reverse(explode("/", $uri));

    if(strpos($filename, "/ajax/") !== false){
        return $arr_uri[1];
    }elseif(strpos($filename, "/view/") !== false){
        return $arr_uri[0];
    }else{
        return false;
    }
}

function get_json($index = null){
    $json = file_get_contents("php://input");
    $json = json_decode($json, true);
    if(!is_array($json)){
        $json = $_POST;
    }
    if(count($json) === 0){
        $json = $_GET;
    }
    if(!is_null($index)){
        $value = $json[$index];
        if(strlen($value) === 0){
            $value = null;
        }
        return $value;
    }
    return $json;
}

// Metodo padrao para erro do JSON de retorno
function json_error($message, $extra = null, $die = true){
	$json_out = array(
		"status" => "2",
		"message" => $message
	);
	if(is_array($extra)){
		$json_out = array_merge($json_out, $extra);
	}
	if($die){
		$json = json_encode($json_out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		if($json === false){
			pre($json_out);
			die();
		}
		$json_out = $json;
		die($json_out);
	}else{
		return $json_out;
	}
}

// Metodo padrao para sucesso do JSON de retorno
function json_success($extra = null, $die = true){
	$json_out = array(
		"status" => "0"
	);
	if(is_string($extra)){
		$extra = json_decode($extra, true);
	}
	if(is_array($extra)){
		$json_out = array_merge($json_out, $extra);
	}
	if($die){
		$json = json_encode($json_out);
		if($json === false){
			pre($json_out);
			die();
		}
		$json_out = $json;
		die($json_out);
	}else{
		return $json_out;
	}
}

function remove_format($text){
	return str_replace([" ", "-", ".", "/", "\\", ":", ",", "(", ")", "º", "[", "]", "'"], "", $text);
}
