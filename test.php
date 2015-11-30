<?php

error_reporting(E_ALL);
ini_set("display_errors", "on");

// TODO: OAuth class contains database method, maybe move this to a separate class
require('modules/ExactOnline/ExactOAuth.class.php');

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
// So we just need to refresh the token
		$OAuth->refreshToken();
}

// require the API, it instantiates itself
require('modules/ExactOnline/ExactApi.class.php');

/*==============================================================================*/
/*======================= SOME TESTS OF THE ACCOUNT CLASS ======================*/
/*==============================================================================*/
// Now it's time to Test the accounts Class
require('modules/ExactOnline/ExactAccounts.class.php');

// First set your division, so Exact can identify you
$division = '1041426';

// Let's get all Accounts from our administration
$Account = new ExactAccounts();
// This function should get all the accounts, working together with the API class..
// It currently accepts the division and the fields from Accounts you want returned
// UPDATE: it now also accepts a filter, in array form 

// Try out the filter, it should be an array with one key and one value like
// a search. The key is the field you're searching, the value is you search term (has to be exact)

// $searchFilter = array (
	// 'City' => 'Geldermalsen'
// );

// $Accountlisting = $Account->listAccounts( $division, 'Name,City,ID,Code', $searchFilter);

// echo "<pre>";
// print_r($Accountlisting);
// echo "</pre>";


$accountCreateFields = array (
	'Name'	=>	'Testaccount 3',
	'Email'	=>	'krijg@nouwat.nl',
	'City'	=>	'Beneden Leeuwen',
	'Code'	=>	'ACC123456'
);

$Account->CreateAccount($division, $accountCreateFields);


/*===========================================================================*/
/*======================= SOME TESTS OF THE SALESINVOICE CLASS===============*/
/*===========================================================================*/

require('modules/ExactOnline/ExactSalesInvoice.class.php');

$SI = new ExactSalesInvoice();

/*===========================================================================*/
/*==================== SOME TESTS OF THE ITEMS CLASS ========================*/
/*===========================================================================*/

require('modules/ExactOnline/ExactItems.class.php');

$Item = new ExactItems();

// $ItemCreateFields = array(
	// 'Code'			=>		'PRO1584',
	// 'Description'	=>		'Omschrijving van dit product'
// );

// $Item->ExportAllItems($division);



?>