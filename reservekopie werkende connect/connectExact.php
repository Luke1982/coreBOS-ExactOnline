<?php

error_reporting(E_ALL);
ini_set("display_errors", "on"); 

// See https://developers.exactonline.com/#OAuth_Tutorial.html%3FTocPath%3DAuthentication|_____2
// APP identification
$client_id = '63aa0ba7-8427-44fd-b884-84ace6eb6664';
$client_secret = '5sj0KRDQTr2u';
$redirect_url = 'http://crmdevelop.cbx-nederland.nl/modules/ExactOnline/index.php';
$division = '1041426';
$api_url = 'https://start.exactonline.nl/api/v1/';

// Authentification URL's
$auth_url = 'https://start.exactonline.nl/api/oauth2/auth'; // For request of the code
$token_url = 'https://start.exactonline.nl/api/oauth2/token'; // For request of the token

// When there is no code, so we're at the start of the OAuth process
if ( !isset($_GET['code']) ) {
	// Go out and get the code
	// First, construct the url by creating an array
	$url_array = array(
		'client_id'=>$client_id,
		'redirect_uri'=>$redirect_url,
		'response_type'=>'code'
		);
	// Build the URL params for the code request
	$url_params =  http_build_query($url_array, '', '&',PHP_QUERY_RFC1738);
	// Redirect to the auth URL. This will set the code as a GET param and come back here
	header("Location: ".$auth_url.'?'.$url_params, TRUE, 302);
	die('Redirect');
	
} else {
	// There was a GET code, and we're going to use it for the second OAuth step

	// First let's see if the current token is still valid
	$jsonStorage = file_get_contents('store_tokens.json'); // Get the storage file
	$jsonStorage = json_decode($jsonStorage, true); // Turn in into assoc array
	// See if the time difference between now and the token set time is more than 10 mins
	$time_diff = time() - $jsonStorage['refreshed_time'];
	
	if ($time_diff >= 600 && isset($jsonStorage['refresh_token']) ) {
		
		// More than 10 minutes, and we do have a refesh token so let's refresh the access token
		// New Curl handler
		$refresh_handler = curl_init();
		// Set specific options for the url
		$refresh_url_params = array(
			'refresh_token'=>$jsonStorage['refresh_token'],
			'grant_type'=>'refresh_token',
			'client_id'=>$client_id,
			'client_secret'=>$client_secret
		);
		// Set CURL options
		$refresh_curl_opts = array(
			CURLOPT_RETURNTRANSFER=>1,
			CURLOPT_SSL_VERIFYPEER=> TRUE,
			CURLOPT_URL=>$token_url,
			CURLOPT_POST=>1,
			CURLOPT_POSTFIELDS=> http_build_query($refresh_url_params, '', '&')
		);
		// Feed the CURL options to the CURL handler
		curl_setopt_array($refresh_handler, $refresh_curl_opts);
		// Execute the CURL
		$refresh_result = curl_exec($refresh_handler);
		// Close the CURL
		curl_close($refresh_handler);
		// Result is JSON, so let's create an array
		$refresh_result = json_decode($refresh_result, true);
		// Now add the current time to the array
		$refresh_result['refreshed_time'] = time();
		// Turn the array back into json
		// And store it back into the file
		file_put_contents('store_tokens.json', json_encode($refresh_result));
		
	} else if ( !isset($jsonStorage['refresh_token']) ) {
	
		// We have no refresh token, so we need one
		$code = $_GET['code'];

		$auth_handler = curl_init();
		$auth_postfields = array (
			'code'=>$code,
			'redirect_uri'=>$redirect_url,
			'grant_type'=>'authorization_code',
			'client_id'=>$client_id,
			'client_secret'=>$client_secret
		);
		$auth_postfields_query = http_build_query($auth_postfields, '', '&');
		$auth_curl_opts = array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $token_url,
			CURLOPT_SSL_VERIFYPEER => TRUE,
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $auth_postfields_query
		);
		curl_setopt_array($auth_handler, $auth_curl_opts);
		$auth_result = curl_exec($auth_handler);
		// This will receive JSON, create an array
		$auth_result = json_decode($auth_result, true);
		// Get the stored data
		$stored = file_get_contents('store_tokens.json');
		$stored = json_decode($stored, true);
		// Store the new access token
		$stored['access_token'] = $auth_result['access_token'];
		$stored['refresh_token'] = $auth_result['refresh_token'];
		// Get the stored array back to JSON
		$stored = json_encode($stored);
		file_put_contents('store_tokens.json',$stored);
	}
		
	$valid_token = file_get_contents('store_tokens.json');
	$valid_token = json_decode($valid_token, true);
	$valid_token = $valid_token['access_token'];
	
	// SEND a TEST request using the access token just aqcuired
	$ch = curl_init();
	// Set the part we want to access, see https://developers.exactonline.com/#RestIntro.html
	$type = 'crm/Accounts';
	// Set URL options
	$get_url_options = array(
		'$select'=>'Name,City,ID'
	);
	$curl_header = array (
		'authorization: Bearer '.$valid_token,
		'Content-Type:application/json'
	);
	// Set the curl options for the test request
	$curl_opts = array();
	$curl_opts[CURLOPT_URL] = $api_url.$division.'/'.$type.'?'.http_build_query($get_url_options, '', '&');
	$curl_opts[CURLOPT_RETURNTRANSFER] = TRUE;
	$curl_opts[CURLOPT_SSL_VERIFYPEER] = TRUE;
	$curl_opts[CURLOPT_HEADER] = FALSE;
	$curl_opts[CURLOPT_HTTPHEADER] = $curl_header;
	$curl_opts[CURLOPT_ENCODING] = '';
	
	// Add the curl options to the handler
	curl_setopt_array($ch, $curl_opts);
	// Execute the curl handler
	$test = curl_exec($ch);
	// $test = json_decode($test);
	
	// echo "<pre>";
	var_dump($test);
	// echo "</pre>";

}


?>