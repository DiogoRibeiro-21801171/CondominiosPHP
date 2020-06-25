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
<title>Mapa resumo de pagamentos</title>
<?php require 'templates/app/inc_head01.inc'; ?>
</head>
<body onload="cleanOnLoad()">
<?php require 'templates/app/inc_head02.inc'; ?>
<!-- place the tree building script where you'd like in the body -->
<script>
/*Choose current leaf - must be done before create tree*/
var currentLeaf = 'Mapa quotas extra';
<?php require 'templates/gestor/inc_tree_gestor.inc'; ?>
showBranch('branch025000000');
</script>
<?php require 'templates/app/inc_head03.inc'; ?>
<!-- .................................................................................................................................. -->
<h2>Mapa de pagamentos de quotas extra</h2>

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

<form action="PagamentoQuotasExtra_MapaResumo.php" method="GET">
    <div class="form-group">
        <label>Tipo de quota:</label>
        <select name="tipoquota">
            <?php
            $primeiraVez = false;
            $tipoquota = filter_input(INPUT_GET, 'tipoquota', FILTER_SANITIZE_SPECIAL_CHARS);
            if (empty($tipoquota)) {
                $tipoquota = "Elevadores";
                $primeiraVez = true;
            }
            if ((strcmp($tipoquota, "Elevadores") == 0)) {
                print '<option value="Elevadores" selected="selected">Elevadores</option>';
            } else {
                print '<option value="Elevadores">Elevadores</option>';;
            }
            ?>
        </select>
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
if ($primeiraVez == false) {
    //$ano = date("Y");
    $quota = "Extra";
    $quotadatahandler = new Quota_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
    $quotadatahandler->mapaResumoPagamento($idcondominio, 'Elev1', 'Elev2', 'Elev3', 'Elev4', $ano, $quota);
}
?>
<!-- .................................................................................................................................. -->	
<?php require 'templates/app/inc_head04.inc'; ?>
</body>
</html>