<?php

/*************************************************************************************************
 * Copyright 2015 MajorLabel -- This file is a part of MajorLabel coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. MajorLabel reserves all rights not expressly
 * granted by the License. coreBOS distributed by MajorLabel is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************
*  Author       : MajorLabel, Guido Goluke
*************************************************************************************************/

// This file acts as the connector between Exact Online and coreBOS.

// Turn these two lines on for debugging
error_reporting(E_ALL);
ini_set("display_errors", "on");

// CODE FOR THE FIRST RUN
if ( isset($_GET['firstrun']) || isset($_GET['code']) ) {
	Authenticate();
	if ( isset($_GET['code']) ) {
		echo '<div style="font-size: 24px; padding: 40px; text-align: center;">';
		echo 'First run complete, your Exact Online Connector is now Authenticated.<br>';
		echo 'Click <a href="/index.php?module=ExactOnline&action=SetCredentials">HERE</a> to go back to the settings screen.';
		echo '</div>';
	}
}
// END CODE FOR FIRST RUN

function Authenticate() {
	include_once('modules/ExactOnline/classes/includeExactClasses.php');
	$OAuth = new ExactOAuth();
	// This function needs to be executed inside EVERY workflow task,
	// before the rest of the function executes. It authenticates the request.

	// If the 'last token time' is 0 (default value), we'll assume this is the very first run
	if ($OAuth->lastTokenTime() == 0) {

		// If we don't have a code (so we're at the start of the Auth process)
		if ( !isset($_GET['code']) ) {
			// Redirect to come back with the code
			$getCode = $OAuth->getCode();
		} else {
			// Now here's the tricky part:
			// When we request a token, we have to also store WHEN we received it
			// Then check if a previous one hasn't expired
			// This all happens in 'getAccessToken' and related methods inside the
			// ExactOAuth Class. This methods returns the access token and refreshes
			// it when necessary, it does need the code from the initial GET request
			$access_token = $OAuth->getAccessToken($_GET['code']);
		}

	} else { 
	// The last refresh time wasn't 0, we'll assume there has been a login before
	// So we just need to check if the current access token has expired and refresh if
	// Necessary
		if ( ($OAuth->lastTokenTime() + 600) <= time() ) {
			$OAuth->refreshToken();
		}
	}
}

function sendAccountToExact($entity) {
	// Authenticate at Exact first
	Authenticate();
	include_once('modules/ExactOnline/classes/includeExactClasses.php');
	global $adb, $current_user;
	list($acc,$acc_id) = explode('x',$entity->data['id']);
	// Get the bill address for this account
	$AR = $adb->pquery('SELECT bill_city, bill_code, bill_country, bill_street FROM vtiger_accountbillads WHERE accountaddressid=?',array($acc_id));
	$AccAddress = $adb->query_result_rowdata($AR,0);
	$SDB = new ExactSettingsDB();
	$Account = new ExactAccounts();
	// Get the division
	$division = $SDB->getDbValue('exactdivision');
	// Send the Account to Exact, the Accounts class will handle checking
	// If the account was already there and update it if it is.
	$accountFields = array(
		'Code'			=>	$entity->data['account_no'],
		'Name'			=>	$entity->data['accountname'],
		'City'			=>	$AccAddress['bill_city'],
		'Email'			=>	$entity->data['email1'],
		'AddressLine1'	=>	$AccAddress['bill_street'],
		'Postcode'		=>	$AccAddress['bill_code'],
		'Status'		=>	'C'
	);
	
	$SendAccountReturn = $Account->CreateAccount($division, $accountFields);
	
	// Handle the return if it was empty
	if ($SendAccountReturn == "") {
		$SendAccountReturn = 'Return was empty';
	}
	
	// Here we handle the creation of a record in the Exact Online module
	// For the return exact gives back
	include_once('include/Webservices/Create.php');
	
	$data_to_save = array(
		'exactrecordname' => $entity->data['accountname'].' ('.$entity->data['account_no'].')',
		'exactonlinereturn' => $SendAccountReturn,
		'assigned_user_id' => '19x'.$current_user->id
	);
	
	vtws_create('ExactOnline', $data_to_save, $current_user);
}

function sendProductToExact($entity) {
	Authenticate();
	// Include the classes
	include_once('modules/ExactOnline/classes/includeExactClasses.php');
	// Get the division
	$SDB = new ExactSettingsDB();
	$division = $SDB->getDbValue('exactdivision');
	$Item = new ExactItems();
	// Get the GUID for the GLAccount for this product
	$GLAccountGUID = $Item->getGLAccountGUID($division, $entity->data['product_no']);
	// Setup the POST array for this product
	$productPostArray = array(
		'GLCosts'			=>	$GLAccountGUID,
		'Code'				=>	$entity->data['product_no'],
		'Description'		=>	$entity->data['productname']
	);
	$Item->sendItem($division, $productPostArray);
}

function sendServiceToExact($entity) {
	Authenticate();
	// Include the classes
	include_once('modules/ExactOnline/classes/includeExactClasses.php');
	// Get the division
	$SDB = new ExactSettingsDB();
	$division = $SDB->getDbValue('exactdivision');
	$Item = new ExactItems();
	// Get the GUID for the GLAccount for this product, we need to specify an extract
	// parameter here to tell the method to look in the services table
	$GLAccountGUID = $Item->getGLAccountGUID($division, $entity->data['service_no'], TRUE);
	// Setup the POST array for this product
	$servicePostArray = array(
		'GLCosts'			=>	$GLAccountGUID,
		'Code'				=>	$entity->data['service_no'],
		'Description'		=>	$entity->data['servicename']
	);
	$Item->sendItem($division, $servicePostArray);
}

function syncGLAccounts() {
	// This function actually empties the vtiger_generalledgers
	// table completely and uses the 'getGLAccounts' method of
	// 'ExactItems' class to re-fill it. Individual products will
	// need to be updated after this
	Authenticate();
	// Include the classes
	include_once('modules/ExactOnline/classes/includeExactClasses.php');
	// Get the division
	$SDB = new ExactSettingsDB();
	$division = $SDB->getDbValue('exactdivision');
	$Item = new ExactItems();
	$Item->updateGLAccounts($division);
}

function sendInvoiceToExact($entity) {
	Authenticate();
	// Include the classes
	include_once('modules/ExactOnline/classes/includeExactClasses.php');
	// Get the division
	$SDB = new ExactSettingsDB();
	$division = $SDB->getDbValue('exactdivision');
	// Instantiate the sales invoice class and execute creation
	$SI = new ExactSalesInvoice();
	$ReturnedSalesInvoice = $SI->CreateSalesInvoice($division, $entity->data['invoice_no']);
	
	// Here we handle the creation of a record in the Exact Online module
	// For the return exact gives back
	include_once('include/Webservices/Create.php');
	global $current_user;
	
	$data_to_save = array(
		'exactrecordname' => 'Invoice '.$entity->data['invoice_no'],
		'exactonlinereturn' => $ReturnedSalesInvoice,
		'assigned_user_id' => '19x'.$current_user->id
	);
	
	vtws_create('ExactOnline', $data_to_save, $current_user);
}

// TEST AREA, WHERE WE'LL CREATE AN ACCOUNT
 if ( isset($_GET['test']) && $_GET['test'] == 1 ) {
	Authenticate();
	// Include the classes
	include_once('modules/ExactOnline/classes/includeExactClasses.php');
	// Get the division
	$SDB = new ExactSettingsDB();
	$division = $SDB->getDbValue('exactdivision');
	
	// Instantiate the sales invoice class and execute creation
	$SI = new ExactSalesInvoice();
	$return = $SI->CreateSalesInvoice($division, '20151261');
	
	$return;
}

function updatePaymentConditions() {
	Authenticate();
	// Include the classes
	include_once('modules/ExactOnline/classes/includeExactClasses.php');
	// Get the division
	$SDB = new ExactSettingsDB();
	$division = $SDB->getDbValue('exactdivision');
	$PC = new ExactPaymentConditions();
	$PC->updatePaymentConds($division);
}

// The functions below will all be available in the settings page of the module
// By using a GET parameter on the URL, we can choose which function we want to
// use. They are mainly for manual synchronization between Exact and coreBOS
if ( isset($_GET['updatepaymentconds']) && $_GET['updatepaymentconds'] == 1 ) {
	updatePaymentConditions();
} 
 
if ( isset($_GET['syncglaccounts']) && $_GET['syncglaccounts'] == 1 ) {
	syncGLAccounts();
}
 
if ( isset($_GET['getdivision']) && $_GET['getdivision'] == 1 ) {
	Authenticate();
	// Include the classes
	include_once('modules/ExactOnline/classes/includeExactClasses.php');
	// Instantiate the API class
	$ExactAPI = new ExactApi();
	// Setup a GET request for the division code
	$divisionCode = $ExactAPI->sendGetRequest('current/Me', '', 'CurrentDivision');
	// Return the division code
	echo $divisionCode['d']['results'][0]['CurrentDivision'];
}

if ( isset($_GET['sendallproducts']) && $_GET['sendallproducts'] == 1 ) {
	Authenticate();
	// Include the classes
	include_once('modules/ExactOnline/classes/includeExactClasses.php');
	// Get the division
	$SDB = new ExactSettingsDB();
	$division = $SDB->getDbValue('exactdivision');
	// Instantiate the Items class
	$Products = new ExactItems();
	// Use the 'send all products' method
	$Products->ExportAllProducts($division);
}

if ( isset($_GET['sendallservices']) && $_GET['sendallservices'] == 1 ) {
	Authenticate();
	// Include the classes
	include_once('modules/ExactOnline/classes/includeExactClasses.php');
	// Get the division
	$SDB = new ExactSettingsDB();
	$division = $SDB->getDbValue('exactdivision');
	// Instantiate the Items class
	$Products = new ExactItems();
	// Use the 'send all products' method
	$Products->ExportAllServices($division);
}

?>