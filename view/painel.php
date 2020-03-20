<?php
require_once(__DIR__ . "/../default/handling.php");
?>
<html lang="pt-Br">

<head>
    <?php require_once(__DIR__ . "/../default/head.php"); ?>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">Tradiary</span>
            <button class="btn btn-secondary ml-auto" onclick="operacao_abrir()">
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </nav>
    <div id="form-operacao" class="jumbotron">
        <div class="container">
            <h1 class="h5 font-weight-normal mb-3">Registro de operação diária</h1>
            <div class="form-row">
                <div class="col-sm-6 col-md-3 form-group">
                    <label for="dtoperacao">Data da operação</label>
                    <input id="dtoperacao" type="date" class="form-control text-center" value="<?=date("Y-m-d")?>">
                </div>
                <div class="col-sm-6 col-md-3 form-group">
                    <label for="dtoperacao">Qtde contratos</label>
                    <input id="dtoperacao" type="number" class="form-control text-center">
                </div>
                <div class="col-sm-6 col-md-3 form-group">
                    <label for="totalbruto">Total bruto</label>
                    <input id="totalbruto" type="number" class="form-control text-center" step="0.010">
                </div>
                <div class="col-sm-6 col-md-3 form-group">
                    <label for="totalliquido">Total líquido</label>
                    <input id="totalliquido" type="number" class="form-control text-center" step="0.010">
                </div>
            </div>
            <div class="form-row">
                <div class="col-4 col-md-1 offset-md-8">
                    <button class="btn btn-block btn-secondary" onclick="operacao_fechar()">Fechar</button>
                </div>
                <div class="col-8 col-md-3">
                    <button class="btn btn-block btn-success" onclick="operacao_gravar()">Gravar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row mt-3">
            <div class="col-sm-12 col-md-6">
                <div class="btn-group w-100 mb-2">
                    <button type="button" class="btn btn-primary">Líquido</button>
                    <button type="button" class="btn btn-light">Bruto</button>
                    <button type="button" class="btn btn-light">Contratos</button>
                    <button type="button" class="btn btn-light">Depósitos</button>
                    <button type="button" class="btn btn-light">Retiradas</button>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="btn-group w-100 mb-3">
                    <button type="button" class="btn btn-primary">Diário</button>
                    <button type="button" class="btn btn-light">Semanal</button>
                    <button type="button" class="btn btn-light">Mensal</button>
                    <button type="button" class="btn btn-light">Anual</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>