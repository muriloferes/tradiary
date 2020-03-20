<?php

require_once(__DIR__."/../../../default/handling.php");

$idusuario = get_json("idusuario");
$senha = get_json("senha");

$usuario = new Usuario($idusuario);
if(!$usuario->exists()){
    json_error("Usuário não encontrado.");
}

if($usuario->getsenha() !== $senha){
    json_error("Senha incorreta.");
}

$_SESSION["idusuario"] = $usuario->getidusuario();

json_success();
