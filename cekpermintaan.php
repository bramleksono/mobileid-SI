<?php
require_once('filemanipulation.php');

function caridevice($deviceid) {
    return findline($deviceid,'./data/device.txt');
}

function caripermintaan($ktpid) {
    $linepid = findline($ktpid,'./data/pid.txt');
    if ($linepid >= 0) {
        //echo 'permintaan ditemukan';
        $req = getline($linepid,'./data/pid.txt');
        $req1 =explode(':>',$req);
        //return berita dan OTP
        return array($req1[2],$req1[3],$req1[4]);
    }
}

//reveice post message
//var_dump($_POST);
//$device=$_POST['device'];
//$noktp=$_POST['ktp'];

$device='1234678882';
$noktp='012312343';

//process message
if (caridevice($device) >= 0) {
    $req = caripermintaan($noktp);
    if ($req) {
        echo "Berita: ".$req[0].PHP_EOL;
        echo "PID: ".$req[1].PHP_EOL;
        echo "OTP: ".$req[2].PHP_EOL;
    }
    else {
        echo "Tidak ada permintaan";
    }
    
}
else {
    echo "Device ID tidak terdaftar";
}

?>