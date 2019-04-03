document.addEventListener("DOMContentLoaded", function(){
  load()
	windowAdjustment();
});
function load(){
    loadActive()
}

function loadActive(){
    var activeHttp = new XMLHttpRequest();
    activeHttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("active").innerHTML = this.responseText;
		  adjusttextareas();
      }
    };
    activeHttp.open("GET", "xmlhttp/xmlhttpAushang.php?action=load", true);
    activeHttp.send(); 
}

function removeElementFromApi(id){
    var remHttp = new XMLHttpRequest();
    remHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          loadActive();
			$.notify("Gelöscht", "success");
        }
      };
    remHttp.open("POST", "xmlhttp/xmlhttpAushang.php?action=delete");
    remHttp.setRequestHeader("Content-Type", "application/json");
    remHttp.send(JSON.stringify({"id": id}));
}
function addElementFromWeb(){
    var inhalt = document.getElementById('web.0.inhalt').value
    var inhalt2 = document.getElementById('web.0.inhalt2').value
	var e = document.getElementById("colors");
	var color = e.options[e.selectedIndex].value;
	if(color == "none"){
		$(e).notify("Farbe auswählen");
		return
	}
	if(inhalt == "" || inhalt == null){
		$(document.getElementById('web.0.inhalt')).notify("Inhalt eingeben");
		return
	}
	var json = JSON.stringify({"inhalt1": inhalt, "inhalt2": inhalt2, "color": color})
    var createHttp = new XMLHttpRequest();
    createHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          loadActive();
			$.notify("Erstellt", "success");
        }
      };
    createHttp.open("POST", "xmlhttp/xmlhttpAushang.php?action=create");
    createHttp.setRequestHeader("Content-Type", "application/json");
    createHttp.send(json);
	$.notify("Bitte warten", "info");
	document.getElementById('web.0.inhalt').value = ""
    document.getElementById('web.0.inhalt2').value = ""
}

function editElementFromApi(id){
	var row = document.getElementById('row.' + id);
	
	var elems = row.getElementsByTagName('textarea');
	for(var i=0; i< elems.length; i++){
		elems[i].disabled = false; 
	}	
	var elems = row.getElementsByTagName('select');
	for(var i=0; i< elems.length; i++){
		elems[i].disabled = false; 
	}
	
	document.getElementById('edit.' + id).style.display = "none";
	document.getElementById('save.' + id).style.display = "block";
	
	$(row).notify("Bearbeitung aktiv", "success");
	
}

function updateToApi(id){
	var inhalt = document.getElementById('textarea.'+ id +'.inhalt').value
	if(document.getElementById('textarea.' + id + '.inhalt2') != null && document.getElementById('textarea.' + id + '.inhalt2') != ""){
 		var inhalt2 = document.getElementById('textarea.'+ id +'.inhalt2').value
	}
	var row = document.getElementById('row.' + id);
	var elems = row.getElementsByTagName('select');
	for(var i=0; i< elems.length; i++){
		 var newColor = elems[i].value; 
	}
	console.log(newColor)
    var editHttp = new XMLHttpRequest();
    editHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          loadActive();
			$(row).notify("Gespeichert");
        }
      };
	
    editHttp.open("POST", "xmlhttp/xmlhttpAushang.php?action=update");
    editHttp.setRequestHeader("Content-Type", "application/json");
	if(inhalt2 != null){
		editHttp.send(JSON.stringify({"id": id,"inhalt1": inhalt,"inhalt2": inhalt2,"color":newColor}));
	}else{
		editHttp.send(JSON.stringify({"id": id,"inhalt1": inhalt,"color":newColor}));
	}
}

function moveElement(id,direction){
	var moveHttp = new XMLHttpRequest();
    moveHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          loadActive();
        }
      };
    moveHttp.open("POST", "xmlhttp/xmlhttpAushang.php?action=move");
    moveHttp.setRequestHeader("Content-Type", "application/json");
    moveHttp.send(JSON.stringify({"id": id,"direction": direction}));

    
}

function textAreaAdjust(o) {
  o.style.height = "1px";
  o.style.height = (3+o.scrollHeight)+"px";
}
function adjusttextareas(){
	var elems = document.getElementsByTagName('textarea');
	for(var i=0; i< elems.length; i++){
		textAreaAdjust(elems[i])
	}
}

function windowAdjustment(){
	var height = document.getElementById("header").offsetHeight;
	var windowheight = window.innerHeight;
	//document.getElementById("spacer").style.height = height;
	console.log(windowheight)
}
