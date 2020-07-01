<?php

// Define nivel de erros
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

// Inicia a sessao
session_start();

// Arquivos padroes
require_once(__DIR__."/function.php");

// Verifica o login
if(!in_array(current_view(), ["login", "logout"])){
    if(!$_SESSION["idusuario"]){
        http_response_code(401);
        die();
    }
}

// Auto registro das classes
spl_autoload_register(function($classname){
    // Nome da classe minusculo
    $classname_lower = strtolower($classname);

    // Procura pela classe de banco de dados
    $filename = __DIR__."/../class/database/table/{$classname_lower}.class.php";
    if(file_exists($filename)){
        require_once($filename);
    }

    // Procura pela classe dos helpers
    $filename = __DIR__."/../class/helper/{$classname_lower}.class.php";
    if(file_exists($filename)){
        require_once($filename);
    }
});