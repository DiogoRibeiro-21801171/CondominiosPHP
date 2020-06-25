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
<meta charset="UTF-8">
<title>LOV rubricas de orçamento</title>
<script>
  function actualizarPai(id,texto) {
	//debugger;
	//parentForm é o name do form da página que chama esta.
	//pf1 é um dos campos desse form
	window.opener.document.mainform.pf1.value=id;
	window.opener.document.mainform.pf2.value=texto;
	window.close();
	return;
  }
</script>
</head>
<body>
<h3>LOV rubricas de orçamento</h3>
<?php
require 'dataHandler/Despesa_DataHandler.php';
require 'templates/app/inc_db.inc';
$idrubricaorcamento = filter_input(INPUT_GET, 'idrubricaorcamento', FILTER_SANITIZE_SPECIAL_CHARS);
$despesadatahandler = new Despesa_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
$despesadatahandler->buildListaRubricaOrcamentoLOV($idrubricaorcamento);
?>
</body>
</html>