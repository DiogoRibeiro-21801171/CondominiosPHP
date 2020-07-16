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
<title>Noticias</title>
<?php require 'templates/app/inc_head01.inc'; ?>
</head>
<body onload="cleanOnLoad()">
<?php require 'templates/app/inc_head02.inc'; ?>
<!-- place the tree building script where you'd like in the body -->
<script>
/*Choose current leaf - must be done before create tree*/
var currentLeaf = 'Noticias';
<?php require 'templates/gestor/inc_tree_gestor.inc'; ?>
</script>
<?php require 'templates/app/inc_head03.inc'; ?>
<!-- .................................................................................................................................. -->
<div class="clearfix">
    <h2>Noticias</h2>
    <a class="btn btn-success pull-right" data-toggle="modal" data-target="#">Criar Noticia</a>


</div>

<?php
if ((isset($_SESSION["idcondominio"]) == 0)) {
    $_SESSION["msg"] = "idcondominio não está definido!";
    header('Location: Login.php');
    exit();
} else {
    $idcondominio = $_SESSION["idcondominio"];
}
$_SESSION["msg"] = "";

require 'dataHandler/Noticia_DataHandler.php';
require 'templates/app/inc_db.inc';
$noticiadatahandler = new Noticia_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
$noticiadatahandler->listaNoticias($idcondominio);


?>
<!-- .................................................................................................................................. -->	
<?php require 'templates/app/inc_head04.inc'; ?>
</body>
</html>