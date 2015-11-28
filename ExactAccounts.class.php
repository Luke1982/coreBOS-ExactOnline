<?php
require('modules/ExactOnline/ExactApi.class.php');

class ExactAccounts extends ExactApi{
	
	public function listAll($division, $selection) {
		$this->sendGetRequest('crm/Accounts', $division, $selection);
	}
	
	public function CreateAccount($division, $fields) {
		if ( is_array($fields) ) {
		$this->sendPostRequest('crm/Accounts', $division, $fields);
		} else {
			echo "When using function 'CreateAccount', Post fields should be in array form";
		}
	}
	
}

?>