<?php
require('modules/ExactOnline/ExactApi.class.php');

class ExactAccounts extends ExactApi{
	
	public function listAll($division, $selection) {
		$this->sendGetRequest('crm/Accounts', $division, $selection);
	}
	
	public function CreateAccount($division, $fields) {
		if ( is_array($fields) ) {
		$this->sendPostRequest($division, $fields);
		} else {
			return "Post fields should be in array form";
		}
	}
	
}

?>