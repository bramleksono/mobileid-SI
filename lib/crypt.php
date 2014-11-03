<?php
function hitunghashdata($string) {
    $algo = "sha256";
    return hash($algo,$string);
}

function hitunghashfile($file) {
    $algo = "sha256";
    return hash_file($algo,$file);
}

function hitunghmacdata($string,$key) {
    $algo = "sha256";
    return hash_hmac($algo,$string,$key);
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

function proseshash($data,$postedhash) {
    $signature =  $data["META"]["signature"];
    $OTP =  $data["META"]["OTP"];
    // $key = pack("H*",$OTP);
    //hitung hmac dengan OTP sebagai key
    $hmacresult = hitunghmacdata($signature,$OTP);
    // $hmacresult = hitunghmacdata($signature,$key);

    // echo "sig=".$signature." OTP=".$OTP." hmac=".$hmacresult.PHP_EOL;
    if (hash_compare($hmacresult, $postedhash)) { 
        return 1; 
    }
    return 0;
}

function proseswebhash($data,$postedhash) {
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

?>