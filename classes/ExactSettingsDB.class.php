<?php

class ExactSettingsDB {
	// THe class is meant to handle all the interaction
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
	
}

// Instantiate yourself
$SDB = new ExactSettingsDB();

?>