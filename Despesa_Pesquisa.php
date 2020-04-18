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
<title>Pesquisa de despesas</title>
<?php require 'inc_head01.inc'; ?>
</head>
<body onload="cleanOnLoad()">
<?php require 'inc_head02.inc'; ?>
<!-- place the tree building script where you'd like in the body -->
<script>

//função necessária para LOV
function abrirJanelaFilho(file,window) {
  //debugger;
  janelaFilho=open(file,window,'resizable=no,width=400,height=400');
  if (janelaFilho.opener == null) {
    janelaFilho.opener = self;
  }
}

/*Choose current leaf - must be done before create tree*/
var currentLeaf = 'Despesas';
<?php require 'inc_tree.inc'; ?>
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
require 'Despesa_DataHandler.php';
require 'inc_db.inc';
?>
<h2>Pesquisa de despesa</h2>
<form name="mainform" action="Despesa_Pesquisa.php" method="GET">
    <table>
        <tr>
            <td>Fornecedor:</td>
            <td>
            	<?php
            	$fornecedor = filter_input(INPUT_GET, 'fornecedor', FILTER_SANITIZE_SPECIAL_CHARS);
            	//print "<p> {$fornecedor} </p>";
            	if (empty($fornecedor)) {
            	    print "<input type=\"text\" name=\"fornecedor\" value=\"%\">";
            	} else {
            	    print "<input type=\"text\" name=\"fornecedor\" value=\"{$fornecedor}\">";
            	}
            	?>
            </td>
        </tr>
        <tr>
            <td>Rubrica de orçamento:</td>
            <td>
            	<?php
            	//$idrubricaorcamento = filter_input(INPUT_GET, 'idrubricaorcamento', FILTER_SANITIZE_SPECIAL_CHARS);
            	////print "<p> {$idrubricaorcamento} </p>";
            	//$despesadatahandler = new Despesa_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
            	//$despesadatahandler->buildListaRubricaOrcamento($idrubricaorcamento);
                ?>
                <?php
                $pf1 = filter_input(INPUT_GET, 'pf1', FILTER_SANITIZE_SPECIAL_CHARS);
                if (empty($pf1)) {
                    $pf1="%";
                    print("   <input name=\"pf1\" type=\"text\" value=\"%\" size=\"8\">");
                } else {
                    print("   <input name=\"pf1\" type=\"text\" value=\"{$pf1}\" size=\"8\">");
                }
                $pf2 = filter_input(INPUT_GET, 'pf2', FILTER_SANITIZE_SPECIAL_CHARS);
                if (empty($pf2) or (strcmp($pf1,"%") == 0)) {
                    print("   <input name=\"pf2\" type=\"text\" value=\"\" size=\"40\" disabled>");
                } else {
                    print("   <input name=\"pf2\" type=\"text\" value=\"{$pf2}\" size=\"40\" disabled>");
                }
                print("&nbsp;<input type=\"button\" value=\"ldv\" onclick=\"abrirJanelaFilho('Despesa_LOV_RubricaOrcamento.php','win2')\">");
            	?>                
            </td>
        </tr>
        <tr>
            <td>Data da fatura:</td>
            <td>
            	<?php
            	$datafatura = filter_input(INPUT_GET, 'datafatura', FILTER_SANITIZE_SPECIAL_CHARS);
            	//print "<p> {$datafatura} </p>";
            	if (empty($datafatura)) {
            	    print "<input type=\"text\" name=\"datafatura\" value=\"" . date("Y") . "%\">";
            	} else {
            	    print "<input type=\"text\" name=\"datafatura\" value=\"{$datafatura}\">";
            	}
                ?>
            </td>
        </tr>
        <tr>
            <td>Data limite de pagamento:</td>
            <td>
            	<?php
            	$datalimitepagamento = filter_input(INPUT_GET, 'datalimitepagamento', FILTER_SANITIZE_SPECIAL_CHARS);
            	//print "<p> {$datalimitepagamento} </p>";
            	if (empty($datalimitepagamento)) {
            	    print "<input type=\"text\" name=\"datalimitepagamento\" value=\"%\">";
            	} else {
            	    print "<input type=\"text\" name=\"datalimitepagamento\" value=\"{$datalimitepagamento}\">";
            	}
                ?>
            </td>
        </tr>
        <tr>
            <td>Data de pagamento:</td>
            <td>
            	<?php
            	$datapagamento = filter_input(INPUT_GET, 'datapagamento', FILTER_SANITIZE_SPECIAL_CHARS);
            	//print "<p> {$datapagamento} </p>";
            	if (empty($datapagamento)) {
            	    print "<input type=\"text\" name=\"datapagamento\" value=\"%\">";
            	} else {
            	    print "<input type=\"text\" name=\"datapagamento\" value=\"{$datapagamento}\">";
            	}
                ?>
            </td>
        </tr>
        <tr>
            <td>Valor com IVA:</td>
            <td>
            	<?php
            	$valorcomiva = filter_input(INPUT_GET, 'valorcomiva', FILTER_SANITIZE_SPECIAL_CHARS);
            	//print "<p> {$valorcomiva} </p>";
            	if (empty($valorcomiva)) {
            	    print "<input type=\"text\" name=\"valorcomiva\" value=\"%\">";
            	} else {
            	    print "<input type=\"text\" name=\"valorcomiva\" value=\"{$valorcomiva}\">";
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
//require 'Despesa_DataHandler.php'; foi importado acima
//require 'inc_db.inc'; foi importado acima
if (!empty($fornecedor) or !empty($datafatura) or !empty($datalimitepagamento) or !empty($datapagamento)) {
    $despesadatahandler = new Despesa_DataHandler($dbHostName, $dbDatabaseName, $dbUsername, $dbPassword);
    $despesadatahandler->pesquisaDespesa($idcondominio, $fornecedor, $pf1, $valorcomiva, $datafatura, $datalimitepagamento, $datapagamento);
}
?>

<!-- .................................................................................................................................. -->	
<?php require 'inc_head04.inc'; ?>
</body>
</html>