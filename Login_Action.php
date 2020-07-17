<?php
require "dataHandler/UtilizadorAplicacao_DataHandler.php";
require 'templates/app/inc_db.inc';
session_start();
//$loginUsername = $_GET["loginUsername"]; //para evitar SQL Injection usar o código abaixo
$loginUsername = filter_input(INPUT_GET, 'loginUsername', FILTER_SANITIZE_SPECIAL_CHARS);
if (empty($loginUsername)) {
    $_SESSION["msg"] = "Parametro nome não preenchido";
    header('Location:Login.php');
    exit();
} else {
    $_SESSION["loginUsername"] = $loginUsername;
    //$palavrachave = $_GET["palavrachave"]; //para evitar SQL Injection usar o código abaixo
    $palavrachave = filter_input(INPUT_GET, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
    if (empty($palavrachave)) {
        $_SESSION["msg"] = "Parametro password não preenchido";
        header('Location:Login.php');
        exit();
    } else {
        
        //parametros importandos do ficheiro de configuração para abrir a ligação à BD
        $dataHandlerUserApp = new UtilizadorAplicacao_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
        
        //vamos validar o username e password introduzidos pelo utilizador no formulário
        //$tipoUtilizador = $dataHandlerUserApp->validateLoginOld($loginUsername, $palavrachave);
        $row = $dataHandlerUserApp->validateLogin($loginUsername, $palavrachave);
        $tipoUtilizador = $row["idperfilutilizador"];
        if ($tipoUtilizador=="Utilizador ou password errados") {
            $_SESSION["msg"] = "Utilizador ou password errados";
            header('Location:Login.php');
            exit();
        }
        $nome = $row["nome"];
        $genero = $row["genero"];
        $idcondominio = $row["idcondominio"];
        $morada = $row["morada"];
        $ultimaatualizacao = $row["ultimaatualizacao"];
       
//        print "<html><body>" . "\n";
//        print $loginUsername . "<br>\n";
//        print $palavrachave . "<br>\n";
//        print $tipoUtilizador . "<br>\n";
//        print $idcondominio . "<br>\n";
//        print "</body></html>" . "\n";
//        exit();
        
        
        $_SESSION["nome"] = $nome;
        $_SESSION["genero"] = $genero;
        $_SESSION["idcondominio"] = $idcondominio;
        $_SESSION["morada"] = $morada;
        $_SESSION["ultimaatualizacao"] = $ultimaatualizacao;
        

        if (strcmp($tipoUtilizador, "ADMINISTRADOR") == 0) {
            $_SESSION["tipoUtilizador"] = "ADMINISTRADOR";
            header('Location:data/admin/PaginaInical_admin.php');
            exit();
        } else if (strcmp($tipoUtilizador, "GESTOR") == 0) {
            $_SESSION["tipoUtilizador"] = "GESTOR";
            header('Location:PaginaInicial.php');
            exit();
        } else if (strcmp($tipoUtilizador, "CONDOMINO") == 0) {
            $_SESSION["tipoUtilizador"] = "CONDOMINO";
            header('Location:PaginaInicial.php');
            exit();
        } else {
            $_SESSION["tipoUtilizador"] = "";
            $_SESSION["msg"] = $tipoUtilizador;
            header('Location: Login.php');
            exit();
        }
    }
}
?>