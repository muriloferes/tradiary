<?php

require_once(__DIR__."/../static/DBTable.class.php");

class Usuario extends DBTable {

	function __construct($id = null){
		$this->table = "usuario";
		$this->columns["idusuario"] = new DBColumnString("idusuario");
		$this->columns["dthrcriacao"] = new DBColumnDateTime("dthrcriacao");
		$this->columns["nome"] = new DBColumnString("nome", 50);
		$this->columns["senha"] = new DBColumnString("senha", 50);
		$this->columns["status"] = new DBColumnString("status", 20);

		parent::__construct($id);
	}

	function getidusuario(){
		return $this->columns["idusuario"]->getvalue();
	}

	function getdthrcriacao($formated = false){
		return $this->columns["dthrcriacao"]->getvalue($formated);
	}

	function getnome(){
		return $this->columns["nome"]->getvalue();
	}

	function getsenha(){
		return $this->columns["senha"]->getvalue();
	}

	function getstatus(){
		return $this->columns["status"]->getvalue();
	}

	function setidusuario($idusuario){
		$this->columns["idusuario"]->setvalue($idusuario);
	}

	function setdthrcriacao($dthrcriacao){
		$this->columns["dthrcriacao"]->setvalue($dthrcriacao);
	}

	function setnome($nome){
		$this->columns["nome"]->setvalue($nome);
	}

	function setsenha($senha){
		$this->columns["senha"]->setvalue($senha);
	}

	function setstatus($status){
		$this->columns["status"]->setvalue($status);
	}

}
