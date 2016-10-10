<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

$mod_strings = array(
	'ModuleName' => 'Exact Online',
	'SINGLE_ModuleName' => 'Exact Online Record',
	'ModuleName ID' => 'Exact Online Record ID',
	'LBL_CUSTOM_INFORMATION' => 'Eigen informatie',
	'LBL_EXACTONLINE_INFORMATION' => 'Exact Record Informatie',
	'LBL_DESCRIPTION_INFORMATION' => 'Beschrijving',
	'SetCredentials' => 'Stel Exact Online gebruikersgegevens in, zoals divisie en return URL. Voer ook de eerste instellingen ronde uit.',
	'SetCredentialsTitle' => 'Stel uw gegevens in',
	'division' => 'Divisie',
	'getdivision' => 'HAAL DE DIVISIE OP',
	'savesettings' => 'BEWAAR',
	'reloadfirstrun' => 'HERHAAL DE EERSTE INSTELLING',
	'authurl' => 'Authentificatie URL',
	'tokenurl' => 'Token URL',
	'apiurl' => 'API URL',
	'settingspagetitle' => 'Exact Online Instellingen pagina',
	'settingsintro' => 'Welkom. Misschien heeft u zojuist uw eerste authentificatie gedaan. Onthoud, u <b>moet</b> eerst uw divisie ophalen. Dit is makkelijk, klik gewoon op de \'HAAL DE DIVISIE\' knop. U ziet uw divisiecode dan vanzelf verschijnen in het veld. Klik dan op \'BEWAAR\' om uw instellingen op te slaan.',
	'settingsintro2' => 'De Authentificatie URL, Token URL en API URL zijn automatisch ingesteld tijdens de eerste instelling. <b>Verander deze niet</b>, behalve als u problemen ondervindt. Zorg dat de landcode in de URL correct is. Voor Nederland moet deze starten met https://start.exactonline.<b>nl</b>, enzovoorts.',
	'settingsintro3' => 'Als u de eerste instelling nogmaals moet uitvoeren, klikt u op HERHAAL DE EERSTE INSTELLING en drukt u op F5 op uw toetsenbord.',
	'syncsettings' => 'Synchronisatie',
	'synsettingsdesc' => 'Synchroniseer u producten en services voor de eerste keer en synchroniseer uw grootboekrekeningen en betalingscondities handmatig.',
	'syncsettingsC1' => 'Op deze pagina kunt u <b>alle</b> producten en diensten in één keer naar Exact sturen. <b>Pas op</b>: dit proces kan erg veel tijd in beslag nemen (tot wel tien minuten) en kan onafgemaakt stoppen afhankelijk van uw server instellingen en het aantal producten en diensten. Als deze automatische synchronisatie niet werkt, moet u de producten en diensten vanuit coreBOS exporteren en in Exact Online importeren. U kunt producten en diensten ook per stuk bewerken (zonder iets te veranderen) om deze één voor één naar Exact te versturen. U moet hiervoor wel de workflow ingesteld hebben.',
	'syncsettingsC2' => 'Hier kunt u uw grootboekrekeningen en betalingscondities vanuit Exact naar coreBOS synchroniseren. Hier zijn ook workflows voor beschikbaar. De "Grootboekrekening" (General Ledgers) workflow is beschikbaar voor producten maar synchroniseert zowel producten als services. U moet hier een interval voor instellen en CRON moet ingesteld zijn op uw server. De "Betalingscondities" (Payment Conditions) workflow is beschikbaar voor Facturen. Ook hier geldt: CRON moet ingesteld staan op uw server.',
	'syncsettingsC3' => 'Nadat u op "verstuur alle producten" of "verstuur alle diensten" klikt, zal uw systeem niet reageren totdat alle producten of diensten verstuurd zijn.',
	'syncglaccounts' => 'Synchroniseer grootboekrekeningen',
	'glaccountsalert' => 'Grootboekrekeningen gesynchroniseerd',
	'syncpaymentconds' => 'Synchroniseer betalingscondities',
	'paymentcondsalert' => 'Betalingscondities gesynchroniseerd',
	'sendallproducts' => 'Stuur alle producten naar Exact',
	'sendallproductsalert' => 'Er wordt gestart met het versturen van alle producten naar Exact. Dit proces kan vastlopen, afhankelijk van het aantal producten en uw server instellingen.',
	'sendallservices' => 'Stuur alle diensten',
	'sendallservicesalert' => 'Er wordt gestart met het versturen van alle diensten naar Exact. Dit proces kan vastlopen, afhankelijk van het aantal diensten en uw server instellingen.',
	'glaccounts_start' => 'Startnummer grootboekrekeningen',
	'glaccounts_stop' => 'Eindnummer grootboekrekeningen',
	'setGLRange' => 'Bewaar bereik grootboekrekeningen',
	'setGLRangealert' => 'Bereik grootboekrekeningen bewaard.',
	'FirstRunPrev' => 'Vorige',
	'FirstRunNext' => 'Volgende',
	'FirstRunTitleOne' => '<br><br>Welkom bij het Exact Online connector voor coreBOS installatieproces. Stap 1: Ga naar het Exact Online APP center en maak een API key aan.',
	'FirstRunStepOne' => 'U moet eerst <a href="http://apps.exactonline.com" target="_blank">http://apps.exactonline.com</a> bezoeken om een API key te registreren. Log in met uw gegevens en klik op "Registreer API Keys". Klik nu op "Registreer een nieuwe API Key". Er wordt om een naam gevraagd. Vul iets makkelijks in, het maakt niet uit wat. U wordt nu gevraagd een "return URL" in te vullen. Kies de volgende:',
	'FirstRunTitleTwo' => 'Vul hier nu uw Client ID en App secret in.',
	'FirstRunStepTwo' => 'Nadat u de vorige stap heeft voltooid en uw app heeft bewaard heeft u deze ontvangen. De Exact Online Connector heeft deze nodig om een verbinding met uw administratie te kunnen maken.',
	'FirstRunTitleThree' => 'Exact Online URL:',
	'FirstRunStepThree' => 'Vul nu de juiste URL voor uw land in<ul><li><b>Nederland: </b>https://start.exactonline.nl</li><li><b>België: </b>https://start.exactonline.be</li><li><b>Duitsland: </b>https://start.exactonline.de</li><li><b>UK: </b>https://start.exactonline.co.uk</li><li><b>USA: </b>https://start.exactonline.com</li></ul>',
	'YourCountryURL' => 'De URL voor uw land',
	'FirstRunTitleFour' => 'Het laatste gedeelte.',
	'FirstRunStepFour' => 'Klik op de bewaarknop onderin om uw waarden in de database op te slaan. Klik <b>hierna</b> op de "voer eerste authentificatie uit" om uw app te authentificeren. U wordt van deze pagina weg geleid naar een pagina waar gevraagd wordt of uw coreBOS installatie toegang tot uw Exact Online administratie mag hebben (het <b>kan</b> zijn dat u eerst in moet loggen bij Exact Online). Klik op \'ja\' en u ziet een scherm dat uw authentificatie bevestigt. Klik op de link in het scherm om terug hier uit te komen.<br><br>Wanneer u hier terugkomt moet u eerst nog uw <b>divisie</b> ophalen. U hoeft daarvoor alleen op de betreffende knop te klikken en op \'bewaar\' te klikken.',
	'saveFirstRun' => 'Bewaar waarden in de database',
	'performFirstAuth' => 'Voer de authentificatie uit.',
	'ExactOnline Record No' => 'ExactOnline nummer',
	'ExactOnline Record Name' => 'ExactOnline Naam',
	'Return Message from Exact' => 'Antwoord van Exact',
	'Assigned To' => 'Toegewezen aan ',
	'Created Time' => 'Aangemaakt op',
	'Modified Time' => 'Gewijzigd op',
);
?>