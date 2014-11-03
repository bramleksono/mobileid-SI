<?php
require_once('./lib/crypt.php');
require_once('./lib/pdfoperation.php');
require_once('./lib/getpiddata.php');
require_once('./lib/filemanipulation.php');
require_once('./lib/sendfile.php');
require_once('./lib/tcpdf_min/tcpdf.php');
require_once('./lib/PDFMerger/PDFMerger.php');

$uploaddir = realpath('./') . '/documents/';
$uploadfile = $uploaddir . basename($_FILES['file_contents']['name']);

if (move_uploaded_file($_FILES['file_contents']['tmp_name'], $uploadfile)) {
	// echo "File is valid, and was successfully uploaded.\n";
	$filename = basename($_FILES['file_contents']['name']);
	
	$user_id = $_POST['userid'];
	//ambil property dari file pid
	$pid = $_POST['PID'];
	$doctitle = $_POST['title'];
	//getPIDproperties return array value [$passphrase,$name_id]
	list($passphrase,$name_id) = getPIDproperties($user_id,$pid);
	
	//buat halaman signature
	$signaturetext = 'Dokumen '.$doctitle.' telah ditandatangan oleh '.$name_id;
	$signatureimagepath = './documents/signature/'.$user_id.'.signature.jpg';
	$signatureoutputpath = './documents/'.$filename.'.sign';
	
	createsignpage ($signaturetext,$signatureimagepath,$signatureoutputpath);
	
	
	$inputdoc = './documents/'.$filename;
	$finaldoc = './documents/signed.'.$filename;

	createsignedpdf($inputdoc,$signatureoutputpath,$finaldoc);
	
	
	//buat file signature, dan simpan di header
	$dochash = hitunghashfile($finaldoc);
	$priv_key = file_get_contents('./key/'.$_POST["userid"].'.priv.pem');
	// echo $priv_key."\n";
	$key = openssl_pkey_get_private($priv_key, $passphrase);
    // //create signature from filehash
    openssl_sign($dochash, $docsignature, $key, OPENSSL_ALGO_SHA256);
    
	//simpan signature di file pid dan POST
	$pidname = $user_id."docsign.".$pid;
	$data = json_decode(file_get_contents("./data/pid/".$pidname), true);
	$docsignature = base64_encode($docsignature);
	$data["hash"] = $dochash;
	$data["docsignature"] = $docsignature;
	$encode = json_encode($data);
	file_put_contents("./data/pid/".$pidname, $encode);
	
	$_POST["hash"] = $dochash;
	$_POST["docsignature"] = $docsignature;
	
	//$text = "inputdoc = ".$inputdoc." signatureoutputpath = ".$signatureoutputpath." finaldoc = ".$finaldoc;
	//writeline($text,$text,"log.txt");
	
	//if ($result) {
	//	echo "File signed";
	sendsignedfile($_POST,$_POST["callbackpath"],$finaldoc);
	//}
	//else echo "Error signing files";
	
} else {
	echo "Possible file upload attack!\n";
}
	//print_r($_POST);
?>
