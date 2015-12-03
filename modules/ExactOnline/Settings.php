<?php
/*************************************************************************************************
 * Copyright 2015 MajorLabel -- This file is a part of MajorLabel coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. MajorLabel reserves all rights not expressly
 * granted by the License. coreBOS distributed by MajorLabel is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************
*  Author       : MajorLabel, Guido Goluke
*************************************************************************************************/

require_once('Smarty_setup.php');
require_once("include/utils/utils.php");
require_once("modules/com_vtiger_workflow/VTWorkflowUtils.php");

global $mod_strings, $app_strings, $theme, $adb, $current_user;
$smarty = new vtigerCRM_Smarty;
$smarty->assign('MOD',$mod_strings);
$smarty->assign('APP',$app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

// Operation to be restricted for non-admin users.
if(!is_admin($current_user)) {	
	$smarty->display(vtlib_getModuleTemplate('Vtiger','OperationNotPermitted.tpl'));	
} else {
	$module = vtlib_purify($_REQUEST['formodule']);

	$menu_array = Array();

	// We don't need layout editor
	
	// $menu_array['LayoutEditor']['location'] = 'index.php?module=Settings&action=LayoutBlockList&parenttab=Settings&formodule='.$module;
	// $menu_array['LayoutEditor']['image_src'] = 'themes/images/orgshar.gif';
	// $menu_array['LayoutEditor']['desc'] = getTranslatedString('LBL_LAYOUT_EDITOR_DESCRIPTION');
	// $menu_array['LayoutEditor']['label'] = getTranslatedString('LBL_LAYOUT_EDITOR');
	
	// We don't need layout editor
	
	// if(vtlib_isModuleActive('FieldFormulas')) {
		// $modules = com_vtGetModules($adb);
		// if(in_array(getTranslatedString($module),$modules)) {
			// $sql_result = $adb->pquery("select * from vtiger_settings_field where name = ? and active=0",array('LBL_FIELDFORMULAS'));
			// if($adb->num_rows($sql_result) > 0) {
				// $menu_array['FieldFormulas']['location'] = $adb->query_result($sql_result, 0, 'linkto').'&formodule='.$module;
				// $menu_array['FieldFormulas']['image_src'] = $adb->query_result($sql_result, 0, 'iconpath');
				// $menu_array['FieldFormulas']['desc'] = getTranslatedString($adb->query_result($sql_result, 0, 'description'),'FieldFormulas');
				// $menu_array['FieldFormulas']['label'] = getTranslatedString($adb->query_result($sql_result, 0, 'name'),'FieldFormulas');
			// }
		// }
	// }
	
	// if(vtlib_isModuleActive('Tooltip')){
		// $sql_result = $adb->pquery("select * from vtiger_settings_field where name = ? and active=0",array('LBL_TOOLTIP_MANAGEMENT'));
		// if($adb->num_rows($sql_result) > 0) {
			// $menu_array['Tooltip']['location'] = $adb->query_result($sql_result, 0, 'linkto').'&formodule='.$module;
			// $menu_array['Tooltip']['image_src'] = vtiger_imageurl($adb->query_result($sql_result, 0, 'iconpath'), $theme);
			// $menu_array['Tooltip']['desc'] = getTranslatedString($adb->query_result($sql_result, 0, 'description'),'Tooltip');
			// $menu_array['Tooltip']['label'] = getTranslatedString($adb->query_result($sql_result, 0, 'name'),'Tooltip');
		// }
	// }

	// if(VTWorkflowUtils::checkModuleWorkflow($module)){
		// $sql_result = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name = ? AND active=0",array('LBL_WORKFLOW_LIST'));
		// if($adb->num_rows($sql_result) > 0) {
			// $menu_array['Workflow']['location'] = $adb->query_result($sql_result, 0, 'linkto').'&list_module='.$module;
			// $menu_array['Workflow']['image_src'] = vtiger_imageurl($adb->query_result($sql_result, 0, 'iconpath'), $theme);
			// $menu_array['Workflow']['desc'] = getTranslatedString($adb->query_result($sql_result, 0, 'description'),'com_vtiger_workflow');
			// $menu_array['Workflow']['label'] = getTranslatedString($adb->query_result($sql_result, 0, 'name'),'com_vtiger_workflow');
		// }
	// }
	

	$menu_array['SetCredentials']['location'] = 'index.php?module=ExactOnline&action=SetCredentials';
	$menu_array['SetCredentials']['image_src'] = 'modules/ExactOnline/images/setexactcredIcon.png';
	$menu_array['SetCredentials']['desc'] = getTranslatedString('SetCredentials','SetCredentials');
	$menu_array['SetCredentials']['label'] = getTranslatedString('SetCredentials');

	
	
	//add blanks for 3-column layout
	$count = count($menu_array)%3;
	if($count>0) {
		for($i=0;$i<3-$count;$i++) {
			$menu_array[] = array();
		}
	}

	$smarty->assign('MODULE',$module);
	$smarty->assign('MODULE_LBL',getTranslatedString($module,$module));
	$smarty->assign('MENU_ARRAY', $menu_array);

	$smarty->display(vtlib_getModuleTemplate('Vtiger','Settings.tpl'));
}
?>