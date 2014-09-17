<?php
function hitunghashktp($string) {
    $algo = "sha256";
    return hash($algo,$string);
}

?>