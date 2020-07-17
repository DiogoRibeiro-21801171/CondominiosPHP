<?php
session_start();
if (!isset($_SESSION["tipoUtilizador"]) || strcmp($_SESSION["tipoUtilizador"], "") == 0) {
    $_SESSION["msg"] = "Utilizador não autenticado";
    header('Location: ../../Login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <title>Modificar User</title>
    <?php require '../../templates/app/inc_head01.inc'; ?>
</head>
<body onload="cleanOnLoad()">
<?php require '../../templates/app/inc_head02.inc'; ?>
<!-- place the tree building script where you'd like in the body -->
<script>
    /*Choose current leaf - must be done before create tree*/
    var currentLeaf = 'Modificar Utilizador';
    <?php require '../../templates/admin/inc_tree_admin.inc'; ?>
</script>
<?php require '../../templates/app/inc_head03.inc'; ?>
<!-- .................................................................................................................................. -->
<div class="clearfix">
    <h2>Modificar Utilizador</h2>
    <a class="btn btn-success pull-right" data-toggle="modal" data-target="#criarUtilizadorModal">Criar Utilizador</a>
</div>

<?php
if ((isset($_SESSION["idcondominio"]) == 0)) {
    $_SESSION["msg"] = "idcondominio não está definido!";
    header('Location: ../../Login.php');
    exit();
} else {
    $idcondominio = $_SESSION["idcondominio"];
}
$_SESSION["msg"] = "";

require '../../dataHandler/admin/ModificarUser_DataHandler.php';
require '../../templates/app/inc_db.inc';
$userdatahandler = new ModificarUser_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
$userdatahandler->listaUsers($idcondominio);

?>

<!-- .................................................................................................................................. -->
<?php require '../../templates/app/inc_head04.inc'; ?>
<script>
    function apagarUser(idcondominio, login) {
        <?php
        $stmt = $userdatahandler->stmt;
        $query = "delete from utilizadoraplicacao where idcondominio = ? and login = ?;";

        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        //"s" significa uma variavel do tipo string. Se fosse uma string e um int seria "si"
        mysqli_stmt_bind_param($stmt, "ss", $idcondominio, $login);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        ?>
    }
</script>
</body>
</html>