<?php
//hello world
require_once('./lib/filemanipulation.php');
require_once('./lib/crypt.php');
require_once('./lib/GCMPushMessage.php');

//konfigurasi
$CAcallbackaddr = "http://red-trigger-44-141737.apse1.nitrousbox.com/SI/terimahash.php";

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
            //echo "Catat sebagai proses baru. PID = $pid".PHP_EOL;
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
    return array ($result,$pid,$OTP);
}

function kirimGCM ($data, $AppID, $PID, $OTP,$CAcallbackaddr) {
    //mengirim pesan ke device (Pesan + AppID + PID + OTP)
    $devices = $data["META"]["DeviceID"];
    $message = $data["META"]["Message"];
    
    $gcpm = new GCMPushMessage($message,$AppID, $PID, $OTP,$devices,$CAcallbackaddr);
    $response = $gcpm->sendGoogleCloudMessage();
    
    //echo "Response:".$response."\n";
}

function response($IDNumber,$pid) {
    $response['STATUS'] = array(
      'Success' => TRUE, 
      'NIK' => $IDNumber,
      'PID' => $pid,
    );
    return json_encode($response);
}

//reveice post message
//var_dump($_POST);
$data = json_decode(file_get_contents('php://input'), true);

$AppID =  $data["META"]["AppID"];
$IDNumber = $data["KTP"]["NIK"];
//cek META field, apakah data lengkap

//process message
if (cariapp($AppID) >= 0) {
    $encode = json_encode($data['KTP']);
    $data["META"]["signature"] = hitunghashdata($encode);
    $daftar = daftarpid($AppID,$data);
    if ($daftar[0] == 1) {
        //mengirim pesan ke device (Pesan + AppID + PID + OTP + CallbackAddr)
        kirimGCM($data,$AppID,$daftar[1],$daftar[2],$CAcallbackaddr);
        //tampilkan response
        header('Content-type: application/json');
        echo response($IDNumber,$daftar[1]);
    }
}
else {
    echo "App ID tidak terdaftar";
}

?>