<?php

require_once(__DIR__."/../static/DBTable.class.php");

class Retirada extends DBTable {

	function __construct($id = null){
		$this->table = "retirada";
		$this->columns["idretirada"] = new DBColumnString("idretirada");
		$this->columns["dthrcriacao"] = new DBColumnDateTime("dthrcriacao");
		$this->columns["idusuario"] = new DBColumnString("idusuario");
		$this->columns["dtretirada"] = new DBColumnDate("dtretirada");
		$this->columns["valor"] = new DBColumnDecimal("valor", 2);

		parent::__construct($id);
	}

	function getidretirada(){
		return $this->columns["idretirada"]->getvalue();
	}

	function getdthrcriacao($formated = false){
		return $this->columns["dthrcriacao"]->getvalue($formated);
	}

	function getidusuario(){
		return $this->columns["idusuario"]->getvalue();
	}

	function getdtretirada($formated = false){
		return $this->columns["dtretirada"]->getvalue($formated);
	}

	function getvalor($formated = false){
		return $this->columns["valor"]->getvalue($formated);
	}

	function setidretirada($idretirada){
		$this->columns["idretirada"]->setvalue($idretirada);
	}

	function setdthrcriacao($dthrcriacao){
		$this->columns["dthrcriacao"]->setvalue($dthrcriacao);
	}

	function setidusuario($idusuario){
		$this->columns["idusuario"]->setvalue($idusuario);
	}

	function setdtretirada($dtretirada){
		$this->columns["dtretirada"]->setvalue($dtretirada);
	}

	function setvalor($valor){
		$this->columns["valor"]->setvalue($valor);
	}

}
