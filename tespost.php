<?php
$data = json_decode(file_get_contents('php://input'), true);
print_r($data);
foreach $data["daftar"] {
    echo $data["daftar"]["app"];
}

?>