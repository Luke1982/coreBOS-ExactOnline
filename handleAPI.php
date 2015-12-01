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

error_reporting(E_ALL);
ini_set("display_errors", "on");

function Authenticate() {
	include('modules/ExactOnline/classes/includeExactClasses.php');
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
	global $adb;
	$SDB = new ExactSettingsDB();
	$Account = new ExactAccounts();
	// Get the division
	$division = $SDB->getDbValue('exactdivision');
	// Send the Account to Exact, the Accounts class will handle checking
	// If the account was already there and update it if it is.
	$accountFields = array(
		'Code'			=>	$entity->data['account_no'],
		'Name'			=>	$entity->data['accountname']
	);
	$Account->CreateAccount($division, $accountFields);
}

?>