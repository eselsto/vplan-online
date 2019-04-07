<?php
$secret = $config->api_secret;

function curlToApi($json,$urlargs){
    global $config;
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $config->url_api."/vertretungsplan.php?$urlargs",
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

function convertklasse($klasse,$fach){

    if(strpos($klasse,"/") == true){

        $explode = explode("/",$klasse);

        $gruppe = $fach."-".substr($explode[1],strlen($fach)+1);
        $output = $explode[0]."/".$gruppe;

        return $output;
    }else{
        return $klasse;
    }
}

function generateid($date,$kurs,$stunde){

    $timestamp = strtotime($date);
    $kursex = explode("/",str_replace(' ','',$kurs));
    if($kursex[0] != "EF" || $kursex[0] != "Q1" || $kursex[0] != "Q2"){}
    $stufe = $kursex[0];
    if(isset($kursex[1])){
        $kurs = $kursex[1];
    }else{
        $kurs = "";
    }
    

    $wochentag = date("w",$timestamp)-1;
    $stunde = $stunde-1;
    $woche = date("W",$timestamp);
    $jahr = date("Y",$timestamp);

    $id = $stufe."/".$kurs."/".$wochentag."/".$stunde."/".$woche."/".$jahr;

    return $id;
}

function convertdate($input){
    $months = array();
    $months['Januar'] = "01";
    $months['Februar'] = "2";
    $months['März'] = "3";
    $months['April'] = "4";
    $months['Mai'] = "5";
    $months['Juni'] = "6";
    $months['Juli'] = "7";
    $months['August'] = "8";
    $months['September'] = "9";
    $months['Oktober'] = "10";
    $months['November'] = "11";
    $months['Dezember'] = "12";
    
    $explode = explode(",",$input);
    $date = array();
    $dayex = explode(".",$explode[1]);
    $monthex = explode(" ",$explode[1]);

    $date['day'] = substr($dayex[0],1);
    $date['month'] = $months[$monthex[2]];
    $date['year'] = $monthex[3];
    
    return $date['year']."-".$date['month']."-".$date['day'];
}

function loadXml(){

    $days = array();
    $i = 1;
    $vertretung = array();
    $aufsichten = array();
    $tranfered_vert = "";
    $error_vert = "";
    $tranfered_days = "";
    $error_days = "";
    $tranfered_aufs = "";
    $error_aufs = "";
    
    $xml = simplexml_load_file(__DIR__.'/../ImportFiles/Vertretungsplan Lehrer.xml');
    if(!$xml){
        die("XML File not found");
    }
    foreach($xml->children() as $child){
        $childname = $child->getName();
        if($childname == "kopf"){

            $date = convertdate($child->titel);
            $refreshed = $child->datum;

        }elseif($childname == "haupt"){

            foreach($child->children() as $aktion){

                $stunde = $aktion->stunde;
                $fach = $aktion->fach;
                $lehrer = $aktion->lehrer;
                $klasse = $aktion->klasse;
                $vfach = $aktion->vfach;
                
                if(strpos($aktion->vlehrer,")") > 0){
                    $vlehrer = "---";
                }else{
                    $vlehrer = $aktion->vlehrer;
                }
                
                $vraum = $aktion->vraum;
                $info = $aktion->info;
    
                $event = array();
                $event["date"] = $date;
                $event["stunde"] = $stunde;
                $event["fach"] = $fach;
                $event["lehrer"] = $lehrer;
                $event["vfach"] = $vfach;
                $event["vlehrer"] = $vlehrer;
                $event["vraum"] = $vraum;
                $event["info"] = $info;

                $event["klasse"] = convertklasse($klasse,$fach);
                $event["id"] = generateid($date,$klasse,$stunde);

                array_push($vertretung,$event);
                if(in_array($date,$days)){
                    
                }else{
                    $days[] = $date;
                }
            }
        }elseif($childname == "aufsichten"){

            foreach($child->children() as $aufsicht){

                $data = $aufsicht->aufsichtinfo;
                $event = array();
                $event["date"] = $date;
                $event["zeit"] = explode(":", $data)[0].":".explode(":", $data)[1];
                $event["lehrer"] = trim(explode("-->", $data)[1], " \t\n\r\0\x0B");
                $event["ort"] = trim(explode("-", $data)[1], " \t\n\r\0\x0B") ;
                $event["id"] = "";
                array_push($aufsichten,$event);
            }
        }
    }

    $output = array();
	$data = array();
    $data["mode"] = "insert";
    $data["type"] = "vertretungen";
	
	$entrys = array();
	
	$trans = json_encode($vertretung);
	$vertretung = json_decode($trans,true);
	
	foreach($vertretung as $entry){
		$dataset["date"] = $entry["date"];
		$dataset["lesson"] = $entry["stunde"][0];
		$dataset["subject"] = $entry["fach"][0];
        $dataset["teacher"] = $entry["lehrer"][0];
        
		if(is_array($entry["klasse"])){
			$dataset["class"] = $entry["klasse"][0];
		}else{
			$dataset["class"] = $entry["klasse"];
        }
        
		$dataset["newTeacher"] = $entry["vlehrer"][0];
		$dataset["newSubject"] = $entry["vfach"][0];
		$dataset["newRoom"] = $entry["vraum"][0];
		$dataset["info"] = $entry["info"][0];
		$dataset["id"] = $entry["id"];
		
		
		$entrys[] = $dataset;
	}
	
	$data["data"] = $entrys;
	$data["days"] = $days;
	
    $output[] = $data;
    $i = 0;
    foreach($days as $day){
        if($i == 0){
            $dates = $day;
            $i = 1;
        }else{
            $dates .= ",".$day;
        }
    }


    $refreshed = json_encode($refreshed);
    $refreshed = json_decode($refreshed,true);
    $output[] = array("mode" =>"update","type" => "config","data" => array("activeDates" => $dates,"lastRefreshed" => $refreshed[0]));
    return $output;
}



$xmlData = loadXml();




$i = 0;
foreach($xmlData[0]["days"] as $day){
    if($i == 0){
        $dates = $day;
        $i = 1;
    }else{
        $dates .= ",".$day;
    }
}

$data = curlToApi("","secret=$secret&dates=$dates");

$activeData = json_decode($data,true);

if(isset($activeData["data"]["vertretungen"])){
    $activeIds = array();
    $deleteData = array();

    foreach($activeData["data"]["vertretungen"] as $dates){
        foreach($dates as $event){
            $activeIds[] = $event["id"];
        }
    }

   

    $deleteData["mode"] = "delete";
    $deleteData["data"] = $activeIds;

    $response = curlToApi(json_encode(array($deleteData)),"secret=$secret&mode=edit");
}

$response = curlToApi(json_encode($xmlData),"secret=$secret&mode=edit");
echo "<h1>Completed</h1>"
?>