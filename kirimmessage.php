<?php
require_once('./lib/GCMPushMessage.php');
include('./addr-path.php');

function kirimGCM ($data) {
    //mengirim pesan ke device (Pesan + AppID + PID + OTP)
    $devices = $data["regid"];
    $message = $data["content"];

    $gcpm = new GCMPushMessage($devices);
    $gcpm->notification($message);
    $response = $gcpm->sendGoogleCloudMessage();
    return json_decode($response,true);
}

$kirim = kirimGCM($_POST);
if ($kirim["success"]) {
    echo json_encode($kirim);
} else "Gagal kirim pesan";

?>