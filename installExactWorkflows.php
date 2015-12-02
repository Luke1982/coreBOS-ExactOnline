<?php
// Turn on debugging level
$Vtiger_Utils_Log = true;

require_once 'include/utils/utils.php';
include_once('vtlib/Vtiger/Module.php');
require 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
global $adb;

// $emm = new VTEntityMethodManager($adb);
// $emm->addEntityMethod("Accounts", "Send Account to Exact Online", "modules/ExactOnline/workflows/SendAccount.exactworkflow.php", "sendAccountToExact");
// echo "Workflow for Accounts installed <br>";

// $emm2 = new VTEntityMethodManager($adb);
// $emm2->addEntityMethod("Products", "Send Product to Exact Online", "modules/ExactOnline/handleAPI.php", "sendProductToExact");
// echo "Workflow for Products installed <br>";

// $emm3 = new VTEntityMethodManager($adb);
// $emm3->addEntityMethod("Services", "Send Service to Exact Online", "modules/ExactOnline/handleAPI.php", "sendServiceToExact");
// echo "Workflow for Products installed <br>";

// $emm4 = new VTEntityMethodManager($adb);
// $emm4->addEntityMethod("Products", "Sync GL Accounts with products module", "modules/ExactOnline/handleAPI.php", "syncGLAccounts");
// echo "Workflow for Syncing GL accounts installed <br>";

$emm5 = new VTEntityMethodManager($adb);
$emm5->addEntityMethod("Invoice", "Send Invoice to Exact Online", "modules/ExactOnline/handleAPI.php", "sendInvoiceToExact");
echo "Workflow for sending Invoices to Exact Online installed <br>";

?>