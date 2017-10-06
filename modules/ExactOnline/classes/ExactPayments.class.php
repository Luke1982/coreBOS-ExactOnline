<?php

Class ExactPayments extends ExactApi {

	private $acc_prefix = 'ACC';
	private $acc_code = 0;
	private $acc_crm_id = 0;
	private $payments = Array();

	function __construct() {

	}

	/**
	 * Updates the payments for an account provided
	 *
	 * @param int 		The division code for the administration
	 * @param string  	The account code
	 * @param string 	The account code prefix
	 **/
	public function updatePaymentsForAccount($division, $acc_code = 0, $prefix) {
		$this->acc_prefix = $prefix != '' ? $prefix : 'ACC'; // Default the account prefix to 'ACC' when none provided
		$this->acc_code = $acc_code;
		$this->setAccountCrmId();

		$exact_response = $this->sendXMLGetRequest($division, 'MatchSets', array('Params_AccountCode=' . $this->acc_code));

		return $exact_response;
		die();

		// Check if the MatchSet array has multiple items. The array is structured
		// differently when only one matchset is present
		if (isset($exact_response['MatchSets']['MatchSet'][0]['MatchLines'])) {
			foreach ($exact_response['MatchSets']['MatchSet'] as $matchset_no => $matchset) {
				$this->getMatchLinesFromMatchSet($matchset_no, $matchset);
			}
		} else {
			$this->getMatchLinesFromMatchSet(0, $exact_response['MatchSets']['MatchSet']);
		}

		$processed_payments = array();
		foreach ($this->payments as $payment) {
			$processed_payments[] = $this->createPayment($payment);
		}

		return $processed_payments;
	}

	private function getMatchLinesFromMatchSet($matchset_no, $matchset) {
		foreach ($matchset['MatchLines']['MatchLine'] as $matchline_no => $matchline) {
			if ($matchline['journal'] == 'VER' && (float)$matchline['amountdc'] > 0.02) {
				$payment = array();
				$payment['accountid'] 	= (int)$this->acc_crm_id;
				$payment['invoiceid']	= $this->getInvoiceCrmId($matchline['entry']);
				$payment['reference']	= $this->acc_prefix . $this->acc_code . '-' . $matchset_no . '-' . $matchline_no;
				$payment['amount']		= $matchline['amountdc'];
				$this->payments[] 		= $payment;
			}
		}
	}


	/**
	 * Gets the account ID from the database and sets it to the instance property
	 *
	 * @param 	string 	The account code, including the prefix
	 * @return 	int 	The Account CRM ID
	 *
	 **/
	private function setAccountCrmId() {
		global $adb;
		$r = $adb->pquery("SELECT accountid FROM vtiger_account WHERE account_no = ?", array($this->acc_prefix . $this->acc_code));
		if ($adb->num_rows($r) > 0) {
			$this->acc_crm_id = $adb->query_result($r, 'accountid', 0);
		}
	}

	/**
	 * Gets the invoice ID from the database
	 *
	 * @param 	string 	The invoice code
	 * @return 	int 	The invoice CRM ID
	 *
	 **/
	private function getInvoiceCrmId($invoice_no) {
		global $adb;
		$r = $adb->pquery("SELECT invoiceid FROM vtiger_invoice WHERE invoice_no = ?", array($invoice_no));
		if ($adb->num_rows($r) > 0) {
			return (int)$adb->query_result($r, 'invoiceid', 0);
		} else {
			return 0;
		}
	}


	/**
	 * Creates a payment (CoproPago) record for each payment
	 *
	 * @param 	(array) 	: Array with payment information
	 * @return 	(object) 	: The newly created payment record
	 *
	 */
	private function createPayment($payment) {
		if (!$this->checkIfPaymentExists($payment)) { // Check if this payment hasn't been entered already.
			global $current_user;
			require_once('modules/CobroPago/CobroPago.php');
			$p = new CobroPago();
			$p->mode = 'create';

			$p->column_fields['assigned_user_id'] 	= $current_user->id;
			$p->column_fields['reference'] 			= $payment['reference'];
			$p->column_fields['parent_id'] 			= $payment['accountid'];
			$p->column_fields['related_id'] 		= $payment['invoiceid'];
			$p->column_fields['paid'] 				= true;
			$p->column_fields['paymentmode'] 		= 'Bank';
			$p->column_fields['amount'] 			= $payment['amount'];
			$p->column_fields['benefit'] 			= $payment['amount'];
			$p->column_fields['description'] 		= 'ExactOnline';

			$handler = vtws_getModuleHandlerFromName('CobroPago', $current_user);
			$meta = $handler->getMeta();
			$p->column_fields = DataTransform::sanitizeRetrieveEntityInfo($p->column_fields, $meta);
			$p->save('CobroPago');

			return $payment;
		} else {
			return 'Payment already existed';
		}
	}

	/**
	 * Checks if a payment hasn't already been entered by it's reference and invoice no
	 *
	 * @param 	(array) 	: Array with payment information
	 * @return 	(bool) 		: true is the payment exists, false if not
	 *
	 */
	private function checkIfPaymentExists($payment) {
		global $adb;
		$r = $adb->pquery("SELECT * FROM vtiger_cobropago INNER JOIN vtiger_crmentity ON vtiger_cobropago.cobropagoid=vtiger_crmentity.crmid WHERE vtiger_cobropago.related_id = ? AND vtiger_cobropago.reference = ? AND vtiger_crmentity.deleted = ?", array((int)$payment['invoiceid'], (string)$payment['reference'], '0'));
		if ($adb->num_rows($r) > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Checks if a payment has an invoice
	 *
	 * @param 	(array) 	: Array with payment information
	 * @return 	(bool) 		: true is the payment has an invoice id, false if not
	 *
	 */
	private function hasInvoice($payment) {
		if ($payment['invoiceid'] == 0) {
			return false;
		} else {
			return true;
		}
	}

}