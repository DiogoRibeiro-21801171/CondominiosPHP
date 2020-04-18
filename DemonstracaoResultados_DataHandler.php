<?php
class DemonstracaoResultados_DataHandler {

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
            $_SESSION["msg"] = "Não consegui criar a ligação à  BD! <br> " . mysqli_connect_errno() . "-" . mysqli_connect_error();
            //mensagem de erro para cliente
            //$_SESSION["msg"] = "Não consegui criar a ligação à  BD! <br> ";
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
    
    function listaDemonstracaoResultadosDespesas($idcondominio, $ano) {
        $query =    "select " .
                    "       r.nivel,  " .
                    "       concat(r.idrubricaorcamento, '-', r.nome),  " .
                    "       orc.valor as orcamentado, " . 
                    "       p.soma_valor_comprometido as faturas_emitidas, " . 
                    "       p.soma_pagamentos as faturas_pagas " .
                    "from rubricaorcamento r " .
                    "left outer join orcamento orc  on r.idrubricaorcamento=orc.idrubricaorcamento " .
                    "left outer join ( " .
                    "    select d.idcondominio, " . // soma as despesas de nível pai 
                    "           year(d.datalimitepagamento) as ano,  " .
                    "           r.parentid, " .
                    "           sum(d.valorcomiva) as soma_valor_comprometido, " .
                    "           sum(if (d.datapagamento is null, 0, d.valorcomiva)) as soma_pagamentos " .
                    "    from despesa d " .
                    "    inner join rubricaorcamento r on r.idrubricaorcamento=d.idrubricaorcamento " .
                    "    where d.idcondominio = ?  " .      //estas condições não são obrigatórias, restringem o 
                    "      and year(d.datalimitepagamento) = ?   " . //volume de linhas agrupado 
                    "      and r.parentid is not null " .
                    "    group by d.idcondominio, year(d.datalimitepagamento), r.parentid " .
                    "    union " .
                    "    select d.idcondominio, " . //mostra a soma das despesas que não têm filhos e a soma das depesas filho de uma mesma rubrica 
                    "           year(d.datalimitepagamento) as ano, " .
                    "           d.idrubricaorcamento, " .
                    "           sum(d.valorcomiva) as soma_valor_comprometido, " .
                    "           sum(if (d.datapagamento is null, 0, d.valorcomiva)) as soma_pagamentos " .
                    "    from despesa d " .
                    "    where d.idcondominio = ? " .      //estas condições não são obrigatórias, restringem o 
                    "      and year(d.datalimitepagamento) = ?  " . //volume de linhas agrupado 
                    "    group by d.idcondominio, year(d.datalimitepagamento), d.idrubricaorcamento " .
                    ") as p on p.idcondominio=orc.idcondominio and p.ano=orc.ano and p.parentid=orc.idrubricaorcamento " . //despesa pai 
                    "where orc.idcondominio = ? " .
                    "  and orc.ano = ? " .
                    "  and r.tiporubricaorcamento = ? " .
                    "  order by 2 ";

        $stmt = mysqli_stmt_init($this->connection);

        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        $despesa = "Despesa";
        //"s" significa uma variavel do tipo string. Se fosse uma string e um int seria "si"
        mysqli_stmt_bind_param($stmt, "sssssss", $idcondominio, $ano, $idcondominio, $ano, $idcondominio, $ano, $despesa);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        mysqli_stmt_bind_result($stmt, $nivel, $nome, $orcamentado, $faturas_emitidas, $faturas_pagas);
        print ("<p>Legenda:</p>\n");
        print ("<ul>\n");
        print ("<li>Orçamentado - Valor orçamentado para essa rubrica no ano escolhido;</li>\n");
        print ("<li>Faturas emitidas - Soma de todas as faturas emitidas pelos fornecedores ao Condomínio no ano escolhido;</li>\n");
        print ("<li>Faturas pagas - Soma das faturas pagas pelo Condomínio no ano escolhido</li>\n");
        print ("</ul>\n");
        /* transportar os valores */
        /* nas expressàµes abaixo à© mais rà¡pido fazer print ('<table>\n'); mas neste caso o PHP não vai interpretar \n como fim de linha */
        /* quando colocamos print("<table>\n"); o PHP faz uma anà¡lise ao argumento e deteta o fim de linha */
        print ("<table class='tabela2'>\n");
        print ("<tr>");
        print ("<th class='quadricula2'>Rubrica de orçamento</th><th class='quadricula2'>Valor orçamentado</th><th class='quadricula2'>Faturas emitidas</th><th class='quadricula2'>Faturas pagas</th>\n");
        print ("</tr>");
        while (mysqli_stmt_fetch($stmt)) {
            $orcamentadoS = number_format($orcamentado,2,'.',' '); 
            $faturas_emitidasS = number_format($faturas_emitidas,2,'.',' ');
            $faturas_pagasS = number_format($faturas_pagas,2,'.',' ');
            print ("<tr>\n");
            $espaco = "";
            for ($i = 1; $i <= intval($nivel); $i++) {
                $espaco = $espaco . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            } 
            printf("<td class='quadricula2'>%s</td><td class='quadricula1'>%s</td><td class='quadricula1'>%s</td><td class='quadricula1'>%s</td>\n", ($espaco . $nome), $orcamentadoS, $faturas_emitidasS, $faturas_pagasS);
            print ("</tr>\n");
        }
        print ("</table>\n");
        /* close statement */
        mysqli_stmt_close($stmt);
        mysqli_close($this->connection);
    }
    
    //------------------------------------------------------------------------------------
    
    function listaDemonstracaoResultadosReceitas($idcondominio, $ano, $tipoReceita) {
        
        $query =    "select r.nivel, " .
                    "       concat(r.idrubricaorcamento, '-', r.nome),  " .
                    "       orc.valor as orcamentado, " .  
                    "       pag.valor_recebido as valor_recebido " .
                    "from rubricaorcamento r " .
                    "left outer join orcamento orc  on r.idrubricaorcamento=orc.idrubricaorcamento " .
                    "left outer join ( " .
                    "    select p.idcondominio, " .  // soma as receitas de nível pai 
                    "           year(p.datapagamento) as ano,  " .
                    "           r.parentid as idrubricaorcamento, " .
                    "           sum(p.valorpago) as valor_recebido " .
                    "    from quota p " .
                    "    inner join rubricaorcamento r on r.idrubricaorcamento=p.idrubricaorcamento " .
                    "    where p.idcondominio = ?  " . // estas condições não são obrigatórias, restringem o 
                    "      and year(p.datapagamento) = ?   " . // volume de linhas agrupado 
                    "      and r.parentid is not null " .
                    "    group by p.idcondominio, year(p.datapagamento), r.parentid " .
                    "    union " .
                    "    select p.idcondominio, " . // mostra a soma das despesas que não têm filhos e a soma das depesas filho de uma mesma rubrica 
                    "           year(p.datapagamento) as ano, " .
                    "           p.idrubricaorcamento, " .
                    "           sum(p.valorpago) as valor_recebido " .
                    "    from quota p " .
                    "    where p.idcondominio = ? " . // estas condições não são obrigatórias, restringem o 
                    "      and year(p.datapagamento) = ?  " . // volume de linhas agrupado 
                    "    group by p.idcondominio, year(p.datapagamento), p.idrubricaorcamento " .
                    "    union ".
                    "    select  re.idcondominio, ".
                    "            extract(year from re.datapagamento), ".
                    "            re.idrubricaorcamento,  ".
                    "            sum(re.valor) as valor_recebido ".
                    "    from receitaextra re ".
                    "    where re.idcondominio = ? ".
                    "      and extract(year from re.datapagamento) = ? ".
                    "      and re.idrubricaorcamento not in ('000010','000015') ".
                    "    group by re.idcondominio,  extract(year from re.datapagamento), re.idrubricaorcamento ".
                    ") as pag on pag.idcondominio=orc.idcondominio and pag.ano=orc.ano and pag.idrubricaorcamento=orc.idrubricaorcamento " . // despesa pai 
                    "where orc.idcondominio = ? " .
                    "  and orc.ano = ? " .
                    "  and r.tiporubricaorcamento = ? ";
        
        $stmt = mysqli_stmt_init($this->connection);
        
        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        //"s" significa uma variavel do tipo string. Se fosse uma string e um int seria "si"
        mysqli_stmt_bind_param($stmt, "sssssssss", $idcondominio, $ano, $idcondominio, $ano, $idcondominio, $ano, $idcondominio, $ano, $tipoReceita);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        mysqli_stmt_bind_result($stmt, $nivel, $nome, $orcamentado, $valor_recebido);
        print ("<p>Legenda:</p>\n");
        print ("<ul>\n");
        print ("<li>Orçamentado - Valor orçamentado para essa rubrica no ano escolhido;</li>\n");
        print ("<li>Valor recebido - Soma dos valores recebidos em quotas nesta rubrica;</li>\n");
        print ("</ul>\n");
        /* transportar os valores */
        /* nas expressàµes abaixo à© mais rà¡pido fazer print ('<table>\n'); mas neste caso o PHP não vai interpretar \n como fim de linha */
        /* quando colocamos print("<table>\n"); o PHP faz uma anà¡lise ao argumento e deteta o fim de linha */
        print ("<table class='tabela2'>\n");
        print ("<tr>");
        print ("<th class='quadricula2'>Rubrica de orçamento</th><th class='quadricula2'>Valor orçamentado</th><th class='quadricula2'>Valor recebido</th><th class='quadricula2'>Diferença</th>\n");
        print ("</tr>");
        while (mysqli_stmt_fetch($stmt)) {
            $orcamentadoS    = number_format($orcamentado,2,'.',' ');
            $valor_recebidoS = number_format($valor_recebido,2,'.',' ');
            $diferenca = $orcamentado-$valor_recebido;
            if ($diferenca>=0) {
                $diferencaS      = number_format(abs($diferenca),2,'.',' ');
            } else {
                $diferencaS      = "(" . number_format(abs($diferenca),2,'.',' ') . ")";
            }
            printf ("<tr>\n");
            $espaco = "";
            for ($i = 1; $i <= intval($nivel); $i++) {
                $espaco = $espaco . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            }
            printf("<td class='quadricula2'>%s</td><td class='quadricula1'>%s</td><td class='quadricula1'>%s</td><td class='quadricula1'>%s</td>\n", ($espaco . $nome), $orcamentadoS, $valor_recebidoS, $diferencaS);
            print ("</tr>\n");
        }
        print ("</table>\n");
        mysqli_stmt_close($stmt);
        mysqli_close($this->connection);
    }
    
    //------------------------------------------------------------------------------------
    
    function saldoContasBancarias($idcondominioP, $anoP) {
        $query =    "select c.descricaoconta, s.dataextracao, s.saldo ".
                    "from contabancaria c ".
                    "inner join contabancariasaldo s on (s.idcondominio=c.idcondominio and s.idconta=c.idconta) ".
                    "where c.idcondominio = ? ".
                    "  and s.dataextracao = (select max(s2.dataextracao) ".
                    "                        from contabancariasaldo s2 ".
                    "                        where s2.idcondominio=c.idcondominio and s2.idconta=c.idconta ".
                    "                          and extract(year from s2.dataextracao) = ? ) ".
                    "order by 1 , 2 ";
        $stmt = mysqli_stmt_init($this->connection);
        if (!mysqli_stmt_prepare($stmt, $query)) {
            print $query;
            print '<div class="msgErro">Erro na preparaà§à£o do prepared statement do saldo das contas bancà¡rias</div>';
           return;
        }
        mysqli_stmt_bind_param($stmt, "ss", $idcondominioP, $anoP);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        mysqli_stmt_bind_result($stmt, $descricaoconta, $dataextracao, $saldo);
        $linha="";
        $countLinhas=0;
        while (mysqli_stmt_fetch($stmt)) {
            $saldoS    = number_format($saldo,2,'.',' ');
            if ($countLinhas!=0) {
                $linha = $linha . "<tr>";
            }
            $linha = $linha . "<td class='quadricula2'>$descricaoconta</td><td class='quadricula2'>$dataextracao</td><td class='quadricula1'>$saldoS</td></tr>";
            $countLinhas = $countLinhas + 1;
        }
        print "<tr><td rowspan='$countLinhas' class='quadricula2'>Saldo das contas bancárias</td>";
        print $linha;
    }
    
    //------------------------------------------------------------------------------------
    
    function executeQuerySumAno($queryP, $idcondominioP, $anoP) {
        $stmt = mysqli_stmt_init($this->connection);
        if (!mysqli_stmt_prepare($stmt, $queryP)) {
            //print $queryP;
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
           return;
        }
        if (substr_count($queryP,"?")>2) {
            mysqli_stmt_bind_param($stmt, "sss", $idcondominioP, $anoP, $anoP);
        } else {
            mysqli_stmt_bind_param($stmt, "ss", $idcondominioP, $anoP);
        }
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        mysqli_stmt_bind_result($stmt, $resultado);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return $resultado;
    }
        
    //------------------------------------------------------------------------------------
    function demonstracaoResultadosResumo($idcondominio, $ano) {
        if (empty($ano)) {
            $ano = date('Y');
        }
        //------------------------------------------
        $query =    "select sum(p.valorpago) as quotasDoAnoPagasNoAno " .
                    "from quota p " .
                    "where p.idtipoquota in ('T1','T2','T3','T4') " . 
                    "  and p.idcondominio = ? " .
                    "  and extract(year from p.datalimite) = ? " .
                    "  and extract(year from p.datapagamento) = ? ";
        $quotasDoAnoPagasNoAno = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $query =    "select sum(p.valorpago) as quotasAtrasadasPagasNoAno " .
                    "from quota p " .
                    "where p.idtipoquota in ('T1','T2','T3','T4') " . 
                    "  and p.idcondominio = ? " .
                    "  and extract(year from p.datalimite) < ? " .
                    "  and extract(year from p.datapagamento) = ? ";
        $quotasAtrasadasPagasNoAno = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $query =    "select sum(p.valorpago) as quotasExtraDoAnoPagasNoAno " .
                    "from quota p " .
                    "where p.idtipoquota in ('Elev1','Elev2','Elev3','Elev4') " .
                    "  and p.idcondominio = ? " .
                    "  and extract(year from p.datalimite) = ? " .
                    "  and extract(year from p.datapagamento) = ? ";
        $quotasExtraDoAnoPagasNoAno = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $query =    "select sum(p.valorpago) as quotasExtraAtasadasPagasNoAno " .
                    "from quota p " .
                    "where p.idtipoquota in ('Elev1','Elev2','Elev3','Elev4') " .
                    "  and p.idcondominio = ? " .
                    "  and extract(year from p.datalimite) < ? " .
                    "  and extract(year from p.datapagamento) = ? ";
        $quotasExtraAtasadasPagasNoAno = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $query =    "select sum(p.valorpago) as quotasExtraDoProximoAnoPagasNoAno " . 
                    "from quota p " . 
                    "where p.idtipoquota in ('Elev1','Elev2','Elev3','Elev4') " . 
                    "  and p.idcondominio = ? " .
                    "  and extract(year from p.datalimite) > ? " . 
                    "  and extract(year from p.datapagamento) = ? ";
        $quotasExtraDoProximoAnoPagasNoAno = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $query =    "select sum(re.valor) as totalReceitasExtra  " .
                    "from receitaextra re " .
                    "where re.idcondominio = ? " .
                    "  and re.idrubricaorcamento not in ('000010','000015') ".
                    "  and extract(year from re.datapagamento) = ? ";
        $totalReceitasExtra = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $query =    "select sum(p.valorapagar) as quotasDoAnoNaoPagas " .
                    "from quota p " .
                    "where p.idtipoquota in ('T1','T2','T3','T4') " .
                    "  and p.idcondominio = ? " .
                    "  and extract(year from p.datalimite) = ? " .
                    "  and p.datalimite < now() ".
                    "  and p.valorpago is null";
        $quotasDoAnoNaoPagas = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $query =    "select sum(p.valorapagar) as quotasExtraDoAnoNaoPagas " .
                    "from quota p " .
                    "where p.idtipoquota in ('Elev1','Elev2','Elev3','Elev4') " .
                    "  and p.idcondominio = ? " .
                    "  and extract(year from p.datalimite) = ? " .
                    "  and p.datalimite < now() ".
                    "  and p.valorpago is null";
        $quotasExtraDoAnoNaoPagas = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $query =    "select sum(p.valorapagar-p.valorpago) as quotasNormaisDoAnoEnganos " .
                    "from quota p " .
                    "where p.idtipoquota in ('T1','T2','T3','T4') " .
                    "  and p.idcondominio = ? " .
                    "  and extract(year from p.datalimite) = ? " .
                    "  and p.valorpago is not null ".
                    "  and p.valorapagar > ifnull(p.valorpago,0)+1";
        $quotasNormaisDoAnoEnganos = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $query =    "select sum(p.valorapagar-p.valorpago) as quotasExtraDoAnoEnganos " .
                    "from quota p " .
                    "where p.idtipoquota in ('Elev1','Elev2','Elev3','Elev4') " .
                    "  and p.idcondominio = ? " .
                    "  and extract(year from p.datalimite) = ? " .
                    "  and p.valorpago is not null ".
                    "  and p.valorapagar > ifnull(p.valorpago,0)+1";
        $quotasExtraDoAnoEnganos = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $query =    "select sum(valor) totalorcamento " .
                    "from rubricaorcamento ro " .
                    "inner join orcamento o on o.idrubricaorcamento=ro.idrubricaorcamento " .
                    "where parentid is null " .
                    "  and tiporubricaorcamento='Despesa' " .
                    "  and o.idcondominio = ? " .
                    "  and ano= ? ";
        $orcamento = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $query =    "select sum(d.valorcomiva) as totalDespesasFaturadasPagas " .
                    "from despesa d " .
                    "where d.idcondominio = ? " .
                    "  and extract(year from d.datalimitepagamento) = ? ";
        $totalFaturado = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $query =    "select sum(d.valorcomiva) as totalDespesasFaturadasPagas " .
                    "from despesa d " .
                    "where d.idcondominio = ? " .
                    "  and extract(year from d.datalimitepagamento) = ? " .
                    "  and extract(year from d.datapagamento) = ? ";
        $totalDespesasFaturadasPagas = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $query =    "select sum(d.valorcomiva) as totalDespesasAnoAnteriorPagasNoAno " .
                    "from despesa d " .
                    " where d.idcondominio= ? ".
                    "  and extract(year from d.datalimitepagamento) < ? " .
                    "  and extract(year from d.datapagamento) = ? ";
        $totalDespesasAnoAnteriorPagasNoAno = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $query =    " select sum(p.valorapagar-ifnull(p.valorpago,0)) as divida ".
                    " from quota p ".
                    " where p.idcondominio= ? ".
                    "  and extract(year from p.datalimite) < ? ".
                    "  and p.datalimite  < ifnull(p.datapagamento,now()) ".
                    "  and p.valorapagar > ifnull(p.valorpago,0)+1";
        $dividasAnosAnteriores = $this->executeQuerySumAno($query, $idcondominio, $ano);
        
        //------------------------------------------
        $resultadoA    = $orcamento - $totalFaturado;
        $resultadoB    = $orcamento - $totalDespesasFaturadasPagas - $totalDespesasAnoAnteriorPagasNoAno;
        $resultadoC    = $quotasDoAnoPagasNoAno + $quotasAtrasadasPagasNoAno + $quotasExtraDoAnoPagasNoAno + $quotasExtraAtasadasPagasNoAno + $quotasExtraDoProximoAnoPagasNoAno + $totalReceitasExtra - $totalDespesasFaturadasPagas - $totalDespesasAnoAnteriorPagasNoAno ;
        
        $resultadoA    =number_format($resultadoA,2,'.',' ');
        $resultadoB    =number_format($resultadoB,2,'.',' ');
        $resultadoC    =number_format($resultadoC,2,'.',' ');
        
        //------------------------------------------
        $quotasDoAnoPagasNoAno              = number_format($quotasDoAnoPagasNoAno,2,'.',' ');
        $quotasAtrasadasPagasNoAno          = number_format($quotasAtrasadasPagasNoAno,2,'.',' ');
        $quotasExtraDoAnoPagasNoAno         = number_format($quotasExtraDoAnoPagasNoAno,2,'.',' ');
        $quotasExtraAtasadasPagasNoAno      = number_format($quotasExtraAtasadasPagasNoAno ,2,'.',' ');
        $quotasExtraDoProximoAnoPagasNoAno  = number_format($quotasExtraDoProximoAnoPagasNoAno,2,'.',' ');
        $totalReceitasExtra                 = number_format($totalReceitasExtra,2,'.',' ');
        $quotasDoAnoNaoPagas                = number_format($quotasDoAnoNaoPagas,2,'.',' ');
        $quotasExtraDoAnoNaoPagas           = number_format($quotasExtraDoAnoNaoPagas,2,'.',' ');
        $quotasNormaisDoAnoEnganos          = number_format($quotasNormaisDoAnoEnganos,2,'.',' ');
        $quotasExtraDoAnoEnganos            = number_format($quotasExtraDoAnoEnganos,2,'.',' ');
        $orcamento                          = number_format($orcamento,2,'.',' ');
        $totalFaturado                      = number_format($totalFaturado,2,'.',' ');
        $totalDespesasFaturadasPagas        = number_format($totalDespesasFaturadasPagas,2,'.',' ');
        $totalDespesasAnoAnteriorPagasNoAno = number_format($totalDespesasAnoAnteriorPagasNoAno,2,'.',' ');
        
        //------------------------------------------
        print ("<table class='tabela2'>\n");
        print ("<tr>");
        print ("<th rowspan=\"6\" class='quadricula2'>Total de receitas</th>");
        print ("<td class='quadricula2'>Quotas do ano pagas no ano</td>");
        print ("<td class='quadricula2'>A1</td>");
        print ("<td class='quadricula1'>{$quotasDoAnoPagasNoAno}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<td class='quadricula2'>Quotas atrasadas pagas no ano</td>");
        print ("<td class='quadricula2'>A2</td>");
        print ("<td class='quadricula1'>{$quotasAtrasadasPagasNoAno}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<td class='quadricula2'>Quotas extra do ano pagas no ano</td>");
        print ("<td class='quadricula2'>B1</td>");
        print ("<td class='quadricula1'>{$quotasExtraDoAnoPagasNoAno}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<td class='quadricula2'>Quotas extra atrasadas pagas no ano</td>");
        print ("<td class='quadricula2'>B2</td>");
        print ("<td class='quadricula1'>{$quotasExtraAtasadasPagasNoAno}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<td class='quadricula2'>Quotas extra do próximo ano pagas no ano</td>");
        print ("<td class='quadricula2'>B3</td>");
        print ("<td class='quadricula1'>{$quotasExtraDoProximoAnoPagasNoAno}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<td class='quadricula2'>Extraordinárias</td>");
        print ("<td class='quadricula2'>C</td>");
        print ("<td class='quadricula1'>{$totalReceitasExtra}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<th rowspan=\"4\" class='quadricula2'>Quotas não pagas</th>");
        print ("<td class='quadricula2'>Quotas do ano não pagas</td>");
        print ("<td class='quadricula2'>A4</td>");
        print ("<td class='quadricula1'>{$quotasDoAnoNaoPagas}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<td class='quadricula2'>Quotas extra do ano não pagas</td>");
        print ("<td class='quadricula2'>B4</td>");
        print ("<td class='quadricula1'>{$quotasExtraDoAnoNaoPagas}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<td class='quadricula2'>Quotas normais - enganos nos pagamentos</td>");
        print ("<td class='quadricula2'>A5</td>");
        print ("<td class='quadricula1'>{$quotasNormaisDoAnoEnganos}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<td class='quadricula2'>Quotas extra do ano - enganos nos pagamentos</td>");
        print ("<td class='quadricula2'>B5</td>");
        print ("<td class='quadricula1'>{$quotasExtraDoAnoEnganos}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<th rowspan=\"4\" class='quadricula2'>Total de despesas</th>");
        print ("<td class='quadricula2'>Orçamentadas</td>");
        print ("<td class='quadricula2'>H</td>");
        print ("<td class='quadricula1'>{$orcamento}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<td class='quadricula2'>Despessas assumidas no ano (pago + não pago)</td>");
        print ("<td class='quadricula2'>I</td>");
        print ("<td class='quadricula1'>{$totalFaturado}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<td class='quadricula2'>Despessas assumidas no ano e pagas no ano</td>");
        print ("<td class='quadricula2'>J</td>");
        print ("<td class='quadricula1'>{$totalDespesasFaturadasPagas}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<td class='quadricula2'>Despessas assumidas em anos anteriores e pagas no ano</td>");
        print ("<td class='quadricula2'>K</td>");
        print ("<td class='quadricula1'>{$totalDespesasAnoAnteriorPagasNoAno}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<th rowspan=\"3\" class='quadricula2'>Resultado</th>");
        print ("<td class='quadricula2'>Orçamentado vs despessas assumidas no ano (pago + não pago)</td>");
        print ("<td class='quadricula2'>H - I</td>");
        print ("<td class='quadricula1'>{$resultadoA}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<td class='quadricula2'>Orçamentado vs despesas pagas no ano</td>");
        print ("<td class='quadricula2'>H - (J + K)</td>");
        print ("<td class='quadricula1'>{$resultadoB}</td>");
        print ("</tr>");
        print ("<tr>");
        print ("<th class='quadricula2'>Receitas recebidas vs despesas efectuadas</th>");
        print ("<td class='quadricula2'>(A1 + A2 + B1 + B2 + B3 + C) - (J + K)</td>");
        print ("<td class='quadricula1'>{$resultadoC}</td>");
        print ("</tr>");
        //print ("<tr>");
        //print ("<th class='quadricula2'>Receitas do próximo ano recebidas este ano</th>");
        //print ("<td class='quadricula2'>D</td>");
        //print ("<td class='quadricula1'>{$quotasExtraDoProximoAnoPagasNoAno}</td>");
        //print ("</tr>");
        print ("<tr>");
        print ("<td class='quadricula2'>Dividas de anos anteriores</td>");
        print ("<td colspan='3' class='quadricula1'>{$dividasAnosAnteriores}</td>");
        print ("</tr>");
        $this->saldoContasBancarias($idcondominio, $ano);
        print ("</table>");
      
        mysqli_close($this->connection);
    }
    
}
