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
<title>Lista de receitas extra</title>
<?php require 'inc_head01.inc'; ?>
</head>
<body onload="cleanOnLoad()">
<?php require 'inc_head02.inc'; ?>
<!-- place the tree building script where you'd like in the body -->
<script>
/*Choose current leaf - must be done before create tree*/
var currentLeaf = 'Receitas extra';
<?php require 'inc_tree.inc'; ?>
showBranch('branch025000000');
</script>
<?php require 'inc_head03.inc'; ?>
<!-- .................................................................................................................................. -->
<h2>Lista de receitas extra</h2>

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

<form action="ReceitasExtra_Lista.php" method="GET">
    <table>
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
if (!empty($ano)) {
    require 'ReceitaExtra_DataHandler.php';
    require 'inc_db.inc';
    $receitaextra = new ReceitaExtra_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
    $receitaextra->pesquisaReceitaExtra($idcondominio, $ano);

}
?>
<!-- .................................................................................................................................. -->	
<?php require 'inc_head04.inc'; ?>
</body>
</html>