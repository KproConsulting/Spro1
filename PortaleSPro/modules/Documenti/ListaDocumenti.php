<?php

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

$rows = array();
$lista_documenti = array(); /* kpro@bid130420181720 */

if(isset($_GET['nome_documento'])){
    $nome_documento = addslashes(html_entity_decode(strip_tags($_GET['nome_documento']), ENT_QUOTES,$default_charset));
    $nome_documento = substr($nome_documento,0,255);
}
else{
    $nome_documento = '';
}

if(isset($_GET['data_documento'])){
    $data_documento = addslashes(html_entity_decode(strip_tags($_GET['data_documento']), ENT_QUOTES,$default_charset));
    $data_documento = substr($data_documento,0,255);
}
else{
    $data_documento = '';
}

if(isset($_GET['data_scadenza_documento'])){
    $data_scadenza_documento = addslashes(html_entity_decode(strip_tags($_GET['data_scadenza_documento']), ENT_QUOTES,$default_charset));
    $data_scadenza_documento = substr($data_scadenza_documento,0,255);
}
else{
    $data_scadenza_documento = '';
}

if(isset($_GET['stato'])){
    $stato_filtro = addslashes(html_entity_decode(strip_tags($_GET['stato']), ENT_QUOTES,$default_charset));
    $stato_filtro = substr($stato_filtro,0,255);
    if($stato_filtro == ''){
        $stato_filtro = 'all';
    }
}
else{
    $stato_filtro = 'all';
}

$q_documenti = "SELECT attac.attachmentsid attachmentsid, 
				attac.name name, 
				attac.path path, 
				notes.title title, 
				notes.notesid notesid, 
				notes.folderid cartella_id,
				notes.data_scadenza data_scadenza,
				notes.stato_documento stato_documento,
				date(ent.createdtime) data_documento,
				ent.createdtime createdtime, 
				ent.modifiedtime modifiedtime 
				FROM {$table_prefix}_notes notes 
				INNER JOIN {$table_prefix}_notescf notescf ON notescf.notesid = notes.notesid 
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notes.notesid 
				LEFT JOIN {$table_prefix}_senotesrel senote ON senote.notesid = notes.notesid
				LEFT JOIN {$table_prefix}_seattachmentsrel seattac ON seattac.crmid = notes.notesid 
				LEFT JOIN {$table_prefix}_attachments attac ON attac.attachmentsid = seattac.attachmentsid 
				LEFT JOIN {$table_prefix}_contactdetails cont ON cont.contactid = senote.crmid
				WHERE ent.deleted = 0 AND (cont.accountid = ".$azienda." OR senote.crmid = ".$azienda.")
				AND notes.active_portal = 1";

if($nome_documento != ""){
	$q_documenti .= " AND notes.title LIKE '%".$nome_documento."%'";
}

if($data_documento != ""){
	$q_documenti .= " AND date(ent.createdtime) = '".$data_documento."'";
}

if($data_scadenza_documento != ""){
	$q_documenti .= " AND notes.data_scadenza = '".$data_scadenza_documento."'";
}

if($stato_filtro != "" && $stato_filtro != "all"){
	$q_documenti .= " AND notes.stato_documento = '".$stato_filtro."'";
}

$q_documenti .= " ORDER BY ent.createdtime DESC";

$res_documenti = $adb->query($q_documenti);
$num_documenti = $adb->num_rows($res_documenti);

for($i=0; $i < $num_documenti; $i++){
	$documento_gia_passato = false; /* kpro@bid130420181720 */
	
	$notesid = $adb->query_result($res_documenti, $i, 'notesid');
	$notesid = html_entity_decode(strip_tags($notesid), ENT_QUOTES,$default_charset);
    /* kpro@bid130420181720 start */             
	if (empty($lista_documenti)) {
		$lista_documenti[] = $notesid;
	} else {
		if (in_array($notesid, $lista_documenti)) {
			$documento_gia_passato = true;
		} else {
			$lista_documenti[] = $notesid;
		}
	}
	
	if(!$documento_gia_passato){ /* kpro@bid130420181720 end */
		$title = $adb->query_result($res_documenti, $i, 'title');
		$title = html_entity_decode(strip_tags($title), ENT_QUOTES,$default_charset);
	
		$attachmentsid = $adb->query_result($res_documenti, $i, 'attachmentsid');
		$attachmentsid = html_entity_decode(strip_tags($attachmentsid), ENT_QUOTES,$default_charset);
		
		$createdtime = $adb->query_result($res_documenti, $i, 'createdtime');
		$createdtime = html_entity_decode(strip_tags($createdtime), ENT_QUOTES,$default_charset);
		
		$modifiedtime = $adb->query_result($res_documenti, $i, 'modifiedtime');
		$modifiedtime = html_entity_decode(strip_tags($modifiedtime), ENT_QUOTES,$default_charset);
		
		$data_documento = $adb->query_result($res_documenti, $i, 'data_documento');
		$data_documento = html_entity_decode(strip_tags($data_documento), ENT_QUOTES,$default_charset);
		if($data_documento != null && $data_documento != ""){
			list($anno, $mese, $giorno) = explode("-", $data_documento);
			$data_documento = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		
		$data_scadenza = $adb->query_result($res_documenti, $i, 'data_scadenza');
		$data_scadenza = html_entity_decode(strip_tags($data_scadenza), ENT_QUOTES,$default_charset);
		if($data_scadenza != null && $data_scadenza != ""){
			list($anno, $mese, $giorno) = explode("-", $data_scadenza);
			$data_scadenza = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		
		$stato_documento = $adb->query_result($res_documenti, $i, 'stato_documento');
		$stato_documento = html_entity_decode(strip_tags($stato_documento), ENT_QUOTES,$default_charset);
		
		$rows[] = array('notesid' => $notesid,
						'attachmentsid' => $attachmentsid,
						'title' => $title,
						'createdtime' => $createdtime,
						'modifiedtime' => $modifiedtime,
						'data_documento' => $data_documento,
						'data_scadenza' => $data_scadenza,
						'stato_documento' => $stato_documento);
	} /* kpro@bid130420181720 */
	
}
	
$json = json_encode($rows);
print $json;
	
?>
