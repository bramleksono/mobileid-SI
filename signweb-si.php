<?php
require_once('./lib/filemanipulation.php');
require_once('./lib/crypt.php');
require_once('./lib/GCMPushMessage.php');
include('./addr-path.php');

function daftarpid($data) {
    $pidtrylimit = 100;
    $i=0;
    $j=0;
    while ($i<1 && $j<$pidtrylimit) {
        $pid=rand();
        $OTP=rand(0,9999);
        $filename = $data["userid"]."websign.".$pid;
        if (file_exists("./data/pid/".$filename) == 0) {
            //echo "Catat sebagai proses baru. PID = $pid".PHP_EOL;
            //catat OTP
            $data["otp"] = $OTP;
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
    return array ('result'=>$result,'pid'=>$pid,'otp'=>$OTP);
}

function kirimGCM ($data, $otp, $pid, $callbackaddr) {
    //mengirim pesan ke device (Pesan + AppID + PID + OTP)
    $data["otp"] = $otp;
    $data["pid"] = $pid;
    unset($data["CAwebsigncallback"]);
    $gcpm = new GCMPushMessage($data["regid"]);
    $gcpm->fillDataWebSign($data, $callbackaddr);
    $response = $gcpm->sendGoogleCloudMessage();
    
    //echo "Response:".$response."\n";
}

function response($IDNumber,$PID,$id) {
    $response['STATUS'] = array(
      'Success' => TRUE, 
      'NIK' => $IDNumber,
      'PID' => $PID,
      'TableID' => $id,
    );
    return json_encode($response);
}

$retarray = daftarpid($_POST);
$otp = $retarray["otp"];
$pid = $retarray["pid"];

if($retarray["result"]!=0){
  kirimGCM($_POST,$otp, $pid, $SIwebsigncallbackaddr);
  echo response($_POST["userid"],$pid,$_POST["id"]);
}
?>