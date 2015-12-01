<?php

require_once('modules/ExactOnline/ExactApi.class.php');

class ExactItems extends ExactApi{
	
	// This class will handle all the products, or Items
	// as Exact calls them. We need to have these in Exact to be able to
	// create Sales Invoices
	
	public function __construct() {
		require_once('vtlib/Vtiger/Module.php');
	}
	
	public function CreateItem($division, $postarray) {
		// Needs:
		// * Division to identify you
		// * An array with the postfields you want to set.
		//   'sendPostRequest' expects this as array!
		
		// Let's send the post request for this Item
		$this->sendPostRequest('logistics/Items', $division, $postarray);
	}
	
	public function ExportAllItems ($division) {
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
	
}

?>