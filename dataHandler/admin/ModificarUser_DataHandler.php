<?php
class ModificarUser_DataHandler {

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

    function apagarUser() {}

    function listaUsers($idcondominio) {

        $query = "select 
                    convert(aes_decrypt(unhex(nome),UNHEX(SHA2('6LrRjvPFaE8YP8yMsyxKcL',512)))  using utf8mb4) as nome,
                    convert(aes_decrypt(unhex(login),UNHEX(SHA2('6LrRjvPFaE8YP8yMsyxKcL',512)))  using utf8mb4) as login,
                    idperfilutilizador
                from utilizadoraplicacao where idcondominio = ?;";

        $stmt = mysqli_stmt_init($this->connection);

        if (!mysqli_stmt_prepare($stmt, $query)) {
            print '<div class="msgErro">Erro na preparacao do prepared statement</div>';
            return;
        }
        //"s" significa uma variavel do tipo string. Se fosse uma string e um int seria "si"
        mysqli_stmt_bind_param($stmt, "s", $idcondominio);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_error($stmt) != "") {
            print '<div class="msgErro">Erro na execução do SQL: ' . mysqli_stmt_error($stmt) . '</div>';
            //print '<div class="msgErro">Erro na execução do SQL: ' . '</div>';
            return;
        }
        mysqli_stmt_bind_result($stmt, $nome, $login, $idperfilutilizador);
        /* transportar os valores */
        /* nas expressões abaixo é mais rápido fazer print ('<table>\n'); mas neste caso o PHP não vai interpretar \n como fim de linha */
        /* quando colocamos print("<table>\n"); o PHP faz uma análise ao argumento e deteta o fim de linha */
        print ("<table class='table table-bordered table-striped'>\n");
        print ("<tr>");
        print ("
        <th >
        <!-- Utilizador -->
            Utilizador
        </th>
        <th >
        <!-- Login -->
            Login
        </th>
        <th >
        <!-- Perfil -->
            Perfil
        </th>
        </th>
        <th >
        <!-- ACAO -->
            Ação
        </th>
        \n");
        print ("</tr>");
        // Conteudo, data, Utilizador
        while (mysqli_stmt_fetch($stmt)) {
            print ("<tr id='$login'>\n");
            printf("
                <td data-target='utilizador'>%s</td>
                <td data-target='login'>%s</td>
                <td data-target='perfil_utilizador'>%s</td>
                <td >
                <a id='editarUtilizador' title='Editar Utilizador' data-toggle='modal' data-target='#editarUtilizadorModal' data-role='editar' data-id='$login'><span class='glyphicon glyphicon-pencil'></span></a>
                <a id='eliminarUtilizador' title='Eliminar Utilizador' data-toggle='modal' data-target='#eliminarUtilizadorModal' data-role='eliminar' data-id='$login'><span class='glyphicon glyphicon-trash'></span></a>
                </td>\n",
                $nome, $login, $idperfilutilizador);
            print ("</tr>\n");
        }
        print ("</table>\n");

        print ("
            <!-- Criar Utilizador -->
            <div class=\"modal fade\" id=\"criarUtilizadorModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"criarUtilizadorLabel\" aria-hidden=\"true\">
                <div class=\"modal-dialog\" role=\"document\">
                    <div class=\"modal-content\">
                        <div class=\"modal-header\">
                            <h5 class=\"modal-title\" id=\"criarUtilizadorLabel\">Criar Utilizador</h5>
                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Fechar\">
                                <span aria-hidden=\"true\">&times;</span>
                            </button>
                        </div>
                        <div class=\"modal-body\">
                            A criar Utilizador?
                        </div>
                        <div class=\"modal-footer\">
                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Cancelar</button>
                            <button onclick='' type=\"submit\" class=\"btn btn-primary\">Submeter</button>
                        </div>
                    </div>
                </div>
            </div> 
        ");

        print ("
            <!-- Apagar Utilizador -->
            <div class=\"modal fade\" id=\"eliminarUtilizadorModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"eliminarUtilizadorLabel\" aria-hidden=\"true\">
                <div class=\"modal-dialog\" role=\"document\">
                    <div class=\"modal-content\">
                        <div class=\"modal-header\">
                            <h5 class=\"modal-title\" id=\"eliminarUtilizadorLabel\">Eliminar Utilizador</h5>
                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Fechar\">
                                <span aria-hidden=\"true\">&times;</span>
                            </button>
                        </div>
                        <div class=\"modal-body\">
                            Pretende eliminar esta Utilizador?
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
            <!-- Editar Utilizador -->
            <div class=\"modal fade\" id=\"editarUtilizadorModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"editarUtilizadorLabel\" aria-hidden=\"true\">
                <div class=\"modal-dialog\" role=\"document\">
                    <div class=\"modal-content\">
                        <div class=\"modal-header\">
                            <h5 class=\"modal-title\" id=\"editarUtilizadorLabel\">Editar Utilizador</h5>
                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Fechar\">
                                <span aria-hidden=\"true\">&times;</span>
                            </button>
                        </div>
                        <div class=\"modal-body\">
                            <form action=\"../Utilizador.php\" method=\"POST\">
                                <div class=\"form-group\">
                                    <label>Utilizador:</label>
                                    <input type= 'text' name='utilizador' value='$login'>
                                </div>
                                <div class=\"form-group\">
                                    <label>Perfil:</label>
                                    <input type= 'text' name='utilizador' value='$idperfilutilizador'>
                                </div>
                            </form>
                        </div>
                        <div class=\"modal-footer\">
                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Cancelar</button>
                            <button type=\"submit\" class=\"btn btn-primary\">Submeter</button>
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