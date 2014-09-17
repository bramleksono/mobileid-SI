<?php
require_once('filemanipulation.php');

function hitunghashktp($ktp,$otp) {
    $algo = "sha256";
    $data = caridataktp($ktp);
    $data2 = explode(':>',$data);
    //nomor ktp ada di array 0
    $pathfoto = './data/foto/'.$data2[3];

    $hashfoto = hash_hmac_file($algo, $pathfoto, $otp);
    $dataktp = $hashfoto . ':>' . $data;
    $hashktp = hash_hmac($algo, $dataktp, $otp);
    echo $hashktp;
    return $hashktp;
}

function hash_compare($a, $b) { 
    if (!is_string($a) || !is_string($b)) { 
        return false; 
    } 
    $len = strlen($a); 
    if ($len !== strlen($b)) { 
        return false; 
    } 
    $status = 0; 
    for ($i = 0; $i < $len; $i++) { 
        $status |= ord($a[$i]) ^ ord($b[$i]); 
    } 
    return $status === 0; 
} 

function caridataktp($ktp) {
    $linektp = findline($ktp,'./data/ktp.txt');
    if ($linektp >= 0) {
        $ktp = getline($linektp,'./data/ktp.txt');
        return $ktp;
    }
}

function caridevice($deviceid) {
    return findline($deviceid,'./data/device.txt');
}

function proseshash($linepid,$hash) {
    $process = getline($linepid,'./data/pid.txt');
    $process1 = explode(':>',$process);
        
    $ktp = $process1[1];
    $otp = $process1[4];
    $hashsi = hitunghashktp($ktp,$otp);
    if (hash_compare($hashsi, $hash)) { 
        return 1; 
    }
    return 0;
}

function caripid($pid) {
    return findline($pid,'./data/pid.txt'); 
}

function kirimcallback($linepid) {
    $process = getline($linepid,'./data/pid.txt');
    $process1 = explode(':>',$process);
    $url=$process1[5];
    $data=array('request' => 'ok');
    sendpost($url,$data);
}

function sendpost($url,$data) {
    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    var_dump($result);
}

//reveice post message
//var_dump($_POST);
//$pid=$_POST['pid'];
//$hash=$_POST['hash'];

$device='1234678882';
$noktp='012312343';
$pid='1562095054';
$otp='9739';
$hash='ac6f527dbb02ef672b85b7f4cf9224611be88ab113a2105b32cc8b6298725fa5';

//process message
if (caridevice($device) >= 0) {
    $linepid = caripid($pid);
    if ($linepid >= 0) {
        if (proseshash($linepid,$hash)) {
            echo "hash benar!";
            kirimcallback($linepid);
        }
        else {
            echo "hash salah";
        }
    }
    else {
        echo "PID tidak ditemukan";
    }
}
else {
    echo "Device ID tidak terdaftar";
}


?>