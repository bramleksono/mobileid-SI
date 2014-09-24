<?php

require_once('./lib/crypt.php');

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

function proseshash($data,$postedhash) {
    $signature =  $data["META"]["signature"];
    $OTP =  $data["META"]["OTP"];
    //hitung hmac dengan OTP sebagai key
    $hmacresult = hitunghmacdata($signature,$OTP);

    echo "sig=".$signature." OTP=".$OTP." hmac=".$hmacresult.PHP_EOL;
    if (hash_compare($hmacresult, $postedhash)) { 
        return 1; 
    }
    return 0;
}

function kirimcallback($url,$IDNumber,$pid) {
    //$url="http://postcatcher.in/catchers/5417ac22dc35d6020000077f";
    $data=array('From' => 'Signing Interface', 'Success' => TRUE, 'NIK' => $IDNumber, 'PID' => $pid);
    sendpost($url,$data);
}

function sendpost($url,$data) {
    $options = array(
        'http' => array(
            'method'  => 'POST',
            'content' => json_encode( $data ),
            'header'=>  "Content-Type: application/json\r\n" .
                        "Accept: application/json\r\n"
          )
    );

    $context     = stream_context_create($options);
    $result      = file_get_contents($url, false, $context);
    $response    = json_decode($result, true);
    return $response;
}

//reveice post message
//var_dump($_POST);
//$pid=$_POST['pid'];
//$hash=$_POST['hash'];

$postdata = json_decode(file_get_contents('php://input'), true);
$AppID =  $postdata["META"]["AppID"];
$PID =  $postdata["META"]["PID"];
$posthash =  $postdata["MESSAGE"]["hmac"];

$filename = $AppID.".".$PID;
if (!file_exists("./data/pid/".$filename) == 0) {
    $data = json_decode(file_get_contents("./data/pid/".$filename), true);
    if (proseshash($data,$posthash) == 1) {
        echo "Hash benar !";
        //kirim callback
        $CallbackURL =  $data["META"]["CallbackURL"];
        $IDNumber = $data["KTP"]["NIK"];
        kirimcallback($CallbackURL,$IDNumber,$PID);
        //jika callback berhasil, hapus file pid
        //unlink("./data/pid/".$filename);
    }
    else echo "Hash salah";
}
else echo "PID tidak ditemukan";

?>