<?php
$menuonline =
"<ul id=\"main-menu\" class=\"sm sm-blue\">";

$menuonline .= "<li><a href=\"?type=online&subsite=vertretungsplan\">Vertretungsplan</a></li>";
$menuonline .= "<li><a href=\"?type=online&subsite=klausuren\">n&auml;chste Klausuren</a></li>";
$menuonline .= "<li><a href=\"?type=online&subsite=aushang\">Aushang</a></li>";
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