<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

$mod_strings = Array(
	'ModuleName' 					=> 'Exact Online',
	'SINGLE_ModuleName' 			=> 'Exact Online Record',
	'ModuleName ID' 				=> 'Exact Online Record ID',

	'LBL_CUSTOM_INFORMATION' 		=> 'Custom Information',
	'LBL_EXACTONLINE_INFORMATION' 	=> 'Exact Record Information',
	'LBL_DESCRIPTION_INFORMATION' 	=> 'Description',
	
	/*	SETTINGS AREA	*/
	'SetCredentials'				=> 'Set Exact API credentials, division and return URL. Perform first run here.',
	'SetCredentialsTitle'			=> 'Set Credentials',
	'division'						=> 'Division',
	'getdivision'					=> 'GET THE DIVISION',
	'savesettings'					=> 'SAVE',
	'reloadfirstrun'				=> 'RESET FIRST RUN',
	'authurl'						=> 'Authentification URL',
	'tokenurl'						=> 'Token URL',
	'apiurl'						=> 'API URL',
	'settingspagetitle'				=> 'Exact Online Settings Page',
	'settingsintro'					=> 'Welcome, maybe you just performed your first Authentification. Remember, you <b>need</b> to set your division first. This is easy, just click \'GET THE DIVISION\' and you will see you division code appear in the input field. Now click \'SAVE\' to save all settings.',
	'settingsintro2'				=> 'Now the Auth URL, Token URL and API URL have been automatically set during your first run. <b>Don\'t change these</b> unless you are experiencing problems. Make sure the countrycode in the URL\'s are correct, so for the Netherlands, the URL should start with https://start.exactonline.<b>nl</b>, and so on.',
	'settingsintro3'				=> 'If you need to re-perform the first-run for some reason, click the \'RESET FIRST RUN\' button and press F5 on your keyboard or reload the page.',
	
	/* SYNC PAGE SETTINGS */
	
	'syncsettings'					=> 'Synchronisation',
	'synsettingsdesc'				=> 'Synchronize your Products and Services for the first time and manually synchronize your General Ledgers and Payment Conditions',
	'syncsettingsC1'				=> 'On this page you can send <b>all</b> products and services at once to Exact. <b>Beware</b>, this may take a very long time (up to ten minutes), and the process may stop depending on your server settings and the amount of products and services you have. If this automated solution does not send all your products or services, you may need to export them from coreBOS manually and import them into Exact. You can also edit and save (no need for modifications) each product or service at a time within coreBOS (make sure you have the workflows setup) to send them one by one.',
	'syncsettingsC2'				=> 'Here, you can also synchronize you General Ledgers and Payment conditions from Exact to coreBOS manually. There are also workflows available for this. The \'General Ledgers\' workflow is available for Products, but will sync the dropdown for both products and services. You need to set an interval for this, and cron needs to be setup on your server. The \'Payment Conditions\' workflow is available for Invoices. Here also, you need to have cron setup for your server.',
	'syncsettingsC3'				=> 'After you click either \'Send all Products\' or \'Send all Services\', keep in mind that your coreBOS system may not respond during the time it takes to send all products or services.',
	'syncglaccounts'				=> 'Synchronize General Ledgers',
	'glaccountsalert'				=> 'General Ledgers Synchronized',
	'syncpaymentconds'				=> 'Synchronize Payment Conditions',
	'paymentcondsalert'				=> 'Payment Conditions Synchronized',
	'sendallproducts'				=> 'Send all products to Exact',
	'sendallproductsalert'			=> 'Starting to send all products, this may not work, depending on your server settings and the amount of products you have.',
	'sendallservices'				=> 'Send all Services',
	'sendallservicesalert'			=> 'Starting to send all services, this may not work, depending on your server settings and the amount of services you have.',
	'glaccounts_start'				=> 'General Ledgers Start number',
	'glaccounts_stop'				=> 'General Ledgers End number',
	'setGLRange'					=> 'Save General Ledger sync range',
	'setGLRangealert'				=> 'General Ledger sync range saved',
	
	/* FIRST RUN */
	'FirstRunPrev'					=> 'Previous',
	'FirstRunNext'					=> 'Next',	
	'FirstRunTitleOne'				=> '<br><br>Welcome to the Exact Online Connector for coreBOS setup process. Step 1: Go to the Exact Online app center and create an API key',
	'FirstRunStepOne'				=> 'First, you need to visit <a href="http://apps.exactonline.com" target="_blank">http://apps.exactonline.com</a> to register an API key. Log in, if you aren\'t already and click "Register API Keys". Now click "Register a new API key". You will be asked to provide a name. Any name will do, name it something descriptive. You will also be asked to fill out a return URL. Use the one below:',
	'FirstRunTitleTwo'				=> 'Now fill out your Client ID and app secret here.',
	'FirstRunStepTwo'				=> 'After you filled out the previous step and saved you app, you\'ll have received these. The Exact Online Connector will need these to connect to your Exact Administration.',
	'FirstRunTitleThree'			=> 'Exact Online URL:',
	'FirstRunStepThree'				=> 'Now fill out the correct URL for your country:<ul><li><b>Netherlands: </b>https://start.exactonline.nl</li><li><b>Belgium: </b>https://start.exactonline.be</li><li><b>Germany: </b>https://start.exactonline.de</li><li><b>UK: </b>https://start.exactonline.co.uk</li><li><b>USA: </b>https://start.exactonline.com</li></ul>',
	'YourCountryURL'				=> 'The URL for your country',
	'FirstRunTitleFour'				=> 'The last part:',
	'FirstRunStepFour'				=> 'Click the Save button below to save your values into the database, <b>after</b> this, click the button \'Perform First Auth\' to Authenticate your app. This will lead you away from this page to a page where you will be asked if coreBOS can have access to you Exact Online administration (you <b>may</b> need to log in if you aren\'t already). Click \'yes\' and you will end up with a screen that confirms your authentification. Click the link on that screen to come back here.<br><br>Then, after you come back here, you still need to get your <b>division</b> code! The Exact Online Connector can get this automatically, you just need to click the button for retrieving it, and then save it into the database by clicking save.',
	'saveFirstRun'					=> 'Save values into the database',
	'performFirstAuth'				=> 'Perform the Authentification'
);

?>
