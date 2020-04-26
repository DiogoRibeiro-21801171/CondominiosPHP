<?php
class Quota_DataHandler {

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
    
    function pesquisaPagamentoQuotas($idcondominioP, $idfracaoP, $anoP) {

        $query = "select p.idfracao, " .
                 "       p.idtipoquota,  " .
                 "       p.idformapagamento, " .
                 "       p.datalimite, " .
                 "       p.datapagamento, " .
                 "       p.valorapagar, " .
                 "       p.valorpago, " .
                 "       p.comentario " .
                 "from quota p " .
                 "where p.idcondominio = ? " .
                 "  and p.idfracao like ? " .
                 "  and p.datalimite like ? " .
                 "  and p.idrubricaorcamento in (select idrubricaorcamento from rubricaorcamento where idrubricaorcamento like '901___') " .
                 "order by if(length(idfracao)=2,concat('0',idfracao),idfracao),p.idtipoquota";

        if (empty($anoP)) {
            $anoP = date("Y") . "%";
        }
        if (strpos($anoP,'%') === false){
            $anoP = $anoP . "%"; 
        }
        $stmt = mysqli_stmt_init($this->connection);

        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        //"s" significa uma variavel do tipo string. Se fosse uma string e um int seria "si"
        mysqli_stmt_bind_param($stmt, "sss",$idcondominioP, $idfracaoP, $anoP);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        mysqli_stmt_bind_result($stmt, $idfracao, $idtipoquota, $idformapagamento, $datalimite, $datapagamento, $valorapagar, $valorpago, $comentario);
        /* transportar os valores */
        /* nas expressões abaixo é mais rápido fazer print ('<table>\n'); mas neste caso o PHP não vai interpretar \n como fim de linha */
        /* quando colocamos print("<table>\n"); o PHP faz uma análise ao argumento e deteta o fim de linha */
        print ("<table class='tabela2'>\n");
        print ("<tr>");
        print ("<th class='quadricula2'>Fração</th><th class='quadricula2'>Tipo de quota</th><th class='quadricula2'>Forma pagamento</th><th class='quadricula2'>Data limite pagamento</th><th class='quadricula2'>Data pagamento</th><th class='quadricula2'>Valor a pagar</th><th class='quadricula2'>Valor pago</th><th class='quadricula2'>Comentário</th><th class='quadricula2'>Problema com pagamento</th>\n");
        print ("</tr>");
        while (mysqli_stmt_fetch($stmt)) {
            if (!empty($valorapagar)) {
                $valorapagarS = number_format($valorapagar,2,'.',' ');
            } else {
                $valorapagarS = "";
                $idformapagamento = "";
            }
            if (!empty($valorpago)) {
                $valorpagoS = number_format($valorpago,2,'.',' ');
            } else {
                $valorpagoS = "";
                $idformapagamento = "";
            }
            print ("<tr>\n");
            printf("<td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula1'>%s</td><td class='quadricula1'>%s</td><td class='quadricula2'>%s</td>", 
                $idfracao, $idtipoquota, $idformapagamento, $datalimite, $datapagamento, $valorapagarS, $valorpagoS, $comentario);
            if (empty($datapagamento) and $datalimite<date("Y-m-d")) {
                $problemapagamento = "Pagamento em atraso";
            } else {
                $delta = $valorapagar - $valorpago;
                if (!empty($datapagamento) and $delta>0) {
                    $problemapagamento = "Pagamento em défice de ". $delta . "€";
                } else {
                    if (!empty($datapagamento) and $delta<0) {
                        $problemapagamento = "Pagamento em excesso de " . abs($delta) . "€";
                    } else {
                        $problemapagamento = "&nbsp;";
                    }
                }
            }
            printf("<td class='quadricula2'>%s</td>\n",$problemapagamento);
            print ("</tr>\n");
        }
        print ("</table>\n");
        /* close statement */
        mysqli_stmt_close($stmt);
        mysqli_close($this->connection);
    }
    
    //------------------------------------------------------------------------------------
    
    function pesquisaPagamentoQuotasExtra($idcondominioP, $idfracaoP, $anoP) {
        
        $query = "select p.idfracao, " .
            "       p.idtipoquota,  " .
            "       p.idformapagamento, " .
            "       p.datalimite, " .
            "       p.datapagamento, " .
            "       p.valorapagar, " .
            "       p.valorpago, " .
            "       p.comentario " .
            "from quota p " .
            "where p.idcondominio = ? " .
            "  and p.idfracao like ? " .
            "  and p.datalimite > ? " .
            "  and p.idrubricaorcamento in (select idrubricaorcamento from rubricaorcamento where idrubricaorcamento like '904___') " .
            "order by if(length(idfracao)=2,concat('0',idfracao),idfracao),p.idtipoquota";
        
        if (empty($anoP)) {
            $anoP = date("Y");
        }
        
        $stmt = mysqli_stmt_init($this->connection);
        
        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        //"s" significa uma variavel do tipo string. Se fosse uma string e um int seria "si"
        mysqli_stmt_bind_param($stmt, "sss",$idcondominioP, $idfracaoP, $anoP);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        mysqli_stmt_bind_result($stmt, $idfracao, $idtipoquota, $idformapagamento, $datalimite, $datapagamento, $valorapagar, $valorpago, $comentario);
        /* transportar os valores */
        /* nas expressões abaixo é mais rápido fazer print ('<table>\n'); mas neste caso o PHP não vai interpretar \n como fim de linha */
        /* quando colocamos print("<table>\n"); o PHP faz uma análise ao argumento e deteta o fim de linha */
        print ("<table class='tabela2'>\n");
        print ("<tr>");
        print ("<th class='quadricula2'>Fração</th><th class='quadricula2'>Tipo quota</th><th class='quadricula2'>Forma pagamento</th><th class='quadricula2'>Data limite pagamento</th><th class='quadricula2'>Data pagamento</th><th class='quadricula2'>Valor a pagar</th><th class='quadricula2'>Valor pago</th><th class='quadricula2'>Comentário</th><th class='quadricula2'>Problema com pagamento</th>\n");
        print ("</tr>");
        while (mysqli_stmt_fetch($stmt)) {
            if (!empty($valorapagar)) {
                $valorapagarS = number_format($valorapagar,2,'.',' ');
            } else {
                $valorapagarS = "";
                $idformapagamento = "";
            }
            if (!empty($valorpago)) {
                $valorpagoS = number_format($valorpago,2,'.',' ');
            } else {
                $valorpagoS = "";
                $idformapagamento = "";
            }
            print ("<tr>\n");
            printf("<td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula1'>%s</td><td class='quadricula1'>%s</td><td class='quadricula2'>%s</td>",
                $idfracao, $idtipoquota, $idformapagamento, $datalimite, $datapagamento, $valorapagarS, $valorpagoS, $comentario);
            if (empty($datapagamento) and $datalimite<date("Y-m-d")) {
                $problemapagamento = "Pagamento em atraso";
            } else {
                $delta = $valorapagar - $valorpago;
                if (!empty($datapagamento) and $delta>0) {
                    $problemapagamento = "Pagamento em défice de ". $delta . "€";
                } else {
                    if (!empty($datapagamento) and $delta<0) {
                        $problemapagamento = "Pagamento em excesso de " . $delta . "€";
                    } else {
                        $problemapagamento = "&nbsp;";
                    }
                }
            }
            printf("<td class='quadricula2'>%s</td>\n",$problemapagamento);
            print ("</tr>\n");
        }
        print ("</table>\n");
        /* close statement */
        mysqli_stmt_close($stmt);
        mysqli_close($this->connection);
    }
    
    // ------------------------------------------------------------------------------------------------------------
    
    function mapaResumoPagamento($idcondominio, $id1, $id2, $id3, $id4, $ano, $tipoquota) {
        
        $query =    "select t1.idfracao, " .
                    "       t1.valorpago as t1, " .
                    "       t2.valorpago as t2, " .
                    "       t3.valorpago as t3, " .
                    "       t4.valorpago as t4, " .
                    "       concat(if((t1.valorapagar-t1.valorpago)>0,'P',''), " .
                    "              if((t1.valorpago is null and t1.datalimite<now())>0,'A',''), " .
                    "              if((t2.valorapagar-t2.valorpago)>0,'P',''), " .
                    "              if((t2.valorpago is null and t2.datalimite<now())>0,'A',''), " .
                    "              if((t3.valorapagar-t3.valorpago)>0,'P',''), " .
                    "              if((t3.valorpago is null and t3.datalimite<now())>0,'A',''), " .
                    "              if((t4.valorapagar-t4.valorpago)>0,'P',''), " .
                    "              if((t4.valorpago is null and t4.datalimite<now())>0,'A','') " .
                    "              ) as Problema_com_pagamento " .
                    "from quota as t1 " .
                    "inner join quota as t2 on t1.idcondominio=t2.idcondominio and t1.idfracao=t2.idfracao " .
                    "inner join quota as t3 on t1.idcondominio=t3.idcondominio and t1.idfracao=t3.idfracao " .
                    "inner join quota as t4 on t1.idcondominio=t4.idcondominio and t1.idfracao=t4.idfracao " .
                    "where t1.idcondominio = ? " .
                    "  and t2.idcondominio = ? " .
                    "  and t3.idcondominio = ? " .
                    "  and t4.idcondominio = ? " .
                    "  and t1.idtipoquota= ? " .
                    "  and t2.idtipoquota= ? " .
                    "  and t3.idtipoquota= ? " .
                    "  and t4.idtipoquota= ? ";
        
        
        if (empty($tipoquota)) {
            $tipoquota = "Normal";
        }
        if (strcmp($tipoquota, "Normal") == 0) {
            $query = $query . 
            "  and t1.datalimite like ? " .
            "  and t2.datalimite like ? " .
            "  and t3.datalimite like ? " .
            "  and t4.datalimite like ? " .
            "order by if(length(t1.idfracao)=2,concat('0',t1.idfracao),t1.idfracao)";
        } else {
            $query = $query .
            "  and t1.datalimite > ? " .
            "  and t2.datalimite > ? " .
            "  and t3.datalimite > ? " .
            "  and t4.datalimite > ? " .
            "order by if(length(t1.idfracao)=2,concat('0',t1.idfracao),t1.idfracao)";
        }

        
        if (empty($ano)) {
            $ano = date("Y") . "%";
        }
        if (strpos($ano,'%') === false) {
            $ano = $ano . "%";
        }
        
        //print($query);
        $stmt = mysqli_stmt_init($this->connection);
        
        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        //"s" significa uma variavel do tipo string. Se fosse uma string e um int seria "si"
        mysqli_stmt_bind_param($stmt, "ssssssssssss",$idcondominio, $idcondominio, $idcondominio, $idcondominio, $id1, $id2, $id3, $id4, $ano, $ano, $ano, $ano);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        mysqli_stmt_bind_result($stmt, $idfracao, $t1, $t2, $t3, $t4, $prob);
        
        print ("<p>Legenda:</p>\n");
        print ("<ul>\n");
        print ("<li>P - Problema com quota (fazer pesquisa de quota indicando a fração para ver detalhe);</li>\n");
        print ("<li>A - quota em atraso (fazer pesquisa de quota indicando a fração para ver detalhe);</li>\n");
        print ("</ul>\n");
        /* transportar os valores */
        /* nas expressões abaixo é mais rápido fazer print ('<table>\n'); mas neste caso o PHP não vai interpretar \n como fim de linha */
        /* quando colocamos print("<table>\n"); o PHP faz uma análise ao argumento e deteta o fim de linha */
        print ("<table class='tabela2'>\n");
        print ("<tr>\n");
        print ("<th class='quadricula2'>Fração</th><th class='quadricula2'>Trimestre 1</th><th class='quadricula2'>Trimestre 2</th><th class='quadricula2'>Trimestre 3</th><th class='quadricula2'>Trimestre 4</th><th class='quadricula2'>Poblema com pagamento</th>\n");
        print ("</tr>\n");
        while (mysqli_stmt_fetch($stmt)) {
            if (!empty($t1)) {
                $t1S = number_format($t1, 2, ',', ' ');
            } else {
                $t1S = "";
            }
            if (!empty($t2)) {
                $t2S = number_format($t2, 2, ',', ' ');
            } else {
                $t2S = "";
            }
            if (!empty($t3)) {
                $t3S = number_format($t3, 2, ',', ' ');
            } else {
                $t3S = "";
            }
            if (!empty($t4)) {
                $t4S = number_format($t4, 2, ',', ' ');
            } else {
                $t4S = "";
            }
            print ("<tr>\n");
            printf("<td class='quadricula2'>%s</td><td class='quadricula1'>%s</td><td class='quadricula1'>%s</td><td class='quadricula1'>%s</td><td class='quadricula1'>%s</td><td class='quadricula2'>%s</td>\n",
                $idfracao, $t1S, $t2S, $t3S, $t4S, $prob);
            print ("</tr>\n");
        }
        print ("</table>\n");
        mysqli_stmt_close($stmt);
        mysqli_close($this->connection);
    }

    //------------------------------------------------------------------------------------
    
    function listaQuotasEmAtraso($idcondominio, $ano) {
        if ($ano==date("Y")) {
            $datareferencia = "". date("Y-m-d");
        } else {
            $datareferencia = "". $ano . "-12-31";
        }
        //print $datareferencia;
        $query  =   "" .
                    "select " .
                    "    p.idfracao , " .
                    "    extract(year from p.datalimite) as ano , " .
                    "    sum(if(p.idrubricaorcamento in ('901011', " .
                    "                                    '901012', " .
                    "                                    '901013', " .
                    "                                    '901014'),p.valorapagar-ifnull(p.valorpago,0),0)) as " .
                    "    divida_despesa_normal , " .
                    "    sum(if(p.idrubricaorcamento in ('904001', " .
                    "                                    '904002', " .
                    "                                    '904003', " .
                    "                                    '904004'),p.valorapagar-ifnull(p.valorpago,0),0)) as " .
                    "                                                divida_despesa_extra , " .
                    "    sum(p.valorapagar-ifnull(p.valorpago,0)) as divida_total " .
                    "from " .
                    "    quota p " .
                    "where " .
                    "    p.idcondominio= ? " .
                    "and p.datalimite <= ? " .
                    "and ( " .
                    "        p.valorapagar > ifnull(p.valorpago,0)+1) " .
                    "group by " .
                    "    extract(year from p.datalimite), " .
                    "    p.idfracao " .
                    "order by " .
                    "    if(length(idfracao)=2,concat('0',idfracao),idfracao),extract(year from p.datalimite)";
        $stmt = mysqli_stmt_init($this->connection);
        
        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        //"s" significa uma variavel do tipo string. Se fosse uma string e um int seria "si"
        mysqli_stmt_bind_param($stmt, "ss", $idcondominio, $datareferencia);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        mysqli_stmt_bind_result($stmt, $fracao, $ano, $divida_despesa_normal, $divida_despesa_extra, $divida_total);
        /* transportar os valores */
        /* nas expressões abaixo é mais rápido fazer print ('<table>\n'); mas neste caso o PHP não vai interpretar \n como fim de linha */
        /* quando colocamos print("<table>\n"); o PHP faz uma análise ao argumento e deteta o fim de linha */
        print ("<table class='tabela2'>\n");
        print ("<tr>");
        print ("<th class='quadricula2'>Fração</th><th class='quadricula2'>Ano</th><th class='quadricula2'>Divida de despesa normal</th><th class='quadricula2'>Divida de despesa extra</th><th class='quadricula2'>Divida total</th>\n");
        print ("</tr>");
        while (mysqli_stmt_fetch($stmt)) {
            $divida_despesa_normalS = number_format($divida_despesa_normal,2,'.',' ');
            $divida_despesa_extraS  = number_format($divida_despesa_extra,2,'.',' ');
            $divida_totalS          = number_format($divida_total,2,'.',' ');
            print ("<tr>\n");
            printf("<td class='quadricula2'>%s</td><td class='quadricula2'>%s</td><td class='quadricula1'>%s</td><td class='quadricula1'>%s</td><td class='quadricula1'>%s</td>\n", $fracao, $ano, $divida_despesa_normalS, $divida_despesa_extraS, $divida_totalS);
            print ("</tr>\n");
        }
        print ("</table>\n");
        mysqli_stmt_close($stmt);
        mysqli_close($this->connection);
    }
    
}
