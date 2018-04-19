<?php

/* kpro@tom18042018 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2018, Kpro Consulting Srl
 */

function kpPostElaborazioneCalcolaSituazioneFormazioneAzienda($account, $giorni_in_scadenza){
	global $adb, $table_prefix, $current_user, $default_charset;
	
	/*$lista_formazioni_non_eseguita = kpGetGormazioneNonEseguitaAzienda($account);

	foreach( $lista_formazioni_non_eseguita as $formazioni_non_eseguita){

		kpAggiornaStatoFormazioneNonEseguita($formazioni_non_eseguita);

	}*/

}

function kpGetGormazioneNonEseguitaAzienda($account){
	global $adb, $table_prefix, $current_user, $default_charset;

	$result = array();

	$query = "SELECT 
				sit.situazformazid id,
				sit.validita_formazione validita_formazione
				FROM {$table_prefix}_situazformaz sit
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sit.situazformazid
				WHERE ent.deleted = 0 AND sit.stato_formazione = 'Non eseguita' AND sit.azienda = ".$account;

	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	for($i=0; $i < $num_result; $i++){

		$id = $adb->query_result($result_query, $i, 'id');
		$id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
		
		$validita_formazione = $adb->query_result($result_query, $i, 'validita_formazione');
		$validita_formazione = html_entity_decode(strip_tags($validita_formazione), ENT_QUOTES, $default_charset);
		if($validita_formazione == null || $validita_formazione == "0000-00-00"){
			$validita_formazione = "";
		}

		$result[] = array("id" => $id,
							"validita_formazione" => $validita_formazione);

	}

	return $result;

}

function kpAggiornaStatoFormazioneNonEseguita($formazioni_non_eseguita){
	global $adb, $table_prefix, $current_user, $default_charset;

	$data_corrente = date("Y-m-d");

	if( $formazioni_non_eseguita["validita_formazione"] != "" && $formazioni_non_eseguita["validita_formazione"] < $data_corrente ){

		$update = "UPDATE {$table_prefix}_situazformaz SET
					stato_formazione = 'Scaduta'
					WHERE situazformazid = ".$formazioni_non_eseguita["id"];

		$adb->query($update);

	}

}