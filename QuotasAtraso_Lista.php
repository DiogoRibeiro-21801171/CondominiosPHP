<?php
session_start();
if (!isset($_SESSION["tipoUtilizador"]) || strcmp($_SESSION["tipoUtilizador"], "") == 0) {
    $_SESSION["msg"] = "Utilizador não autenticado";
    header('Location:Login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<title>Lista de pagamentos em atraso</title>
<?php require 'templates/app/inc_head01.inc'; ?>
</head>
<body onload="cleanOnLoad()">
<?php require 'templates/app/inc_head02.inc'; ?>
<!-- place the tree building script where you'd like in the body -->
<script>
/*Choose current leaf - must be done before create tree*/
var currentLeaf = 'Quotas em atraso';
<?php require 'templates/gestor/inc_tree_gestor.inc'; ?>
showBranch('branch025000000');
</script>
<?php require 'templates/app/inc_head03.inc'; ?>
<!-- .................................................................................................................................. -->
<h2>Lista de quotas em atraso</h2>

<?php
if ((isset($_SESSION["idcondominio"]) == 0)) {
    $_SESSION["msg"] = "idcondominio não está definido!";
    header('Location:Login.php');
    exit();
} else {
    $idcondominio = $_SESSION["idcondominio"];
}
$_SESSION["msg"] = "";
?>

<form action="QuotasAtraso_Lista.php" method="GET">
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
if (!empty($ano)) {
    require 'dataHandler/Quota_DataHandler.php';
    require 'templates/app/inc_db.inc';
    //print("<h3>Pagamentos em atraso <input type=\"checkbox\" id=\"obj_03\" onclick=\"showHide(this)\" class=\"mycheckbox\" ></h3>");
    //print("<div id=\"obj_03_tb\" style=\"display:none\">");
    print("<h3>Pagamentos em atraso</h3>");
    $quotas = new Quota_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
    $quotas->listaQuotasEmAtraso($idcondominio, $ano);
    print("</div>");
}
?>
<!-- .................................................................................................................................. -->	
<?php require 'templates/app/inc_head04.inc'; ?>
</body>
</html>