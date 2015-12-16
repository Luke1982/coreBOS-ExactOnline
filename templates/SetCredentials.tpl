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

<div style="width: 90%; margin: 20px 0 0 5%;" id="ExactSettings">
	{if $firstrun == true}
		<h2>{$MOD.FirstRunTitle}</h2>
		<div id="firstrunprev"><<&nbsp;{$MOD.FirstRunPrev}</div>
		<div id="firstrunwindow">
			<img src="modules/ExactOnline/images/exact-logo-new.png" id="exactlogofirstrun" />
			<div id="firstrunpanelscontainer">
				<div class="firstrunpanel">
					<h3>{$MOD.FirstRunTitleOne}</h3>
					<p>{$MOD.FirstRunStepOne}</p>
					<span id="firstrunreturnurl">http://{$servername}/index.php?module=ExactOnline&action=ExactOnlineAjax&file=handleAPI</span>
				</div>				
				<div class="firstrunpanel">
					<h3>{$MOD.FirstRunTitleTwo}</h3>
					<p>{$MOD.FirstRunStepTwo}</p>
					<p>Client ID:</p>
					<input size="100" id="clientID" name="clientID" value="" type="text">
					<p>Client Secret:</p>
					<input size="100" id="clientsecret" name="clientsecret" value="" type="text">
				</div>
				<div class="firstrunpanel">
					<h3>{$MOD.FirstRunTitleThree}</h3>
					<p>{$MOD.FirstRunStepThree}</p>
					<p>{$MOD.YourCountryURL}</p>
					<input size="100" id="countryurl" name="countryurl" value="" type="text">
				</div>
				<div class="firstrunpanel">
					<h3>{$MOD.FirstRunTitleFour}</h3>
					<p>{$MOD.FirstRunStepFour}</p>
					<div id="saveFirstRun" class="firstrunbutton">{$MOD.saveFirstRun}</div>
					<div id="performFirstAuth" class="firstrunbutton"><a href="index.php?module=ExactOnline&action=ExactOnlineAjax&file=handleAPI&firstrun=1">{$MOD.performFirstAuth}</a></div>
				</div>
			</div>
		</div>
		<div id="firstrunnext">{$MOD.FirstRunNext}&nbsp;>></div>
	{else}
	<!-- NORMAL SETTINGS SCREEN, IF THE FIRST RUN WAS COMPLETED -->
	<img src="modules/ExactOnline/images/exact-logo-new.png" id="exactlogosettingspage" /><h2>{$MOD.settingspagetitle}</h2>

	<div id="settingstext">
		<div class="settingstextcolumn">
			<p>{$MOD.settingsintro}</p>
		</div>	
		<div class="settingstextcolumn">
			<p>{$MOD.settingsintro2}</p>
		</div>	
		<div class="settingstextcolumn">
			<p>{$MOD.settingsintro3}</p>
		</div>
	</div>
		
	<table cellpadding="5" id="exactsettingstable">
		<tbody>
			<tr>
				<td>{$MOD.division}:</td>
				<td><input class="exactsettingsinput" size="50" id="division" name="division" value="{$division}" type="text"></td>
				<td>Return URL:</td>
				<td><textarea class="exactsettingsinput" cols="50" rows="3" id="returnurl" name="returnurl" value="{$returnurl}">{$returnurl}</textarea></td>
			</tr>		
			<tr>
				<td>Client ID:</td>
				<td><input class="exactsettingsinput" size="50" id="clientID" name="clientID" value="{$clientID}" type="text"></td>
				<td>Client Secret:</td>
				<td><input class="exactsettingsinput" size="50" id="clientsecret" name="clientsecret" value="{$clientsecret}" type="text"></td>
			</tr>			
			<tr>
				<td>{$MOD.authurl}:</td>
				<td><input class="exactsettingsinput" size="50" id="authurl" name="authurl" value="{$authurl}" type="text"></td>
				<td>{$MOD.tokenurl}:</td>
				<td><input class="exactsettingsinput" size="50" id="tokenurl" name="tokenurl" value="{$tokenurl}" type="text"></td>
			</tr>			
			<tr>
				<td>{$MOD.apiurl}:</td>
				<td><input class="exactsettingsinput" size="50" id="apiurl" name="apiurl" value="{$apiurl}" type="text"></td>
				<td></td>
				<td></td>
			</tr>
		</tbody>
	</table>
	<div id="SettingsButtons">
		<span class="settingsbutton" id="getdivision">{$MOD.getdivision}</span>
		<span class="settingsbutton" id="savesettings">{$MOD.savesettings}</span>
		<span class="settingsbutton" id="reloadfirstrun">{$MOD.reloadfirstrun}</span>
	</div>
	{/if}
	
</div>
