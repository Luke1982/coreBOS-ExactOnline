<?php

/**
* Exact API / oAauth
* Copyright (c) iWebDevelopment B.V. (https://www.iwebdevelopment.nl)
*
* Licensed under The MIT License
* For full copyright and license information, please see the LICENSE.txt
* Redistributions of files must retain the above copyright notice.
*
* @copyright     Copyright (c) iWebDevelopment B.V. (https://www.iwebdevelopment.nl)
* @link          https://www.iwebdevelopment.nl
* @since         01-06-2015
* @license       http://www.opensource.org/licenses/mit-license.php MIT License
*/

require_once 'ExactApi.php';

// Configuration, change these:
$clientId 		= 'ad9fdc98-e35f-406a-b9b3-a3c3a036b2d2';
$clientSecret 	= 'tvR2hu9drZJL';
$redirectUri 	= "http://crmdevelop.cbx-nederland.nl/modules/ExactOnline/index.php";
$division		= "1041412";

try {
		
	// Initialize ExactAPI
	$exactApi = new ExactApi('nl', $clientId, $clientSecret, $division);
	
	$exactApi->getOAuthClient()->setRedirectUri($redirectUri);
	
	if (!isset($_GET['code'])) {
		
		// Redirect to Auth-endpoint
		$authUrl = $exactApi->getOAuthClient()->getAuthenticationUrl();
		header('Location: ' . $authUrl, TRUE, 302);
		die('Redirect');
		
	} else {
		
		// Receive data from Token-endpoint
		$tokenResult = $exactApi->getOAuthClient()->getAccessToken($_GET['code']);
		$exactApi->setRefreshToken($tokenResult['refresh_token']);
		
		// List accounts
		$response = $exactApi->sendRequest('crm/Accounts', 'get');
		// echo "<pre>";
		var_dump($response);
		// echo "</pre>";
		
		// Create account
		// $response = $exactApi->sendRequest('crm/Accounts', 'post', array(
			// 'Status'			=>	'C',
			// 'IsSupplier'		=>	True,
			// 'Name'				=>	'iWebDevelopment B.V.',
			// 'AddressLine1'		=>	'Ceresstraat 1',
			// 'Postcode'			=>	'4811CA',
			// 'City'				=>	'Breda',
			// 'Country'			=>	'NL',
			// 'Email'				=>	'info@iwebdevelopment.nl',
			// 'Phone'				=>	'+31(0)76-7002008',
			// 'Website'			=>	'www.iwebdevelopment.nl'

		// ));
		// var_dump($response);
		
	}
	
}catch(ErrorException $e){
	echo "<pre>";
	print_r($e);
	echo "</pre>";
	
}