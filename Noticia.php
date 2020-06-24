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
<?php require 'templates/inc_head01.inc'; ?>
</head>
<body onload="cleanOnLoad()">
<?php require 'templates/inc_head02.inc'; ?>
<!-- place the tree building script where you'd like in the body -->
<script>
/*Choose current leaf - must be done before create tree*/
var currentLeaf = 'Noticias';
<?php require 'templates/inc_tree.inc'; ?>
</script>
<?php require 'templates/inc_head03.inc'; ?>
<!-- .................................................................................................................................. -->
<div class="clearfix">
    <h2>Noticias</h2>
    <a class="btn btn-success pull-right" data-toggle="modal" data-target="#eliminarNoticia">Criar Noticia</a>

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
require 'templates/inc_db.inc';
$noticiadatahandler = new Noticia_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
$noticiadatahandler->listaNoticias($idcondominio);

print ("
    <!-- Apagar Noticia -->
    <div class=\"modal fade\" id=\"eliminarNoticia\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"eliminarNoticiaLabel\" aria-hidden=\"true\">
        <div class=\"modal-dialog\" role=\"document\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\" id=\"eliminarNoticiaLabel\">Eliminar Noticia</h5>
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Fechar\">
                        <span aria-hidden=\"true\">&times;</span>
                    </button>
                </div>
                <div class=\"modal-body\">
                    Pretende eliminar esta noticia?
                </div>
                <div class=\"modal-footer\">
                    <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Cancelar</button>
                    <button onclick='' type=\"button\" class=\"btn btn-danger\">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
")

?>
<!-- .................................................................................................................................. -->	
<?php require 'templates/inc_head04.inc'; ?>
</body>
</html>