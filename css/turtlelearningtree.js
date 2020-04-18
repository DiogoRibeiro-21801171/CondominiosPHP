var openImg = new Image();
openImg.src = "/condominio/imagens/open.gif";
var closedImg = new Image();
closedImg.src = "/condominio/imagens/closed.gif";


function tree(){
	this.branches = new Array();
	this.add = addBranch;
	this.write = writeTree;
}

function branch(id, text){
	this.id = id;
	this.text = text;
	this.write = writeBranch;
	this.add = addLeaf;
	this.leaves = new Array();
}

function leaf(text, link){
	this.text = text;
	this.link = link;
	this.write = writeLeaf;
}

function writeTree(){
	var treeString = '';
	var numBranches = this.branches.length;
	for(var i=0;i<numBranches;i++)
		treeString += this.branches[i].write();
	document.write(treeString);
}

function addBranch(branch){
	this.branches[this.branches.length] = branch;
}

function writeBranch(){
	var branchString = '<span class="branch" onClick="showBranch(\'' + this.id + '\')"';
	branchString += '><img src="/condominio/imagens/closed.gif" id="I' + this.id + '">' + this.text;
	branchString += '</span>';
	branchString += '<span class="leaf" id="';
	branchString += this.id + '">';
	var numLeaves = this.leaves.length;
	for(var j=0;j<numLeaves;j++)
		branchString += this.leaves[j].write();
	branchString += '</span>';
	return branchString;
}

function addLeaf(leaf){
	this.leaves[this.leaves.length] = leaf;
}

function writeLeaf(){
	/*var leafString = '<a href="' + this.link + '" target="basefrm">';*/
	var leafString = '<a href="' + this.link + '">';
	leafString += '<img src="/condominio/imagens/doc.gif" border="0">';
	/*change apearance of current leaf */
	if (this.text === currentLeaf) {
		leafString += '<span class="treeHighLight">[ ' + this.text + ' ]</span>'; 
	} else {
		leafString += this.text;
	}
	leafString += '</a><br>';
	return leafString;
}

function showBranch(branch){
	var objBranch = document.getElementById(branch).style;
	if(objBranch.display=="block")
		objBranch.display="none";
	else
		objBranch.display="block";
	swapFolder('I' + branch);
}
function swapFolder(img){
	objImg = document.getElementById(img);
	if(objImg.src.indexOf('/condominio/imagens/closed.gif')>-1)
		objImg.src = openImg.src;
	else
		objImg.src = closedImg.src;
}
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