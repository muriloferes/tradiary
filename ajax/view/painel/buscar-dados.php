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
    "ORDER BY 1"
]);
$arr = $res->fetchAll();

$chart_labels = [];
$chart_data_resultado = [];
$chart_data_saldo = [];

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

    $chart_labels[] = $periodo;
    $chart_data_resultado[] = $row["valor"];
    $chart_data_saldo[] = (end($chart_data_saldo) ?? 0) + $row["valor"];
}
$table .= "  </tbody>";
$table .= "</table>";

if(in_array($tempo, ["dia", "semana"])){
    $chart_title = "Saldo";
    $chart_data = $chart_data_saldo;
}else{
    $chart_title = "Resultado";
    $chart_data = $chart_data_resultado;
}

json_success([
    "table" => $table,
    "chart" => mount_chart($chart_title, $chart_labels, $chart_data)
]);

function mount_chart($title, $labels, $data){
    return [
        "type" => "line",
        "data" => [
            "labels" => $labels,
            "datasets" => [[
                "label" => $title,
                "data" => $data,
                "backgroundColor" => "rgba(0, 123, 255, 0.25)",
                "borderColor" => "rgba(0, 123, 255, 1)",
                "borderWidth" => 2,
                "lineTension" => 0.1,
                "pointBackgroundColor" => "rgba(0, 0, 0, 0)",
                "pointBorderColor" => "rgba(0, 0, 0, 0)",
                "pointBorderWidth" => 0
            ]]
        ],
        "options" => [
            "responsive" => true,
            "scales" => [
                "yAxes" => [
                    "ticks" => [
                        "beginAtZero" => true
                    ]
                ]
            ]
        ]
    ];
}