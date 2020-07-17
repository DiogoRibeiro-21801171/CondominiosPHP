var myTree = new tree();
myTree.add(new leaf('Pagina principal','PaginaInicial_admin.php'));
myTree.add(new leaf('Modificar Utilizador','ModificarUser_admin.php'));
myTree.add(new leaf('Sair','../../Login.php'));
myTree.write();