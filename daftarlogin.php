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
        if (file_exists($filename) == 0) {
            echo "catat sebagai proses baru".PHP_EOL;
            //catat OTP
            $decode = json_decode($data, true);
            $decode["META"]["OTP"] = $OTP;
            $data = json_encode($decode);
            //tulis ke file
            if (!file_put_contents("./data/pid/".$filename, $data)) {
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
    //parse json
    $decode = json_decode($data, true);
    
    $apiKey = "AIzaSyDnkJE8ZKNfwbqGHDlg5k1PVh1hOMv8Ru0";
    $devices = $decode["META"]["DeviceID"];
    $message = $decode["META"]["Message"];
    
    $gcpm = new GCMPushMessage($apiKey);
    $gcpm->setDevices($devices);
    $response = $gcpm->send($message, array('title' => 'test title', 'message' => $message));
    
    //return value?
}

//reveice post message
//var_dump($_POST);
$data = json_decode(file_get_contents('php://input'), true);

$AppID =  $data["META"]["AppID"];
//cek META field, apakah data lengkap

//process message
if (cariapp($AppID) >= 0) {
    $encode = json_encode($data['KTP']);
    $data["META"]["signature"] = hitunghashktp($encode);
    $json_data = json_encode($data);
    if (daftarpid($AppID,$json_data) == 1) {
        //mengirim pesan ke device
        //kirimGCM($json_data);
        echo "Permintaan berhasil";
    }
}
else {
    echo "App ID tidak terdaftar";
}

?>