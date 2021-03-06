<?php

/* kpro@tom08052017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 *
 * Script per riallineare i tempi di fermo impianto
 */

require('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
require_once('vtlib/Vtecrm/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix;
session_start();

if(isset($_GET['id'])){
    $id = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['id']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $id = substr($id, 0, 100);

    printf("<br/><br/> Id: %s", $id);

    $dati_fermo_impiato = getFermoImpianto($id);

    printf("<br/>- Inizio: %s %s", $dati_fermo_impiato["data_inizio"], $dati_fermo_impiato["ora_inizio"]);

    $utc_inizio = getUTCFromDate($dati_fermo_impiato["data_inizio"], $dati_fermo_impiato["ora_inizio"]);

    printf(" --> UTC: %s", $utc_inizio);

    printf("<br/>- Fine: %s %s", $dati_fermo_impiato["data_fine"], $dati_fermo_impiato["ora_fine"]);

    $utc_fine = getUTCFromDate($dati_fermo_impiato["data_fine"], $dati_fermo_impiato["ora_fine"]);

    printf(" --> UTC: %s", $utc_fine);

    if($utc_fine == ""){

        $utc_diff = "";

    }
    else{

        $utc_diff = ( ($utc_fine - $utc_inizio) / 1000 ) / 60;

    }

    printf("<br/> ----> Diff: %s", $utc_diff);

    setTempiFermoImpianto($id, $utc_inizio, $utc_fine, $utc_diff);
    
}
else{

    printf("Indicare l'ID di una manutenzione");
    
}

function getFermoImpianto($id){
    global $adb, $table_prefix;

    $result = "";

    $query = "SELECT 
                kp_data_inizio,
                kp_data_fine,
                kp_ora_inizio,
                kp_ora_fine
                FROM {$table_prefix}_fermiimpianto
                WHERE fermiimpiantoid = ".$id;

    $result_query = $adb->query($query);
    $num_result = $adb->num_rows($result_query);

    if($num_result > 0){

        $data_inizio = $adb->query_result($result_query, $i, 'kp_data_inizio');
        $data_inizio = html_entity_decode(strip_tags($data_inizio), ENT_QUOTES, $default_charset);
        if($data_inizio == null || $data_inizio == "" || $data_inizio == "0000-00-00"){
            $data_inizio = "";
        }

        $data_fine = $adb->query_result($result_query, $i, 'kp_data_fine');
        $data_fine = html_entity_decode(strip_tags($data_fine), ENT_QUOTES, $default_charset);
        if($data_fine == null || $data_fine == "" || $data_fine == "0000-00-00"){
            $data_fine = "";
        }

        $ora_inizio = $adb->query_result($result_query, $i, 'kp_ora_inizio');
        $ora_inizio = html_entity_decode(strip_tags($ora_inizio), ENT_QUOTES, $default_charset);

        $ora_fine = $adb->query_result($result_query, $i, 'kp_ora_fine');
        $ora_fine = html_entity_decode(strip_tags($ora_fine), ENT_QUOTES, $default_charset);

        $result = array("data_inizio" => $data_inizio,
                        "ora_inizio" => $ora_inizio,
                        "data_fine" => $data_fine,
                        "ora_fine" => $ora_fine);

    }

    return $result;

}

function getUTCFromDate($data, $time){
    global $adb, $table_prefix;

    $result = "";

    date_default_timezone_set('UTC');

    if($data != "" && $time != ""){
        
        $date = new DateTime($data." ".$time);
        
        $utc = $date->format('U');

        $result = $utc * 1000;

    }

    return $result;

}

function setTempiFermoImpianto($id, $inizio, $fine, $differenza){
    global $adb, $table_prefix;

    if($differenza == ""){

        $update = "UPDATE {$table_prefix}_fermiimpianto SET 
                    kp_time_start = ".$inizio."
                    WHERE fermiimpiantoid = ".$id;

    }
    else{

        $update = "UPDATE {$table_prefix}_fermiimpianto SET 
                    kp_time_start = ".$inizio.",
                    kp_time_end = ".$fine.",
                    kp_tempo_totale = ".$differenza."
                    WHERE fermiimpiantoid = ".$id;

    }

    $adb->query($update);

}

?>