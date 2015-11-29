<?php

error_reporting(E_ALL);
ini_set("display_errors", "on");

// TODO: OAuth class contains database method, maybe move this to a separate class
require('modules/ExactOnline/ExactOAuth.class.php');

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
// $Accountlisting = $Account->listAccounts( $division, 'Name,City,ID,Code');

// echo "<pre>";
// print_r($Accountlisting);
// echo "</pre>";


// Now for the creation of a new Account
// Create an Array with the fields you want to create
// $accountCreateFields = array (
	// 'Name'	=>	'Testaccount 2',
	// 'Email'	=>	'krijg@nouwat.nl',
	// 'City'	=>	'Krimpen aan de IJssel',
	// 'Code'	=>	'ACC102547'
// );
// Send ALL Accounts to Exact
// Will probably timeout or face the access token time 
// limit in its current form.
// $Account->sendAllAccounts($division);

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

$Item->ExportAllItems($division);



?>