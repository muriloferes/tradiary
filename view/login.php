<?php
require_once(__DIR__ . "/../default/handling.php");

if($_SESSION["idusuario"]){
    header("Location: painel");
    die();
}
?>
<html lang="pt-Br">

<head>
    <?php require_once(__DIR__ . "/../default/head.php"); ?>
</head>

<body style="align-items: center; display: flex">
    <div style="margin: auto; max-width: 100%; padding: 15px; width: 350px">
        <h1 class="h3 mb-4 font-weight-normal text-center">LOGIN</h1>
        <label for="idusuario">Usuário</label>
        <select id="idusuario" class="form-control mb-3">
            <option value=""></option>
            <?php
            die("aqui 5");
            $usuario = new Usuario();
            $arr_usuario = $usuario->search("status = 'ativo'", "nome");
            foreach($arr_usuario as $usuario){
                echo "<option value='{$usuario->getidusuario()}'>{$usuario->getnome()}</option>";
            }
            ?>
        </select>
        <label for="senha">Senha</label>
        <input type="password" id="senha" class="form-control mb-3">
        <button class="btn btn-lg btn-primary btn-block" onclick="entrar()">Entrar</button>
    </div>
</body>

</html>