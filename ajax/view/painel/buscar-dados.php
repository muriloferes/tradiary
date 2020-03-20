<?php

require_once(__DIR__."/../../../default/handling.php");

$coluna = get_json("coluna");
$tempo = get_json("tempo");

$connection = connection();

switch($coluna){
    case "contratos":
        $coluna_label = "Contratos";
        break;
    case "totalbruto":
        $coluna_label = "Total bruto";
        break;
    case "totalliquido":
        $coluna_label = "Total líquido";
        break;
    case "deposito":
        $coluna_label = "Depósito";
        break;
    case "retirada":
        $coluna_label = "Retirada";
        break;
}

switch($tempo){
    case "dia":
        $tempo_query_coluna = "dtoperacao";
        $tempo_label = "Dia";
        break;
    case "semana":
        $tempo_query_coluna = "(EXTRACT(YEAR FROM dtoperacao) || '-' || LPAD(EXTRACT(WEEK FROM dtoperacao)::TEXT, 2, '0'))";
        $tempo_label = "Semana";
        break;
    case "mes":
        $tempo_query_coluna = "(EXTRACT(YEAR FROM dtoperacao) || '-' || LPAD(EXTRACT(MONTH FROM dtoperacao)::TEXT, 2, '0'))";
        $tempo_label = "Mês";
        break;
    case "ano":
        $tempo_query_coluna = "EXTRACT(YEAR FROM dtoperacao)";
        $tempo_label = "Ano";
        break;
}

$res = $connection->query([
    "SELECT {$tempo_query_coluna} AS periodo, SUM({$coluna}) AS valor",
    "FROM operacao",
    "WHERE idusuario = '{$_SESSION["idusuario"]}'",
    "GROUP BY 1",
    "ORDER BY 1 DESC"
]);
$arr = $res->fetchAll();

$table  = "<table class='table table-striped table-bordered table-hover'>";
$table .= "  <thead class='thead-dark'>";
$table .= "    <th class='text-center' style='width: 50%'>{$tempo_label}</th>";
$table .= "    <th class='text-center' style='width: 50%'>{$coluna_label}</th>";
$table .= "  </thead>";
$table .= "  <tbody>";
foreach($arr as $row){
    $color = null;
    switch($coluna){
        case "totalbruto":
        case "totalliquido":
            if($row["valor"] > 0){
                $color = "success";
            }elseif($row["valor"] < 0){
                $color = "danger";
            }
            $coluna_label = "Total líquido";
            break;
        case "deposito":
            if($row["valor"] > 0){
                $color = "danger";
            }else{
                $color = "success";
            }
            break;
        case "retirada":
            if($row["valor"] > 0){
                $color = "success";
            }else{
                $color = "danger";
            }
            break;
    }
    $tr_class = [];
    if(strlen($color) > 0){
        $tr_class[] = "table-{$color}";
    }
    $tr_class = implode(" ", $tr_class);

    $periodo = $row["periodo"];
    $periodo = implode("/", array_reverse(explode("-", $periodo)));

    $valor = $row["valor"];
    $valor = number_format($valor, ($coluna === "contratos" ? 0 : 2), ",", ".");

    $table .= "<tr class='{$tr_class}'>";
    $table .= "  <td class='text-center'>{$periodo}</td>";
    $table .= "  <td class='text-right'>{$valor}</td>";
    $table .= "</tr>";
}
$table .= "  </tbody>";
$table .= "</table>";

json_success([
    "table" => $table
]);
