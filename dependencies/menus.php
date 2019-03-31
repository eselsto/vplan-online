<?php
$menuonline =
"<ul id=\"main-menu\" class=\"sm sm-blue\">";
if(substr($subsite,0,5) == "admin"){
	$menuonline .= "<li><a href=\"?subsite=adminVert\">Vertretungen Import</a></li>";
	$menuonline .= "<li><a href=\"?subsite=adminKlausuren\">Klausuren Import</a></li>";
	$menuonline .= "<li><a href=\"?subsite=adminAushang\">Aushang Admin</a></li>";
	$menuonline .= "<li><a href=\"?subsite=vertretungsplan\">Administration Verlassen</a></li>";
}else{
	$menuonline .= "<li><a href=\"?subsite=vertretungsplan\">Vertretungsplan</a></li>";
	$menuonline .= "<li><a href=\"?subsite=klausuren\">n&auml;chste Klausuren</a></li>";
	$menuonline .= "<li><a href=\"?subsite=aushang\">Aushang</a></li>";
	$menuonline .= "<li><a href=\"?subsite=admin\">Admin</a></li>";	
}
$menuonline .=
	"<li>
		<a href=>Impressum</a>
	</li>
	<li>
		<a href=>Datenschutz</a>
	</li>	
</ul>";
echo $menuonline;
?>