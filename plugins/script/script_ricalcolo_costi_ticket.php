<?php

/* kpro@bid06032018 */

echo("Togliere il die!"); die;

include_once('../../config.inc.php');
chdir($root_directory);
include_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix;
session_start();

require_once('modules/SproCore/Timecards/Timecards_utils.php');

$q = "SELECT tmc.timecardsid,
    tmc.worktime AS ore_effettive,
    tick.ticketid,
    ent.smownerid
    FROM {$table_prefix}_timecards tmc 
    INNER JOIN {$table_prefix}_troubletickets tick ON tick.ticketid = tmc.ticket_id
    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tmc.timecardsid
    INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = tick.ticketid
    WHERE ent.deleted = 0 AND ent1.deleted = 0
    AND tmc.kp_costo_interno = 0";
$res = $adb->query($q);
$num = $adb->num_rows($res);
if($num > 0){
    for($i = 0; $i < $num; $i++){
        $timecardsid = $adb->query_result($res, $i, 'timecardsid');
        $timecardsid = html_entity_decode(strip_tags($timecardsid), ENT_QUOTES, $default_charset);

        $ore_effettive = $adb->query_result($res, $i, 'ore_effettive');
        $ore_effettive = html_entity_decode(strip_tags($ore_effettive), ENT_QUOTES, $default_charset);

        $ticket_id = $adb->query_result($res, $i, 'ticketid');
        $ticket_id = html_entity_decode(strip_tags($ticket_id), ENT_QUOTES, $default_charset);

        $utente = $adb->query_result($res, $i, 'smownerid');
        $utente = html_entity_decode(strip_tags($utente), ENT_QUOTES, $default_charset);

        $ore_effettive = getOreEffettive($ore_effettive);

        if($ore_effettive != ""){
            $costo_intervento = 0;

            $costo_utente = getCostoUtente($ticket_id, $utente);

            $array_ora_esploso = explode(":",$ore_effettive);
            $ore = ltrim($array_ora_esploso[0],'0');
            $minuti = ltrim($array_ora_esploso[1],'0');
            $minuti_decimali = ($minuti * 100) / 60;

            $costo_intervento += $costo_utente * $ore;
            $costo_intervento += ($costo_utente * $minuti_decimali) / 100;

            $update = "UPDATE {$table_prefix}_timecards
                SET kp_costo_interno = {$costo_intervento}
                WHERE timecardsid = ".$timecardsid;
            $adb->query($update);

            RicalcoloCostiTicket($ticket_id);
        }
    }
}

function getOreEffettive($stringa){

    if($stringa == null || $stringa == ""){			
        $stringa = "";	
    }
    else{
        $pattern = '/^[0-2]{1}[0-9]{1}.[0-5]{1}[0-9]{1}$/';
        if(preg_match($pattern, $stringa)){				
            $array_delimiters = array(':','.','-','/');
            $array_ora_esploso = explode($array_delimiters[0], str_replace($array_delimiters, $array_delimiters[0], $stringa));
            $ore_controllo = $array_ora_esploso[0];
            $minuti_controllo = $array_ora_esploso[1];				
            if($ore_controllo >= 24 || $minuti_controllo >= 60){					
                $stringa = "";					
            }
            else{
                $stringa = $ore_controllo.':'.$minuti_controllo;
            }				
        }
        else{				
            $stringa = "";				
        }
    }

    return $stringa;
}