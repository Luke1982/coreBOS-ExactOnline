<?php

class ExactApi {
	
	private $apiUrl = 'https://start.exactonline.nl/api/v1/';
	
	function __construct() {
		include_once('modules/ExactOnline/classes/ExactSettingsDB.class.php');
		$SDB = new ExactSettingsDB();
		$this->apiUrl = $SDB->getDbValue('exactapiurl');
	}
	
	public function sendGetRequest($suburl = NULL, $division = NULL, $select = NULL, $filter = NULL, $filterint = false) {
		$SDB = new ExactSettingsDB();
		// Every get request has to have:
		// * The API URL
		// * The division
		// * Your access token in the HEADER
		// * The selection of fields you want returned
		// * OPTIONAL: a filter, to select a specific record, this should be an array
		// * OPTIONAL: a 'filterint' flagg. Setting this to true will remove quotes around the searchterm

		// setup a curl
		$get_curl_handler =  curl_init();
		// Setup the URL
		// Check if a filter was used and setup the URL accordingly
		if ( isset($filter) && is_array($filter) ) {
			// Do the Account code special work for filtering
			if ( array_key_exists('Code', $filter) && $suburl == 'crm/Accounts') {
				$filter['Code'] = preg_replace("/[^0-9,.]/", "", $filter['Code']);
				$filter['Code'] = str_pad($filter['Code'], 18, " ", STR_PAD_LEFT);
			}
			foreach ($filter as $key => $value) {
				$searchfield 	= (string)$key;
				$searchterm 	= (string)urlencode($value);
			}
			if ($filterint) {
				$request_url = $this->apiUrl.$division.'/'.$suburl.'?$select='.$select.'&$filter='.$searchfield.'%20eq%20'.$searchterm.'';
			} else {
				$request_url = $this->apiUrl.$division.'/'.$suburl.'?$select='.$select.'&$filter='.$searchfield.'%20eq%20\''.$searchterm.'\'';
			}
			
			echo $request_url."<br />";
		} else {
			// Setup a special request URL for the division code GET
			if ( $division == '' ) {
				$request_url = $this->apiUrl.$suburl.'?$select='.$select;
			} else {
				// Regular call, not a division code request
				$request_url = $this->apiUrl.$division.'/'.$suburl.'?$select='.$select;
			}
		}
		// Setup the header
		$get_curl_header = array (
			'authorization: Bearer '.$SDB->getDbValue('access_token'),
			'Accept: application/JSON'
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
		// Close the curl
		curl_close($get_curl_handler);	
		// Return the JSON in PHP Array form		
		return json_decode($get_curl_result, true);
	}
	
	public function sendPostRequest($suburl, $division, $postfields) {
		$SDB = new ExactSettingsDB();
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
			'authorization: Bearer '.$SDB->getDbValue('access_token'),
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
		// TEST
		// print_r($postfields);
		// Add the cURL options to the handler
		curl_setopt_array($post_curl_handler, $post_curl_opts);
		// Execute the cURL
		$post_curl_result = curl_exec($post_curl_handler);
		// Close the curl
		curl_close($post_curl_handler);
		// Returns the result
		return $post_curl_result;
	}
	
	public function sendPutRequest($suburl, $division, $putfields, $GUID) {
		$SDB = new ExactSettingsDB();
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
		if ( (array_key_exists('Code', $putfields)) && ($suburl == 'crm/Accounts') ) {
			$putfields['Code'] = preg_replace("/[^0-9,.]/", "", $putfields['Code']);
			$putfields['Code'] = str_pad($putfields['Code'], 18, " ", STR_PAD_LEFT);
		}
		// Setup the postfields array so that is is a JSON string
		$putfields = json_encode($putfields);
		// Setup the header
		$put_curl_header = array (
			'authorization: Bearer '.$SDB->getDbValue('access_token'),
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
			CURLOPT_POSTFIELDS		=> $putfields
		);
		// Add the cURL options to the handler
		curl_setopt_array($put_curl_handler, $put_curl_opts);
		// Execute the cURL
		$put_curl_result = curl_exec($put_curl_handler);
		// Close the curl
		curl_close($put_curl_handler);
		// return the result
		return $put_curl_result;
	}
	
}

?>