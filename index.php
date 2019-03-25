<?php
require_once("config.php");
$config = new config;
if($config->https){
	if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
			$redirect = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: '.$redirect);
			exit();
	}
}
$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];
if (!$config->login($user,$pass)) {
	header('WWW-Authenticate: Basic realm="Vertretungsplan"');
	header('HTTP/1.0 401 Unauthorized');
	die ("Not authorized");
}



$langhtmlonliverttitle = "Vertretungsplan";

$langhtmlonlivertheadline = "Vertretungsplan der Otto-K&uuml;hne-Schule";
$langhtmlonlivertunderlineone = "Bitte achte bei jeder Vertretungsstunde darauf, dass auch wirklich dein Kurs gemeint ist.";
$langhtmlonlivertunderlinetwo = "letzte Aktualisierung:";
$langhtmlonlivertdayhead = "Vertretungsplan f&uuml;r";
$langhtmlonlivertdayunterline = "Ge&auml;nderte Aufsichten:";

$langhtmlonliverttablerowone = "Klasse";
$langhtmlonliverttablerowtwo= "Std.";
$langhtmlonliverttablerowthree = "Fach";
$langhtmlonliverttablerowfour = "Fach neu";
$langhtmlonliverttablerowfive = "Vert.";
$langhtmlonliverttablerowsix = "Raum neu";
$langhtmlonliverttablerowseven = "Bemerkung";



header('Content-Type: text/html; charset=UTF-8');


if(isset($_GET["subsite"])){
	$subsite = htmlspecialchars($_GET["subsite"]);
}else{
	$subsite = "vertretungsplan";
}

if ($subsite == ""){
	$subsite = "vertretungsplan";
}
echo
"<!doctype html>
<html lang=DE>
<meta charset=\"utf-8\">
<head>
	<link href=\"css/online/sm-core-css.css\" rel=\"stylesheet\" type=\"text/css\">
	<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, minimum-scale=1.0\">
	<link href=\"css/".$subsite.".css\" rel=\"stylesheet\" type=\"text/css\">
	<link href=\"css/online//sm-blue.css\" rel=\"stylesheet\" type=\"text/css\">
	<link rel=\"stylesheet\" type=\"text/css\" href=\"css/online/vertretungsplan.css\">
	<title>
		$langhtmlonliverttitle
	</title>
</head>";

//EDIT//END
echo "<body>";
require_once('menus.php');
require_once($subsite.'.php');
echo "</body></html>";

?>