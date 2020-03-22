<?php

require_once(__DIR__."/../../../default/handling.php");

$dtoperacao = get_json("dtoperacao");

try{
    $operacao = Operacao::searchFirst("idusuario = '{$_SESSION["idusuario"]}' AND dtoperacao = '{$dtoperacao}'");
    if($operacao === false){
        throw new Error("Operação não encontrada na data informada.");
    }
    $operacao->delete();
    json_success();
}catch(Exception $e){
    json_error($e->getMessage());
}
