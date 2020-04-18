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
<?php require 'inc_head01.inc'; ?>
</head>
<body onload="cleanOnLoad()">
<?php require 'inc_head02.inc'; ?>
<!-- place the tree building script where you'd like in the body -->
<script>
/*Choose current leaf - must be done before create tree*/
var currentLeaf = 'Detalhes de pagamentos de quotas extra';
<?php require 'inc_tree.inc'; ?>
showBranch('branch025000000');
</script>
<?php require 'inc_head03.inc'; ?>
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
<h2>Detalhes de pagamentos de quotas extra</h2>
<form action="PagamentoQuotasExtra_Detalhe.php" method="GET">
    <table>
        <tr>
            <td>Fração:</td>
            <td>
            	<?php
            	$idfracao = filter_input(INPUT_GET, 'idfracao', FILTER_SANITIZE_SPECIAL_CHARS);
            	//print "<p> {$idfracao} </p>";
            	if (empty($idfracao)) {
            	    print "<input type=\"text\" name=\"idfracao\" value=\"%\">";
            	} else {
            	    print "<input type=\"text\" name=\"idfracao\" value=\"{$idfracao}\">";
            	}
                ?>
            </td>
        </tr>
        <tr>
            <td>Ano:</td>
            <td>
            	<?php
            	$ano = filter_input(INPUT_GET, 'ano', FILTER_SANITIZE_SPECIAL_CHARS);
            	//print "<p> {$ano} </p>";
            	if (empty($ano)) {
            	    print "<input type=\"text\" name=\"ano\" value=\"" . date("Y") . "\">";
            	} else {
            	    print "<input type=\"text\" name=\"ano\" value=\"{$ano}\">";
            	}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="Pesquisar">
            </td>
        </tr>
    </table>
</form>
<hr>

<?php
require 'Quota_DataHandler.php';
require 'inc_db.inc';
if (!empty($ano) or !empty($idfracao)) {
    $quotadatahandler = new Quota_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
    $quotadatahandler->pesquisaPagamentoQuotasExtra($idcondominio, $idfracao, $ano);
}
?>
<!-- .................................................................................................................................. -->	
<?php require 'inc_head04.inc'; ?>
</body>
</html>
