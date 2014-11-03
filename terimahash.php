<?php
require_once('./lib/crypt.php');

function kirimcallback($url,$data,$postdata) {
    //$url="http://postcatcher.in/catchers/5417ac22dc35d6020000077f";
    $data=array('From' => 'Signing Interface', 
                'Success' => true, 
                'NIK' => $data["KTP"]["NIK"], 
                'PID' => $postdata["META"]["PID"], 
                'signature' => $data["META"]["signature"], 
                'OTP' => $data["META"]["OTP"], 
                'hmac' => $postdata["MESSAGE"]["hmac"]);
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
    // echo json_encode($data);
    $context     = stream_context_create($options);
    $result      = file_get_contents($url, false, $context);
    $response    = json_decode($result, true);
    return $response;
}

function response($Status,$IDNumber,$pid,$Message) {
    $response['STATUS'] = array(
      'Success' => $Status, 
      'NIK' => $IDNumber,
      'PID' => $pid,
      'Message' => $Message,
    );
    return json_encode($response);
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
        $data["META"]["HMAC"] = $posthash;
        if (!file_put_contents("./data/pid/".$filename, json_encode($data))) {
            // echo "kesalahan menyimpan process id";
            echo response(true, $data["KTP"]["NIK"],$postdata["META"]["PID"],"kesalahan menyimpan file");
        }
        else echo response(true, $data["KTP"]["NIK"],$postdata["META"]["PID"],"OK");
        // echo "Hash benar !";

        //kirim callback
        $CallbackURL =  $data["META"]["CallbackURL"];
        $IDNumber = $data["KTP"]["NIK"];
        kirimcallback($CallbackURL,$data,$postdata);
        //jika callback berhasil, hapus file pid
        // unlink("./data/pid/".$filename);
    }
    // else echo "Hash salah";
    else echo response(false, $data["KTP"]["NIK"],$PID,"Hash salah");
}
// else echo "PID tidak ditemukan";
else echo response(false, 0,$postdata["META"]["PID"],"PID tidak ditemukan");;

?>