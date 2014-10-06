<?php
//data you want to sign
$data = '42cb563de298586b605797b86bd636a6a8e623b3af217138b2822bcb3bed512f';

//create new private and public key
// $new_key_pair = openssl_pkey_new(array(
//     "private_key_bits" => 2048,
//     "private_key_type" => OPENSSL_KEYTYPE_RSA,
// ));
// openssl_pkey_export($new_key_pair, $private_key_pem);

// $details = openssl_pkey_get_details($new_key_pair);
// $public_key_pem = $details['key'];
$pub_key = file_get_contents('./key/3271231008950005.cert.pem');
$priv_key = file_get_contents('./key/3271231008950005.priv.pem');

$key = openssl_pkey_get_private($priv_key, "demo");
$pub = openssl_pkey_get_public($pub_key);

//create signature
// openssl_sign($data, $signature, $private_key_pem, OPENSSL_ALGO_SHA256);
openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256);

//save for later
// file_put_contents('private_key.pem', $private_key_pem);
// file_put_contents('public_key.pem', $public_key_pem);
// file_put_contents('signature.dat', $signature);

//verify signature
// $r = openssl_verify($data, $signature, $public_key_pem, "sha256WithRSAEncryption");
$r = openssl_verify($data, $signature, $pub, "sha256WithRSAEncryption");
var_dump($r);
?>