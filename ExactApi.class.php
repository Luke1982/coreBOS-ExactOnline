<?php

class ExactApi {
	
	private $apiUrl = 'https://start.exactonline.nl/api/v1/';
	
	public function __construct() {
		require_once('modules/ExactOnline/ExactOAuth.class.php');
	}
	
	public function sendGetRequest($suburl = NULL, $division = NULL, $select = NULL) {
		global $OAuth;
		// Every get request has to have:
		// * The API URL
		// * The division
		// * Your access token in the HEADER
		// * The selection of fields you want returned
		
		// setup a curl
		$get_curl_handler =  curl_init();
		// Setup the URL
		$request_url = $this->apiUrl.$division.'/'.$suburl.'?$select='.$select;
		// Setup the header
		$get_curl_header = array (
			'authorization: Bearer '.$OAuth->getDbValue('access_token')
		);
		// Setup the cURL options
		$get_curl_opts = array(
			CURLOPT_URL 			=> $request_url,
			CURLOPT_RETURNTRANSFER 	=> TRUE,
			CURLOPT_SSL_VERIFYPEER 	=> TRUE,
			CURLOPT_HEADER 			=> FALSE,
			CURLOPT_HTTPHEADER 		=> $get_curl_header,
			CURLOPT_ENCODING 		=> ''
		);
		// Add the cURL options to the handler
		curl_setopt_array($get_curl_handler, $get_curl_opts);
		// Execute the cURL
		$get_curl_result = curl_exec($get_curl_handler);
		// TEST to see the result
		var_dump($get_curl_result);
	}
	
	public function sendPostRequest($suburl, $division, $postfields) {
		global $OAuth;
		// Every get request has to have:
		// * The API URL
		// * The division
		// * Your access token in the HEADER
		// * The postfields you want to create or update

		// setup a curl
		$post_curl_handler =  curl_init();
		// Setup the URL
		$request_url = $this->apiUrl.$division.'/'.$suburl;
		// Setup the postfields array so that is is a JSON string
		$postfields = json_encode($postfields);
		// Setup the header
		$post_curl_header = array (
			'authorization: Bearer '.$OAuth->getDbValue('access_token')
		);
		// Setup the cURL options
		$post_curl_opts = array(
			CURLOPT_URL 			=> $request_url,
			CURLOPT_RETURNTRANSFER 	=> TRUE,
			CURLOPT_SSL_VERIFYPEER 	=> TRUE,
			CURLOPT_HEADER 			=> FALSE,
			CURLOPT_HTTPHEADER 		=> $post_curl_header,
			CURLOPT_ENCODING 		=> '',
			CURLOPT_POST			=> TRUE,
			CURLOPT_POSTFIELDS		=> $postfields
		);
		// Add the cURL options to the handler
		curl_setopt_array($post_curl_handler, $post_curl_opts);
		// Execute the cURL
		$post_curl_result = curl_exec($post_curl_handler);
		// TEST to see the result
		var_dump($post_curl_result);		
	}
	
}

?>