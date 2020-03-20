<?php

require_once(__DIR__."/../../../default/handling.php");

$dtoperacao = get_json("dtoperacao");
$contratos = get_json("contratos");
$totalbruto = get_json("totalbruto");
$totalliquido = get_json("totalliquido");
$deposito = get_json("deposito");
$retirada = get_json("retirada");

try{
    $operacao = Operacao::searchFirst("idusuario = '{$_SESSION["idusuario"]}' AND dtoperacao = '{$dtoperacao}'");
    if($operacao === false){
        $operacao = new Operacao();
    }
    $operacao->setidusuario($_SESSION["idusuario"]);
    $operacao->setdtoperacao($dtoperacao);
    $operacao->setcontratos($contratos);
    $operacao->settotalbruto($totalbruto);
    $operacao->settotalliquido($totalliquido);
    $operacao->setdeposito($deposito);
    $operacao->setretirada($retirada);
    $operacao->save();
    json_success();
}catch(Exception $e){
    json_error($e->getMessage());
}
