<?php
header('Content-Type: text/html; charset=UTF-8');
require_once("config.php");
require_once("dependencies/lang.php");
$config = new config;
if($config->https){
	if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
			$redirect = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: '.$redirect);
			exit();
	}
}
if(isset($_SERVER['PHP_AUTH_USER'])){
	if (!$config->login($user,$pass)) {
		header('WWW-Authenticate: Basic realm="Vertretungsplan"');
		header('HTTP/1.0 401 Unauthorized');
		die ("Not authorized");
	}
	$user = $_SERVER['PHP_AUTH_USER'];
	$pass = $_SERVER['PHP_AUTH_PW'];
}

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
?>
<div style="height: 10px;"><space></space></div>
<div style="position: fixed;bottom: 0;margin: 0 auto;background-color: white;width: 100%">
<h4>
	<a href="https://gitlab.com/witt-oks/vertretungsplan-online">Vertretungsplan</a> - © Copyright 2017 - <?php echo date("Y")?> Nils Witt - 
	<a href="https://gitlab.com/witt-oks/vertretungsplan-online/issues">Issue / Idea Tracking<a>
</h4>
</div>
</body></html>