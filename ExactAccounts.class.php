<?php

require_once('modules/ExactOnline/ExactApi.class.php');

class ExactAccounts extends ExactApi{
	
	public function __construct() {
		require_once('vtlib/Vtiger/Module.php');
	}
	
	public function listAccounts($division, $selection, $filter = NULL) {
		// Returns an array of all accounts with the account fields
		// Provided in the '$selection' (comma separated input)
		// See if there was a filter and include it in the getrequest call
		if ( isset($filter) && is_array($filter)) {
			return $this->sendGetRequest('crm/Accounts', $division, $selection, $filter);
		} else { //If there was no filter
			return $this->sendGetRequest('crm/Accounts', $division, $selection);
		}
		
	}
	
	public function CreateAccount($division, $fields) {
		if ( is_array($fields) ) {
			// Don't accept an account that doesn't have a code
			if ( isset($fields['Code']) && $fields['Code'] != "" ) {
				// We should check if the account already exists first
				if ( !$this->AccountExists($division, $fields) ) {
					// It doesn't exist, so send a POST
					$this->sendPostRequest('crm/Accounts', $division, $fields);
				} else {
					// It already exists, so send a PUT
					// We need to provide the Exact 'guid' code for this, so let's retrieve it
					$ExactGUID = $this->getAccountGUID($division, $fields['Code']);
					$this->sendPutRequest('crm/Accounts', $division, $fields, $ExactGUID);
				}
			} else {
				echo "You need to provide an Account code, make sure to set array key with a capital C.";
			}
		} else {
			echo "When using function 'CreateAccount', Post fields should be in array form";
		}
	}
	
	public function AccountExists($division, $fields) {
		// Setup the filter array, so checking if account exists is much quicker
		// Because we let the Exact server filter first, so we don't have to
		// No need to prepare the account Code 18 characters, the 'sendGetRequest'
		// takes care of that
		$filterCode = array(
			'Code'	=>	$fields['Code']
		);
		// Now get the array filtering on the code
		$AccountsCodeArray = $this->listAccounts($division,'Code',$filterCode);
		// Only perform action if the result was an array
		if ( is_array($AccountsCodeArray) ) {
			$FeedArray = $AccountsCodeArray['feed'];
			// Now check if this array has a child named 'entry' to see if the code exists
			if ( array_key_exists('entry', $FeedArray) ) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			echo "Result from listAccounts wasn't an array";
		}
	}
	
	public function getAccountGUID($division, $code) {
		// Takes a provided code and gets the correct guid for it.
		$codeFilter = array(
			'Code' => $code
		);
		$AccountsCodeArray = $this->listAccounts($division,'ID',$codeFilter);
		return $AccountsCodeArray['d']['results'][0]['ID'];
	}
	
	public function sendAllAccounts($division) {
		// This function will be a helper for when the initial setup
		// Starts. It will send ALL coreBOS accounts to Exact and
		// Respects the Account numbering used in Corebos (by setting
		// the Exact 'Code' to the coreBOS account no, but removing
		// any non-numerical prefix, since Exact can't handle that).
		global $adb;
		$accountResult = $adb->pquery('SELECT accountid, account_no, accountname, phone, email1 FROM vtiger_account', array());
		while ( $Account = $adb->fetch_array($accountResult) ) {
			$addressResult = $adb->pquery('SELECT bill_city, bill_country, bill_street, bill_pobox FROM vtiger_accountbillads WHERE accountaddressid=?', array($Account['accountid']));
			while ($AccountAddress = $adb->fetch_array($addressResult)) {
				$Account['bill_city']		=	$AccountAddress['bill_city'];
				$Account['bill_country']	=	$AccountAddress['bill_country'];
				$Account['bill_street']		=	$AccountAddress['bill_street'];
				$Account['bill_pobox']		=	$AccountAddress['bill_pobox'];
			}
			// Setup the array the CreateAccount method wants
			$AccountCreateFields = array(
				'Name'				=>	$Account['accountname'],
				'Code'				=>	$Account['account_no'],
				'Phone'				=>	$Account['phone'],
				'Email'				=>	$Account['email1'],
				'City'				=>	$Account['bill_city'],
				'Country'			=>	$Account['bill_country'],
				'AddressLine1'		=>	$Account['bill_street'],
				'Postcode'			=>	$Account['bill_pobox'],
				'Status'			=>	'C'
			);
			// Fire method 'CreateAccount' for each account
			$this->CreateAccount($division, $AccountCreateFields);
		}
	}
	
}

// Instantiate yourself
$Account = new ExactAccounts();

?>