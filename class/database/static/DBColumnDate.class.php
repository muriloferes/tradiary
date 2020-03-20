<?php

require_once(__DIR__."/DBColumn.class.php");

final class DBColumnDate extends DBColumn{

	function __construct($name){
		$this->type = "date";
		parent::__construct($name);
	}

	function getvalue($formated = FALSE){
		if($formated && strlen($this->value) > 0){
			return DBValue::convert_date($this->value, "Y-m-d", "d/m/Y");
		}else{
			return $this->value;
		}
	}

	function setvalue($value){
		$this->value = DBValue::date($value);
	}

}