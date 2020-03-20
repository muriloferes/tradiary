<?php

require_once(__DIR__."/../static/DBTable.class.php");

class Operacao extends DBTable {

	function __construct($id = null){
		$this->table = "operacao";
		$this->columns["idoperacao"] = new DBColumnString("idoperacao");
		$this->columns["dthrcriacao"] = new DBColumnDateTime("dthrcriacao");
		$this->columns["idusuario"] = new DBColumnString("idusuario");
		$this->columns["dtoperacao"] = new DBColumnDate("dtoperacao");
		$this->columns["totalbruto"] = new DBColumnDecimal("totalbruto", 2);
		$this->columns["totalliquido"] = new DBColumnDecimal("totalliquido", 2);
		$this->columns["contratos"] = new DBColumnInteger("contratos");
		$this->columns["deposito"] = new DBColumnDecimal("deposito", 2);
		$this->columns["retirada"] = new DBColumnDecimal("retirada", 2);

		parent::__construct($id);
	}

	function getidoperacao(){
		return $this->columns["idoperacao"]->getvalue();
	}

	function getdthrcriacao($formated = false){
		return $this->columns["dthrcriacao"]->getvalue($formated);
	}

	function getidusuario(){
		return $this->columns["idusuario"]->getvalue();
	}

	function getdtoperacao($formated = false){
		return $this->columns["dtoperacao"]->getvalue($formated);
	}

	function gettotalbruto($formated = false){
		return $this->columns["totalbruto"]->getvalue($formated);
	}

	function gettotalliquido($formated = false){
		return $this->columns["totalliquido"]->getvalue($formated);
	}

	function getcontratos(){
		return $this->columns["contratos"]->getvalue();
	}

	function getdeposito($formated = false){
		return $this->columns["deposito"]->getvalue($formated);
	}

	function getretirada($formated = false){
		return $this->columns["retirada"]->getvalue($formated);
	}

	function setidoperacao($idoperacao){
		$this->columns["idoperacao"]->setvalue($idoperacao);
	}

	function setdthrcriacao($dthrcriacao){
		$this->columns["dthrcriacao"]->setvalue($dthrcriacao);
	}

	function setidusuario($idusuario){
		$this->columns["idusuario"]->setvalue($idusuario);
	}

	function setdtoperacao($dtoperacao){
		$this->columns["dtoperacao"]->setvalue($dtoperacao);
	}

	function settotalbruto($totalbruto){
		$this->columns["totalbruto"]->setvalue($totalbruto);
	}

	function settotalliquido($totalliquido){
		$this->columns["totalliquido"]->setvalue($totalliquido);
	}

	function setcontratos($contratos){
		$this->columns["contratos"]->setvalue($contratos);
	}

	function setdeposito($deposito){
		$this->columns["deposito"]->setvalue($deposito);
	}

	function setretirada($retirada){
		$this->columns["retirada"]->setvalue($retirada);
	}

}
