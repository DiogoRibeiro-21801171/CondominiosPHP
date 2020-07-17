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
    <a class="btn btn-success pull-right" data-toggle="modal" data-target="#criarNoticiaModal">Criar Noticia</a>

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

<script>

    function noticiaAEliminar() {
        $(document).ready(function () {
            $(document).on('click', 'a[data-role=eliminar]', function () {
                return $(this).data('id')
            })
        });
    }

    function noticiaAEditar() {
        $(document).ready(function () {
            $(document).on('click', 'a[data-role=editar]', function () {
                return $(this).data('id')
            })
        });
    }

    function criarNoticia(idcondominio, login) {
        <?php
            /*
        $stmt = $noticiadatahandler->stmt;
        $query = "INSERT INTO noticia(idcondominio, data, noticia) VALUES (?, ?, ?);";

        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        //"s" significa uma variavel do tipo string. Se fosse uma string e um int seria "si"
        mysqli_stmt_bind_param($stmt, "ss", $idcondominio, $data, $noticia);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
          */
        ?>
    }

    function apagarNoticia() {
        <?php

        $stmt = $noticiadatahandler->stmt;
        $query = "DELETE FROM noticia where idnoticia=" . "<script type=\"text/JavaScript\"> noticiaAEliminar() </script>";

        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        //"s" significa uma variavel do tipo string. Se fosse uma string e um int seria "si"
        mysqli_stmt_bind_param($stmt, "s", $idnoticia);
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