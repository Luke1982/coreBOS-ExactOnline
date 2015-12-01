<?php

// Includes the ExactSettingsDB class that self-instantiates as $this
require_once('modules/ExactOnline/ExactSettingsDB.class.php');

class ExactOAuth extends ExactSettingsDB {
	
	function __construct() {
		require_once('vtlib/Vtiger/Module.php');
	}
	
	// Function to get a code. You need this code to get the access token
	// It needs the client ID, the redirect URL (same as the request URL),
	// response type ('code'), and optionally a force_login parameter, not provided here
	// This function redirects to the auth URL and requests the user to authorize user
	// After that it comes back with a code in the GET parameter
	public function getCode() {
		$code_url_params = array(
			'client_id'		=>	$this->getDbValue('exactclientid'),
			'redirect_uri'	=>	$this->getDbValue('exactreturnurl'),
			'response_type'	=>	'code',
			'force_login'	=>	0
		);
		$code_url_query = http_build_query($code_url_params, '', '&', PHP_QUERY_RFC3986);
		header( "Location: ".$this->getDbValue('exactauthurl').'?'.$code_url_query, TRUE, 302 );
	}
	
	// function to get to the access token
	// And store it in the database
	// Code parameter should be the returned GET value called 'code'
	// You receive this from the first step in the Auth process
	// Described on https://developers.exactonline.com/#OAuth_Tutorial.html%3FTocPath%3DAuthentication|_____2
	public function getAccessToken($code) {
		// First, let's see if there IS an access token in the database, and if it hasn't expired
		// The last check is to see if the refresh time isn't zero, which would indicate first run
		if ( $this->getDbValue('access_token') != "" &&  time() <= ($this->lastTokenTime() + 600 ) && $this->lastTokenTime() != 0) {
			// Well if all of the above is OK, we can use the current token
			return $this->getDbValue('access_token');
		} else if ( $this->getDbValue('access_token') != "" &&  time() >= ($this->lastTokenTime() + 600 ) ) {
			// There IS an access token, but it expired
			$this->refreshToken();
			// OK, we're all refreshed now, let's return the new token
			return $this->getDbValue('access_token');
		} else if ( $this->lastTokenTime() == 0 ) {
			// This is the first time, let's get an access token!
			// Setup a CURL
			$token_curl = curl_init();
			// Setup the post fields for this curl request
			$token_postfields = array(
				'code'			=>	$code,
				'redirect_uri'	=>	$this->getDbValue('exactreturnurl'),
				'grant_type'	=>	'authorization_code',
				'client_id'		=>	$this->getDbValue('exactclientid'),
				'client_secret'	=>	$this->getDbValue('exactsecret')
			);
			// Turn the postfields into a query
			$token_postfields_query = http_build_query($token_postfields, '', '&');
			// Setup the Curl options
			$token_curl_opts = array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => $this->getDbValue('exacttokenurl'),
				CURLOPT_SSL_VERIFYPEER => TRUE,
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => $token_postfields_query
			);
			// Add the Curl options to the Curl handler
			curl_setopt_array($token_curl, $token_curl_opts);
			// Execute the curl
			$token_result = curl_exec($token_curl);
			// We'll receive JSON, so let's create an array from that
			$token_result_array = json_decode($token_result, true);
			// This situation should only happen once, when then first ever
			// token is requested, the TRUE parameter indicates to update the timestamp
			$this->storeToken($token_result_array, TRUE);
			// We're good to go, return the access token
			return $this->getDbValue('access_token');
		}
	}
	
	public function lastTokenTime() {
		return $this->getDbValue('exactrefreshedtime');
	}
	
	public function storeToken($tokenResultArray, $newTimeStamp = FALSE) {
		// Loop through results from getAccessToken of RefreshToken
		foreach ($tokenResultArray as $key => $value) {
			// Only store the Access token en Refresh Token
			if ($key == 'access_token') {
				$this->saveAccessToken($value);
			} else if ($key == 'refresh_token') {
				$this->saveRefreshToken($value);
			}
		}
		// If this is the initial token request
		if ($newTimeStamp == TRUE) {
			$this->saveRefreshedTime();
		}
	}
	
	public function refreshToken() {
		// setup the refresh cURL
		$refresh_handler = curl_init();
		// Setup the POST parameters
		$refresh_post_params = array(
			'refresh_token'		=>	$this->getDbValue('refresh_token'),
			'grant_type'		=>	'refresh_token',
			'client_id'			=>	$this->getDbValue('exactclientid'),
			'client_secret'		=>	$this->getDbValue('exactsecret')
		);
		// Create a query string
		$refresh_postfields_query = http_build_query($refresh_post_params, '', '&');
		// Setup the cURL options
		$refresh_curl_opts = array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $this->getDbValue('exacttokenurl'),
			CURLOPT_SSL_VERIFYPEER => TRUE,
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $refresh_postfields_query
		);
		// Add the Curl options to the Curl handler
		curl_setopt_array($refresh_handler, $refresh_curl_opts);
		// Execute the curl
		$refresh_result = curl_exec($refresh_handler);
		// Comes back JSON, so first make an array
		$refresh_token_array = json_decode($refresh_result, true);
		// Use the storeToken function with TRUE to store the new data AND timestamp
		$this->storeToken($refresh_token_array, TRUE);
	}
	
}

// Instantiate yourself
$OAuth = new ExactOAuth();

?>