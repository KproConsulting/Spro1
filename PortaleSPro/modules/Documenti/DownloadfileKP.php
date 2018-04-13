<?php
/********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

/* kpro@tom17062016 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package portaleVteSicurezza
 * @version 1.0
 */

require_once("../../PortalConfig.php");
include_once('../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_language;
session_start();

//print_r($_SESSION);die;

$contact_id = 0;
$default_language = "it_it";
if(isset($_SESSION['customer_sessionid'])){
	
    $contact_id = $_SESSION['customer_id'];
    $customer_account_id = $_SESSION['customer_account_id'];
    $sessionid = $_SESSION['customer_sessionid'];
    $default_language = $_SESSION['portal_login_language'];
  
}
else{
	$contact_id = 0;
}

require_once($portal_name.'/string/'.$default_language.'.php');

if($contact_id != 0){
    
    $q_account = "SELECT accountid
					FROM {$table_prefix}_contactdetails 
					WHERE contactid = ".$contact_id;
	$res_account = $adb->query($q_account);
			
	if($adb->num_rows($res_account) > 0){
		
		$azienda = $adb->query_result($res_account, 0, 'accountid');
		$azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES,$default_charset);
		
	}
	else{
	    header("Location: ../../login.php");  
	}
	
}
else{
    header("Location: ../../login.php"); 
}
 
require_once('config.php');
require_once('include/database/PearDatabase.php');

global $adb;
global $fileId, $default_charset;
global $table_prefix;
$attachmentsid = $_REQUEST['fileid'];
$entityid = $_REQUEST['entityid'];

if($attachmentsid != '' && $attachmentsid != 0 && $entityid != '' && $entityid != 0 && $azienda != 0 && $azienda != ""){
	
	$returnmodule=$_REQUEST['return_module'];
	
	$dbQuery = "SELECT *FROM ".$table_prefix."_attachments attac
				INNER JOIN ".$table_prefix."_seattachmentsrel seattac ON seattac.attachmentsid = attac.attachmentsid
				LEFT JOIN ".$table_prefix."_senotesrel senote ON senote.notesid = seattac.crmid
				LEFT JOIN ".$table_prefix."_contactdetails cont ON cont.contactid = senote.crmid
				INNER JOIN ".$table_prefix."_notes notes ON notes.notesid = seattac.crmid
				INNER JOIN ".$table_prefix."_crmentity ent ON ent.crmid = notes.notesid
				WHERE ent.deleted = 0 AND (cont.accountid = ".$azienda." OR senote.crmid = ".$azienda.") AND attac.attachmentsid = ?";	

	//$dbQuery = "SELECT * FROM ".$table_prefix."_attachments WHERE attachmentsid = ?" ;

	$result = $adb->pquery($dbQuery, array($attachmentsid)) or die("Couldn't get file list");
	if($adb->num_rows($result) > 0) /* kpro@bid130420181720 */
	{
		$fileType = @$adb->query_result($result, 0, "type");
		$name = @$adb->query_result($result, 0, "name");
		$filepath = @$adb->query_result($result, 0, "path");
		$name = html_entity_decode($name, ENT_QUOTES, $default_charset);
		$saved_filename = $attachmentsid."_".$name;
		$filesize = filesize($filepath.$saved_filename);
		//ds@38
		if(substr(strtolower($saved_filename), -3) == 'zip') {
			$filesize = $filesize + ($filesize % 1024);
		}
		//ds@38e
		$fileContent = fread(fopen($filepath.$saved_filename, "r"), $filesize);

		header("Content-type: $fileType");
		header("Content-length: $filesize");
		header("Cache-Control: private");
		header("Content-Disposition: attachment; filename=\"$name\""); //crmv@3079m
		header("Content-Description: PHP Generated Data");
		echo $fileContent;
	}
	else
	{
		echo "Record doesn't exist.";
	}
}
else{
	die;
}
?>
