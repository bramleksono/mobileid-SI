<?php
function hitunghashdata($string) {
    $algo = "sha256";
    return hash($algo,$string);
}

function hitunghmacdata($string,$key) {
    $algo = "sha256";
    return hash_hmac($algo,$string,$key);
}
?>