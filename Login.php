<!DOCTYPE html>
<html lang="pt">
<head>
<title>Pagina inicial</title>
<meta charset="utf-8">
<meta name="Author" content="Jose Aser Lorenzo">
<meta name="description" content="condominio">
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="shortcut icon" href="/condominio/imagens/icon.gif">
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

    <h2>Login</h2>
    <form action="Login_Action.php" method="GET">
        <div class="form-group">
            <label>Nome Utilizador</label>
            <?php
            if (isset($_SESSION["loginUsername"])) {
                $loginUsername = $_SESSION["loginUsername"];
                print "<input type=\"text\" name=\"loginUsername\" value=\"{$loginUsername}\">";
            } else {
                print "<input type=\"text\" name=\"loginUsername\" value=\"\">";
            }
            ?>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password">
        </div>
        <input type="submit" value="Entrar">
    </form>

</body>
</html>