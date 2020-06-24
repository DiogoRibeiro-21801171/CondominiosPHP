<meta charset="utf-8">
<meta name="Author" content="Jose Aser Lorenzo">
<link rel="shortcut icon" href="/condominio/imagens/icon.gif">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->
<link rel="stylesheet" href="/condominio/css/bootstrap.min.css" type="text/css" />
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />  -->
<link rel="stylesheet" href="/condominio/css/style.min.css" />
<link rel="StyleSheet" href="/condominio/css/turtlelearning.css" type="text/css" />

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"> </script>  -->
<script src="/condominio/css/jquery.min.js"> </script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"> </script> -->
<script src="/condominio/css/jstree.min.js"> </script>
<script src="/condominio/css/turtlelearningtree.js"></script>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>

<script>
function cleanOnLoad() {
	var x = document.getElementsByClassName("mycheckbox");
	for (var i = 0; i < x.length; i++) {
	    x[i].checked = false;
	} 
}
function showHide(me) {
	var idObjShow1 = document.getElementById(me.id);
	var idObjShow2 = document.getElementById(me.id+'_tb');
	if (idObjShow1.checked) {
	   idObjShow2.style.display = 'block';
	} else {
	   idObjShow2.style.display = 'none';
	}
}
</script>