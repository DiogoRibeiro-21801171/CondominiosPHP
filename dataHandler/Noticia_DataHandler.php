<?php
class Noticia_DataHandler {

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
            header("Location: Login.php");
            exit();
        }

        if (!mysqli_set_charset($this->connection, "utf8")) {
            //mensagem de erro completa para debug
            $_SESSION["msg"] = '<div class="msgErro">Não consegui carregar character set utf8: ' . mysqli_error($this->connection);
            //mensagem de erro para cliente
            //$_SESSION["msg"] = '<div class="msgErro">Não consegui carregar character set utf8: ';
            header("Location: Login.php");
            exit();
        }
    }

    //------------------------------------------------------------------------------------

    function listaNoticias($idcondominio) {

        $query = "select data, noticia, idnoticia from noticia where idcondominio = ? order by data desc";

        $stmt = mysqli_stmt_init($this->connection);

        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        //"s" significa uma variavel do tipo string. Se fosse uma string e um int seria "si"
        mysqli_stmt_bind_param($stmt, "s",$idcondominio);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        mysqli_stmt_bind_result($stmt, $data, $noticia, $idnoticia);
        /* transportar os valores */
        /* nas expressões abaixo é mais rápido fazer print ('<table>\n'); mas neste caso o PHP não vai interpretar \n como fim de linha */
        /* quando colocamos print("<table>\n"); o PHP faz uma análise ao argumento e deteta o fim de linha */
        print ("<table class='table table-bordered table-striped'>\n");
        print ("<tr>");
        print ("
        <th >
        <!-- DATA -->
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </th>
        <th >
        <!-- NOTICIA -->
            Noticia
        </th>
        </th>
        <th >
        <!-- ACAO -->
            Ação
        </th>
        \n");
        print ("</tr>");
        // Conteudo, data, noticia
        while (mysqli_stmt_fetch($stmt)) {
            print ("<tr id='$idnoticia'>\n");
            printf("
                <td data-target='data'>%s</td>
                <td data-target='noticia'>%s</td>
                <td >
                <a id='editarNoticia' title='Editar Noticia' data-toggle='modal' data-target='#editarNoticiaModal' data-role='editar' data-id='$idnoticia'><span class='glyphicon glyphicon-pencil'></span></a>
                <a id='eliminarNoticia' title='Eliminar Noticia' data-toggle='modal' data-target='#eliminarNoticiaModal' data-role='editar' data-id='$idnoticia'><span class='glyphicon glyphicon-trash'></span></a>
                </td>\n",
                $data, $noticia);
            print ("</tr>\n");
        }
        print ("</table>\n");

        print ("
            <!-- Apagar Noticia -->
            <div class=\"modal fade\" id=\"eliminarNoticiaModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"eliminarNoticiaLabel\" aria-hidden=\"true\">
                <div class=\"modal-dialog\" role=\"document\">
                    <div class=\"modal-content\">
                        <div class=\"modal-header\">
                            <h5 class=\"modal-title\" id=\"eliminarNoticiaLabel\">Eliminar Noticia</h5>
                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Fechar\">
                                <span aria-hidden=\"true\">&times;</span>
                            </button>
                        </div>
                        <div class=\"modal-body\">
                            Pretende eliminar esta noticia?
                        </div>
                        <div class=\"modal-footer\">
                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Cancelar</button>
                            <button onclick='' type=\"submit\" class=\"btn btn-danger\" value='eliminar'>Eliminar</button>
                        </div>
                    </div>
                </div>
            </div> 
        ");

        print ("
            <!-- Editar Noticia -->
            <div class=\"modal fade\" id=\"editarNoticiaModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"editarNoticiaLabel\" aria-hidden=\"true\">
                <div class=\"modal-dialog\" role=\"document\">
                    <div class=\"modal-content\">
                        <div class=\"modal-header\">
                            <h5 class=\"modal-title\" id=\"editarNoticiaLabel\">Editar Noticia</h5>
                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Fechar\">
                                <span aria-hidden=\"true\">&times;</span>
                            </button>
                        </div>
                        <div class=\"modal-body\">
                            <form action=\"../Noticia.php\" method=\"POST\">
                                <div class=\"form-group\">
                                    <label>Data:</label>
                                    <input type= 'date' name='data' value='$data'>
                                </div>
                                <div class=\"form-group\">
                                    <label>Noticia:</label>
                                    <textarea rows=\"3\" cols='70'>$noticia</textarea>
                                </div>
                            </form>
                        </div>
                        <div class=\"modal-footer\">
                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Cancelar</button>
                            <button type=\"submit\" class=\"btn btn-danger\">Submeter</button>
                        </div>
                    </div>
                </div>
            </div> 
        ");

        /* close statement */
        mysqli_stmt_close($stmt);
        mysqli_close($this->connection);
    }

}