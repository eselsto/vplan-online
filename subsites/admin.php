<?php
if($admin){
    echo "TRUE";
}else{
	header('WWW-Authenticate: Basic realm="Vertretungsplan"');
	header('HTTP/1.0 401 Unauthorized');
	die ("Not authorized");
}


?>