<?php
class Despesa_DataHandler {

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
    
    function buildListaRubricaOrcamento($idrubricaorcamentoP) {
        //  build lista de rubricas de orçamento relacionadas com despesas
        $query = 
            "select ro.idrubricaorcamento, ro.nivel, concat(ro.idrubricaorcamento,' - ',ro.nome) as idnome " .
            "from rubricaorcamento ro " .
            "where ro.tiporubricaorcamento='Despesa' ";
        $stmt = mysqli_stmt_init($this->connection);
        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        // mysqli_stmt_bind_param($stmt, "sss", $idcondominioP, $fornecedorP, $datalimitepagamentoP);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        mysqli_stmt_bind_result($stmt, $idrubricaorcamento, $nivel, $nome);
        print("<select name=\"idrubricaorcamento\">");
        if(empty($idrubricaorcamentoP) or strcmp($idrubricaorcamentoP,"%") == 0) {
            print("<option value=\"%\" selected>%</option>");
        } else {
            print("<option value=\"%\">%</option>");
        }
        while (mysqli_stmt_fetch($stmt)) {
            $espaco = "";
            for ($i = 1; $i <= intval($nivel); $i++) {
                $espaco = $espaco . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            } 
            if(strcmp($idrubricaorcamento,$idrubricaorcamentoP) == 0) {
                print("<option value=\"" . $idrubricaorcamento . "\" selected>" . $espaco . $nome . "</option>");
            } else {
                print("<option value=\"" . $idrubricaorcamento . "\">" . $espaco . $nome . "</option>");
            }
        }
        print("</select>");
        mysqli_stmt_close($stmt);
        mysqli_close($this->connection);
        
    }
    
    //------------------------------------------------------------------------------------
    
    function buildListaRubricaOrcamentoLOV() {
        //  build lista de rubricas de orçamento relacionadas com despesas para LOV
        $query =
        "select ro.idrubricaorcamento, ro.nivel, ro.nome " .
        "from rubricaorcamento ro " .
        "where ro.tiporubricaorcamento='Despesa' ";
        $stmt = mysqli_stmt_init($this->connection);
        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        // mysqli_stmt_bind_param($stmt, "sss", $idcondominioP, $fornecedorP, $datalimitepagamentoP);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        mysqli_stmt_bind_result($stmt, $idrubricaorcamento, $nivel, $nome);
        while (mysqli_stmt_fetch($stmt)) {
            $espaco = "";
            for ($i = 1; $i <= intval($nivel); $i++) {
                $espaco = $espaco . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            }
            print("<a href=\"\" onclick=\"actualizarPai('" . $idrubricaorcamento . "','" . $nome ."');\">" . $idrubricaorcamento . "&nbsp;&nbsp;&nbsp;" . $espaco . $nome ."</a><br>");
        }
        print("</select>");
        mysqli_stmt_close($stmt);
        mysqli_close($this->connection);
        
    }
    
    
    //------------------------------------------------------------------------------------
    
    function pesquisaDespesa($idcondominioP, $fornecedorP, $idrubricaorcamentoP, $valorcomivaP, $datafaturaP, $datalimitepagamentoP, $datapagamentoP) {

        $query =    "select d.datafatura, " .
                    "       d.datalimitepagamento, " .
                    "       d.datapagamento, " .
                    "       f.nome as fornecedor, " .
                    "       concat(d.idrubricaorcamento,'-',ro.nome) as rubricaorcamento, " .
                    "       d.fatura, " .
                    "       d.valorcomiva, " .
                    "       d.idformapagamento, " .
                    "       d.comentario " .
                    "from despesa d " .
                    "inner join rubricaorcamento ro on ro.idrubricaorcamento=d.idrubricaorcamento " .
                    "left outer join fornecedor f on f.idfornecedor=d.idfornecedor " .
                    "where d.idcondominio = ? " .
                    "and d.idrubricaorcamento like ? " .
                    "and d.valorcomiva like ? " .
                    "and d.datafatura like ? ";
                    
        // forçar % no like caso o utilizador limpe o campo de pesquisa
        if (empty($idrubricaorcamentoP)) {
            $idrubricaorcamentoP = '%';
        }
        if (empty($valorcomivaP)) {
            $valorcomivaP = '%';
        }
        if (empty($datafaturaP)) {
            $datafaturaP = '%';
        }
        
        
        // fornecedorP pode ser null
        if (empty($fornecedorP)) {
            $fornecedorP = 1;
            $query = $query . " and 1 like ? and f.nome is null ";
        } else {
            if (strcmp($fornecedorP,"%") == 0) {
                $query = $query . " and (f.nome like ? or f.nome is null)  ";
            } else {
                $query = $query . " and f.nome like ? ";
            }
        }
        // datalimitepagamentoP pode ser null
        if (empty($datalimitepagamentoP)) {
            $datalimitepagamentoP = 1;
            $query = $query . " and 1 like ? and d.datalimitepagamento is null ";
        } else {
            if (strcmp($datalimitepagamentoP,"%") == 0) {
                $query = $query . " and (d.datalimitepagamento like ? or d.datalimitepagamento is null)";
            } else {
                $query = $query . " and d.datalimitepagamento like ? ";
            }
        }
        // datapagamento pode ser null
        if (empty($datapagamentoP)) {
            $datapagamentoP = 1;
            $query = $query . " and 1 like ? and d.datapagamento is null ";
        } else {
            if (strcmp($datapagamentoP,"%") == 0 ) {
                $query = $query . " and (d.datapagamento like ? or d.datapagamento is null) ";
            } else {
                $query = $query . " and d.datapagamento like ? ";
            }
        }
        $query = $query .  " order by ifnull(d.datapagamento,'2999-01-01'), d.datalimitepagamento ";
        
        //print $query;

        $stmt = mysqli_stmt_init($this->connection);

        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        
//         //Gerar combinações de parâmetros de acordo com os critérios de pesquisa que foram selecicionados
//         if (empty($datalimitepagamentoP)) {
//             if (empty($datapagamentoP)) {
//                 mysqli_stmt_bind_param($stmt, "sssss", $idcondominioP, $fornecedorP, $idrubricaorcamentoP, $valorcomivaP, $datafaturaP);
//             } else {
//                 mysqli_stmt_bind_param($stmt, "ssssss", $idcondominioP, $fornecedorP, $idrubricaorcamentoP, $valorcomivaP, $datafaturaP, $datapagamentoP);
//             }
//         } else {
//             if (empty($datapagamentoP)) {
//                 mysqli_stmt_bind_param($stmt, "ssssss", $idcondominioP, $fornecedorP, $idrubricaorcamentoP, $valorcomivaP, $datafaturaP, $datalimitepagamentoP);
//             } else {
//                 mysqli_stmt_bind_param($stmt, "sssssss", $idcondominioP, $fornecedorP, $idrubricaorcamentoP, $valorcomivaP, $datafaturaP, $datalimitepagamentoP, $datapagamentoP);
//             }
//         }
        mysqli_stmt_bind_param($stmt, "sssssss", $idcondominioP, $idrubricaorcamentoP, $valorcomivaP, $datafaturaP, $fornecedorP, $datalimitepagamentoP, $datapagamentoP);
        mysqli_stmt_execute($stmt);
        

        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        mysqli_stmt_bind_result($stmt, $datafatura, $datalimitepagamento, $datapagamento, $fornecedor, $rubricaorcamento, $fatura, $valorcomiva, $idformapagamento, $comentario);
        
        print ("<p>Legenda:</p>\n");
        print ("<ul>\n");
        print ("<li>Nas despesas em que não é emitida fatura colocamos data de fatura do dia do recibo;</li>\n");
        print ("<li>Despesa com data limite de pagamento em branco significa essa data nao foi definida;</li>\n");
        print ("<li>Despesa com data de pagamento em branco significa que ainda não foi paga;</li>\n");
        print ("<li>Algumas despesas entre Janeiro e Abril estão por identificar e por isso aparecem na rubrica de orçamento 090700 - Desconhecida;</li>\n");
        print ("</ul>\n");
        /* transportar os valores */
        /* nas expressões abaixo é mais rápido fazer print ('<table>\n'); mas neste caso o PHP não vai interpretar \n como fim de linha */
        /* quando colocamos print("<table>\n"); o PHP faz uma análise ao argumento e deteta o fim de linha */
        print ("<table class='tabela2'>\n");
        print ("<tr>");
        print ("<th class='quadricula2'>Data fatura</th><th class='quadricula2'>Data limite pagamento</th><th class='quadricula2'>Data pagamento</th><th class='quadricula2'>Fornecedor</th><th class='quadricula2'>Rubrica orçamento</th><th class='quadricula2'>Fatura</th><th class='quadricula2'>Valor com IVA</th><th class='quadricula2'>Forma pagamento</th><th class='quadricula2'>Comentário</th>\n");
        print ("</tr>");
        while (mysqli_stmt_fetch($stmt)) {
            $valorcomivaS = number_format($valorcomiva,2,'.',' ');
            print ("<tr>\n");
            printf("<td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula1'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td>\n", 
                $datafatura, $datalimitepagamento, $datapagamento, $fornecedor, $rubricaorcamento, $fatura, $valorcomivaS, $idformapagamento, $comentario);
            print ("</tr>\n");
        }
        print ("</table>\n");
        mysqli_stmt_close($stmt);
        mysqli_close($this->connection);
    }

}
