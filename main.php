<?php
//hello world

require_once('filemanipulation.php');

function cariapp($appid) {
    return findline($appid,'./data/app.txt');
}
function cariktp($ktpid) {
    return findline($ktpid,'./data/ktp.txt');
}
function daftarpid($appid,$ktpid,$message,$callbackurl) {
    $pidtrylimit = 100;
    $i=0;
    $j=0;
    while ($i<1 && $j<$pidtrylimit) {
        $pid=rand();
        $OTP=rand(0,9999);
        //$pid='0000001';
        if (findline($pid,'./data/pid.txt') < 0) {
            //echo "catat sebagai proses baru";
            $txt= $appid . ":>" . $ktpid . ":>" . $message . ":>" . $pid . ":>" . $OTP . ":>" . $callbackurl;
            writeline($txt,$txt,'./data/pid.txt');
            $i++;
            $result=1;
        }
        if ($j>$pidtrylimit-2) {
            //echo "tidak mendapat PID unik";
            $result=0;
        }
        $j++;
    }
    return $result;
}

//reveice post message
//var_dump($_POST);
$data = json_decode(file_get_contents('php://input'), true);

$app =  $data["daftar"]["app"];
$noktp =  $data["daftar"]["noktp"];
echo $noktp;
$berita =  $data["daftar"]["berita"];
$callback = $data["daftar"]["callback"];

//process message
if (cariapp($app) >= 0) {
    if (cariktp($noktp) >= 0) {
        //echo "ktp terdaftar";
        if (daftarpid($app,$noktp,$berita,$callback)) {
            echo "permintaan didaftarkan";
        }
        else
            echo "permintaan gagal. PID tidak unik";
    }
    else {
        echo "No KTP tidak ditemukan";
    }
}
else {
    echo "App ID tidak terdaftar";
}

?>