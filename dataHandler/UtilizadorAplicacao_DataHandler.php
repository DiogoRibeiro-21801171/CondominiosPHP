<?php
class UtilizadorAplicacao_DataHandler {

    private $connection;
    
    //------------------------------------------------------------------------------------

    function __construct($hostName, $databaseName, $username, $password) {
        error_reporting(E_ERROR); 
        $this->connection = mysqli_connect($hostName, $username, $password, $databaseName);
        //reativar os erros para os valores normais
        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        if (mysqli_connect_errno()) {
            //mensagem de erro completa para debug
            $_SESSION["msg"] = "Não consegui criar a ligação à BD! <br> " . mysqli_connect_errno() . "-" . mysqli_connect_error();
            //mensagem de erro para cliente
            //$_SESSION["msg"] = "Não consegui criar a ligação à BD! <br> ";
            header("Location: login.php");
            exit();
        }
        if (!mysqli_set_charset($this->connection, "utf8")) {
            $_SESSION["msg"] = '<div class="msgErro">Não consegui carregar character set utf8: ' . mysqli_error($this->connection);
            header("Location: login.php");
            exit();
        }
    }

    //------------------------------------------------------------------------------------
    
    function validateLogin($login, $password) {
        
        //este tipo de query evita SQL Injetion porque a instrução é compilada antes de receber os parâmetros reais, que depois só podem ser usados naquele ponto
        // $query = "select perfil, idcondominio, nome, genero from utilizador_aplicacao where login=? and pwd=password(?) and estado='ATIVO'";
        $query =    "select ua.idperfilutilizador, " .
                    "       convert(aes_decrypt(unhex(ua.nome),UNHEX(SHA2('6LrRjvPFaE8YP8yMsyxKcL',512))) using utf8) as nome, " .
                    "       ua.genero, " .
                    "       ua.idcondominio, " .
                    "       c.morada, " .
                    "       c.ultimaatualizacao " .
                    "from utilizadoraplicacao as ua " .
                    "inner join condominio as c on c.idcondominio=ua.idcondominio " .
                    "where login = hex(aes_encrypt(?,UNHEX(SHA2('6LrRjvPFaE8YP8yMsyxKcL',512)))) " .
                    "and pwd=password(?) " .
                    "and ua.idestadoutilizador='ATIVO' ";

        $stmt = mysqli_stmt_init($this->connection);
        
        if (!mysqli_stmt_prepare($stmt, $query)) {
            return 'Erro na preparacao do prepared statement: ' . mysqli_error($this->connection);
        }        
        
        //"ss" significa duas strings. Se fosse uma string e um int seria "si"
        mysqli_stmt_bind_param($stmt, "ss", $login, $password);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_stmt_error($stmt) != "") {
            return 'Erro na execução do SQL: ' . mysqli_stmt_error($stmt);    
        }
        
        $resultSet = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($resultSet)>=1) {
            $row = mysqli_fetch_array($resultSet);
        } else {
            $row["idperfilutilizador"] = "Utilizador ou password errados";
        }
        mysqli_free_result($resultSet);
        mysqli_stmt_close($stmt);
        mysqli_close($this->connection);

//         print "<html><body>" . "\n";
//         print "login=" . $login . "<br>\n";
//         print "password=" . $password . "<br>\n";
//         var_dump($row);
//         //var_dump($result);
//         print "</body></html>";
//         exit();

        return $row; 
    }
    
    //------------------------------------------------------------------------------------

    function validateLoginOld($login, $password) {
        $query = "SELECT perfil FROM utilizador_aplicacao where login = '" . $login . "' AND pwd = password('" . $password . "') and estado='ATIVO'";
        //Para testar SQL injetion alterar a classe LoginAction em 3 pontos:
        //     Usar $loginUsername = $_GET["loginUsername"];
        //     Usar $palavrachave = $_GET["palavrachave"];
        //     Invocar esta função de validação de password
        // Este query está sujeito a SQL Injetion no campo LOGIN:
        //     Fazer login sem saber o nome do user e com um privilégio qualquer: "aa' or '1'='1' limit 1 -- " (é importante o espaço depois do inicio de comentário)
        //     Fazer login sem saber o nome do user e com o privilégio de administrador: "aa' or '1'='1' and perfil='ADMINISTRADOR' limit 1 -- "(é importante o espaço depois do inicio de comentário)
        // Este query está sujeito a SQL Injetion no campo PASSWORD:
        //     Fazer login sem saber o nome do user e com um privilegio qualquer: "aa') or 1=1 limit 1 -- " (é importante o espaço em branco depois do inicio de comentário)
        //     Fazer login sem saber o nome do user e com o privilegio ADMIN: "aa') or 1=1 and perfil='ADMINISTRADOR' limit 1 -- " ((é importante o espaço em branco depois do inicio de comentário)
        //print $query;
        //exit();
        $result = mysqli_query($this->connection, $query);
        $rowsFound = mysqli_num_rows($result);
        // exactly one row? then we have found the user
        if ($rowsFound == 1) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $tipoUtilizador = $row["perfil"]; 
        } else {
            $tipoUtilizador = "";
        }
        mysqli_free_result($result);
        mysqli_close($this->connection);
        return $tipoUtilizador;
    }
}
?>