var myTree = new tree();
myTree.add(new leaf('Pagina principal','PaginaInicial.php'));
myTree.add(new leaf('Noticias','Noticia.php'));
var branch025000000 = new branch('branch025000000','Receitas');
	branch025000000.add(new leaf('Mapa quotas','PagamentoQuotas_MapaResumo.php'));
	branch025000000.add(new leaf('Detalhes quotas','PagamentoQuotas_Detalhe.php'));
	branch025000000.add(new leaf('Mapa quotas extra','PagamentoQuotasExtra_MapaResumo.php'));
	branch025000000.add(new leaf('Detalhes quotas extra','PagamentoQuotasExtra_Detalhe.php'));
    branch025000000.add(new leaf('Quotas em atraso','QuotasAtraso_Lista.php'));
	branch025000000.add(new leaf('Receitas extra','ReceitasExtra_Lista.php'));
myTree.add(branch025000000);
myTree.add(new leaf('Despesas','Despesa_Pesquisa.php'));
myTree.add(new leaf('Demonstração de resultados','DemonstracaoResultados_Mapa.php'));
myTree.add(new leaf('Problemas','Problema_Pesquisa.php'));
myTree.add(new leaf('Sair','Login.php'));
myTree.write();