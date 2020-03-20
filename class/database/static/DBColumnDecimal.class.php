<?php

require_once(__DIR__."/DBColumn.class.php");

final class DBColumnDecimal extends DBColumn{

	protected $precision;

	function __construct($name, $precision){
		$this->type = "decimal";
		$this->precision = $precision;
		parent::__construct($name);
	}

	function getvalue($formated = FALSE){
		if($formated){
			return number_format($this->value, $this->precision, ",", ".");
		}else{
			return $this->value;
		}
	}

	function setvalue($value){
		$this->value = DBValue::decimal($value);
	}

}