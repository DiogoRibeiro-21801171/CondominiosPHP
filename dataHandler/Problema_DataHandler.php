<?php
class Problema_DataHandler {

    private $connection;
    
    //------------------------------------------------------------------------------------
    
    function __construct($hostName, $databaseName, $username, $password) {
        //Caso não consiga criar a ligação o erro aparece no output. 
        //Esta instrução desativa o erro, pois vou analisar se ocorre e escrever uma mensagem em conformidade
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
            //mensagem de erro completa para debug
            $_SESSION["msg"] = '<div class="msgErro">Não consegui carregar character set utf8: ' . mysqli_error($this->connection);
            //mensagem de erro para cliente
            //$_SESSION["msg"] = '<div class="msgErro">Não consegui carregar character set utf8: ';
            header("Location: login.php");
            exit();
        }
    }
    
    //------------------------------------------------------------------------------------
    
    function pesquisaProblema($idcondominioP, $statusP) {

        $query =    "select p.idproblema, " .
                    "       p.dataabertura, " .
                    "       p.dataresolucao, " .
                    "       p.prioridade, " .
                    "       p.status, " .
                    "       p.descricao, " .
                    "       p.comentarios " .
                    "from problema p " .
                    "where p.idcondominio = ? ";
                    
        
        if (empty($statusP)) {
            $status = "%";
        }
        if ((strcmp($statusP, "%") == 0)) {
            $query = $query . " order by p.dataabertura";
        } else {
            if ((strcmp($statusP, "NaoResolvidos") == 0)) {
                $query = $query . "  and status != 'Resolvido'  order by p.dataabertura";
            } else {
                $query = $query . "  and status = ?  order by p.dataabertura";
            }
            
        }
        
        $stmt = mysqli_stmt_init($this->connection);

        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        
        if ((strcmp($statusP, "%") == 0)) {
            mysqli_stmt_bind_param($stmt, "s", $idcondominioP);
        } else {
            if ((strcmp($statusP, "NaoResolvidos") == 0)) {
                mysqli_stmt_bind_param($stmt, "s", $idcondominioP);
            } else {
                mysqli_stmt_bind_param($stmt, "ss", $idcondominioP, $statusP);
            }
            
        }
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        mysqli_stmt_bind_result($stmt, $idproblema, $dataabertura, $dataresolucao, $prioridade, $status, $descricao, $comentarios);
        /* transportar os valores */
        /* nas expressões abaixo é mais rápido fazer print ('<table>\n'); mas neste caso o PHP não vai interpretar \n como fim de linha */
        /* quando colocamos print("<table>\n"); o PHP faz uma análise ao argumento e deteta o fim de linha */
        print ("<table class='tabela2'>\n");
        print ("<tr>");
        print ("<th class='quadricula2'>Id problema</th><th class='quadricula2'>Data abertura</th><th class='quadricula2'>Data resolução</th><th class='quadricula2'>Prioridade</th><th class='quadricula2'>Status</th><th class='quadricula2'>Descricao</th><th class='quadricula2'>Comentários</th>\n");
        print ("</tr>");
        while (mysqli_stmt_fetch($stmt)) {
            print ("<tr>\n");
            printf("<td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td>\n", $idproblema, $dataabertura, $dataresolucao, $prioridade, $status, $descricao, $comentarios);
            print ("</tr>\n");
        }
        print ("</table>\n");
        mysqli_stmt_close($stmt);
        mysqli_close($this->connection);
    }
}
