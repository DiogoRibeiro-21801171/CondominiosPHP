var myTree = new tree();
myTree.add(new leaf('Pagina principal','PaginaInicial_admin.php'));
myTree.add(new leaf('Adicionar Utilizador','Noticia.php'));
myTree.add(new leaf('Alterar Utilizador','Login.php'));
myTree.add(new leaf('Remover Utilizador','Login.php'));
myTree.add(new leaf('Sair','Login.php'));
myTree.write();