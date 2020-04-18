<!DOCTYPE html>
<html lang="pt">
<head>
<title>Pagina inicial</title>
<meta charset="utf-8">
<meta name="Author" content="Jose Aser Lorenzo">
<meta name="description" content="condominio">
<link rel="shortcut icon" href="/condominio/imagens/icon.gif">
<link href="/condominio/css/turtlelearning.css" rel="StyleSheet" type="text/css" />
<title>Login</title>
</head>
<body>
    <?php
    session_start();
    if ((isset($_SESSION["msg"])) && !(strcmp($_SESSION["msg"], "") == 0)) {
        print '<div class="msgErro">' . $_SESSION["msg"] . '</div>';
    }
    $_SESSION["msg"] = "";
    if (isset($_SESSION["tipoUtilizador"])) {
        $_SESSION["tipoUtilizador"] = "";
    }
    ?>
    <!--
	<img src="imagens/top.jpg" align="right"  border="0" alt="top.jpg">
	-->
    <h2>Login</h2>
    <form action="Login_Action.php" method="GET">
        <table>
            <tr>
                <td>Nome :</td>
                <td>
                <?php
                if (isset($_SESSION["loginUsername"])) {
                    $loginUsername = $_SESSION["loginUsername"];
                    print "<input type=\"text\" name=\"loginUsername\" value=\"{$loginUsername}\">";
                } else {
                    print "<input type=\"text\" name=\"loginUsername\" value=\"\">";
                }
                ?>
                </td>
            </tr>
            <tr>
                <td>Palavra chave :</td>
                <td><input type="password" name="password"></td>
            </tr>
            <tr>
                <td colspan="2">
                    <!-- <input type="reset" value="Limpar">  --> 
                    <input type="submit" value="Entrar">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>