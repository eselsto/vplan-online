<?php
if($admin){
    echo "<h1>Bitte Wählen</h1>";
}else{
	header('WWW-Authenticate: Basic realm="Vertretungsplan"');
	header('HTTP/1.0 401 Unauthorized');
	die ("Not authorized");
}


?>