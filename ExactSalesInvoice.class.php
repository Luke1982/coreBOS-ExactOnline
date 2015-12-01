<?php

class ExactSalesInvoice extends ExactApi{
	
	public function __construct() {
		require_once('vtlib/Vtiger/Module.php');
		// To be removed, we know how to get JSON now
		// require_once('modules/ExactOnline/functions.php');
		require_once('modules/ExactOnline/ExactAccounts.class.php');
	}
	
	public function CreateSalesInvoice($division, $invoiceno) {
		// Here's the function that creates a sales invoice
		// It only needs the division and invoice no.
		global $adb, $Account;
		// Get some more information from the invoice
		$IR = $adb->pquery('SELECT subject, invoicedate FROM vtiger_invoice WHERE invoice_no=?', array($invoiceno));
		$invoiceData = $adb->query_result_rowdata($IR,0);
		// Get the Account number related to this invoice
		$accountResultforInvoice = $adb->pquery('SELECT account_no FROM vtiger_account LEFT JOIN vtiger_invoice ON vtiger_account.accountid=vtiger_invoice.accountid WHERE vtiger_invoice.invoice_no=?',array($invoiceno));
		$AccNoForThisInvoice = $adb->query_result($accountResultforInvoice,0,'account_no');
		// Now exact wants to receive it's own 'GUID' code for this account.
		// We have a method for this in the 'Accounts' class, use it here
		$AccGuidForThisInv = $Account->getAccountGUID($division, $AccNoForThisInvoice);
		// Every invoice needs to send 'SalesInvoiceLines', the products that it lists
		$InvoiceLines = $this->getSalesInvoiceLines($division,$invoiceno);
		// Next we'll send a Post request, right now it is fixed using the Journal code '50',
		// This should be selectable in the future?
		
		// Setup the fields for the post request
		$SIpostfields = array(
			'Journal'			=>	'50',
			'OrderedBy'			=>	$AccGuidForThisInv,
			'InvoiceTo'			=>	$AccGuidForThisInv,
			'InvoiceDate'		=>	$invoiceData['invoicedate'],
			'Type'				=>	8020,
			'OrderNumber'		=>	$invoiceno,
			'Description'		=>	$invoiceData['subject'],
			'SalesInvoiceLines'	=>	$InvoiceLines
		);
		// Send the invoice
		$this->sendPostRequest('salesinvoice/SalesInvoices', $division, $SIpostfields);
	}
	
	public function getSalesInvoiceLines($division, $invoiceno) {
		global $adb;
		// Get the internal CRM id for the invoice
		$invoiceCrmIdResult = $adb->pquery('SELECT invoiceid FROM vtiger_invoice WHERE invoice_no=?', array($invoiceno));
		$invoiceCrmId = $adb->query_result($invoiceCrmIdResult,0,'invoiceid');
		// Get all inventory lines related to this invoice
		$IQ = 	'SELECT productid, quantity, listprice, discount_percent, discount_amount ';
		$IQ .= 	'FROM vtiger_inventoryproductrel WHERE id=?';
		$InventoryResults = $adb->pquery($IQ, array($invoiceCrmId));
		// Start an array for the inventorylines
		$SalesInvoiceLinesArray = array();
		while ($inventoryrow = $adb->fetch_array($InventoryResults)) {
			
			// We need some information from the Products module in coreBOS
			$PQ = $adb->pquery('SELECT product_no FROM vtiger_products WHERE productid=?', array($inventoryrow['productid']));
			$ProductCode = $adb->query_result($PQ,0,'product_no');
			// Could also be a service, Exact doesn't distinguish, but if we
			// get back an empty Product code from coreBOS, we should look in Services
			if ($ProductCode == "") {
				$SQ = $adb->pquery('SELECT service_no FROM vtiger_service WHERE serviceid=?', array($inventoryrow['productid']));
				$ProductCode = $adb->query_result($SQ,0,'service_no');
			}
			// Now we need to get the Exact GUID for this product code
			$ProductGUID = $this->getItemGUID($division, $ProductCode);
			
			// Now we need to do some calculation to get the actual selling price
			if ( $inventoryrow['discount_amount'] == NULL && $inventoryrow['discount_percent'] == NULL ) {
				// Product was sold for list price
				$sellingPrice = $inventoryrow['listprice'];
			} else if ( $inventoryrow['discount_amount'] != NULL ) {
				// Absolute amount was discounted
				$sellingPrice = ($inventoryrow['listprice'] - $inventoryrow['discount_amount']);
			} else if ( $inventoryrow['discount_percent'] != NULL ) {
				// Percentage discount was awarded
				$discountFactor = ( 100 - $inventoryrow['discount_percent'] ) / 100;
				$sellingPrice = $inventoryrow['listprice'] * $discountFactor;
			}
			
			// Now let's build the SalesInvoice Line
			$SalesInvoiceLineArray = array(
				'Item'			=>		$ProductGUID,
				'Quantity'		=>		$inventoryrow['quantity'],
				'UnitPrice'		=>		$sellingPrice,
				'VATCode'		=>		'02'
			);
			
			// Add this line to the SalesInvoiceLinesArray
			$SalesInvoiceLinesArray[] = $SalesInvoiceLineArray;
		}
		// Return the array with the SalesInvoiceLines
		return $SalesInvoiceLinesArray;
	}
	
	// This function should move to the Items class later on
	// It takes the division and a productcode to return a GUID
	public function getItemGUID($division, $productcode) {
		// Setup the filter
		$productFilter = array(
			'Code'	=>	$productcode
		);
		$ProductArray = $this->sendGetRequest('logistics/Items', $division, 'ID', $productFilter);
		return $ProductArray['d']['results'][0]['ID'];
	}
	
	// TEST function to see what 'Journals' are available
	public function journals($division) {
		return $this->sendGetRequest('financial/Journals', $division, 'Code,BankName,Description');
	}
	
	// TEST function to see what 'GLAccounts' are available
	public function glaccounts($division) {
		return $this->sendGetRequest('financial/GLAccounts', $division, 'ID,Code,Description');
	}
}

$SI = new ExactSalesInvoice();
?>