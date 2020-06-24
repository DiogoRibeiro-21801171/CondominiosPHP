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
<title>Pesquisa de problemas</title>
<?php require 'templates/inc_head01.inc'; ?>
</head>
<body onload="cleanOnLoad()">
<?php require 'templates/inc_head02.inc'; ?>
<!-- place the tree building script where you'd like in the body -->
<script>
/*Choose current leaf - must be done before create tree*/
var currentLeaf = 'Problemas';
<?php require 'templates/inc_tree.inc'; ?>
</script>
<?php require 'templates/inc_head03.inc'; ?>
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
<h2>Pesquisa de problemas</h2>
<form action="Problema_Pesquisa.php" method="GET">
    <table>
        <tr>
            <td>Status:</td>
            <td>
            	<select name="status">
    				<?php
    				$primeiraVez = false;
                	$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
                	if (empty($status)) {
                	    $status = "NãoResolvidos";
                	    $primeiraVez = true;
                	}
                	if ((strcmp($status, "NãoResolvidos") == 0)) {
                	    print '<option value="NaoResolvidos" selected="selected">Não resolvidos</option>';
                	} else {
                	    print '<option value="NaoResolvidos">Não resolvidos</option>';;
                	}
                	if ((strcmp($status, "%") == 0)) {
                	    print '<option value="%" selected="selected">%</option>';
                	} else {
                	    print '<option value="%">%</option>';;
                	}
                	if ((strcmp($status, "Novo") == 0)) {
                	    print '<option value="Novo" selected="selected">Novo</option>';
                	} else {
                	    print '<option value="Novo">Novo</option>';;
                	}
                	if ((strcmp($status, "Em resolução") == 0)) {
                	    print '<option value="Em resolução" selected="selected">Em resolução</option>';
                	} else {
                	    print '<option value="Em resolução">Em resolução</option>';;
                	}
                	if ((strcmp($status, "À espera") == 0)) {
                	    print '<option value="À espera" selected="selected">À espera</option>';
                	} else {
                	    print '<option value="À espera">À espera</option>';;
                	}
                	if ((strcmp($status, "Resolvido") == 0)) {
                	    print '<option value="Resolvido" selected="selected">Resolvido</option>';
                	} else {
                	    print '<option value="Resolvido">Resolvido</option>';;
                	}
                	?>
            	</select>
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
require 'dataHandler/Problema_DataHandler.php';
require 'templates/inc_db.inc';
if ($primeiraVez == false) {
    $problemadatahandler = new Problema_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
    $problemadatahandler->pesquisaProblema($idcondominio, $status);
}
?>
<!-- .................................................................................................................................. -->	
<?php require 'templates/inc_head04.inc'; ?>
</body>
</html>