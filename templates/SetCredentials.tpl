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
</script>

<div style="width: 90%; margin: 20px 0 0 5%;">
	<h2>Exact Online Settings page</h2>

	<p>Go to the APP centre at <a href="http://apps.exactonline.nl" target="_blank">apps.exactonline.nl</a> and register an app. You will need to fill out a return URL. That needs to look like this:<span style="color: red">http://{$servername}/index.php?module=ExactOnline&action=ExactOnlineAjax&file=handleAPI</span></p>
	<p>After you've registered your app, come back here and paste:</p>
	<ul>
		<li>Your Client ID</li>
		<li>Your Client Secret</li>
		<li>Your division code. To see what this is, go to <a href="http://start.exactonline.nl/api/v1/current/Me?$select=CurrentDivision" target="_blank">http://start.exactonline.nl/api/v1/current/Me?$select=CurrentDivision</a> <b>while logged in to exact</b></li>
	</ul>

	{capture name='suggestedreturnurl'}http://{$servername}/index.php?module=ExactOnline&action=ExactOnlineAjax&file=handleAPI{/capture}

	<form id="setCredentialsForm" name="setExactCredentials" method="POST" action="">
	<table style="width: 90%; margin: 20px 0 0 5%">
		<tbody>
			<tr>
				<td class="test">Division:</td>
				<td><input size="100" id="division" name="division" value="{$division}" type="text"></td>
				<td class="test">Return URL:</td>
				<td><input size="100" id="returnurl" name="returnurl" value="{$smarty.capture.suggestedreturnurl}" type="text" readonly></td>
			</tr>		
			<tr>
				<td class="test">Client ID:</td>
				<td><input size="100" id="clientID" name="clientID" value="{$clientID}" type="text"></td>
				<td class="test">Client Secret:</td>
				<td><input size="100" id="clientsecret" name="clientsecret" value="{$clientsecret}" type="text"></td>
			</tr>
			<tr>
				<td colspan="4" style="text-align: center"><input type="submit" value="Save Exact online settings"></td>
			</tr>
		</tbody>
	</table>
	</form>
	<div style="width: 200px;height:50px;border:1px solid #666666;" id="getdivisionbutton">
		<span style="color: #666666">GET THE DIVISION CODE</span>
	</div>
	
	{if $firstrun == true}
	<h2>You NEED to perform the first Auth, first fill in you Client ID, Client secret, division and return URL and hit 'Save Exact Online Settings'</h2>
	<div style="width: 100%; padding:20px 0; text-align: center;">
		<a href="index.php?module=ExactOnline&action=ExactOnlineAjax&file=handleAPI&firstrun=1">Perform First Run</a>
	</div>
	{/if}
	
</div>
