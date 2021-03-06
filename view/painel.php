<?php
require_once(__DIR__ . "/../default/handling.php");
?>
<html lang="pt-Br">

<head>
    <?php require_once(__DIR__ . "/../default/head.php"); ?>
    <link href="style/css/view/painel.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">TRA<b>DIARY</b></span>
            <button id="btn-operacao-alterar" class="btn btn-secondary ml-auto" onclick="operacao_alternar()">
                <i class="fa fa-plus"></i>
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </nav>
    <div id="form-operacao" class="jumbotron">
        <div class="container">
            <h1 class="h5 font-weight-normal mb-3">Registro de operação diária</h1>
            <div class="form-row align-items-end">
                <div class="col-sm-6 col-md-3 form-group">
                    <label for="dtoperacao">Data da operação</label>
                    <input id="dtoperacao" type="date" class="form-control text-center">
                </div>
                <div class="col-sm-6 col-md-3 form-group">
                    <label for=contratos>Quantidade contratos</label>
                    <input id=contratos type="number" class="form-control text-center">
                </div>
                <div class="col-sm-6 col-md-3 form-group">
                    <label for="totalbruto">Total bruto no dia</label>
                    <input id="totalbruto" type="number" class="form-control text-center" step="0.010">
                </div>
                <div class="col-sm-6 col-md-3 form-group">
                    <label for="totalliquido">Total líquido no dia</label>
                    <input id="totalliquido" type="number" class="form-control text-center" step="0.010">
                </div>
                <div class="col-sm-6 col-md-3 form-group">
                    <label for="deposito">Depósito</label>
                    <input id="deposito" type="number" class="form-control text-center" step="0.010">
                </div>
                <div class="col-sm-6 col-md-3 form-group">
                    <label for="retirada">Retirada</label>
                    <input id="retirada" type="number" class="form-control text-center" step="0.010">
                </div>
                <div class="col-4 col-md-1 offset-md-2 mb-3">
                    <button class="btn btn-block btn-secondary" style="height: 38px" onclick="operacao_excluir()">
                        <i class="fa fa-trash-alt"></i>
                    </button>
                </div>
                <div class="col-8 col-md-3 mb-3">
                    <button class="btn btn-block btn-success" onclick="operacao_gravar()">Gravar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row mt-3">
            <div class="col-sm-12 col-md-6">
                <div id="grp-tipo" class="btn-group w-100 mb-2">
                    <button type="button" class="btn btn-primary" tipo="grafico" onclick="buscar_dados_grafico()">Gráfico</button>
                    <button type="button" class="btn btn-light" tipo="tabela" onclick="buscar_dados_tabela()">Tabela</button>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div id="grp-tempo" class="btn-group w-100 mb-3">
                    <button type="button" class="btn btn-primary" tempo="dia">Diário</button>
                    <button type="button" class="btn btn-light" tempo="semana">Semanal</button>
                    <button type="button" class="btn btn-light" tempo="mes">Mensal</button>
                    <button type="button" class="btn btn-light" tempo="ano">Anual</button>
                </div>
            </div>
        </div>
        <div id="page-chart">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <canvas id="chart-semana-atual"></canvas>
                </div>
                <div class="col-sm-12 col-md-6">
                    <canvas id="chart-ultimos-30-dias"></canvas>
                </div>
                <div class="col-sm-12 col-md-6">
                    <canvas id="chart-ultimas-semanas"></canvas>
                </div>
                <div class="col-sm-12 col-md-6">
                    <canvas id="chart-ultimos-meses"></canvas>
                </div>
                <div class="col-sm-12 col-md-6">
                    <canvas id="chart-ultimos-anos"></canvas>
                </div>
                <div class="col-sm-12 col-md-6">
                    <canvas id="chart-mes-atual"></canvas>
                </div>
                <div class="col-sm-12 col-md-6">
                    <canvas id="chart-ultimos-90-dias"></canvas>
                </div>
                <div class="col-sm-12 col-md-6">
                    <canvas id="chart-ano-atual"></canvas>
                </div>
                <div class="col-sm-12 col-md-12 mb-5">
                    <canvas id="chart-saldo-diario"></canvas>
                </div>
            </div>
        </div>
        <div id="page-table"></div>
    </div>
</body>

</html>