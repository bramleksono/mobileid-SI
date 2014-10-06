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
    $signature =  $data["hash"];
    $OTP =  $data["otp"];
    // $key = pack("H*",$OTP);
    //hitung hmac dengan OTP sebagai key
    $hmacresult = hitunghmacdata($signature,$OTP);
    // echo $signature."\n";
    // echo $OTP."\n";
    // echo $hmacresult."\n";
    // $hmacresult = hitunghmacdata($signature,$key);

    // echo "sig=".$signature." OTP=".$OTP." hmac=".$hmacresult.PHP_EOL;
    if (hash_compare($hmacresult, $postedhash)) { 
        return 1; 
    }
    return 0;
}

function kirimcallback($url,$data,$postdata) {
    unset($postdata["password"]);
    $postdata["Success"] = true;
    $postdata["websignature"] = $data["websignature"];
    return sendpost($data["CAwebsigncallback"],$postdata);
}

function sendpost($url,$data) {
    $options = array(
        'http' => array(
            'method'  => 'POST',
            'content' => http_build_query($data),
            'header'=>  "Content-type: application/x-www-form-urlencoded"
          )
    );
    // echo json_encode($data);
    $context     = stream_context_create($options);
    $result      = file_get_contents($url, false, $context);
    $response    = json_decode($result, true);
    return $response;
    // return $result;
}

function response($Status,$IDNumber,$pid,$Message) {
    $response = array(
      'Success' => $Status, 
      'NIK' => $IDNumber,
      'PID' => $pid,
      'Message' => $Message,
    );
    return json_encode($response);
}

$postdata = json_decode(file_get_contents('php://input'), true);
$PID =  $postdata["PID"];
$posthash =  $postdata["hmac"];

$filename = $postdata["userid"]."websign.".$PID;
// echo $filename."\n";
if (!file_exists("./data/pid/".$filename) == 0) {
    $data = json_decode(file_get_contents("./data/pid/".$filename), true);
    // echo json_encode($data);
    if (proseshash($data,$posthash) == 1) {
        $data["hmac"] = $posthash;
        $passphrase = $postdata["password"]."".$postdata["password"];
        // echo $passphrase."\n";

        $priv_key = file_get_contents('./key/3271231008950005.priv.pem');
        // echo $priv_key."\n";
        $key = openssl_pkey_get_private($priv_key, $passphrase);
        // //create signature
        openssl_sign($data["hash"], $websignature, $key, OPENSSL_ALGO_SHA256);

        $data["websignature"] = base64_encode($websignature);
        $encode = json_encode($data);
        // echo $encode."\n";
        // echo $data["hash"]."\n";

        if (!file_put_contents("./data/pid/".$filename, $encode)) {
            // echo "kesalahan menyimpan process id";
            echo response(false, $data["userid"],$postdata["PID"],"kesalahan menyimpan file");
        }
        // else echo response(true, $data["userid"],$postdata["PID"],"OK");
        // echo "Hash benar !";

        //kirim callback
        $CallbackURL =  $data["CAwebsigncallback"];
        $response = kirimcallback($CallbackURL,$data,$postdata);
        if($response["Success"] == true){
            echo response(true, $data["userid"],$postdata["PID"],"SI - OK");
        } else {
            echo response(false, $data["userid"],$postdata["PID"],"CA response false");
        }
        // echo kirimcallback($CallbackURL,$data,$postdata);
        //jika callback berhasil, hapus file pid
        // unlink("./data/pid/".$filename);
    }
    // else echo "Hash salah";
    else echo response(false, $data["KTP"]["NIK"],$PID,"Hash salah");
}
// else echo "PID tidak ditemukan";
else echo response(false, 0,$postdata["META"]["PID"],"PID tidak ditemukan");;

?>