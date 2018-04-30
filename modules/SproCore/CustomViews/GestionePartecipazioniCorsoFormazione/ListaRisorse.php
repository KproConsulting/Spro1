<?php

/* kpro@tom14122016 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location: ".$site_URL."/index.php");
}
else{
	$current_user->id = $_SESSION['authenticated_user_id'];
}

$rows = array();

if(isset($_GET['crmid'])){
	$crmid = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['crmid']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$crmid = substr($crmid,0,100);
	
	if(isset($_GET['nome_risorsa'])){
        $nome_risorsa = addslashes(html_entity_decode(strip_tags($_GET['nome_risorsa']), ENT_QUOTES, $default_charset)); /* kpro@bid300420180900 */
        $nome_risorsa = substr($nome_risorsa, 0, 255);
    }
    else{
        $nome_risorsa = '';
    }
	
	if(isset($_GET['nome_azienda'])){
        $nome_azienda = addslashes(html_entity_decode(strip_tags($_GET['nome_azienda']), ENT_QUOTES, $default_charset)); /* kpro@bid300420180900 */
        $nome_azienda = substr($nome_azienda, 0, 255);
    }
    else{
        $nome_azienda = '';
    }
	
	if(isset($_GET['nome_stabilimento'])){
        $nome_stabilimento = addslashes(html_entity_decode(strip_tags($_GET['nome_stabilimento']), ENT_QUOTES, $default_charset)); /* kpro@bid300420180900 */
        $nome_stabilimento = substr($nome_stabilimento, 0, 255);
    }
    else{
        $nome_stabilimento = '';
    }
	
	$lista_partecipanti = "(";
	
	$q_partecipanti = "SELECT 
						part.kp_risorsa kp_risorsa
						FROM {$table_prefix}_kppartecipformaz part
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = part.kppartecipformazid
						WHERE ent.deleted = 0 AND part.kp_formazione = ".$crmid;
	
	$res_partecipanti = $adb->query($q_partecipanti);
	$num_partecipanti = $adb->num_rows($res_partecipanti);

	for($i = 0; $i < $num_partecipanti; $i++){
		
		$risorsa_partecipante = $adb->query_result($res_partecipanti, $i, 'kp_risorsa');
		$risorsa_partecipante = html_entity_decode(strip_tags($risorsa_partecipante), ENT_QUOTES,$default_charset);
		
		if($risorsa_partecipante != null && $risorsa_partecipante != "" && $risorsa_partecipante != 0){
			
			if($lista_partecipanti == "("){
				$lista_partecipanti .= $risorsa_partecipante;
			}
			else{
				$lista_partecipanti .= ",".$risorsa_partecipante;
			}
			
		}
		
	}
	
	$lista_partecipanti .= ")";
	
	//Ora dovrò estrarre l'elenco delle risorse che non sono nella lista partecipanti in modo da mostrare solo le risorse che non sono ancora iscritte
	
	$q_risorse = "SELECT 
					cont.contactid contactid,
					CONCAT(cont.lastname, ' ', cont.firstname) nome_risorsa,
					acc.accountname accountname,
					stab.nome_stabilimento nome_stabilimento
					FROM {$table_prefix}_contactdetails cont 
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = cont.contactid
					INNER JOIN {$table_prefix}_account acc ON acc.accountid = cont.accountid
					LEFT JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = cont.stabilimento
					WHERE ent.deleted = 0";

	if($lista_partecipanti != "()"){
		$q_risorse .= " AND cont.contactid NOT IN ".$lista_partecipanti;
	}
	
	if($nome_risorsa != ""){
		$q_risorse .= " AND CONCAT(cont.lastname, ' ', cont.firstname) LIKE '%".$nome_risorsa."%'"; 
	}
	
	if($nome_azienda != ""){
		$q_risorse .= " AND acc.accountname LIKE '%".$nome_azienda."%'"; 
	}
	
	if($nome_stabilimento != ""){
		$q_risorse .= " AND stab.nome_stabilimento LIKE '%".$nome_stabilimento."%'"; 
	}
	
	$q_risorse .= " ORDER BY CONCAT(cont.lastname, ' ', cont.firstname) ASC";
	$q_risorse .= " LIMIT 0, 100";
	
	$res_risorse = $adb->query($q_risorse);
	$num_risorse = $adb->num_rows($res_risorse);
	
	for($i = 0; $i < $num_risorse; $i++){
		
		$risorsa = $adb->query_result($res_risorse, $i, 'contactid');
		$risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES,$default_charset);
		
		$nome_risorsa = $adb->query_result($res_risorse, $i, 'nome_risorsa');
		$nome_risorsa = html_entity_decode(strip_tags($nome_risorsa), ENT_QUOTES,$default_charset);
		
		$nome_azienda = $adb->query_result($res_risorse, $i, 'accountname');
		$nome_azienda = html_entity_decode(strip_tags($nome_azienda), ENT_QUOTES,$default_charset);
		if($nome_azienda == null){
			$nome_azienda = "";
		}
		
		$nome_stabilimento = $adb->query_result($res_risorse, $i, 'nome_stabilimento');
		$nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES,$default_charset);
		if($nome_stabilimento == null){
			$nome_stabilimento = "";
		}
		
		$rows[] = array('risorsa' => $risorsa,
						'nome_risorsa' => $nome_risorsa,
						'nome_azienda' => $nome_azienda,
						'nome_stabilimento' => $nome_stabilimento);
		
	}
		
}

$json = json_encode($rows);
print $json;

?>
