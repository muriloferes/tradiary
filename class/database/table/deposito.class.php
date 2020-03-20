<?php

require_once(__DIR__."/../static/DBTable.class.php");

class Deposito extends DBTable {

	function __construct($id = null){
		$this->table = "deposito";
		$this->columns["iddeposito"] = new DBColumnString("iddeposito");
		$this->columns["dthrcriacao"] = new DBColumnDateTime("dthrcriacao");
		$this->columns["idusuario"] = new DBColumnString("idusuario");
		$this->columns["dtdeposito"] = new DBColumnDate("dtdeposito");
		$this->columns["valor"] = new DBColumnDecimal("valor", 2);

		parent::__construct($id);
	}

	function getiddeposito(){
		return $this->columns["iddeposito"]->getvalue();
	}

	function getdthrcriacao($formated = false){
		return $this->columns["dthrcriacao"]->getvalue($formated);
	}

	function getidusuario(){
		return $this->columns["idusuario"]->getvalue();
	}

	function getdtdeposito($formated = false){
		return $this->columns["dtdeposito"]->getvalue($formated);
	}

	function getvalor($formated = false){
		return $this->columns["valor"]->getvalue($formated);
	}

	function setiddeposito($iddeposito){
		$this->columns["iddeposito"]->setvalue($iddeposito);
	}

	function setdthrcriacao($dthrcriacao){
		$this->columns["dthrcriacao"]->setvalue($dthrcriacao);
	}

	function setidusuario($idusuario){
		$this->columns["idusuario"]->setvalue($idusuario);
	}

	function setdtdeposito($dtdeposito){
		$this->columns["dtdeposito"]->setvalue($dtdeposito);
	}

	function setvalor($valor){
		$this->columns["valor"]->setvalue($valor);
	}

}
