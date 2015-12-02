<?php

error_reporting(E_ALL);
ini_set("display_errors", "on");

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
$module = Vtiger_Module::getInstance('ExactOnline');
$infoBlock = Vtiger_Block::getInstance('LBL_EXACTONLINE_INFORMATION', $module);
$responseField = Vtiger_Field::getInstance('exactresponse', $module);
if (!$responseField) {
	$responseField = new Vtiger_Field();
	$responseField->name = 'exactresponse';
	$responseField->label = 'Response from Exact';
	$responseField->column = 'exactresponse';
	$responseField->columntype = 'VARCHAR(1024)';
	$responseField->uitype = 19;
	$responseField->typeofdata = 'V~M';
	$infoBlock->addField($responseField);
}

?>