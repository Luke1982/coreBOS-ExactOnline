<?php

error_reporting(E_ALL);
ini_set("display_errors", "on"); 

require('modules/ExactOnline/ExactOAuth.class.php');
$OAuth = new ExactOauth();

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

// Now it's time to Test the accounts Class
require('modules/ExactOnline/ExactAccounts.class.php');
// First set your division, so Exact can identify you
$division = '1041426';
// 


?>