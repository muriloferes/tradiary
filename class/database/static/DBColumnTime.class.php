<?php

require_once(__DIR__."/DBColumn.class.php");

final class DBColumnTime extends DBColumn{

	function __construct($name){
		$this->type = "time";
		parent::__construct($name);
	}

	function getvalue(){
		if(strlen($this->value) > 0){
			return substr($this->value, 0, 8);
		}else{
			return NULL;
		}
	}

	function setvalue($value){
		$this->value = DBValue::time($value);
	}

}