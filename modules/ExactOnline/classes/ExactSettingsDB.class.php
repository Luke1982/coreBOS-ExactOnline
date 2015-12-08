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

class ExactSettingsDB {
	// This class is meant to handle all the interaction
	// between the Settings database for the Exact Online
	// module and the module
	
	function __construct() {
		require_once('vtlib/Vtiger/Module.php');
	}
	
	//function to get the value of a specific column from the database
	public function getDbValue($columnname) {
		global $adb;
		$ColumnResult = $adb->pquery('SELECT * FROM vtiger_exactonline_settings WHERE exactonlineid=?',array(0));
		$Field = $adb->query_result($ColumnResult,0,$columnname);
		return $Field;
	}
	
	//function to save the access token into the database
	public function saveAccessToken($value) {
		global $adb;
		$adb->pquery('UPDATE vtiger_exactonline_settings SET access_token=? WHERE exactonlineid=0',array($value));
	}
	
	//function to save the refresh token into the database
	public function saveRefreshToken($value) {
		global $adb;
		$adb->pquery('UPDATE vtiger_exactonline_settings SET refresh_token=? WHERE exactonlineid=0',array($value));
	}
	
	//function to save the refreshed time into the database
	public function saveRefreshedTime() {
		global $adb;
		$adb->pquery('UPDATE vtiger_exactonline_settings SET exactrefreshedtime=? WHERE exactonlineid=0',array(time()));
	}
	
	//function to set the refreshed time back to zero
	public function resetRefreshedTime() {
		global $adb;
		$adb->pquery('UPDATE vtiger_exactonline_settings SET exactrefreshedtime=? WHERE exactonlineid=0',array(0));
	}
	
	//function to save the division into the database
	public function saveDivision($division) {
		global $adb;
		$adb->pquery('UPDATE vtiger_exactonline_settings SET exactdivision=? WHERE exactonlineid=0',array($division));
	}

	//function to save the clientID into the database
	public function saveClientID($clientID) {
		global $adb;
		$adb->pquery('UPDATE vtiger_exactonline_settings SET exactclientid=? WHERE exactonlineid=0',array($clientID));
	}	
	
	//function to save the clientsecret into the database
	public function saveClientsecret($clientsecret) {
		global $adb;
		$adb->pquery('UPDATE vtiger_exactonline_settings SET exactsecret=? WHERE exactonlineid=0',array($clientsecret));
	}	
	
	//function to save the clientsecret into the database
	public function saveReturnUrl($returnurl) {
		global $adb;
		$adb->pquery('UPDATE vtiger_exactonline_settings SET exactreturnurl=? WHERE exactonlineid=0',array($returnurl));
	}
	
	//function to save the AUTH URL into the database
	public function saveAuthUrl($authurl) {
		global $adb;
		$adb->pquery('UPDATE vtiger_exactonline_settings SET exactauthurl=? WHERE exactonlineid=0',array($authurl));
	}
	
	//function to save the TOKEN URL into the database
	public function saveTokenUrl($tokenurl) {
		global $adb;
		$adb->pquery('UPDATE vtiger_exactonline_settings SET exacttokenurl=? WHERE exactonlineid=0',array($tokenurl));
	}
	
	//function to save the API URL into the database
	public function saveApiUrl($apiurl) {
		global $adb;
		$adb->pquery('UPDATE vtiger_exactonline_settings SET exactapiurl=? WHERE exactonlineid=0',array($apiurl));
	}
}

?>