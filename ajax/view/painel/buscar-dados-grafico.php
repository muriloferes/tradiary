<?php

require_once(__DIR__."/../../../default/handling.php");

$connection = connection();

$chart_semana_atual = query_to_chart("Semana atual", "bar", [
    "SELECT dtoperacao AS periodo, totalliquido AS valor",
    "FROM operacao",
    "WHERE idusuario = '{$_SESSION["idusuario"]}'",
    "  AND extract(WEEK FROM dtoperacao) = extract(WEEK FROM current_date)",
    "  AND extract(YEAR FROM dtoperacao) = extract(YEAR FROM current_date)",
    "ORDER BY 1"
]);

$chart_mes_atual = query_to_chart("Mês atual", "line", [
    "SELECT dtoperacao AS periodo, totalliquido AS valor",
    "FROM operacao",
    "WHERE idusuario = '{$_SESSION["idusuario"]}'",
    "  AND extract(MONTH FROM dtoperacao) = extract(MONTH FROM current_date)",
    "  AND extract(YEAR FROM dtoperacao) = extract(YEAR FROM current_date)",
    "ORDER BY 1"
], true);

$chart_ano_atual = query_to_chart("Ano atual", "line", [
    "SELECT dtoperacao AS periodo, totalliquido AS valor",
    "FROM operacao",
    "WHERE idusuario = '{$_SESSION["idusuario"]}'",
    "  AND extract(YEAR FROM dtoperacao) = extract(YEAR FROM current_date)",
    "ORDER BY 1"
], true);

$chart_saldo_diario = query_to_chart("Saldo Geral", "line", [
    "SELECT dtoperacao AS periodo, totalliquido AS valor",
    "FROM operacao",
    "WHERE idusuario = '{$_SESSION["idusuario"]}'",
    "ORDER BY 1"
], true);

$chart_ultimas_semanas = query_to_chart("Últimas 12 semanas", "bar", [
    "SELECT (EXTRACT(YEAR FROM dtoperacao) || '-' || LPAD(EXTRACT(WEEK FROM dtoperacao)::TEXT, 2, '0')) AS periodo,",
    "  SUM(totalliquido) AS valor",
    "FROM operacao",
    "WHERE idusuario = '{$_SESSION["idusuario"]}'",
    "  AND dtoperacao >= CURRENT_DATE - '12 weeks'::INTERVAL",
    "GROUP BY 1",
    "ORDER BY 1"
]);

$chart_ultimos_meses = query_to_chart("Últimos 12 meses", "bar", [
    "SELECT (EXTRACT(YEAR FROM dtoperacao) || '-' || LPAD(EXTRACT(MONTH FROM dtoperacao)::TEXT, 2, '0')) AS periodo,",
    "  SUM(totalliquido) AS valor",
    "FROM operacao",
    "WHERE idusuario = '{$_SESSION["idusuario"]}'",
    "  AND dtoperacao >= CURRENT_DATE - '12 months'::INTERVAL",
    "GROUP BY 1",
    "ORDER BY 1"
]);

$chart_ultimos_anos = query_to_chart("Últimos 5 anos", "bar", [
    "SELECT EXTRACT(YEAR FROM dtoperacao) AS periodo,",
    "  SUM(totalliquido) AS valor",
    "FROM operacao",
    "WHERE idusuario = '{$_SESSION["idusuario"]}'",
    "  AND dtoperacao >= CURRENT_DATE - '5 years'::INTERVAL",
    "GROUP BY 1",
    "ORDER BY 1"
]);

json_success([
    "chart_semana_atual" => $chart_semana_atual,
    "chart_mes_atual" => $chart_mes_atual,
    "chart_ano_atual" => $chart_ano_atual,
    "chart_saldo_diario" => $chart_saldo_diario,
    "chart_ultimas_semanas" => $chart_ultimas_semanas,
    "chart_ultimos_meses" => $chart_ultimos_meses,
    "chart_ultimos_anos" => $chart_ultimos_anos
]);

function query_to_chart($title, $type, $query, $cumulative = false){
    $connection = connection();

    if(is_array($query)) $query = implode(" ", $query);

    $res = $connection->query($query);
    $arr = $res->fetchAll();

    $chart_labels = [];
    $chart_data = [];

    foreach($arr as $row){
        $periodo = $row["periodo"];
        $periodo = implode("/", array_reverse(explode("-", $periodo)));

        $chart_labels[] = $periodo;
        if($cumulative){
            $chart_data[] = $row["valor"] + end($chart_data);
        }else{
            $chart_data[] = $row["valor"];
        }
    }

    $config = [
        "data" => $chart_data,
        "labels" => $chart_labels,
        "title" => $title,
        "type" => $type
    ];

    switch($type){
        case "line":
            $config["backgroundColor"] = "rgba(0, 123, 255, 0.25)";
            $config["borderColor"] = "rgba(0, 123, 255, 1)";
            break;
        case "bar":
            $config["backgroundColor"] = [];
            $config["borderColor"] = [];
            foreach($config["data"] as $valor){
                if($valor < 0){
                    $config["backgroundColor"][] = "rgba(220, 53, 69, 0.25)";
                    $config["borderColor"][] = "rgba(220, 53, 69, 1)";
                }else{
                    $config["backgroundColor"][] = "rgba(40, 167, 69, 0.25)";
                    $config["borderColor"][] = "rgba(40, 167, 69, 1)";
                }
            }
            break;
    }

    return mount_chart($config);
}

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