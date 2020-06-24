	<div class="container-fluid">
		<!-- Cabecalho ===================================================================================== -->
		<div class="row">
			<div class="col-sm-12">
				<span id="event_result3">
				</span>
				<table>
					<tr>
						<td class="tabletitlerow1" rowspan="2">
							<a href="http://www.condominio25abril17.pt">
								<img src="/condominio/imagens/top.jpg" width="70x" alt="logo">
							</a>
						</td>
						<td class="tabletitlerow1">
								<?php
								if ((isset($_SESSION["morada"])) && (strcmp($_SESSION["morada"], "") != 0)) {
								    print '<h4>Condomínio ' . $_SESSION["morada"] . '</h4>';
                                }
                                ?>
                        </td>
                    </tr>
					<tr>
                        
                        <td class="tabletitlerow1">
                        		<?php
                                if ((isset($_SESSION["ultimaatualizacao"])) && (strcmp($_SESSION["ultimaatualizacao"], "") != 0)) {
                                    print '<h6>Última atualização de dados em: ' . $_SESSION["ultimaatualizacao"] . '</h6>';
                                }
                                ?>
								
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="row">
			<!-- arvore ================================================================================ -->
			<div class="col-sm-2">
				<div id="treemenu1" class="turtlelearningtree">