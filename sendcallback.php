<?php

function kirimcallback($linepid) {
    $url="http://postcatcher.in/catchers/5417ac22dc35d6020000077f";
    $data=array('request' => 'ok');
    $json = json_encode($data);
    sendpost($url,$json);
}

function sendpost($url,$data) {
    $ch = curl_init($url);                                                                      
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json',                                                                                
        'Content-Length: ' . strlen($data))                                                                       
    );                                                                                                                   

    $result = curl_exec($ch);
    if($result === FALSE){
        die(curl_error($ch));
    }
}

kirimcallback($linepid);

?>