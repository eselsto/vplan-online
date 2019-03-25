<?php
$json = file_get_contents($config->url_api.'aushang.php?secret='.$config->api_secret);
$aushangdata = json_decode($json);
if(isset($aushangdata->access)){
	if(!$aushangdata->access){
		die();
	}
}
echo "<table height=20 style='table-layout:fixed;height:20px'></table>";
		echo
		"<table class='aushangtable' border=0 cellpadding=0 cellspacing=0 style='border-collapse:collapse;table-layout:fixed;width:100%'>
			<col style='width:50%'>
			<col style='width:50%'>";
foreach($aushangdata as $row){
	if(!isset($row->spalten)){
		$zweispalten = false;
	}elseif($row->spalten == "true"){
		$zweispalten = true;
	}else{
		$zweispalten = false;
	}

	if($zweispalten){

				echo "<tr height=24 style='height:18.0pt'>
					<td colspan=1 height=24 class=\"aushang\" style='background-color:".$row->Color.";height:18.0pt;'>".$row->Content."</td>";

				echo "<td colspan=1 height=24 class=\"aushang\" style='background-color:".$row->Color.";height:18.0pt;'>".$row->Content2."</td>
				</tr>"; 

	}else{
		echo "<tr height=24 style='height:18.0pt'>
						<td colspan=2 height=24 class=\"aushang\" style=background-color:".$row->Color."; style='height:18.0pt;'>".$row->Content."</td>
						</tr>";
		
	}
}
echo"</table>";
?>