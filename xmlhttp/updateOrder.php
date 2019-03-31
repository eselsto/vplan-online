<?php
require_once("../config.php");
$config = new config;
$json = file_get_contents("php://input");
$data = json_decode($json,true);



$data_string = json_encode($data);                                                                                   
                                                                                                                     
$ch = curl_init($config->url_api.'/aushang.php?aushang=updateOrder&secret='.$config->api_secret);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
);   
$result = curl_exec($ch);
echo $result;
?>