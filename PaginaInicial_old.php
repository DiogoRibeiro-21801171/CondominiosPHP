<?php
session_start();
if (!isset($_SESSION["tipoUtilizador"]) || strcmp($_SESSION["tipoUtilizador"], "") == 0) {
    $_SESSION["msg"] = "Utilizador não autenticado";
    header('Location:login.php');
    exit();
}
?>

<!--
<!doctype html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="Author" content="Jose Aser Lorenzo">
        <meta name="description" content="PHP Aplicação Exemplo">
        <link rel="shortcut icon" href="imagens/icon.gif">
        <link href="minhaFolhaEstilo.css" rel="stylesheet" type="text/css">
        <title>Pagina Inicial</title>
    </head>
    <body>
        <img src="imagens/top.jpg" align="right"  border="0" alt="top.jpg">
        
        <form action="resultadoPesquisa.php" method="GET">
            <table border="">
                <tr>
                    <td>Avião:</td>
                    <td><input type="text" size="40" name="descricaoAviao" value="%">(utilize % como "wildcard")</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" value="Pesquisar">
                    </td>
                </tr>
            </table>
        </form>
        <br>
        <a href="login.php">Sair da aplicação</a>
    </body>
</html>
-->
<!DOCTYPE html>
<html lang="pt">
<head>
<title>Pagina inicial</title>
<meta charset="utf-8">
<meta name="Author" content="Jose Aser Lorenzo">
<link rel="shortcut icon" href="/condominio/imagens/icon.gif">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->
<link rel="stylesheet" href="/condominio/css/bootstrap.min.css" type="text/css" />
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />  -->
<link rel="stylesheet" href="/condominio/css/style.min.css" />
<link rel="StyleSheet" href="/condominio/css/turtlelearning.css" type="text/css" />

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"> </script>  -->
<script src="/condominio/css/jquery.min.js"> </script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"> </script> -->
<script src="/condominio/css/jstree.min.js"> </script>
<script src="/condominio/css/turtlelearningtree.js"></script>
<script>
function cleanOnLoad() {
	var x = document.getElementsByClassName("mycheckbox");
	for (var i = 0; i < x.length; i++) {
	    x[i].checked = false;
	} 
}
function showHide(me) {
	var idObjShow1 = document.getElementById(me.id);
	var idObjShow2 = document.getElementById(me.id+'_tb');
	if (idObjShow1.checked) {
	   idObjShow2.style.display = 'block';
	} else {
	   idObjShow2.style.display = 'none';
	}
}
</script>
</head>
<body onload="cleanOnLoad()">
	
	<div class="container-fluid">
		<!-- Cabecalho ===================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<table>
					<tr>
						<td class="tabletitlerow1">
							<a href="http://www.turtlelearning.com">
								<img src="/condominio/imagens/top.jpg" width="70x" alt="logo">
							</a>
						</td>
						<td class="tabletitlerow1">Página principal
							<span id="event_result3">
							</span>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="row">
			<!-- arvore ================================================================================ -->
			<div class="col-sm-2">
				<div id="treemenu1" class="turtlelearningtree">
<!-- place the tree building script where you'd like in the body -->
<script>
/*Choose current leaf - must be done before create tree*/
var currentLeaf = 'Pagina principal';
var myTree = new tree();
myTree.add(new leaf('Pagina principal','PaginaInicial.php'));
var branch025000000 = new branch('branch025000000','Receitas');
	branch025000000.add(new leaf('Mapa resumo','Pagamento_MapaResumo.php'));
	branch025000000.add(new leaf('Detalhe por fração','Pagamento_ReceitasPorFracao.php'));
myTree.add(branch025000000);
var branch025010000 = new branch('branch025010000','Despesas');
	branch025010000.add(new leaf('Mapa resumo','despesasmaparesumo.php'));
	branch025010000.add(new leaf('Detalhe despesa','despesasmaparesumo.php'));
myTree.add(branch025010000);
myTree.add(new leaf('Sair','login.php'));
myTree.write();
</script>
				</div>
			</div>
			<!-- colunaMeio ============================================================================== -->
			<div class="col-sm-10">
				<div id="colunameio">
<!-- .................................................................................................................................. -->
<!-- ------------------------------------------------------------------------ -->
<h2>Condomínio Av 25 de Abril, 17, 1675-184 Pontinha</h2>

<p>
    Este site pretende facilitar a comunicação entre condóminos e gestão do Condomínio, mostrando informação relevante. 
</p>
<p>
	Foi feito por Jose Aser e mostra a visão da Comissão de Acompanhamento, sendo independente da Prado Condomínios.
	Se encontrar algum erro nos dados apresentados por favor entre em contacto com a Comissão de Acompanhamento, 
	enviando email para <b><a href="mailto:condominio25abril17@gmail.com">condominio25abril17@gmail.com</a></b>.
</p>
<hr>
<?php
print "<p>Bem vindo " . $_SESSION["loginUsername"] . "</p>";
print "<p>Tipo de utilizador: " . $_SESSION["tipoUtilizador"] . "</p>";
?>

<!-- ------------------------------------------------------------------------ -->
<!-- .................................................................................................................................. -->	
				</div>	
			</div>
		</div>
		<div class="row">
			<!-- rodapé ==================================================================================== -->
			<div class="col-sm-12" id="rodape">
				<p class="rodape">
					Realizado por Jose Aser Lorenzo ®.
				</p>
			</div>
		</div>
	</div>
</body>
</html>