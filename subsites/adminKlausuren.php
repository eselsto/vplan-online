<?php
$secret = $config->api_secret;

function curlToApi($json,$urlargs){
    global $config;
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $config->url_api."/klausuren.php?$urlargs",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_POSTFIELDS => $json,
    CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache"
    ),
    ));
    return curl_exec($curl);
}

function convertStdToTime($std,$lessons){
	if(strlen($std) == 5){
		$stunden = explode("-",$std);
		$von = $lessons[substr($stunden[0],0,1)]["begin"].":00";
		$bis = $lessons[substr($stunden[1],0,1)]["end"].":00";
		return array($von,$bis);
	}else{
		$stunden = explode("-",$std);
		return array($stunden[0],$stunden[1]);
	}
}

function isTime($time) {
    if (preg_match("/^([1-2][0-3]|[01]?[1-9]):([0-5]?[0-9]):([0-5]?[0-9])$/", $time))
        return true;
    return false;
}

function xmlToArray($xml){
    for($i = 0; $i < count($xml); $i++){

        $exceldatum = $xml->klausur[$i]->datum;
        $anzeigen = $xml->klausur[$i]->anzeigen;
        $std = $xml->klausur[$i]->stunde;
        $stufe = $xml->klausur[$i]->stufe;
        $kurs = $xml->klausur[$i]->kurs;
        $lehrer = $xml->klausur[$i]->lehrer;
        $raum = $xml->klausur[$i]->raum;
        $eins = $xml->klausur[$i]->eins;
        $zwei = $xml->klausur[$i]->zwei;
        $drei = $xml->klausur[$i]->drei;
        $vier = $xml->klausur[$i]->vier;
        $funf = $xml->klausur[$i]->fünf;
        $sechs = $xml->klausur[$i]->sechs;
        $sieben = $xml->klausur[$i]->sieben;
    
        $unixdatum = ($exceldatum - 25569) * 86400;
    
        $datum = gmdate("d-m-Y", $unixdatum);
        if($std == ""){$std = "-";}
        if($raum == ""){$raum = "-";}
        if($eins == ""){$eins = "-";}
        if($zwei == ""){$zwei = "-";}
        if($drei == ""){$drei = "-";}
        if($vier == ""){$vier = "-";}
        if($funf == ""){$funf = "-";}
        if($sechs == ""){$sechs = "-";}
        if($sieben == ""){$sieben = "-";
        }
    
        $time = convertStdToTime($std,$config["lessons"]);
        $von = $time[0];
        $bis = $time[1];
    
        $dataset = array();
    
        $dataset["date"] = json_decode(json_encode($datum),true);
        $dataset["excelDate"] = json_decode(json_encode($exceldatum),true)[0];
        $dataset["unixtime"] = json_decode(json_encode($unixdatum),true);
        
        $dataset["display"] = json_decode(json_encode($anzeigen),true)[0];
        $dataset["lesson"] = json_decode(json_encode($std),true)[0];
        $dataset["grade"] = json_decode(json_encode($stufe),true)[0];
        $dataset["course"] = json_decode(json_encode($kurs),true)[0];
        
        $dataset["room"] = json_decode(json_encode($raum),true)[0];
        $dataset["lessonOne"] = json_decode(json_encode($eins),true)[0];
        $dataset["lessonTwo"] = json_decode(json_encode($zwei),true)[0];
        $dataset["lessonThree"] = json_decode(json_encode($drei),true)[0];
        $dataset["lessonFour"] = json_decode(json_encode($vier),true)[0];
        $dataset["lessonFive"] = json_decode(json_encode($funf),true)[0];
        $dataset["lessonSix"] = json_decode(json_encode($sechs),true)[0];
        $dataset["lessonSeven"] = json_decode(json_encode($sieben),true)[0];

        if(isTime(json_decode(json_encode($von),true))){
            $dataset["from"] = json_decode(json_encode($von),true);
        }else{
            $dataset["from"] = "00:00:00";
        }
        if(isTime(json_decode(json_encode($bis),true))){
            $dataset["to"] = json_decode(json_encode($bis),true);
        }else{
            $dataset["to"] = "00:00:00";
        }
        if(json_decode(json_encode($lehrer),true)[0] != NULL){
            $dataset["teacher"] = json_decode(json_encode($lehrer),true)[0];
        }else{
            $dataset["teacher"] = "---";
        }
        

        $data[] = $dataset;
    }
    return $data;
}

$xml = simplexml_load_file(__DIR__.'/../ImportFiles/klausuren.xml');


if(true){
    $deleteData = array();

    $deleteData["mode"] = "delete";
    $deleteData["type"] = "klausuren";
    $deleteData["data"] = array("%");

    $response = curlToApi(json_encode(array($deleteData)),"secret=$secret&mode=edit");
}

$insert["mode"] = "insert";
$insert["type"] = "klausuren";
$insert["data"] = xmlToArray($xml);

$response = curlToApi(json_encode(array($insert)),"secret=$secret&mode=edit");

echo "<h1>Completed</h1>"
?>