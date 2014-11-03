<?php
function sendsignedfile($data,$target_url,$filepath) {
    //This needs to be the full path to the file you want to send.
	$file_name_with_full_path = realpath($filepath);
	$data['file_contents'] = '@'.$file_name_with_full_path;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$target_url);
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$result=curl_exec ($ch);
	curl_close ($ch);
	echo $result;
}
?>