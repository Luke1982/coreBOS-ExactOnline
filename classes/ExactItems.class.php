<?php

class ExactItems extends ExactApi{
	
	// This class will handle all the products, or Items
	// as Exact calls them. We need to have these in Exact to be able to
	// create Sales Invoices
	
	public function __construct() {
		require_once('vtlib/Vtiger/Module.php');
	}
	
	public function sendItem($division, $postarray) {
		// Needs:
		// * Division to identify you
		// * An array with the postfields you want to set.
		//   'sendPostRequest' expects this as array!
		
		// Check if the product or service code is supplied
		if ( array_key_exists('Code', $postarray) ) {
			// Check if the item already exists
			if ( $this->itemExists($division, $postarray['Code']) ) {
				// Get the item's GUID
				$itemGUID = $this->getItemGUID($division, $postarray['Code']);
				// Send a PUT
				$this->sendPutRequest('logistics/Items', $division, $postarray, $itemGUID);
			} else {
				// Let's send the post request for this Item
				return $this->sendPostRequest('logistics/Items', $division, $postarray);				
			}
		} else {
			// Product code was not supplied properly
			return "Supply a proper product code (array), remember: capital C";
		}

	}
	
	public function getItemGUID($division, $code) {
		$itemGUID = $this->sendGetRequest('logistics/Items', $division, 'ID', array('Code'=>$code));
		return $itemGUID['d']['results'][0]['ID'];
	}
	
	public function ExportAllProducts ($division) {
		// Attempts to send ALL items from coreBOS to Exact
		global $adb;
		// Get all the products from coreBOS
		$productResult = $adb->pquery('SELECT product_no, productname FROM vtiger_products', array());
		// Loop them and insert them into Exact
		while ( $product = $adb->fetch_array($productResult) ) {
			$productPostFields = array(
				'Code'			=>		$product['product_no'],
				'Description'	=>		$product['productname']
			);
			// Let's send the post request for this Item in teh loop
			$this->sendPostRequest('logistics/Items', $division, $productPostFields);
		}
	}
	
	public function ExportAllServices ($division) {
		// Attempts to send ALL services from coreBOS to Exact
		global $adb;
		// Get all the products from coreBOS
		$serviceResult = $adb->pquery('SELECT service_no, servicename FROM vtiger_service', array());
		// Loop them and insert them into Exact
		while ( $service = $adb->fetch_array($serviceResult) ) {
			$servicePostFields = array(
				'Code'			=>		$service['service_no'],
				'Description'	=>		$service['servicename']
			);
			// Let's send the post request for this Item in teh loop
			$this->sendPostRequest('logistics/Items', $division, $servicePostFields);
		}
	}
	
	public function getGLAccounts($division) {
		// This function gets all the GLAccounts from Exact
		// We'll set up a cron job in coreBOS to sync them
		// from Exact to coreBOS and add them to a field in
		// Products module. Make sure to filter on 'Debet'
		$GLAccountsFilter = array(
			'BalanceSide'	=>	'D'
		);
		$GLAccountsArray = $this->sendGetRequest('financial/GLAccounts',$division,'ID,Code,Description',$GLAccountsFilter);
		// Prepare the values for the dropdown in corebos
		$dropdownValues = array();
		foreach ($GLAccountsArray['d']['results'] as $value) {
			$dropdownValues[] = $value['Code']." - ".$value['Description'];
		}
		// Return the array so we can add it to the field in Products
		return $dropdownValues;
	}
	
	public function updateGLAccounts($division) {
		global $adb;
		$adb->query('TRUNCATE vtiger_generalledgers');
		$adb->query('TRUNCATE vtiger_glservices');
		// Get the ledgers from Exact
		$GLAccountsArray = $this->getGLAccounts($division);
		foreach ($GLAccountsArray as $key => $value) {
			$key = $key + 1;
			$adb->pquery('INSERT INTO vtiger_generalledgers (generalledgers, sortorderid, presence) VALUES (?,?,?)', array($value, $key, 1));
			$adb->pquery('INSERT INTO vtiger_glservices (glservices, sortorderid, presence) VALUES (?,?,?)', array($value, $key, 1));
		}
	}
	
	public function getGLAccountGUID($division, $productCode, $isservice = NULL) {
		// This method will get the GUID of the General Ledger by
		// Looking into the coreBOS database and selecting the first
		// part of the string, which is the GLAccount code. It will
		// Then send a GET request to Exact and return the Exact GUID string
		// for the General Ledger associated with this product
		global $adb;
		// Get the value from the database
		// Check if it's a service
		if ($isservice == TRUE) {
			$GLresult = $adb->pquery('SELECT glservices FROM vtiger_service WHERE service_no=?',array($productCode));
			$GL = $adb->query_result($GLresult,0,'glservices');
		} else {
			// If it was a product
			$GLresult = $adb->pquery('SELECT generalledgers FROM vtiger_products WHERE product_no=?',array($productCode));
			$GL = $adb->query_result($GLresult,0,'generalledgers');
		}
		// Get the code for the General Ledger, which is always the first word.
		$GLcode = explode(' ',trim($GL));
		$GLcode = $GLcode[0];
		// Send a GET request to Exact to get the GUID of the General Ledger
		// First setup the filter
		$GLfilter = array (
			'Code'	=>	$GLcode
		);
		$GLgetResult = $this->sendGetRequest('financial/GLAccounts', $division, 'ID', $GLfilter);
		return $GLgetResult['d']['results'][0]['ID'];
	}
	
	public function itemExists($division, $code) {
		// This function checks the product of service name
		// you provide and checks if that doesn't already
		// exists within Exact
		
		// First setup a filter array
		$ItemFilter = array(
			'Code'	=>	$code
		);
		// Now perform the GET request based on the product of service code
		// Returns an array
		$ItemResult = $this->sendGetRequest('logistics/Items', $division, 'Code', $ItemFilter);
		// If the item does NOT exist, array below will not exist
		if ( is_array($ItemResult['d']['results'][0]) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
}

?>