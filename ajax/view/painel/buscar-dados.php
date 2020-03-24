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



$acumulado = 0;
$arr_tr = [];

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

    $decimais = ($coluna === "contratos" ? 0 : 2);

    $periodo = $row["periodo"];
    $periodo = implode("/", array_reverse(explode("-", $periodo)));

    $valor = $row["valor"];
    $valor = number_format($valor, $decimais, ",", ".");
    
    $acumulado += $row["valor"];
    $acumulado_formatado = number_format($acumulado, $decimais, ",", ".");

    $tr  = "<tr class='{$tr_class}'>";
    $tr .= "  <td class='text-center'>{$periodo}</td>";
    $tr .= "  <td class='text-right'>{$valor}</td>";
    $tr .= "  <td class='text-right'>{$acumulado_formatado}</td>";
    $tr .= "</tr>";
    $arr_tr[] = $tr;

    $chart_labels[] = $periodo;
    $chart_data_resultado[] = $row["valor"];
    $chart_data_saldo[] = round(((end($chart_data_saldo) ?? 0) + $row["valor"]), 2);
}
$table  = "<table class='table table-striped table-bordered table-hover'>";
$table .= "  <thead class='thead-dark'>";
$table .= "    <th class='text-center' style='width: 34%'>{$tempo_label}</th>";
$table .= "    <th class='text-center' style='width: 33%'>{$coluna_label}</th>";
$table .= "    <th class='text-center' style='width: 33%'>Acumulado</th>";
$table .= "  </thead>";
$table .= "  <tbody>";
$table .= implode("", array_reverse($arr_tr));
$table .= "  </tbody>";
$table .= "</table>";

$chart_config = ["labels" => $chart_labels];

if(in_array($tempo, ["dia"])){
    $chart_config["title"] = "Saldo";
    $chart_config["data"] = $chart_data_saldo;
    $chart_config["type"] = "line";
    $chart_config["backgroundColor"] = "rgba(0, 123, 255, 0.25)";
    $chart_config["borderColor"] = "rgba(0, 123, 255, 1)";
}else{
    $chart_config["title"] = "Resultado";
    $chart_config["data"] = $chart_data_resultado;
    $chart_config["type"] = "bar";
    $chart_config["backgroundColor"] = [];
    $chart_config["borderColor"] = [];
    foreach($chart_config["data"] as $valor){
        if($valor < 0){
            $chart_config["backgroundColor"][] = "rgba(220, 53, 69, 0.25)";
            $chart_config["borderColor"][] = "rgba(220, 53, 69, 1)";
        }else{
            $chart_config["backgroundColor"][] = "rgba(40, 167, 69, 0.25)";
            $chart_config["borderColor"][] = "rgba(40, 167, 69, 1)";
        }
    }
}

json_success([
    "table" => $table,
    "chart" => mount_chart($chart_config)
]);

function mount_chart($config){
    return [
        "type" => $config["type"],
        "data" => [
            "labels" => $config["labels"],
            "datasets" => [[
                "label" => $config["title"],
                "data" => $config["data"],
                "backgroundColor" => $config["backgroundColor"],
                "borderColor" => $config["borderColor"],
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