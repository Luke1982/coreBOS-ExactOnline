<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class ExactOnline extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'vtiger_exactonline';
	var $table_index= 'exactonlineid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = true;
	var $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_exactonlinecf', 'exactonlineid');
	// Uncomment the line below to support custom field columns on related lists
	// var $related_tables = Array('vtiger_exactonlinecf'=>array('exactonlineid','vtiger_exactonline', 'exactonlineid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_exactonline', 'vtiger_exactonlinecf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_exactonline'   => 'exactonlineid',
		'vtiger_exactonlinecf' => 'exactonlineid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'ExactOnline Record No'=> Array('exactonline' => 'exactrecordno'),
		'Assigned To' => Array('crmentity' => 'smownerid')
	);
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'ExactOnline Record Name'=> 'exactrecordname',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'exactrecordname';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'ExactOnline Record Name'=> Array('exactonline' => 'exactrecordname')
	);
	var $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'ExactOnline Record Name'=> 'exactrecordname'
	);

	// For Popup window record selection
	var $popup_fields = Array('exactrecordname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'exactrecordname';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'exactrecordname';

	// Required Information for enabling Import feature
	var $required_fields = Array('exactrecordname'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'exactrecordname';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'exactrecordname');

	function __construct() {
		global $log;
		$this_module = get_class($this);
		$this->column_fields = getColumnFields($this_module);
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
		$sql = 'SELECT 1 FROM vtiger_field WHERE uitype=69 and tabid = ?';
		$tabid = getTabid($this_module);
		$result = $this->db->pquery($sql, array($tabid));
		if ($result and $this->db->num_rows($result)==1) {
			$this->HasDirectImageField = true;
		}
	}

	function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id,$module);
		}
	}

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	function getQueryByModuleField($module, $fieldname, $srcrecord, $query='') {
		// $srcrecord could be empty
	}

	/**
	 * Get list view query (send more WHERE clause condition if required)
	 */
	function getListQuery($module, $usewhere='') {
		$query = "SELECT vtiger_crmentity.*, $this->table_name.*";

		// Keep track of tables joined to avoid duplicates
		$joinedTables = array();

		// Select Custom Field Table Columns if present
		if(!empty($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$query .= " FROM $this->table_name";

		$query .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		$joinedTables[] = $this->table_name;
		$joinedTables[] = 'vtiger_crmentity';

		// Consider custom table join as well.
		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				" = $this->table_name.$this->table_index";
			$joinedTables[] = $this->customFieldTable[0];
		}
		$query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		$joinedTables[] = 'vtiger_users';
		$joinedTables[] = 'vtiger_groups';

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
				" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
				" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($module));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other = CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);

			if(!in_array($other->table_name, $joinedTables)) {
				$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
				$joinedTables[] = $other->table_name;
			}
		}

		global $current_user;
		$query .= $this->getNonAdminAccessControlQuery($module,$current_user);
		$query .= "	WHERE vtiger_crmentity.deleted = 0 ".$usewhere;
		return $query;
	}

	/**
	 * Apply security restriction (sharing privilege) query part for List view.
	 */
	function getListViewSecurityParameter($module) {
		global $current_user;
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

		$sec_query = '';
		$tabid = getTabid($module);

		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1
			&& $defaultOrgSharingPermission[$tabid] == 3) {

				$sec_query .= " AND (vtiger_crmentity.smownerid in($current_user->id) OR vtiger_crmentity.smownerid IN 
					(
						SELECT vtiger_user2role.userid FROM vtiger_user2role 
						INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid 
						INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid 
						WHERE vtiger_role.parentrole LIKE '".$current_user_parent_role_seq."::%'
					) 
					OR vtiger_crmentity.smownerid IN 
					(
						SELECT shareduserid FROM vtiger_tmp_read_user_sharing_per 
						WHERE userid=".$current_user->id." AND tabid=".$tabid."
					) 
					OR (";

					// Build the query based on the group association of current user.
					if(sizeof($current_user_groups) > 0) {
						$sec_query .= " vtiger_groups.groupid IN (". implode(",", $current_user_groups) .") OR ";
					}
					$sec_query .= " vtiger_groups.groupid IN 
						(
							SELECT vtiger_tmp_read_group_sharing_per.sharedgroupid 
							FROM vtiger_tmp_read_group_sharing_per
							WHERE userid=".$current_user->id." and tabid=".$tabid."
						)";
				$sec_query .= ")
				)";
		}
		return $sec_query;
	}

	/**
	 * Create query to export the records.
	 */
	function create_export_query($where)
	{
		global $current_user;
		$thismodule = $_REQUEST['module'];

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery($thismodule, "detail_view");

		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, vtiger_users.user_name AS user_name 
				FROM vtiger_crmentity INNER JOIN $this->table_name ON vtiger_crmentity.crmid=$this->table_name.$this->table_index";

		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				" = $this->table_name.$this->table_index";
		}

		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id and vtiger_users.status='Active'";

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
				" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
				" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($thismodule));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		$rel_mods[$this->table_name] = 1;
		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other = CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);

			if($rel_mods[$other->table_name]) {
				$rel_mods[$other->table_name] = $rel_mods[$other->table_name] + 1;
				$alias = $other->table_name.$rel_mods[$other->table_name];
				$query_append = "as $alias";
			} else {
				$alias = $other->table_name;
				$query_append = '';
				$rel_mods[$other->table_name] = 1;
			}

			$query .= " LEFT JOIN $other->table_name $query_append ON $alias.$other->table_index = $this->table_name.$columnname";
		}

		$query .= $this->getNonAdminAccessControlQuery($thismodule,$current_user);
		$where_auto = " vtiger_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";

		return $query;
	}

	/**
	 * Initialize this instance for importing.
	 */
	function initImport($module) {
		$this->db = PearDatabase::getInstance();
		$this->initImportableFields($module);
	}

	/**
	 * Create list query to be shown at the last step of the import.
	 * Called From: modules/Import/UserLastImport.php
	 */
	function create_import_query($module) {
		global $current_user;
		$query = "SELECT vtiger_crmentity.crmid, case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name, $this->table_name.* FROM $this->table_name
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index
			LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_crmentity.crmid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_users_last_import.assigned_user_id='$current_user->id'
			AND vtiger_users_last_import.bean_type='$module'
			AND vtiger_users_last_import.deleted=0";
		return $query;
	}

	/**
	 * Delete the last imported records.
	 */
	function undo_import($module, $user_id) {
		global $adb;
		$count = 0;
		$query1 = "select bean_id from vtiger_users_last_import where assigned_user_id=? AND bean_type='$module' AND deleted=0";
		$result1 = $adb->pquery($query1, array($user_id)) or die("Error getting last import for undo: ".mysql_error());
		while ( $row1 = $adb->fetchByAssoc($result1))
		{
			$query2 = "update vtiger_crmentity set deleted=1 where crmid=?";
			$result2 = $adb->pquery($query2, array($row1['bean_id'])) or die("Error undoing last import: ".mysql_error());
			$count++;
		}
		return $count;
	}

	/**
	 * Transform the value while exporting
	 */
	function transform_export_value($key, $value) {
		return parent::transform_export_value($key, $value);
	}

	/**
	 * Function which will set the assigned user id for import record.
	 */
	function set_import_assigned_user()
	{
		global $current_user, $adb;
		$record_user = $this->column_fields["assigned_user_id"];

		if($record_user != $current_user->id){
			$sqlresult = $adb->pquery("select id from vtiger_users where id = ? union select groupid as id from vtiger_groups where groupid = ?", array($record_user, $record_user));
			if($this->db->num_rows($sqlresult)!= 1) {
				$this->column_fields["assigned_user_id"] = $current_user->id;
			} else {
				$row = $adb->fetchByAssoc($sqlresult, -1, false);
				if (isset($row['id']) && $row['id'] != -1) {
					$this->column_fields["assigned_user_id"] = $row['id'];
				} else {
					$this->column_fields["assigned_user_id"] = $current_user->id;
				}
			}
		}
	}

	/**
	 * Function which will give the basic query to find duplicates
	 */
	function getDuplicatesQuery($module,$table_cols,$field_values,$ui_type_arr,$select_cols='') {
		$select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, vtiger_users_last_import.deleted,".$table_cols;

		// Select Custom Field Table Columns if present
		if(isset($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$from_clause = " FROM $this->table_name";

		$from_clause .= " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		// Consider custom table join as well.
		if(isset($this->customFieldTable)) {
			$from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				" = $this->table_name.$this->table_index";
		}
		$from_clause .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		$where_clause = " WHERE vtiger_crmentity.deleted = 0";
		$where_clause .= $this->getListViewSecurityParameter($module);

		if (isset($select_cols) && trim($select_cols) != '') {
			$sub_query = "SELECT $select_cols FROM $this->table_name AS t " .
				" INNER JOIN vtiger_crmentity AS crm ON crm.crmid = t.".$this->table_index;
			// Consider custom table join as well.
			if(isset($this->customFieldTable)) {
				$sub_query .= " LEFT JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
			}
			$sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";
		} else {
			$sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
		}

		$query = $select_clause . $from_clause .
					" LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
					" INNER JOIN (" . $sub_query . ") AS temp ON ".get_on_clause($field_values,$ui_type_arr,$module) .
					$where_clause .
					" ORDER BY $table_cols,". $this->table_name .".".$this->table_index ." ASC";

		return $query;
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		if($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'EO-', '0000001');
			
			// Create the workflow task entity 
			include_once 'include/utils/utils.php';
			include_once('vtlib/Vtiger/Module.php');
			require 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
			global $adb;
			
			// Create the row in the settings table
			$adb->query("INSERT INTO `vtiger_exactonline_settings` (`exactonlineid`, `exactauthurl`, `exacttokenurl`, `exactapiurl`, `exactclientid`, `exactsecret`, `exactreturnurl`, `access_token`, `refresh_token`, `exactrefreshedtime`, `exactdivision`) VALUES ('0', 'https://start.exactonline.nl/api/oauth2/auth', 'https://start.exactonline.nl/api/oauth2/token', 'https://start.exactonline.nl/api/v1/', NULL, NULL, NULL, NULL, NULL, '0', NULL)");
			
			$emm = new VTEntityMethodManager($adb);
			$emm->addEntityMethod("Accounts", "Send Account to Exact Online", "modules/ExactOnline/handleAPI.php", "sendAccountToExact");

			$emm2 = new VTEntityMethodManager($adb);
			$emm2->addEntityMethod("Products", "Send Product to Exact Online", "modules/ExactOnline/handleAPI.php", "sendProductToExact");

			$emm3 = new VTEntityMethodManager($adb);
			$emm3->addEntityMethod("Services", "Send Service to Exact Online", "modules/ExactOnline/handleAPI.php", "sendServiceToExact");


			$emm4 = new VTEntityMethodManager($adb);
			$emm4->addEntityMethod("Products", "Sync GL Accounts with coreBOS", "modules/ExactOnline/handleAPI.php", "syncGLAccounts");

			$emm5 = new VTEntityMethodManager($adb);
			$emm5->addEntityMethod("Invoice", "Send Invoice to Exact Online", "modules/ExactOnline/handleAPI.php", "sendInvoiceToExact");

			$emm6 = new VTEntityMethodManager($adb);
			$emm6->addEntityMethod("Invoice", "Synchronize Payment Conditions with Exact", "modules/ExactOnline/handleAPI.php", "updatePaymentConditions");
			
			// Create the workflow tasks?? Is this possible?
			
			// Create the field for General Ledgers in products module
			// This should auto-create the table
			
			// Get the Products module
			$module = Vtiger_Module::getInstance('Products');
			// Get the main info block for products
			$productsInfoBlock					= 	Vtiger_Block::getInstance('LBL_PRODUCT_INFORMATION', $module);
			
			// Setup the field
			$glaccProducts						=	new Vtiger_Field();
			$glaccProducts->name				=	'exact_glaccounts';
			$glaccProducts->label				=	'General Ledger Accounts';
			$glaccProducts->table				=	'vtiger_products';
			$glaccProducts->column				=	'generalledgers';
			$glaccProducts->columntype			=	'VARCHAR(100)';
			$glaccProducts->uitype				=	16;
			$glaccProducts->typeofdata			=	'V~M';
		
			// Now add the field instance to the Products block instance
			$productsInfoBlock->addField($glaccProducts);

			// Only temp values for the dropdown, Workflow task will sync this with Exact later
			$glaccProducts->setPicklistValues( array('GLAccount1', 'GLAccount2') );
			
			// Setup the same field in Services, give it the same name so
			// we can use the same database table for the values
			$module = Vtiger_Module::getInstance('Services');
			// Get the main info block for services
			$servicesInfoBlock					= 	Vtiger_Block::getInstance('LBL_SERVICE_INFORMATION', $module);
			
			// Setup the field
			$glaccServices						=	new Vtiger_Field();
			$glaccServices->name				=	'exact_glaccounts';
			$glaccServices->label				=	'General Ledger Accounts';
			$glaccServices->table				=	'vtiger_service';
			$glaccServices->column				=	'generalledgers';
			$glaccServices->columntype			=	'VARCHAR(100)';
			$glaccServices->uitype				=	16;
			$glaccServices->typeofdata			=	'V~M';
		
			// Now add the field instance to the Products block instance
			$servicesInfoBlock->addField($glaccServices);		
			
			// Only temp values for the dropdown, Workflow task will sync this with Exact later
			$glaccServices->setPicklistValues( array('GLAccount1', 'GLAccount2') );			
			
			// Setup a field for the VAT-codes, exact will want these in it's own
			// code format
			$module = Vtiger_Module::getInstance('Products');
			// Get the pricing info block for products
			$productsPricingBlock					= 	Vtiger_Block::getInstance('LBL_PRICING_INFORMATION', $module);
			
			// Setup the field
			$productsVATCodesField					=	new Vtiger_Field();
			$productsVATCodesField->name			=	'exact_vatcodes';
			$productsVATCodesField->label			=	'Exact VAT Codes';
			$productsVATCodesField->table			=	'vtiger_products';
			$productsVATCodesField->column			=	'exact_vatcodes';
			$productsVATCodesField->columntype		=	'VARCHAR(100)';
			$productsVATCodesField->uitype			=	16;
			$productsVATCodesField->typeofdata		=	'V~M';
			// Set it as 'mass editable', which you'll probably want to do after
			// first installation of the module.
			$productsVATCodesField->masseditable	=	1;
		
			// Now add the field instance to the Products pricingblock instance
			$productsPricingBlock->addField($productsVATCodesField);		
			
			// Add the picklist values, with a maximum of 7 different choices
			// The user should set up his VAT codes in Exact
			$productsVATCodesField->setPicklistValues( array('01','02','03','04','05','06','07') );			
			
			// Setup a field for the VAT-codes in Services, using the same dropdown-table
			// As the VAT code field in Services will have
			$module = Vtiger_Module::getInstance('Services');
			// Get the pricing info block for products
			$servicesPricingBlock					= 	Vtiger_Block::getInstance('LBL_PRICING_INFORMATION', $module);
			
			// Setup the field
			$servicesVATCodesField					=	new Vtiger_Field();
			$servicesVATCodesField->name			=	'exact_vatcodes';
			$servicesVATCodesField->label			=	'Exact VAT Codes';
			$servicesVATCodesField->table			=	'vtiger_service';
			$servicesVATCodesField->column			=	'exact_vatcodes';
			$servicesVATCodesField->columntype		=	'VARCHAR(100)';
			$servicesVATCodesField->uitype			=	16;
			$servicesVATCodesField->typeofdata		=	'V~M';
			// Set it as 'mass editable', which you'll probably want to do after
			// first installation of the module.
			$servicesVATCodesField->masseditable	=	1;
		
			// Now add the field instance to the Products pricingblock instance
			$servicesPricingBlock->addField($servicesVATCodesField);		
			
			// Add the picklist values, with a maximum of 7 different choices
			// The user should set up his VAT codes in Exact
			$servicesVATCodesField->setPicklistValues( array('01','02','03','04','05','06','07') );
			
			// Setup a dropdown of VAT codes in Accounts. This will override the VAT codes
			// from the product or service when selected.
			$module = Vtiger_Module::getInstance('Accounts');
			$accountsBlock = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $module);

			$accountsVatDropdown = new Vtiger_Field();
			$accountsVatDropdown->name = 'exact_acc_vat';
			$accountsVatDropdown->label	= 'Exact VAT Code for Account';
			$accountsVatDropdown->table = 'vtiger_account';
			$accountsVatDropdown->column = 'exact_acc_vat';
			$accountsVatDropdown->columtype = 'VARCHAR(100)';
			$accountsVatDropdown->uitype = 16;
			$accountsVatDropdown->typeofdata = 'V~O';
			$accountsVatDropdown->masseditable = 1;

			$accountsBlock->addField($accountsVatDropdown);

			$accountsVatDropdown->setPicklistValues( array('--','01','02','03','04','05','06','07','08','09','10','11') );
			
			// Setup a field for the Payment Conditions in the Invoices module
			//exact will want these in it's own code format
			$module = Vtiger_Module::getInstance('Invoice');
			// Get the pricing info block for products
			$invoiceDescriptionBlock				= 	Vtiger_Block::getInstance('LBL_INVOICE_INFORMATION', $module);
			
			// Setup the field
			$invoicePaymentCondField				=	new Vtiger_Field();
			$invoicePaymentCondField->name			=	'exact_payment_cond';
			$invoicePaymentCondField->label			=	'Exact Payment Condition';
			$invoicePaymentCondField->table			=	'vtiger_invoice';
			$invoicePaymentCondField->column		=	'exact_payment_cond';
			$invoicePaymentCondField->columntype	=	'VARCHAR(100)';
			$invoicePaymentCondField->uitype		=	16;
			$invoicePaymentCondField->typeofdata	=	'V~M';
			// Now add the field instance to the Products pricingblock instance
			$invoiceDescriptionBlock->addField($invoicePaymentCondField);		
			
			// Add some dummy picklist values, will be synced with Exact when the
			// Module is authenticated
			$invoicePaymentCondField->setPicklistValues( array('Condition1','Condition2') );

		} else if($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} else if($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
			$moduleInstance = Vtiger_Module::getInstance('ExactOnline');
			if ($moduleInstance->version == "0.88") {
				updateFromVersionZeroPointEightSeven();
			}
		}
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	public static function convertResponseToArray($xml) {
		$xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
		$json = json_encode($xml);
		return json_decode($json, true);
	}
}

function updateFromVersionZeroPointEightSeven() {
	// Setup a field for the Payment Conditions in the Accounts module
	//exact will want these in it's own code format
	$module = Vtiger_Module::getInstance('Accounts');
	// Get the pricing info block for accounts
	$accountsDescriptionBlock				= 	Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $module);
	
	// Setup the field
	$accountsPaymentCondField				=	new Vtiger_Field();
	$accountsPaymentCondField->name			=	'exact_payment_cond';
	$accountsPaymentCondField->label		=	'Exact Payment Condition for Account';
	$accountsPaymentCondField->table		=	'vtiger_account';
	$accountsPaymentCondField->column		=	'exact_payment_cond';
	$accountsPaymentCondField->columntype	=	'VARCHAR(100)';
	$accountsPaymentCondField->uitype		=	16;
	$accountsPaymentCondField->typeofdata	=	'V~M';

	$accountsDescriptionBlock->addField($accountsPaymentCondField);		
	
	// Add some dummy picklist values, will be synced with Exact when the
	// Module is authenticated
	$accountsPaymentCondField->setPicklistValues( array('Condition1','Condition2') );

	// Setup a field for the Payment Conditions in the Salesorder module
	// exact will want these in it's own code format
	$module = Vtiger_Module::getInstance('SalesOrder');
	// Get the pricing info block for Salesorders
	$soDescriptionBlock				= 	Vtiger_Block::getInstance('LBL_SO_INFORMATION', $module);
	
	// Setup the field
	$soPaymentCondField				=	new Vtiger_Field();
	$soPaymentCondField->name		=	'exact_payment_cond';
	$soPaymentCondField->label		=	'Exact Payment Condition for SalesOrders';
	$soPaymentCondField->table		=	'vtiger_salesorder';
	$soPaymentCondField->column		=	'exact_payment_cond';
	$soPaymentCondField->columntype	=	'VARCHAR(100)';
	$soPaymentCondField->uitype		=	16;
	$soPaymentCondField->typeofdata	=	'V~M';

	$soDescriptionBlock->addField($soPaymentCondField);		
	
	// Add some dummy picklist values, will be synced with Exact when the
	// Module is authenticated
	$soPaymentCondField->setPicklistValues( array('Condition1','Condition2') );

	// Setup a field for the Payment Conditions in the Quotes module
	// exact will want these in it's own code format
	$module = Vtiger_Module::getInstance('Quotes');
	// Get the pricing info block for Salesorders
	$quDescriptionBlock				= 	Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $module);
	
	// Setup the field
	$quPaymentCondField				=	new Vtiger_Field();
	$quPaymentCondField->name		=	'exact_payment_cond';
	$quPaymentCondField->label		=	'Exact Payment Condition for Quotes';
	$quPaymentCondField->table		=	'vtiger_quotes';
	$quPaymentCondField->column		=	'exact_payment_cond';
	$quPaymentCondField->columntype	=	'VARCHAR(100)';
	$quPaymentCondField->uitype		=	16;
	$quPaymentCondField->typeofdata	=	'V~M';

	$quDescriptionBlock->addField($quPaymentCondField);		
	
	// Add some dummy picklist values, will be synced with Exact when the
	// Module is authenticated
	$quPaymentCondField->setPicklistValues( array('Condition1','Condition2') );			
}
?>
