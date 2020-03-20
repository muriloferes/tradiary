<?php

require_once(__DIR__."/DBColumn.class.php");

final class DBColumnDateTime extends DBColumn{

	function __construct($name){
		$this->type = "datetime";
		parent::__construct($name);
	}

	function getvalue($formated = FALSE){
		if($formated && strlen($this->value) > 0){
			return DBValue::convert_date($this->value, "Y-m-d H:i:s", "d/m/Y H:i:s");
		}else{
			return $this->value;
		}
	}

	function setvalue($value){
		$this->value = DBValue::datetime($value);
	}

}