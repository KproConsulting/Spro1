<?php

/* kpro@bid210220171400 */

/**
 * @author Bidese Jacopo
 * @copyright (c) 2017, Kpro Consulting Srl
 * @package importExport
 * @version 1.0
 */

require_once('../import_export_utils/import_utils.php'); /* kpro@bid190420171000 */

include_once('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

$current_user->id = 1;

$path_ftp = "csv/";
$dir = 'Formazione';
$dir_old = 'Old_File/';

$path_logs = "logs/";
$logs_file_name = $dir."_import_log.txt";
$error_logs_file_name = $dir."_import_error.txt";

$data_corrente = date("Y-m-d");

$errori = 0;
$record_creati = 0;
$record_aggiornati = 0;
$record_processati = 0;

$data_inizio_importazione = date("Y-m-d H:i:s");
$data_per_nome_file_csv = date("YmdHis");

$report_finale = " 
IMPORTAZIONE FORMAZIONE delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$logs_file_name, "w+");
fwrite($handle_log_file, $report_finale);
fclose($handle_log_file);

$report_finale = "
ERRORI IMPORTAZIONE FORMAZIONE delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$error_logs_file_name, "w+");
fwrite($handle_log_file, $report_finale);
fclose($handle_log_file);
    
if (is_dir($path_ftp.$dir)) {
    try {

        if ($dh = opendir($path_ftp.$dir)) {

            while (($file = readdir($dh)) !== false) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);

                if ($ext == 'csv' || $ext == 'CSV') {
                    $row = 1;
                    RimuoviCapoRigaCSVperImport($path_ftp.$dir . '/' . $file);
                    if (($handle = fopen($path_ftp.$dir . '/' . $file, "r")) !== false) {
                        $file_csv_size = filesize($path_ftp.$dir . '/'. $file);

                        while (($array_dati_riga = fgetcsv($handle, $file_csv_size, ";")) !== false) {

                            if($row == 1){
                                //StampaIntestazioneCSV($array_dati_riga,$path_logs,$logs_file_name);
                            }
                            else if ($row > 1) {
                                for ($i = 0; $i < count($array_dati_riga); $i++) {
                                    if ($array_dati_riga[$i] != null) {
                                        $array_dati_riga[$i] = trim(rimuoviApiciStringaCsvPerImportCrm($array_dati_riga[$i]));
                                    } else {
                                        $array_dati_riga[$i] = '';
                                    }
                                } 

                                $codice_fiscale_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[0], "Testo", false);
                                $descrizione_tipo_corso_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[3], "Testo", false);
                                $data_corso_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[5], "Data", false);
                                $ore_corso_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[8], "Numero", false);
                                $stato_corso_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[9], "Testo", false);
                                
                                if($codice_fiscale_controllo != "" && $data_corso_controllo != "" 
                                    && $ore_corso_controllo != 0 && $descrizione_tipo_corso_controllo != "" 
                                    && (strtolower($stato_corso_controllo) == "eseguita parzialmente" || strtolower($stato_corso_controllo) == "eseguita")){

                                    $record_processati++;

                                    $array_dati_tipi_corso = ImportTipiCorso($array_dati_riga);

                                    $id_crm_tipo_corso = $array_dati_tipi_corso[0];
                                    $nome_tipo_corso = $array_dati_tipi_corso[1];

                                    if($id_crm_tipo_corso != 0){
                                        
                                        $res_import = ImportPartecipazioniFormazione($array_dati_riga, $id_crm_tipo_corso, $nome_tipo_corso);
                                        if($res_import == 1){
                                            $record_creati++;
                                        }
                                        else if($res_import == -1){
                                            $errori++;

                                            $report_finale = " 
Nessuna risorsa trovata con codice fiscale: ".$codice_fiscale_controllo;
                                            $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                            fwrite($handle_log_file, $report_finale);
                                            fclose($handle_log_file);
                                        }
                                    }
                                    else{
                                        $errori++;

                                        $report_finale = " 
Errore nella creazione/controllo del tipo corso";
                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                        fwrite($handle_log_file, $report_finale);
                                        fclose($handle_log_file);
                                    }
                                }
                                else{
                                    $errori++;

                                    $report_finale = " 
Record privo dei dati obbligatori: ".$codice_fiscale_controllo." - ".$data_corso_controllo." - ".$ore_corso_controllo." - ".$descrizione_tipo_corso_controllo." - ".$stato_corso_controllo;
                                    $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                    fwrite($handle_log_file, $report_finale);
                                    fclose($handle_log_file);
                                }
                            }
                            $row++;
                        }
                        fclose($handle);

                        /*copy($path_ftp.$dir.'/'.$file, $path_ftp.$dir.'/'.$dir_old.$file);
                        unlink($path_ftp.$dir.'/'.$file);*/

                        $data_fine_importazione = date("Y-m-d H:i:s");

                        $report_finale = " 
terminato alle ".$data_fine_importazione.": processati ".$record_processati.", creati ".$record_creati.", aggiornati ".$record_aggiornati.", errori ".$errori;
                        $handle_log_file=fopen($path_logs.$logs_file_name, "a+");
                        fwrite($handle_log_file, $report_finale);
                        fclose($handle_log_file);
                    }
                }
            }
            closedir($dh);
        }
    } catch (Exception $e) {
        print($e);
    }
}

function ImportTipiCorso($array_dati_riga){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $new_tipo_corso = 0;
    $array_dati_tipi_corso = array();

    $descrizione_tipo_corso = normalizzaStringaCsvPerImportCrm($array_dati_riga[3], "Testo", false);

    $q_verifica_tipo_corso = "SELECT tc.tipicorsoid,
                        tc.tipicorso_name
                        FROM {$table_prefix}_tipicorso tc
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tc.tipicorsoid
                        WHERE ent.deleted = 0 AND tc.tipicorso_name = '".$descrizione_tipo_corso."'";
    $res_verifica_tipo_corso = $adb->query($q_verifica_tipo_corso);
    if($adb->num_rows($res_verifica_tipo_corso) == 0){
        $tipo_corso = CRMEntity::getInstance('TipiCorso');
        $tipo_corso->column_fields['tipicorso_name'] = $descrizione_tipo_corso;
        $tipo_corso->column_fields['validita_tipi_corso'] = 'Illimitata';
        $tipo_corso->column_fields['assigned_user_id'] = 1;
        $tipo_corso->save('TipiCorso', $longdesc=true, $offline_update=false, $triggerEvent=false);

        $new_tipo_corso = $tipo_corso->id;

        $nome_tipo_corso = $descrizione_tipo_corso;
    }
    else{
        $new_tipo_corso = $adb->query_result($res_verifica_tipo_corso, 0, 'tipicorsoid');
        $new_tipo_corso = html_entity_decode(strip_tags($new_tipo_corso), ENT_QUOTES, $default_charset);

        $nome_tipo_corso = $adb->query_result($res_verifica_tipo_corso, 0, 'tipicorso_name');
        $nome_tipo_corso = html_entity_decode(strip_tags($nome_tipo_corso), ENT_QUOTES, $default_charset);
    }

    if ($new_tipo_corso == 0 || $new_tipo_corso == '' || $new_tipo_corso == null) {
        $new_tipo_corso = 0;
    }

    $array_dati_tipi_corso[] = $new_tipo_corso;
    $array_dati_tipi_corso[] = $nome_tipo_corso;

    return $array_dati_tipi_corso;
}

/* kpro@bid030520170900 */
function ImportCorsoDiFormazione($array_dati_riga){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $descrizione_corso = normalizzaStringaCsvPerImportCrm($array_dati_riga[4], "Testo", false);

    $id_corso = 0;

    $q_verifica_corso = "SELECT cors.kpformazioneid
                        FROM {$table_prefix}_kpformazione cors
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = cors.kpformazioneid
                        WHERE ent.deleted = 0 AND cors.kp_nome_corso = '".$descrizione_corso."'";
    $res_verifica_corso = $adb->query($q_verifica_corso);
    if($adb->num_rows($res_verifica_corso) > 0){
        $id_corso = $adb->query_result($res_verifica_corso, 0, 'kpformazioneid');
        $id_corso = html_entity_decode(strip_tags($id_corso), ENT_QUOTES, $default_charset);
    }

    if ($id_corso == 0 || $id_corso == '' || $id_corso == null) {
        $id_corso = 0;
    }

    return $id_corso;
}
/* kpro@bid030520170900 end */

function ImportPartecipazioniFormazione($array_dati_riga, $id_crm_tipo_corso, $nome_tipo_corso){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $codice_fiscale = $array_dati_riga[0];

    $q_verifica_risorsa = "SELECT cont.contactid,
                        cont.lastname,
                        cont.firstname
                        FROM {$table_prefix}_contactdetails cont
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = cont.contactid
                        INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = cont.accountid
                        INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = cont.stabilimento
                        WHERE ent.deleted = 0 AND ent1.deleted = 0 AND ent2.deleted = 0
                        AND cont.kp_codice_fiscale = '".$codice_fiscale."'";
    $res_verifica_risorsa = $adb->query($q_verifica_risorsa);
    if($adb->num_rows($res_verifica_risorsa) > 0){
        $id_crm_risorsa = $adb->query_result($res_verifica_risorsa, 0, 'contactid');
        $id_crm_risorsa = html_entity_decode(strip_tags($id_crm_risorsa), ENT_QUOTES, $default_charset);

        $cognome = $adb->query_result($res_verifica_risorsa, 0, 'lastname');
        $cognome = html_entity_decode(strip_tags($cognome), ENT_QUOTES, $default_charset);

        $nome = $adb->query_result($res_verifica_risorsa, 0, 'firstname');
        $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);
        
        $ore_previste_corso = str_replace(",",".",$array_dati_riga[7]);
        $ore_previste_corso = normalizzaStringaCsvPerImportCrm($ore_previste_corso, "Numero", false);
        $ore_effettuate_corso = str_replace(",",".",$array_dati_riga[8]);
        $ore_effettuate_corso = normalizzaStringaCsvPerImportCrm($ore_effettuate_corso, "Numero", false);
        $data_formazione = normalizzaStringaCsvPerImportCrm($array_dati_riga[5], "Data", false);
        $data_scadenza = normalizzaStringaCsvPerImportCrm($array_dati_riga[6], "Data", false);
        $stato_corso = normalizzaStringaCsvPerImportCrm($array_dati_riga[9], "Testo", false);

        $id_crm_corso_formazione = ImportCorsoDiFormazione($array_dati_riga); /* kpro@bid030520170900 */

        $data_array = explode('-',$data_formazione);
        $anno_corso = $data_array[0];

        $q_controllo_partecipazione = "SELECT form.kppartecipformazid
                                    FROM {$table_prefix}_kppartecipformaz form
                                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = form.kppartecipformazid
                                    WHERE ent.deleted = 0 AND form.kp_risorsa = ".$id_crm_risorsa."
                                    AND form.kp_tipo_corso = ".$id_crm_tipo_corso." AND form.kp_data_formazione = '".$data_formazione."'";
        $res_controllo_partecipazione = $adb->query($q_controllo_partecipazione);
        if($adb->num_rows($res_controllo_partecipazione) == 0){
            $partecipazione_formazione = CRMEntity::getInstance('KpPartecipFormaz');
            $partecipazione_formazione->column_fields['assigned_user_id'] = 1;
            $partecipazione_formazione->column_fields['kp_nome_partecipaz'] = $cognome." ".$nome." - ".$nome_tipo_corso." - ".$anno_corso;
            $partecipazione_formazione->column_fields['kp_risorsa'] = $id_crm_risorsa;
            $partecipazione_formazione->column_fields['kp_tipo_corso'] = $id_crm_tipo_corso;
            $partecipazione_formazione->column_fields['kp_formazione'] = $id_crm_corso_formazione; /* kpro@bid030520170900 */
            $partecipazione_formazione->column_fields['kp_data_formazione'] = $data_formazione;
            if($data_scadenza != ''){
                $partecipazione_formazione->column_fields['kp_data_scad_for'] = $data_scadenza;
            }
            $partecipazione_formazione->column_fields['kp_tot_ore_formazio'] = $ore_previste_corso;
            $partecipazione_formazione->column_fields['kp_tot_ore_effet'] = $ore_effettuate_corso;
            $partecipazione_formazione->column_fields['kp_stato_partecip'] = $stato_corso;
            $partecipazione_formazione->save('KpPartecipFormaz', $longdesc=true, $offline_update=false, $triggerEvent=false);

            $new_partecipazione_formazione = $partecipazione_formazione->id;

            return 1;
        }
        else{
            return 0;
        }
    }
    else{
        return -1;
    }
}

?>