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

	'ModuleFieldLabel' 				=> 'ModuleFieldLabel Text',
	
	/*		SETTINGS AREA	*/
	'SetCredentials'				=> 'Set Exact API credentials',
	'SetCredentialsTitle'			=> 'Set Credentials',
	'division'						=> 'Division',
	'getdivision'					=> 'GET THE DIVISION',
	'savesettings'					=> 'SAVE',
	'reloadfirstrun'				=> 'RESET FIRST RUN',
	'authurl'						=> 'Authentification URL',
	'tokenurl'						=> 'Token URL',
	'apiurl'						=> 'API URL',
	'settingsintro'					=> 'Welcome, maybe you just performed your first Authentification. Remember, you <b>need</b> to set your division first. This is easy, just click \'GET THE DIVISION\' and you will see you division code appear in the input field. Now click \'SAVE\' to save all settings.<br><br>Now the Auth URL, Token URL and API URL have been automatically set during your first run. <b>Don\'t change these</b> unless you are experiencing problems. Make sure the countrycode in the URL\'s are correct, so for the Netherlands, the URL should start with https://start.exactonline.<b>nl</b>, and so on.<br><br>If you need to re-perform the first-run for some reason, click the \'RESET FIRST RUN\' button and press F5 on your keyboard or reload the page.',
	
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
