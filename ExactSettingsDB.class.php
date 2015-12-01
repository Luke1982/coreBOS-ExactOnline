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
		$ColumnResult = $adb->pquery('SELECT * FROM vtiger_exactonline WHERE exactonlineid=?',array(0));
		$Field = $adb->query_result($ColumnResult,0,$columnname);
		return $Field;
	}
	
	//function to set the value of a specific column from the database
	public function setDbValue($columnname, $value) {
		global $adb;
		$adb->pquery('UPDATE vtiger_exactonline SET ?=? WHERE exactonlineid=?',array($columnname, $value, 0));
	}
	
}

// Instantiate yourself
$SDB = new ExactSettingsDB();

?>