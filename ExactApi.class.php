<?php

class ExactApi {
	
	private $apiUrl = 'https://start.exactonline.nl/api/v1/';
	
	public function __construct() {
		require_once('modules/ExactOnline/ExactOAuth.class.php');
		require_once('modules/ExactOnline/functions.php');
	}
	
	public function sendGetRequest($suburl = NULL, $division = NULL, $select = NULL, $filter = NULL) {
		global $OAuth;
		// Every get request has to have:
		// * The API URL
		// * The division
		// * Your access token in the HEADER
		// * The selection of fields you want returned
		// * OPTIONAL: a filter, to select a specific record, this should be an array
		
		// setup a curl
		$get_curl_handler =  curl_init();
		// Setup the URL
		// Check if a filter was used and setup the URL accordingly
		if ( isset($filter) && is_array($filter) ) {
			foreach ($filter as $key => $value) {
				$searchfield 	= (string)$key;
				$searchterm 	= (string)$value;
			}
			$request_url = $this->apiUrl.$division.'/'.$suburl.'?$select='.$select.'&$filter='.$searchfield.'%20eq%20\''.$searchterm.'\'';
		} else {
			$request_url = $this->apiUrl.$division.'/'.$suburl.'?$select='.$select;
		}
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
			CURLOPT_ENCODING 		=> '',
			CURLOPT_CUSTOMREQUEST	=> 'GET'
		);
		// Add the cURL options to the handler
		curl_setopt_array($get_curl_handler, $get_curl_opts);
		// Execute the cURL
		$get_curl_result = curl_exec($get_curl_handler);
		// TEST the new XML2array function
		$get_result_array = xml2array($get_curl_result);
		// Return the XML in PHP Array form
		return $get_result_array;
	}
	
	public function sendPostRequest($suburl, $division, $postfields) {
		global $OAuth;
		// Every POST request has to have:
		// * The API URL
		// * The division
		// * Your access token in the HEADER
		// * The postfields you want to create or update

		// setup a curl
		$post_curl_handler =  curl_init();
		// Setup the URL
		$request_url = $this->apiUrl.$division.'/'.$suburl;
		// If there is a postfield named 'Code' it should be padded to end up 18
		// Characters long with leading spaces. Also, we should first remove all
		// non-numeric characters, since Exact won't accept them
		// This also checks if the post is done for an Account, because Accounts
		// is the only one that needs the 18 character code
		if ( array_key_exists('Code', $postfields) && $suburl == 'crm/Accounts') {
			$postfields['Code'] = preg_replace("/[^0-9,.]/", "", $postfields['Code']);
			$postfields['Code'] = str_pad($postfields['Code'], 18, " ", STR_PAD_LEFT);
		}
		// Setup the postfields array so that is is a JSON string
		$postfields = json_encode($postfields);
		// Setup the header
		$post_curl_header = array (
			'authorization: Bearer '.$OAuth->getDbValue('access_token'),
			'Content-Type:application/json'
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
	
	public function sendPutRequest($suburl, $division, $postfields, $GUID) {
		global $OAuth;
		// Every PUT request has to have:
		// * The API URL
		// * The internal Exact guid for this record
		// * The division
		// * Your access token in the HEADER
		// * The postfields you want to create or update

		// setup a curl
		$put_curl_handler =  curl_init();
		// Setup the URL
		$request_url = $this->apiUrl.$division."/".$suburl."(guid'".$GUID."')";
		// If there is a postfield named 'Code' it should be padded to end up 18
		// Characters long with leading spaces. Also, we should first remove all
		// non-numeric characters, since Exact won't accept them
		if ( array_key_exists('Code', $postfields && $suburl == 'crm/Accounts') ) {
			$postfields['Code'] = preg_replace("/[^0-9,.]/", "", $postfields['Code']);
			$postfields['Code'] = str_pad($postfields['Code'], 18, " ", STR_PAD_LEFT);
		}
		// Setup the postfields array so that is is a JSON string
		$postfields = json_encode($postfields);
		// Setup the header
		$put_curl_header = array (
			'authorization: Bearer '.$OAuth->getDbValue('access_token'),
			'Content-Type:application/json'
		);
		// Setup the cURL options
		$put_curl_opts = array(
			CURLOPT_URL 			=> $request_url,
			CURLOPT_RETURNTRANSFER 	=> TRUE,
			CURLOPT_SSL_VERIFYPEER 	=> TRUE,
			CURLOPT_HEADER 			=> FALSE,
			CURLOPT_HTTPHEADER 		=> $put_curl_header,
			CURLOPT_ENCODING 		=> '',
			CURLOPT_CUSTOMREQUEST	=> 'PUT',
			CURLOPT_POSTFIELDS		=> $postfields
		);
		// Add the cURL options to the handler
		curl_setopt_array($put_curl_handler, $put_curl_opts);
		// Execute the cURL
		$put_curl_result = curl_exec($put_curl_handler);
		// TEST to see the result
		var_dump($put_curl_result);		
	}
	
}

// Instantiate yourself
$ExactAPI = new ExactOAuth();

?>