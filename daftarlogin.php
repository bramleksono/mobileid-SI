<?php
//hello world
require_once('./lib/filemanipulation.php');
require_once('./lib/crypt.php');
require_once('./lib/GCMPushMessage.php');

function cariapp($appid) {
    return findline($appid,'./data/app.txt');
}

function daftarpid($appid,$data) {
    $pidtrylimit = 100;
    $i=0;
    $j=0;
    while ($i<1 && $j<$pidtrylimit) {
        $pid=rand();
        $OTP=rand(0,9999);
        $filename = $appid.".".$pid;
        if (file_exists("./data/pid/".$filename) == 0) {
            echo "Catat sebagai proses baru. PID = $pid".PHP_EOL;
            //catat OTP
            $data["META"]["OTP"] = $OTP;
            $encode = json_encode($data);
            //tulis ke file
            if (!file_put_contents("./data/pid/".$filename, $encode)) {
                echo "kesalahan menyimpan process id";
            }
            $i++;
            $result=1;
        }
        if ($j>$pidtrylimit-2) {
            echo "tidak mendapat PID unik";
            $result=0;
        }
        $j++;
    }
    return $result;
}

function kirimGCM ($data) {
    $devices = $data["META"]["DeviceID"];
    $message = $data["META"]["Message"];
    
    $gcpm = new GCMPushMessage($message,$devices);
    $response = $gcpm->sendGoogleCloudMessage();
    
    echo "Response:".$response."\n";
}

//reveice post message
//var_dump($_POST);
$data = json_decode(file_get_contents('php://input'), true);

$AppID =  $data["META"]["AppID"];
//cek META field, apakah data lengkap

//process message
if (cariapp($AppID) >= 0) {
    $encode = json_encode($data['KTP']);
    $data["META"]["signature"] = hitunghashdata($encode);
    if (daftarpid($AppID,$data) == 1) {
        //mengirim pesan ke device
        kirimGCM($data);
        echo "\r\nPermintaan berhasil";
    }
}
else {
    echo "App ID tidak terdaftar";
}

?>