<?php
session_start();
if (!isset($_SESSION["tipoUtilizador"]) || strcmp($_SESSION["tipoUtilizador"], "") == 0) {
    $_SESSION["msg"] = "Utilizador não autenticado";
    header('Location: Login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<title>Demonstracao de resultados</title>
<?php require 'templates/app/inc_head01.inc'; ?>
</head>
<body onload="cleanOnLoad()">
<?php require 'templates/app/inc_head02.inc'; ?>
<!-- place the tree building script where you'd like in the body -->
<script>
/*Choose current leaf - must be done before create tree*/
var currentLeaf = 'Demonstração de resultados';
<?php require 'templates/gestor/inc_tree_gestor.inc'; ?>
showBranch('branch025010000');
</script>
<?php require 'templates/app/inc_head03.inc'; ?>
<!-- .................................................................................................................................. -->
<?php
if ((isset($_SESSION["msg"])) && !(strcmp($_SESSION["msg"], "") == 0)) {
    print '<div class="msgErro">' . $_SESSION["msg"] . '</div>';
}
if ((isset($_SESSION["idcondominio"]) == 0)) {
    $_SESSION["msg"] = "idcondominio não está definido!";
    header('Location: Login.php');
    exit();
} else {
    $idcondominio = $_SESSION["idcondominio"];
}

$_SESSION["msg"] = "";
?>
<h2>Demonstracao de resultados</h2>
<form action="DemonstracaoResultados_Mapa.php" method="GET">
    <div class="form-group">
        <label>Ano:</label>
        <?php
        $ano = filter_input(INPUT_GET, 'ano', FILTER_SANITIZE_SPECIAL_CHARS);
        //print "<p> {$ano} </p>";
        if (empty($ano)) {
            print "<input type=\"text\" name=\"ano\" value=\"" . date("Y") . "\">";
        } else {
            print "<input type=\"text\" name=\"ano\" value=\"{$ano}\">";
        }
        ?>
    </div>
    <input type="submit" value="Pesquisar">
</form>

<hr>
<?php
require 'dataHandler/DemonstracaoResultados_DataHandler.php';
require 'templates/app/inc_db.inc';
if (!empty($ano)) {
    print("<h3>Despesas <input type=\"checkbox\" id=\"obj_01\" onclick=\"showHide(this)\" class=\"mycheckbox\" ></h3>");
    print("<div id=\"obj_01_tb\" style=\"display:none\">");
    $demonstracaoresultadosdatahandler = new DemonstracaoResultados_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
    $demonstracaoresultadosdatahandler->listaDemonstracaoResultadosDespesas($idcondominio, $ano);
    print("</div>");
    print("<h3>Receitas <input type=\"checkbox\" id=\"obj_02\" onclick=\"showHide(this)\" class=\"mycheckbox\" ></h3>");
    print("<div id=\"obj_02_tb\" style=\"display:none\">");
    $demonstracaoresultadosdatahandler = new DemonstracaoResultados_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
    $tipoReceita = "Receita";
    $demonstracaoresultadosdatahandler->listaDemonstracaoResultadosReceitas($idcondominio, $ano, $tipoReceita);
    print("</div>");
    print("<h3>Resultado <input type=\"checkbox\" id=\"obj_05\" onclick=\"showHide(this)\" class=\"mycheckbox\" ></h3>");
    print("<div id=\"obj_05_tb\" style=\"display:none\">");
    $demonstracaoresultadosdatahandler = new DemonstracaoResultados_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
    $demonstracaoresultadosdatahandler->demonstracaoResultadosResumo($idcondominio, $ano);
    print("</div>");
}
?>
<!-- .................................................................................................................................. -->	
<?php require 'templates/app/inc_head04.inc'; ?>
</body>
</html>