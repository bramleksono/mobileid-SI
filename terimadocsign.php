<?php
require_once('./lib/crypt.php');

function kirimcallback($url,$data,$postdata) {
    unset($postdata["password"]);
    $postdata["Success"] = true;
    return sendpost($data["CAdocsigncallback"],$postdata);
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

$filename = $postdata["userid"]."docsign.".$PID;
// echo $filename."\n";
if (!file_exists("./data/pid/".$filename) == 0) {
    $data = json_decode(file_get_contents("./data/pid/".$filename), true);
    // echo json_encode($data);
    if (proseswebhash($data,$posthash) == 1) {
        $data["hmac"] = $posthash;
        $passphrase = $postdata["password"]."".$postdata["password"];
        // echo $passphrase."\n";

        $data["docpassphrase"] = $passphrase;
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
        $CallbackURL =  $data["CAdocsigncallback"];
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