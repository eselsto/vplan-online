<?php
require_once("../config.php");

function curlToApi($config,$mode,$json){

    $ch = curl_init($config->url_api.'/aushang.php?aushang='.$mode.'&secret='.$config->api_secret);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json))
    );
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        $result = curl_error($ch);
        curl_close($ch);
    }
    return $result;
}

function activeSelect($color,$id){
    $colors = array();
    $colors["red"] = "Rot";
    $colors["yellow"] = "Gelb";
    $colors["olive"] = "Olive";
    $colors["lime"] = "hell Grün";
    $colors["aqua"] = "hell Blau";
    $colors["orange"] = "Orange";
    $colors["fuchsia"] = "Hell-Magenta";
    $output = '<div class="input-group mb-3">
        <div class="input-group-prepend">
            <label class="input-group-text" for="colorselect">Farbe</label>
        </div>
        <select class="custom-select" id="color.'.$id.'" disabled>';
    for($i=0;$i < sizeof($colors); $i++){
        if(key($colors) == $color){
            $output .= "<option value=".key($colors)." selected>".$colors[key($colors)]."</option>";
        }else{
            $output .= "<option value=".key($colors).">".$colors[key($colors)]."</option>";
        }

        next($colors);
    }

        $output .= '</select></div>';
    return $output;
}

$config = new config;

$json = file_get_contents("php://input");
$data = json_decode($json,true);
$json = json_encode($data);
$action = htmlspecialchars($_GET["action"]);

if($action == "create"){
    echo curlToApi($config,"create",$json);
} elseif ($action == "createPreset") {
    echo curlToApi($config, "createPreset", $json);
}elseif($action == "delete"){
    echo curlToApi($config,"delete",$json);
}elseif($action == "update"){
    echo curlToApi($config,"update",$json);
}elseif($action == "move"){
    echo curlToApi($config,"updateOrder",$json);
} elseif ($action == "movePreset") {
    echo curlToApi($config, "updateOrderPreset", $json);
}elseif($action == "load"){
    $json = file_get_contents($config->url_api.'/aushang.php?aushang=1&secret='.$config->api_secret);
    $aushangdata = json_decode($json);

    foreach($aushangdata as $row){
        if(!isset($row->spalten)){
            $zweispalten = false;
        }elseif($row->spalten == "true"){
            $zweispalten = true;
        }else{
            $zweispalten = false;
        }
        $colorselect = activeSelect($row->Color,$row->ID);
        if($zweispalten){

            echo '<tr id="row.'.$row->ID.'">
                <td style="max-width:10px">'.$row->Order.'</td>
                <td>
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-danger" onClick="removeElementFromApi('.$row->ID.')" ><i class="material-icons">delete_forever</i></button>
                        <button type="button" class="btn btn-warning" id="edit.'.$row->ID.'" onClick="editElementFromApi('.$row->ID.')" ><i class="material-icons">edit</i></button>
                        <button type="button" class="btn btn-success" id="save.'.$row->ID.'" onClick="updateToApi('.$row->ID.')" style="display: none;"><i class="material-icons">save</i></button>
                        <button type="button" class="btn btn-primary" onClick="moveElement(' . $row->ID . ',\'up\',\'1\')"><i class="material-icons">arrow_upward</i></button>
                        <button type="button" class="btn btn-primary"onClick="moveElement(' . $row->ID . ',\'down\',\'1\')" ><i class="material-icons">arrow_downward</i></button>
                    </div>
                </td>
                <td style="background-color:' . $row->Color . '"><textarea id="textarea.' . $row->ID . '.content" class="form-control" onkeyup="textAreaAdjust(this)" disabled>' . $row->Content . '</textarea></td>
                <td style="background-color:' . $row->Color . '"><textarea id="textarea.' . $row->ID . '.content2" class="form-control" onkeyup="textAreaAdjust(this)" disabled>' . $row->Content2 . '</textarea></td>
                <td style="background-color:'.$row->Color.'">'.$colorselect.'</td>
            </tr>';
        }else{
            echo '<tr id="row.'.$row->ID.'">
                <td style="max-width:10px">'.$row->Order/10 .'</td>
                <td style="max-width:100px">
                    <div class="btn-group" role="group" aria-label="Actions">
                        <button type="button" class="btn btn-danger" onClick="removeElementFromApi('.$row->ID.')" ><i class="material-icons">delete_forever</i></button>
                        <button type="button" class="btn btn-warning" id="edit.'.$row->ID.'" onClick="editElementFromApi('.$row->ID.')" ><i class="material-icons">edit</i></button>
                        <button type="button" class="btn btn-success" id="save.'.$row->ID.'" onClick="updateToApi('.$row->ID.')" style="display: none;"><i class="material-icons">save</i></button>
                        <button type="button" class="btn btn-primary" onClick="moveElement(' . $row->ID . ',\'up\',\'1\')"><i class="material-icons">arrow_upward</i></button>
                        <button type="button" class="btn btn-primary" onClick="moveElement(' . $row->ID . ',\'down\',\'1\')" ><i class="material-icons">arrow_downward</i></button>
                    </div>
                    <div class="btn-group" role="group" aria-label="Actions">
                        <button type="button" class="btn btn-primary" onClick="addElementToPresets(' . $row->ID . ')" ><i class="material-icons">archive</i></button>
                    </div>
                </td>
                <td style="background-color:' . $row->Color . '"><textarea id="textarea.' . $row->ID . '.content" class="form-control" onkeyup="textAreaAdjust(this)" disabled>' . $row->Content . '</textarea></td>
                <td style="background-color:'.$row->Color.'"></td>
                <td style="background-color:'.$row->Color.'">'.$colorselect.'</td>
                <!--<td style="background-color:'.$row->Color.'"></td>-->
            </tr>';
        }


    }
} elseif ($action == "presets") {
    $json = file_get_contents($config->url_api . '/aushang.php?aushang=presets&secret=' . $config->api_secret);
    $aushangdata = json_decode($json);

    foreach ($aushangdata as $row) {
        if (!isset($row->spalten)) {
            $zweispalten = false;
        } elseif ($row->spalten == "true") {
            $zweispalten = true;
        } else {
            $zweispalten = false;
        }
        $colorselect = activeSelect($row->Color, $row->ID);
        if ($zweispalten) {

            echo '<tr id="row.' . $row->ID . '">
                <td style="max-width:10px">' . $row->Order . '</td>
                <td>
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-success" onClick="addElementFromPresets(' . $row->ID . ')" ><i class="material-icons">add_box</i></button>
                        <button type="button" class="btn btn-primary" onClick="moveElement(' . $row->ID . ',\'up\',\'3\')"><i class="material-icons">arrow_upward</i></button>
                        <button type="button" class="btn btn-primary" onClick="moveElement(' . $row->ID . ',\'down\',\'3\')" ><i class="material-icons">arrow_downward</i></button>
                        <button type="button" class="btn btn-danger" onClick="removeElementFromApi(' . $row->ID . ')" ><i class="material-icons">delete_forever</i></button>
                        <button type="button" class="btn btn-warning" id="edit.' . $row->ID . '" onClick="editElementFromApi(' . $row->ID . ')" ><i class="material-icons">edit</i></button>
                        <button type="button" class="btn btn-success" id="save.' . $row->ID . '" onClick="updateToApi(' . $row->ID . ')" style="display: none;"><i class="material-icons">save</i></button>
                    </div>
                    <div class="btn-group" role="group" aria-label="Actions">
                        <button type="button" class="btn btn-success" onClick="addElementFromPresets(' . $row->ID . ')" ><i class="material-icons">unarchive</i></button>
                    </div>
                </td>
                <td style="background-color:' . $row->Color . '"><textarea id="textarea.' . $row->ID . '.content" class="form-control" onkeyup="textAreaAdjust(this)" disabled>' . $row->Content . '</textarea></td>
                <td style="background-color:' . $row->Color . '"><textarea id="textarea.' . $row->ID . '.content2" class="form-control" onkeyup="textAreaAdjust(this)" disabled>' . $row->Content2 . '</textarea></td>
                <td style="background-color:' . $row->Color . '">' . $colorselect . '</td>
            </tr>';
        } else {
            echo '<tr id="row.' . $row->ID . '">
                <td style="max-width:10px">' . $row->Order / 10 . '</td>
                <td style="max-width:100px">
                    <div class="btn-group" role="group" aria-label="Actions">
                        <button type="button" class="btn btn-danger" onClick="removeElementFromApi(' . $row->ID . ')" ><i class="material-icons">delete_forever</i></button>
                        <button type="button" class="btn btn-warning" id="edit.' . $row->ID . '" onClick="editElementFromApi(' . $row->ID . ')" ><i class="material-icons">edit</i></button>
                        <button type="button" class="btn btn-success" id="save.' . $row->ID . '" onClick="updateToApi(' . $row->ID . ')" style="display: none;"><i class="material-icons">save</i></button>
                        <button type="button" class="btn btn-primary" onClick="moveElement(' . $row->ID . ',\'up\',\'3\')"><i class="material-icons">arrow_upward</i></button>
                        <button type="button" class="btn btn-primary" onClick="moveElement(' . $row->ID . ',\'down\',\'3\')" ><i class="material-icons">arrow_downward</i></button>
                    </div>
                    <div class="btn-group" role="group" aria-label="Actions">
                        <button type="button" class="btn btn-success" onClick="addElementFromPresets(' . $row->ID . ')" ><i class="material-icons">unarchive</i></button>
                    </div>
                </td>
                <td style="background-color:' . $row->Color . '"><textarea id="textarea.' . $row->ID . '.content" class="form-control" onkeyup="textAreaAdjust(this)" disabled>' . $row->Content . '</textarea></td>
                <td style="background-color:' . $row->Color . '"></td>
                <td style="background-color:' . $row->Color . '">' . $colorselect . '</td>
                <!--<td style="background-color:' . $row->Color . '"></td>-->
            </tr>';
        }
    }
}