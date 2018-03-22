<?php

/* kpro@bid02092016 */

/**
 * @author BideseJacopo
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package importExport
 * @version 1.0
 */
require_once('../import_export_utils/import_utils.php'); /* kpro@bid190420171000 */

iinclude_once('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();
$current_user->id = 1;

$path_ftp = "/home/erp/Import_Export/Export_da_Erp/";
$dir = 'MansioniRisorsa';
$dir_old = 'Old_File/';

$path_logs = "logs/";
$logs_file_name = $dir."_import_log.txt";
$error_logs_file_name = $dir."_import_error_risorse.txt";
$error_logs_file_name2 = $dir."_import_error_mansioni.txt";

$data_corrente = date("Y-m-d");

$errori = 0;
$record_creati = 0;
$record_aggiornati = 0;
$record_processati = 0;

$data_inizio_importazione = date("Y-m-d H:i:s");

$report_finale = " 
IMPORTAZIONE MANSIONI RISORSE delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$logs_file_name, "w+");
fwrite($handle_log_file, $report_finale);
fclose($handle_log_file);

$report_finale = "
ERRORI IMPORTAZIONE MANSIONI RISORSE (Risorse) delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$error_logs_file_name, "w+");
fwrite($handle_log_file, $report_finale);
fclose($handle_log_file);

$report_finale = "
ERRORI IMPORTAZIONE MANSIONI RISORSE (Mansioni) delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$error_logs_file_name2, "w+");
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
                            
                            $array_codici_fiscali = array();
                            if($row == 1){
                                //StampaIntestazioneCSV($array_dati_riga,$path_logs,$logs_file_name);
                            }
                            else if ($row > 1) {
                                $risorsa_gia_passata = false;
                                
                                for ($i = 0; $i < count($array_dati_riga); $i++) {
                                    if ($array_dati_riga[$i] != null) {
                                        $array_dati_riga[$i] = trim(rimuoviApiciStringaCsvPerImportCrm($array_dati_riga[$i]));
                                    } else {
                                        $array_dati_riga[$i] = '';
                                    }
                                }
                                
                                $mansione_principale_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[1], "Testo", false);
                                $codice_fiscale_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[0], "Testo", false);
                                
                                if($mansione_principale_controllo != '' && $codice_fiscale_controllo != ''){

                                    if (empty($array_codici_fiscali)) {
                                        $array_codici_fiscali[] = $codice_fiscale_controllo;
                                    } else {
                                        if (in_array($codice_fiscale_controllo, $array_codici_fiscali)) {
                                            $risorsa_gia_passata = true;
                                        } else {
                                            $array_codici_fiscali[] = $codice_fiscale_controllo;
                                        }
                                    }

                                    if(!$risorsa_gia_passata){
                                        $record_processati++;
                                        $res_import = ImportMansioneRisorsa($array_dati_riga,'Principale');
                                        if($res_import == 1){
                                            $record_creati++;
                                        }
                                        else if($res_import == 0){
                                            $errori++;

                                            $report_finale = "
Mansione non trovata: ".$mansione_principale_controllo;
                                            $handle_log_file=fopen($path_logs.$error_logs_file_name2, "a+");
                                            fwrite($handle_log_file, $report_finale);
                                            fclose($handle_log_file);
                                        }
                                        else{
                                            $errori++;

                                            $report_finale = "
Risorsa non trovata: ".$codice_fiscale_controllo;
                                            $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                            fwrite($handle_log_file, $report_finale);
                                            fclose($handle_log_file);
                                        }

                                        $mansione_secondaria_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[2], "Testo", false);
                                        if($mansione_secondaria_controllo != ''){
                                            $res_import2 = ImportMansioneRisorsa($array_dati_riga,'Accessoria');
                                            if($res_import2 == 1){
                                                $record_creati++;
                                            }
                                            else if($res_import2 == 0){
                                                $errori++;

                                                $report_finale = "
Mansione non trovata: ".$mansione_secondaria_controllo;
                                                $handle_log_file=fopen($path_logs.$error_logs_file_name2, "a+");
                                                fwrite($handle_log_file, $report_finale);
                                                fclose($handle_log_file);
                                            }
                                            else{
                                                $errori++;

                                                $report_finale = "
Risorsa non trovata: ".$codice_fiscale_controllo;
                                                $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                fwrite($handle_log_file, $report_finale);
                                                fclose($handle_log_file);
                                            }
                                        }
                                    }
                                }
                                else{
                                    $errori++;

                                    $report_finale = "
Record privo dei campi obbligatori: ".$codice_fiscale_controllo." - ".$mansione_principale_controllo;
                                    $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                    fwrite($handle_log_file, $report_finale);
                                    fclose($handle_log_file);
                                }
                            }
                            $row++;
                        }
                        fclose($handle);

                        copy($path_ftp.$dir.'/'.$file, $path_ftp.$dir.'/'.$dir_old.$file);
                        unlink($path_ftp.$dir.'/'.$file);

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

function ImportMansioneRisorsa($dati_riga,$tipo_mansione) {
    global $adb, $table_prefix, $default_charset;

    $codice_fiscale = $dati_riga[0];
    if($tipo_mansione == 'Principale'){
        $mansione = $dati_riga[1];
    }
    else{
        $mansione = $dati_riga[2];
    }

    $q_verifica_risorsa = "SELECT cont.contactid,
                        cont.accountid,
                        cont.stabilimento,
                        cont.data_assunzione
                        FROM {$table_prefix}_contactdetails cont
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = cont.contactid
                        WHERE ent.deleted = 0 AND cont.kp_codice_fiscale = '".$codice_fiscale."'";
    $res_verifica_risorsa = $adb->query($q_verifica_risorsa);
    if($adb->num_rows($res_verifica_risorsa) > 0){

        $q_verifica_mansione = "SELECT man.mansioniid
                            FROM {$table_prefix}_mansioni man
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = man.mansioniid
                            WHERE ent.deleted = 0 AND man.mansione_name = '".$mansione."'";
        $res_verifica_mansione = $adb->query($q_verifica_mansione);
        if($adb->num_rows($res_verifica_mansione) > 0){
            $id_mansione = $adb->query_result($res_verifica_mansione, 0, 'mansioniid');
            $id_mansione = html_entity_decode(strip_tags($id_mansione), ENT_QUOTES, $default_charset);

            $id_risorsa = $adb->query_result($res_verifica_risorsa, 0, 'contactid');
            $id_risorsa = html_entity_decode(strip_tags($id_risorsa), ENT_QUOTES, $default_charset);

            $id_azienda = $adb->query_result($res_verifica_risorsa, 0, 'accountid');
            $id_azienda = html_entity_decode(strip_tags($id_azienda), ENT_QUOTES, $default_charset);

            $id_stabilimento = $adb->query_result($res_verifica_risorsa, 0, 'stabilimento');
            $id_stabilimento = html_entity_decode(strip_tags($id_stabilimento), ENT_QUOTES, $default_charset);

            $data_assunzione = $adb->query_result($res_verifica_risorsa, 0, 'data_assunzione');
            $data_assunzione = html_entity_decode(strip_tags($data_assunzione), ENT_QUOTES, $default_charset);

            $q_verifica_mansione_risorsa = "SELECT mnsr.mansionirisorsaid
                                        FROM {$table_prefix}_mansionirisorsa mnsr
                                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = mnsr.mansionirisorsaid
                                        WHERE ent.deleted = 0
                                        AND mnsr.risorsa = ".$id_risorsa."
                                        AND mnsr.mansione = ".$id_mansione;
            $res_verifica_mansione_risorsa = $adb->query($q_verifica_mansione_risorsa);
            if($adb->num_rows($res_verifica_mansione_risorsa) == 0){
                $mansione_risorsa = CRMEntity::getInstance('MansioniRisorsa');
                $mansione_risorsa->column_fields['risorsa'] = $id_risorsa;
                $mansione_risorsa->column_fields['mansione'] = $id_mansione;
                $mansione_risorsa->column_fields['data_inizio'] = $data_assunzione;
                $mansione_risorsa->column_fields['tipo_mansione'] = $tipo_mansione;
                $mansione_risorsa->column_fields['stato_mansione'] = 'Attiva';
                $mansione_risorsa->column_fields['azienda'] = $id_azienda;
                $mansione_risorsa->column_fields['stabilimento'] = $id_stabilimento;
                $mansione_risorsa->column_fields['eredita_t_corso'] = 'Si';
                $mansione_risorsa->column_fields['eredita_t_visita'] = 'Si';
                $mansione_risorsa->column_fields['assigned_user_id'] = 1;
                $mansione_risorsa->save('MansioniRisorsa', $longdesc=true, $offline_update=false, $triggerEvent=false);

                $new_mansione_risorsa = $mansione_risorsa->id;

                $q_update_mansione_risorsa = "UPDATE {$table_prefix}_mansionirisorsa SET
                                            eredita_t_corso = 'No',
                                            eredita_t_visita = 'No'
                                            WHERE mansionirisorsaid = ".$new_mansione_risorsa;
                $adb->query($q_update_mansione_risorsa);

                return 1;
            }
            else{
                return -2;
            }
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