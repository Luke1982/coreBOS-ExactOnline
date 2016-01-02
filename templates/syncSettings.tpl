{*************************************************************************************************
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
*************************************************************************************************}

<script>
headjs=document.createElement('script');
headjs.src='modules/ExactOnline/ExactOnline.js';

document.getElementsByTagName('body')[0].appendChild(headjs);

headcss = document.createElement('link');
headcss.href = 'https://fonts.googleapis.com/css?family=Open+Sans';
headcss.type = 'text/css';
headcss.rel = 'stylesheet';

headcss2 = document.createElement('link');
headcss2.href = 'modules/ExactOnline/css/settings.css';
headcss2.type = 'text/css';
headcss2.rel = 'stylesheet';

document.getElementsByTagName('head')[0].appendChild(headcss);
document.getElementsByTagName('head')[0].appendChild(headcss2);

</script>

<div id="syncsettingspage">

		<p class="synccolumn">{$MOD.syncsettingsC1}</p>
		<p class="synccolumn">{$MOD.syncsettingsC2}</p>
		<p class="synccolumn">{$MOD.syncsettingsC3}</p>
		
		<div id="glaccounts_range">
			<label for="glaccounts_start">{$MOD.glaccounts_start}</label>
			<input type="text" size="10" name="glaccounts_start" id="glaccounts_start" value="{$gl_start_range}">
			<label for="glaccounts_stop">{$MOD.glaccounts_stop}</label>
			<input type="text" size="10" name="glaccounts_stop" id="glaccounts_stop" value="{$gl_stop_range}">
		</div>
		
		<div id="syncsettingsButtons">
			<div class="syncbutton" id="syncGLAccounts" data-alert="{$MOD.glaccountsalert}">
				{$MOD.syncglaccounts}
			</div>
			<div class="syncbutton" id="syncPaymentConds" data-alert="{$MOD.paymentcondsalert}">
				{$MOD.syncpaymentconds}
			</div>			
			<div class="syncbutton" id="sendAllProducts" data-alert="{$MOD.sendallproductsalert}">
				{$MOD.sendallproducts}
			</div>			
			<div class="syncbutton" id="sendAllServices" data-alert="{$MOD.sendallservicesalert}">
				{$MOD.sendallservices}
			</div>
			<div class="syncbutton" id="setGLAccountsRange" data-alert="{$MOD.setGLRangealert}">
				{$MOD.setGLRange}
			</div>
		</div>
		
</div>
