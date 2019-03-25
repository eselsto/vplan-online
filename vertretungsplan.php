<?php
function monat($monat){
	$monate = array();
	$monate[] = "Januar";
	$monate[] = "Januar";
	$monate[] = "Februar";
	$monate[] = "März";
	$monate[] = "April";
	$monate[] = "Mai";
	$monate[] = "Juni";
	$monate[] = "Juli";
	$monate[] = "August";
	$monate[] = "September";
	$monate[] = "Oktober";
	$monate[] = "November";
	$monate[] = "Dezember";

	if(substr($monat,0,1) == 0){
		return $monate[substr($monat,1,1)];
	}else{
		return $monate[$monat];
	}
}
function displaydate($day){
	$wochentage = array();
	$wochentage[] = "Sonntag";
	$wochentage[] = "Montag";
	$wochentage[] = "Dienstag";
	$wochentage[] = "Mittwoch";
	$wochentage[] = "Donnerstag";
	$wochentage[] = "Freitag";
	$wochentage[] = "Samstag";

	$timestamp = strtotime($day);
	$wochentag = date("w", $timestamp);
	$monat = date("m",$timestamp);
	$date = $wochentage[$wochentag].", ".date("d",$timestamp).". ".monat($monat)." ".date("Y",$timestamp);
	return $date;
}


$days = "";
$i = 0;
$url = $config->url_api.'vertretungsplan.php?active&secret='.$config->api_secret;
$json = file_get_contents($url);
$data = json_decode($json,true);
if(isset($data["access"])){
	if(!$data["access"]){
		die();
	}
}
$refreshed = "";
echo "<h1>$langhtmlonlivertheadline</h1>
	<p>$langhtmlonlivertunderlineone
	<br>
	<span>$langhtmlonlivertunderlinetwo ".$data["info"]["refreshed"]."</span>
	</p>";
foreach ($data["info"]["days"] as $day) {
	if(isset($data["data"]["vertretungen"][$day])){
		$vertretungen = $data["data"]["vertretungen"][$day];
		
		
		$date = displaydate($day);
		echo "<p>
			<span class=\"vpfuer\">$langhtmlonlivertdayhead <span class=\"vpfuerdatum\">".$date."</span></span>
			<br>
			</p>";
		echo "<table border=\"2\" class=\"tablekopf\">
			<tr>
			<th class=\"thlplanklasse\">$langhtmlonliverttablerowone</th>
			<th class=\"thlplanstunde\">$langhtmlonliverttablerowtwo</th>
			<th class=\"thlplanfach\">$langhtmlonliverttablerowthree</th>
			<th class=\"thlplanvfach\">$langhtmlonliverttablerowfour</th>
			<th class=\"thlplanvlehrer\">$langhtmlonliverttablerowfive</th>
			<th class=\"thlplanvraum\">$langhtmlonliverttablerowsix</th>
			<th class=\"thlplaninfo\">$langhtmlonliverttablerowseven</th>
			</tr>";
		//Preprocessing
		for($i = 0;$i < count($vertretungen);$i++){
			$ident = false;
			if($vertretungen[$i]['Kurs'] == $vertretungen[$i+1]['Kurs']){
				if($vertretungen[$i]['Fach'] == $vertretungen[$i+1]['Fach']){
					if($vertretungen[$i]['Fach-new'] == $vertretungen[$i+1]['Fach-new']){
						if($vertretungen[$i]['Lehrer-neu'] == $vertretungen[$i+1]['Lehrer-neu']){
							if($vertretungen[$i]['Raum-new'] == $vertretungen[$i+1]['Raum-new']){
								if($vertretungen[$i]['info'] == $vertretungen[$i+1]['info']){
									$ident = true;
								}
							}
						}
					}
				}
			}
			if($ident){
				echo "<tr>
					<td class=\"tdaktionen\">".$vertretungen[$i]['Kurs']."</td>
					<td class=\"tdaktionen\">".$vertretungen[$i]['Stunde']." / ".$vertretungen[$i+1]['Stunde']."</td>
					<td class=\"tdaktionen\">".$vertretungen[$i]['Fach']."</td>
					<td class=\"tdaktionen\">".$vertretungen[$i]['Fach-new']."</td>
					<td class=\"tdaktionen\">".$vertretungen[$i]['Lehrer-neu']."</td>
					<td class=\"tdaktionen\">".$vertretungen[$i]['Raum-new']."</td>
					<td class=\"tdinfo\">".$vertretungen[$i]['info']."</td>
					</tr>";
				$i++;
			}else{
				echo "<tr>
					<td class=\"tdaktionen\">".$vertretungen[$i]['Kurs']."</td>
					<td class=\"tdaktionen\">".$vertretungen[$i]['Stunde']."</td>
					<td class=\"tdaktionen\">".$vertretungen[$i]['Fach']."</td>
					<td class=\"tdaktionen\">".$vertretungen[$i]['Fach-new']."</td>
					<td class=\"tdaktionen\">".$vertretungen[$i]['Lehrer-neu']."</td>
					<td class=\"tdaktionen\">".$vertretungen[$i]['Raum-new']."</td>
					<td class=\"tdinfo\">".$vertretungen[$i]['info']."</td>
					</tr>";	
			}
		}
		echo "</table>";
	}
	if(isset($data["data"]["aufsichten"][$day])){
		$aufsichten = $data["data"]["aufsichten"][$day];
		echo "<span class=\"aufsichtenkopf\">$langhtmlonlivertdayunterline</span><table>";
		foreach ($aufsichten as $row) {	
			echo "<tr><td class=\"aufsicht\">".$row["Zeit"].": ".$row["Ort"]." --> ".$row["Lehrer"]."</td></tr>";
		}
		echo "</table>";
	}
}
?>