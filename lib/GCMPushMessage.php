<?php
/*
	Class to send push notifications using Google Cloud Messaging for Android

	Example usage
	-----------------------
	$an = new GCMPushMessage($apiKey);
	$an->setDevices($devices);
	$response = $an->send($message);
	-----------------------
	
	$apiKey Your GCM api key
	$devices An array or string of registered device tokens
	$message The mesasge you want to push out

	@author Matt Grundy

	Adapted from the code available at:
	http://stackoverflow.com/questions/11242743/gcm-with-php-google-cloud-messaging

*/
class GCMPushMessage {

	// var $url = 'https://android.googleapis.com/gcm/send';
	// var $serverApiKey = "";
	// var $devices = array();
	
	/*
		Constructor
		@param $apiKeyIn the server API key
	*/
	// function GCMPushMessage($apiKeyIn){
	function GCMPushMessage($message, $AppID, $PID, $OTP, $id,$SIcallbackaddr){
		// $this->serverApiKey = $apiKeyIn;
		// $this->data = array('message' => $message, 'OTP' => $OTP);
        
        //mengirim pesan ke device (Pesan + AppID + PID + OTP)
		$json_message = '{"info":"'.$message.'","AppID":"'.$AppID.'","PID":"'.$PID.'","OTP":"'.$OTP.'","SIaddress":"'.$SIcallbackaddr.'"}';
		$this->data = array('message' => $json_message);
		$this->ids = array($id);
	}

	/*
		Set the devices to send to
		@param $deviceIds array of device tokens to send to
	*/
	// function setDevices($deviceIds){
	
	// 	if(is_array($deviceIds)){
	// 		$this->devices = $deviceIds;
	// 	} else {
	// 		$this->devices = array($deviceIds);
	// 	}
	
	// }

	/*
		Send the message to the device
		@param $message The message to send
		@param $data Array of data to accompany the message
	*/
	// function send($message, $data = false){
		
	// 	if(!is_array($this->devices) || count($this->devices) == 0){
	// 		$this->error("No devices set");
	// 	}
		
	// 	if(strlen($this->serverApiKey) < 8){
	// 		$this->error("Server API Key not set");
	// 	}
		
	// 	$fields = array(
	// 		'registration_ids'  => $this->devices,
	// 		'data'              => array( "message" => $message ),
	// 	);
		
	// 	if(is_array($data)){
	// 		foreach ($data as $key => $value) {
	// 			$fields['data'][$key] = $value;
	// 		}
	// 	}

	// 	$headers = array( 
	// 		'Authorization: key=' . $this->serverApiKey,
	// 		'Content-Type: application/json'
	// 	);

	// 	// Open connection
	// 	$ch = curl_init();
		
	// 	// Set the url, number of POST vars, POST data
	// 	curl_setopt( $ch, CURLOPT_URL, $this->url );
		
	// 	curl_setopt( $ch, CURLOPT_POST, true );
	// 	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
	// 	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
	// 	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		
	// 	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );
		
	// 	// Execute post
	// 	$result = curl_exec($ch);
		
	// 	// Close connection
	// 	curl_close($ch);
		
	// 	return $result;
	// }
	
	// function sendGoogleCloudMessage( $message, $id )
	function sendGoogleCloudMessage()
	{
		$data = $this->data;
		$ids = $this->ids;

		// $data = array('title' => 'test title', 'message' => $message);
		// $ids = array($id);
		//print "device id:".$ids[0]."\r\n";

		//------------------------------
		// Replace with real GCM API 
		// key from Google APIs Console
		// 
		// https://code.google.com/apis/console/
		//------------------------------

		$apiKey = 'AIzaSyD25ltxbNup5rv5k_9T0BIK7xKuzmzKq50';

		//------------------------------
		// Define URL to GCM endpoint
		//------------------------------

		$url = 'https://android.googleapis.com/gcm/send';

		//------------------------------
		// Set GCM post variables
		// (Device IDs and push payload)
		//------------------------------

		$post = array(
		                'registration_ids'  => $ids,
		                'data'              => $data,
		                );

		//------------------------------
		// Set CURL request headers
		// (Authentication and type)
		//------------------------------

		$headers = array( 
		                    'Authorization: key=' . $apiKey,
		                    'Content-Type: application/json'
		                );

		//------------------------------
		// Initialize curl handle
		//------------------------------

		$ch = curl_init();

		//------------------------------
		// Set URL to GCM endpoint
		//------------------------------

		curl_setopt( $ch, CURLOPT_URL, $url );

		//------------------------------
		// Set request method to POST
		//------------------------------

		curl_setopt( $ch, CURLOPT_POST, true );

		//------------------------------
		// Set our custom headers
		//------------------------------

		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

		//------------------------------
		// Get the response back as 
		// string instead of printing it
		//------------------------------

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		//------------------------------
		// Set post data as JSON
		//------------------------------

		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $post ) );


		//QUICK SSL HACK!!
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

		//------------------------------
		// Actually send the push!
		//------------------------------

		$result = curl_exec( $ch );

		//------------------------------
		// Error? Display it!
		//------------------------------

		if ( curl_errno( $ch ) )
		{
		    echo 'GCM error: ' . curl_error( $ch );
		}

		//------------------------------
		// Close curl handle
		//------------------------------

		curl_close( $ch );

		//------------------------------
		// Debug GCM response
		//------------------------------

		return $result;
	}

	function error($msg){
		echo "Android send notification failed with error:";
		echo "\t" . $msg;
		exit(1);
	}
}
