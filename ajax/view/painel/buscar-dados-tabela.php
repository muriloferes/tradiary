<?php

require_once(__DIR__."/../../../default/handling.php");

$tempo = get_json("tempo");

$connection = connection();

switch($tempo){
    case "dia":
        $tempo_query_coluna = "dtoperacao";
        $tempo_label = "Dia";
        $quebra_query_coluna = "EXTRACT(WEEK FROM dtoperacao)";
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
    "SELECT {$tempo_query_coluna} AS periodo, SUM(totalliquido) AS valor",
    ($quebra_query_coluna ? ", {$quebra_query_coluna} AS quebra" : ""),
    "FROM operacao",
    "WHERE idusuario = '{$_SESSION["idusuario"]}'",
    "GROUP BY 1".($quebra_query_coluna ? ", 3" : ""),
    "ORDER BY 1"
]);
$arr = $res->fetchAll();

$quebra = null;
$acumulado = 0;
$arr_tr = [];

foreach($arr as $row){
    $color = null;
    if($row["valor"] > 0){
        $color = "success";
    }elseif($row["valor"] < 0){
        $color = "danger";
    }

    $tr_class = [];
    if(strlen($color) > 0){
        $tr_class[] = "table-{$color}";
    }
    $tr_class = implode(" ", $tr_class);

    $periodo = $row["periodo"];
    $periodo = implode("/", array_reverse(explode("-", $periodo)));

    $valor = $row["valor"];
    $valor = number_format($valor, 2, ",", ".");
    
    $acumulado += $row["valor"];
    $acumulado_formatado = number_format($acumulado, 2, ",", ".");

    if(isset($row["quebra"])){ 
        if(!is_null($quebra) && $quebra !== $row["quebra"]){
            $tr  = "<tr class='table-break'>";
            $tr .= "  <td></td>";
            $tr .= "  <td></td>";
            $tr .= "  <td></td>";
            $tr .= "</tr>";
            $arr_tr[] = $tr;
        }
        $quebra = $row["quebra"];
    }

    $tr  = "<tr class='{$tr_class}'>";
    $tr .= "  <td class='text-center'>{$periodo}</td>";
    $tr .= "  <td class='text-right'>{$valor}</td>";
    $tr .= "  <td class='text-right'>{$acumulado_formatado}</td>";
    $tr .= "</tr>";
    $arr_tr[] = $tr;
}
$table  = "<table class='table table-striped table-bordered table-hover'>";
$table .= "  <thead class='thead-dark'>";
$table .= "    <th class='text-center' style='width: 34%'>{$tempo_label}</th>";
$table .= "    <th class='text-center' style='width: 33%'>Total líquido</th>";
$table .= "    <th class='text-center' style='width: 33%'>Acumulado</th>";
$table .= "  </thead>";
$table .= "  <tbody>";
$table .= implode("", array_reverse($arr_tr));
$table .= "  </tbody>";
$table .= "</table>";

json_success([
    "table" => $table
]);
