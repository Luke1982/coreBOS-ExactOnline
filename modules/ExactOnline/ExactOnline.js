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

jQuery(window).load(function(){
	
	// Function to save the values obtained from the first run
	var saveFirstRun = function() {
		jQuery.ajax({
			type: "POST",
			data:	{
				'clientID'		:	jQuery('input#clientID').val(),
				'clientsecret'	:	jQuery('input#clientsecret').val(),
				'countryurl'	:	jQuery('input#countryurl').val(),
				'firstrunsave'	:	true
			},
			success: alert('Settings Saved')
		});
	}
	
	jQuery('#saveFirstRun').click(function(){
		saveFirstRun();
	});
	
	// Function to save ALL values in the normal settings screen
	var saveCreds = function() {
		jQuery.ajax({
			type: "POST",
			data:	{
				'division'		:	jQuery('input#division').val(),
				'clientID'		:	jQuery('input#clientID').val(),
				'clientsecret'	:	jQuery('input#clientsecret').val(),
				'returnurl'		:	jQuery('textarea#returnurl').val(),
				'authurl'		:	jQuery('input#authurl').val(),
				'tokenurl'		:	jQuery('input#tokenurl').val(),
				'apiurl'		:	jQuery('input#apiurl').val(),
				'save'			:	true
			},
			success: alert('Settings Saved')
		});
	}
	
	jQuery('#savesettings').click(function(){
		saveCreds();
	});
	
	// Function to autofill the division field
	function getDivision() {
		return jQuery.ajax({
			url		:	'index.php?module=ExactOnline&action=ExactOnlineAjax&file=handleAPI&getdivision=1',
			success	:	function(data) {
				jQuery('#division').val(data);
			}
		});
	}
	
	jQuery('#getdivision').click(function(){
		getDivision();
	});
	
	// Some jQuery to control the panel slides on the first run
	jQuery('#firstrunnext').click(function(){
		jQuery('#firstrunpanelscontainer').animate({
			'left'	:	'-=900'
		});
	});
	jQuery('#firstrunprev').click(function(){
		jQuery('#firstrunpanelscontainer').animate({
			'left'	:	'+=900'
		});
	});
	
	// Make the division input border red to get attention for the fact
	// That it needs to be filled if it's empty
	setInterval(divisionBorderRed(), 500);
	
	function divisionBorderRed(){
		if (jQuery('input#division').val() == '') {
			jQuery('input#division').css('border','1px solid red');
		}
	}
	
	// Function to reload the first run, effectively setting the
	// 'refreshedtime' in the exact settings table to 0 (zero)
	var reloadFirstRun = function() {
		jQuery.ajax({
			type: "POST",
			data:	{
				'reloadfirstrun'	:	true
			},
			success: alert('First Run Reset, please reload this page.')
		});
	}
	
	jQuery('#reloadfirstrun').click(function(){
		reloadFirstRun();
	});
	
	// Function to get the General Ledgers from Exact
	// This launches a function in the handleAPI file
	// That in it's turn launches a method of the Items class
	jQuery('#syncGLAccounts').click(function(){
		syncGLAccounts();
	});
	var syncGLAccounts = function() {
		jQuery.ajax({
			type: 	'POST',
			url:	'index.php?module=ExactOnline&action=ExactOnlineAjax&file=handleAPI&syncglaccounts=1',
			success: alert(jQuery('#syncGLAccounts').data('alert'))
		});
	}	
	// Function to get the Payment Conditions from Exact
	// This launches a function in the handleAPI file
	// That in it's turn launches a method of the PaymentConds class
	jQuery('#syncPaymentConds').click(function(){
		syncPaymentConds();
	});
	var syncPaymentConds = function() {
		jQuery.ajax({
			type: 	'POST',
			url:	'index.php?module=ExactOnline&action=ExactOnlineAjax&file=handleAPI&updatepaymentconds=1',
			success: alert(jQuery('#syncPaymentConds').data('alert'))
		});
	}
	// Function for starting the 'send all products' method in the handleAPI file
	jQuery('#sendAllProducts').click(function(){
		sendAllProducts();
	});
	var sendAllProducts = function() {
		jQuery.ajax({
			type: 	'POST',
			url:	'index.php?module=ExactOnline&action=ExactOnlineAjax&file=handleAPI&sendallproducts=1',
			success: alert(jQuery('#sendAllProducts').data('alert'))
		});
	}
	// Function for starting the 'send all services' method in the handleAPI file
	jQuery('#sendAllServices').click(function(){
		sendAllServices();
	});
	var sendAllServices = function() {
		jQuery.ajax({
			type: 	'POST',
			url:	'index.php?module=ExactOnline&action=ExactOnlineAjax&file=handleAPI&sendallservices=1',
			success: alert(jQuery('#sendAllServices').data('alert'))
		});
	}
	
	jQuery('#setGLAccountsRange').click(function(){
		jQuery.ajax({
			type:		'POST',
			data:		{
				'GLstart' 	: jQuery('#glaccounts_start').val(),
				'GLstop'	: jQuery('#glaccounts_stop').val(),
				'setGLrange': true
			},
			success:	alert(jQuery('#setGLAccountsRange').data('alert'))
		});
	});

});
