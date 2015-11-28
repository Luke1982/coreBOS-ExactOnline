<?php
require('modules/ExactOnline/ExactApi.class.php');

class ExactAccounts extends ExactApi{
	
	public function listAll($division, $selection) {
		$this->sendGetRequest('crm/Accounts', $division, $selection);
	}
	
}

?>