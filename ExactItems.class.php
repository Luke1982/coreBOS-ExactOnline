<?php

class ExactItems extends ExactApi{
	
	// This class will handle all the products, or Items
	// as Exact calls them. We need to have these in Exact to be able to
	// create Sales Invoices
	
	public function __construct() {
		require_once('vtlib/Vtiger/Module.php');
		require_once('modules/ExactOnline/functions.php');
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
	
}

?>