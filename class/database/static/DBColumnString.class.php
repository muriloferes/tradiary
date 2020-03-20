<?php

require_once(__DIR__."/DBColumn.class.php");

final class DBColumnString extends DBColumn{

	protected $length;

	function __construct($name, $length = NULL){
		$this->type = "string";
		$this->length = $length;
		parent::__construct($name);
	}

	function setvalue($value){
		$this->value = DBValue::string($value, $this->length);
	}

}