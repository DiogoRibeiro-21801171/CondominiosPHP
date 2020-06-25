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
<title>Pesquisa de pagamento</title>
<?php require 'templates/app/inc_head01.inc'; ?>
</head>
<body onload="cleanOnLoad()">
<?php require 'templates/app/inc_head02.inc'; ?>
<!-- place the tree building script where you'd like in the body -->
<script>
/*Choose current leaf - must be done before create tree*/
var currentLeaf = 'Detalhes quotas';
<?php require 'templates/gestor/inc_tree_gestor.inc'; ?>
showBranch('branch025000000');
</script>
<?php require 'templates/app/inc_head03.inc'; ?>
<!-- .................................................................................................................................. -->
<?php
if ((isset($_SESSION["msg"])) && !(strcmp($_SESSION["msg"], "") == 0)) {
    print '<div class="msgErro">' . $_SESSION["msg"] . '</div>';
}
if ((isset($_SESSION["idcondominio"]) == 0)) {
    $_SESSION["msg"] = "idcondominio não está definido!";
    header('Location:Login.php');
    exit();
} else {
    $idcondominio = $_SESSION["idcondominio"];
}

$_SESSION["msg"] = "";
?>
<h2>Detalhes de pagamentos de quotas</h2>
<form action="PagamentoQuotas_Detalhe.php" method="GET">
    <div class="form-group">
        <label>Fração:</label>
        <?php
        $idfracao = filter_input(INPUT_GET, 'idfracao', FILTER_SANITIZE_SPECIAL_CHARS);
        //print "<p> {$idfracao} </p>";
        if (empty($idfracao)) {
            print "<input type=\"text\" name=\"idfracao\" value=\"%\">";
        } else {
            print "<input type=\"text\" name=\"idfracao\" value=\"{$idfracao}\">";
        }
        ?>
    </div>
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
require 'dataHandler/Quota_DataHandler.php';
require 'templates/app/inc_db.inc';
if (!empty($ano) or !empty($idfracao)) {
    $quotadatahandler = new Quota_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
    $quotadatahandler->pesquisaPagamentoQuotas($idcondominio, $idfracao, $ano);
}
?>
<!-- .................................................................................................................................. -->	
<?php require 'templates/app/inc_head04.inc'; ?>
</body>
</html>
