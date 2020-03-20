<?php

require_once(__DIR__."/DBColumn.class.php");

final class DBColumnInteger extends DBColumn{

	function __construct($name){
		$this->type = "integer";
		parent::__construct($name);
	}

	function setvalue($value){
		$this->value = DBValue::integer($value);
	}

}