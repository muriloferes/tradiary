<?php

session_start();

if($_SESSION["idusuario"]){
    header("Location: painel");
}else{
    header("Location: login");
}

