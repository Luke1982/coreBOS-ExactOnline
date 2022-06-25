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
require_once("Smarty_setup.php");
require_once("include/utils/utils.php");
global $adb,$log,$current_language,$app_strings,$theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$smarty = new vtigerCRM_Smarty();
$smarty->assign('APP', $app_strings);
$mod =  array_merge(
		return_module_language($current_language,'ExactOnline'),
		return_module_language($current_language,'Settings'));
$smarty->assign("MOD", $mod);
$smarty->assign("THEME",$theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("MODULE_NAME", 'ExactOnline');
$smarty->assign("MODULE_ICON", 'modules/ExactOnline/images/setexactcredIcon.png');
$smarty->assign("MODULE_TITLE", $mod['SetCredentials']);
$smarty->assign("MODULE_Description", $mod['SetCredentials']);

// Get the SettingsDBClass
include_once('modules/ExactOnline/classes/ExactSettingsDB.class.php');
$SDB = new ExactSettingsDB();

// Check if the last refreshed time is 0, indicating first run
if ( $SDB->getDbValue('exactrefreshedtime') == 0 ) {
	// Assign a smarty var so we can adapt the display indicating
	// The first Auth is still needed
	$smarty->assign("firstrun", true);
}

// Assign some variables to Smarty
$smarty->assign("division", $SDB->getDbValue('exactdivision'));
$smarty->assign("clientID", $SDB->getDbValue('exactclientid'));
$smarty->assign("clientsecret", $SDB->getDbValue('exactsecret'));
$smarty->assign("returnurl", $SDB->getDbValue('exactreturnurl'));
$smarty->assign("authurl", $SDB->getDbValue('exactauthurl'));
$smarty->assign("tokenurl", $SDB->getDbValue('exacttokenurl'));
$smarty->assign("apiurl", $SDB->getDbValue('exactapiurl'));
$smarty->assign("servername", $_SERVER['SERVER_NAME']);

// Handle the post request if made by JQuery to save values into the settings table
if ( $_POST['save'] == true ) {
	$SDB->saveDivision($_POST['division']);
	$SDB->saveClientID($_POST['clientID']);
	$SDB->saveClientsecret($_POST['clientsecret']);
	$SDB->saveReturnUrl($_POST['returnurl']);
	$SDB->saveAuthUrl($_POST['authurl']);
	$SDB->saveTokenUrl($_POST['tokenurl']);
	$SDB->saveApiUrl($_POST['apiurl']);
}

// Handle the post request if made by JQuery to save values FROM THE FIRST RUN
if ( $_POST['firstrunsave'] == true ) {
	$SDB->saveClientID($_POST['clientID']);
	$SDB->saveClientsecret($_POST['clientsecret']);
	
	$firstRunReturnUrl = 'https://'.$_SERVER['SERVER_NAME'].'/index.php?module=ExactOnline&action=ExactOnlineAjax&file=handleAPI';
	$SDB->saveReturnUrl($firstRunReturnUrl);
	
	$firstRunAuthUrl = $_POST['countryurl'].'/api/oauth2/auth';
	$SDB->saveAuthUrl($firstRunAuthUrl);	
	
	$firstRunTokenUrl = $_POST['countryurl'].'/api/oauth2/token';
	$SDB->saveTokenUrl($firstRunTokenUrl);

	$firstRunApiUrl = $_POST['countryurl'].'/api/v1/';
	$SDB->saveApiUrl($firstRunApiUrl);
}

// Handle the POST request if the user wants to perform the first run again
if ( $_POST['reloadfirstrun'] == true ) {
	$SDB->resetRefreshedTime();
}

$smarty->display(vtlib_getModuleTemplate('ExactOnline', 'SetCredentials.tpl'));
?>