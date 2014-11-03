<?php

function openDocSignPIDfile($filename) {
    return $data = json_decode(file_get_contents("./data/pid/".$filename), true);
}

function getPIDproperties($user_id,$pid) {
    $filename = $user_id."docsign.".$pid;

    $data = openDocSignPIDfile($filename);
    $passphrase = $data["docpassphrase"];
    $name_id = $data["nameid"];

    return array ($passphrase,$name_id);
}

?>