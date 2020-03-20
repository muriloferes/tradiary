<?php

require_once(__DIR__."/DBValue.class.php");

abstract class DBColumn{

	protected $name; // Nome da coluna no banco de dados
	protected $type; // Tipo da columna no banco de dados
	public $value; // Valor da coluna

	function __construct($name){
		$this->name = $name;
		$this->value = null;
	}

	function getname(){
		return $this->name;
	}

	function gettype(){
		return $this->type;
	}

	function getvalue(){
		return $this->value;
	}

	function setvalue($value){
		$this->value = $value;
	}

}