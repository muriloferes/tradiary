<?php

class DBValue{

	// Converte a data de um formato para outro
	// Exemplo: convert_date("20/02/2009","d/m/Y","Y-m-d"); (retorna "2009-02-20")
	// Exemplo: convert_date("2014-08-09 10-50","Y-m-d H-i","d/m/Y H:i:s"); (retorna "09/08/2014 10:50:00")
	static function convert_date($date, $from_format, $to_format){
		if(strlen($date) == 0){
			return $date;
		}
		if(strlen($from_format) == 3){
			$date = substr($date, 0, 2)."-".substr($date, 2, 2)."-".substr($date, 4);
			$from_format = substr($from_format, 0, 1)."-".substr($from_format, 1, 1)."-".substr($from_format, 2);
		}
		$separator = "-";
		$from_format = str_replace(array("/", "-", " ", ":"), $separator, $from_format);
		$from_format = explode($separator, strtoupper($from_format));
		$date = str_replace(array("/", "-", " ", ":"), $separator, $date);
		$date = explode($separator, $date);
		foreach($from_format as $i => $char){
			switch($char){
				case "D": $day = $date[$i];
					break;
				case "H": $hou = $date[$i];
					break;
				case "I": $min = $date[$i];
					break;
				case "M": $mon = $date[$i];
					break;
				case "S": $sec = $date[$i];
					break;
				case "Y": $yea = $date[$i];
					break;
			}
		}
		$date = mktime($hou, $min, $sec, $mon, $day, $yea);
		return date($to_format, $date);
	}

	// Verifica se a data e valida e retorna no formato 'Y-m-d'
	static function date($value){

		// Verifica se nao veio com a hora junto
		$arr_value = explode(" ", $value);
		if(count($arr_value) === 2){
			$value = $arr_value[0];
		}

		// Retorna o dia de uma data
		$date_day = function($date){
			$day = null;
			for($i = 0; $i < strlen($date); $i++){
				if(is_numeric(substr($date, $i, 1))){
					$day .= substr($date, $i, 1);
				}else{
					return $day;
				}
			}
			return $day;
		};

		// Retorna o mes de uma data
		$date_month = function($date){
			$month = null;
			$cont = 0;
			for($i = 0; $i < strlen($date); $i++){
				if(is_numeric(substr($date, $i, 1))){
					if($cont == 1){
						$month .= substr($date, $i, 1);
					}
				}else{
					if($cont > 0){
						return $month;
					}else{
						$cont++;
					}
				}
			}
			return $month;
		};

		// Retorna o ano de uma data
		$date_year = function($date){
			$year = null;
			$cont = 0;
			for($i = 0; $i < strlen($date); $i++){
				if(is_numeric(substr($date, $i, 1))){
					if($cont == 2){
						$year .= substr($date, $i, 1);
					}
				}else{
					if($cont > 1){
						return $year;
					}else{
						$cont++;
					}
				}
			}
			return $year;
		};

		// Verifica se a data informada eh valida
		$valid_date = function($date) use ($date_day, $date_month, $date_year){
			$day = $date_day($date);
			$month = $date_month($date);
			$year = $date_year($date);
			if(!is_numeric($day) || !is_numeric($month) || !is_numeric($year)){
				return false;
			}elseif($month < 1 || $month > 12){
				return false;
			}elseif(($day < 1) || ($day > 30 && ($month == 4 || $month == 6 || $month == 9 || $month == 11 )) || ($day > 31)){
				return false;
			}elseif($month == 2 && ($day > 29 || ($day > 28 && (floor($year / 4) != $year / 4)))){
				return false;
			}else{
				return true;
			}
		};

		if(substr($value, 4, 1) == "-" && substr($value, 7, 1) == "-" && strlen($value) == 10){
			$value = implode("/", array_reverse(explode("-", $value)));
		}
		if($valid_date($value)){
			return $date_year($value)."-".$date_month($value)."-".$date_day($value);
		}else{
			return null;
		}
	}

	// Verifica se a data e hora sao validos e retorna no formato 'Y-m-d H:i:s'
	static function datetime($value){
		$arr_value = explode(" ", $value);
		$date = DBValue::date($arr_value[0]);
		if(is_null($date)){
			return null;
		}
		$time = DBValue::time($arr_value[1]);
		if(is_null($time)){
			$time = "00:00:00";
		}
		return $date." ".$time;
	}

	// Verifica se um numero e valido e retona com o separador decimal '.'
	static function decimal($value){
		$v = strpos($value, ",");
		$p = strpos($value, ".");
		if($v === false && $p === false){ // Valor inteiro sem separador de decimal e milhar (nao precisa de tratamento)
		}elseif($v !== false && $p === false){ // Virgula no separador decimal e sem separador de milhar
			$value = str_replace(",", ".", $value);
		}elseif($v === false && $p !== false){ // Ponto no separador de decimal e sem separador de milhar (nao precisa de tratamento)
		}elseif($v > $p){ // Virgula no separador de decimal e ponto no separador de milhar
			$value = str_replace(".", "", $value);
			$value = str_replace(",", ".", $value);
		}elseif($p > $v){ // Ponto no separador de decimal e virgula no separador de milhar
			$value = str_replace(",", "", $value);
		}
		if(is_numeric($value)){
			return $value;
		}else{
			return null;
		}
	}

	// Verifica se e um numero inteiro
	static function integer($value){
		return DBValue::decimal($value);
	}

	// Verifica se o texto eh valido e corta fora o resto do texto caso atinja tamanho maximo
	static function string($value, $length = null){
		if(is_string($value) || is_numeric($value)){
			return (is_null($length) ? $value : substr($value, 0, $length));
		}else{
			return null;
		}
	}

	// Verifica se o horario eh valido no formato 'hh:mm:ss' ou 'hh:mm' e retorna no format 'hh:mm:ss'
	static function time($value){
		$separator = ":";
		$value = substr($value, 0, 8);
		if((strlen($value) == 8) && (substr($value, 2, 1) == $separator) && (substr($value, 5, 1) == $separator)){
			$hou = (int) substr($value, 0, 2);
			$min = (int) substr($value, 3, 2);
			$sec = (int) substr($value, 6, 2);
		}elseif((strlen($value) == 5) && (substr($value, 2, 1) == $separator)){
			$hou = (int) substr($value, 0, 2);
			$min = (int) substr($value, 3, 2);
			$sec = 0;
		}else{
			return null;
		}
		if(($hou < 0 || $hou > 23) || ($min < 0 || $min > 59) || ($sec < 0 || $sec > 59)){
			return null;
		}else{
			return str_pad($hou, 2, "0", STR_PAD_LEFT).$separator.str_pad($min, 2, "0", STR_PAD_LEFT).$separator.str_pad($sec, 2, "0", STR_PAD_LEFT);
		}
	}

}