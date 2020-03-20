<?php

require_once(__DIR__."/../../../default/handling.php");

$dtoperacao = get_json("dtoperacao");

$operacao = Operacao::searchFirst("idusuario = '{$_SESSION["idusuario"]}' AND dtoperacao = '{$dtoperacao}'");
if($operacao === false){
    json_success();
}

json_success([
    "data" => [
        "contratos" => $operacao->getcontratos(),
        "totalbruto" => $operacao->gettotalbruto(),
        "totalliquido" => $operacao->gettotalliquido(),
        "deposito" => $operacao->getdeposito(),
        "retirada" => $operacao->getretirada()
    ]
]);
