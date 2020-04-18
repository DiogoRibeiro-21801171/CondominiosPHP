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
<title>Pagina inicial</title>
<?php require 'inc_head01.inc'; ?>
</head>
<body onload="cleanOnLoad()">
<?php require 'inc_head02.inc'; ?>
<!-- place the tree building script where you'd like in the body -->
<script>
/*Choose current leaf - must be done before create tree*/
var currentLeaf = 'Pagina principal';
<?php require 'inc_tree.inc'; ?>
</script>
<?php require 'inc_head03.inc'; ?>
<!-- .................................................................................................................................. -->
<?php
$genero = $_SESSION["genero"];
if ($genero == 'F') {
    print "<h2>Bem vinda " . $_SESSION["nome"] . "</h2>";
} else {
    print "<h2>Bem vindo " . $_SESSION["nome"] . "</h2>";
}
//print "<h3>Tipo de utilizador: " . $_SESSION["tipoUtilizador"] . "</h3>";
?>
<p>
    Este site pretende facilitar a comunicação entre condóminos e gestão do Condomínio, mostrando informação relevante. 
</p>
<p>
	Foi feito por Jose Aser e mostra a visão da Comissão de Acompanhamento, sendo independente da Prado Condomínios.
	Se encontrar algum erro nos dados apresentados por favor entre em contacto com a Comissão de Acompanhamento, 
	enviando email para <b><a href="mailto:condominio25abril17@gmail.com">condominio25abril17@gmail.com</a></b>.
</p>
<!-- .................................................................................................................................. -->	
<?php require 'inc_head04.inc'; ?>
</body>
</html>