<?php

/* kpro@bid21102016 */

/* kpro@bid190420170930 */

/**
 * @author BideseJacopo
 * @copyright (c) 2016, Kpro Consulting Srl
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

$path_ftp = "/home/erp/Import_Export/Export_da_Erp/";
$dir = 'Componenti_Impianto';
$dir_old = 'Old_File/';

$path_logs = "logs/";
$logs_file_name = $dir."_import_log.txt";
$dettaglio_logs_file_name = $dir."_dettaglio_import_log.txt";
$error_logs_file_name = $dir."_import_error.txt";

$data_corrente = date("Y-m-d");

$errori = 0;
$record_creati = 0;
$record_aggiornati = 0;
$record_processati = 0;

$data_inizio_importazione = date("Y-m-d H:i:s");

$report_finale = " 
IMPORTAZIONE COMPONENTI IMPIANTO delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$logs_file_name, "w+");
fwrite($handle_log_file, $report_finale);
fclose($handle_log_file);

$report_finale = " 
DETTAGLIO IMPORTAZIONE COMPONENTI IMPIANTO delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$dettaglio_logs_file_name, "w+");
fwrite($handle_log_file, $report_finale);
fclose($handle_log_file);

$report_finale = " 
ERRORI IMPORTAZIONE COMPONENTI IMPIANTO delle ".$data_inizio_importazione;
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
                    PulisciTabellaCustom();
                    RimuoviCapoRigaCSVperImport($path_ftp.$dir . '/' . $file);
                    if (($handle = fopen($path_ftp.$dir . '/'. $file, "r")) !== false) {
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

                                    $id_contratto_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[0], "Testo", false);
                                    $sezione_contratto_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[1], "Testo", false);
                                    $id_righe_mezzo_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[4], "Testo", false);
                                    $codice_azienda_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[2], "Testo", false);
                                    $codice_stabilimento_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[3], "Testo", false);
                                    $tipo_riga_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[26], "Testo", false);
                                    
                                    if($id_contratto_controllo != "" && $sezione_contratto_controllo != "" && $id_righe_mezzo_controllo != ""
                                        && $codice_azienda_controllo != "" && $codice_stabilimento_controllo != "" && $tipo_riga_controllo != ""){

                                        $record_processati++;

                                        $id_crm_impianto = ImportImpianti($array_dati_riga);
                                        
                                        if($id_crm_impianto != 0){

                                            switch($tipo_riga_controllo){                                        
                                                case 'ESTINTORI':                                                    
                                                    $res_import = ImportComponenti($array_dati_riga, $id_crm_impianto, $tipo_riga_controllo);
                                                    if($res_import == 1){
                                                        $record_creati++;
                                                    }
                                                    else if($res_import == 0){
                                                        $record_aggiornati++;
                                                    }
                                                    else{
                                                        $errori++;

                                                        $report_finale = " 
Componente vecchio non presente, non creato.";
                                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                        fwrite($handle_log_file, $report_finale);
                                                        fclose($handle_log_file);
                                                    }
                                                    break;
                                                case 'IDRANTI':
                                                    $res_import = ImportComponenti($array_dati_riga, $id_crm_impianto, $tipo_riga_controllo);
                                                    if($res_import == 1){
                                                        $record_creati++;
                                                    }
                                                    else if($res_import == 0){
                                                        $record_aggiornati++;
                                                    }
                                                    else{
                                                        $errori++;

                                                        $report_finale = " 
Componente vecchio non presente, non creato.";
                                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                        fwrite($handle_log_file, $report_finale);
                                                        fclose($handle_log_file);
                                                    }
                                                    break;
                                                case 'IMPIANTO':
                                                    $res_import = ImportComponenti($array_dati_riga, $id_crm_impianto, $tipo_riga_controllo);
                                                    if($res_import == 1){
                                                        $record_creati++;
                                                    }
                                                    else if($res_import == 0){
                                                        $record_aggiornati++;
                                                    }
                                                    else{
                                                        $errori++;

                                                        $report_finale = " 
Componente vecchio non presente, non creato.";
                                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                        fwrite($handle_log_file, $report_finale);
                                                        fclose($handle_log_file);
                                                    }
                                                    break;
                                                case 'PPA':
                                                    $res_import = ImportComponenti($array_dati_riga, $id_crm_impianto, $tipo_riga_controllo);
                                                    if($res_import == 1){
                                                        $record_creati++;
                                                    }
                                                    else if($res_import == 0){
                                                        $record_aggiornati++;
                                                    }
                                                    else{
                                                        $errori++;

                                                        $report_finale = " 
Componente vecchio non presente, non creato.";
                                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                        fwrite($handle_log_file, $report_finale);
                                                        fclose($handle_log_file);
                                                    }
                                                    break;
                                                case 'SERRAMENTI':
                                                    $res_import = ImportComponenti($array_dati_riga, $id_crm_impianto, $tipo_riga_controllo);
                                                    if($res_import == 1){
                                                        $record_creati++;
                                                    }
                                                    else if($res_import == 0){
                                                        $record_aggiornati++;
                                                    }
                                                    else{
                                                        $errori++;

                                                        $report_finale = " 
Componente vecchio non presente, non creato.";
                                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                        fwrite($handle_log_file, $report_finale);
                                                        fclose($handle_log_file);
                                                    }
                                                    break;
                                                case 'DETTAGLIO_ALTRI':
                                                    $res_import = ImportComponenti($array_dati_riga, $id_crm_impianto, $tipo_riga_controllo);
                                                    if($res_import == 1){
                                                        $record_creati++;
                                                    }
                                                    else if($res_import == 0){
                                                        $record_aggiornati++;
                                                    }
                                                    else{
                                                        $errori++;

                                                        $report_finale = " 
Componente vecchio non presente, non creato.";
                                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                        fwrite($handle_log_file, $report_finale);
                                                        fclose($handle_log_file);
                                                    }
                                                    break;
                                            }
                                        }
                                        else{
                                            $errori++;

                                            $report_finale = " 
Errore nella creazione/controllo del impianto del componente";
                                            $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                            fwrite($handle_log_file, $report_finale);
                                            fclose($handle_log_file);
                                        }
                                    }
                                    else{
                                        $errori++;

                                        $report_finale = " 
Record privo dei dati obbligatori: ".$id_contratto_controllo." - ".$sezione_contratto_controllo." - ".$id_righe_mezzo_controllo." - ".$codice_azienda_controllo." - ".$codice_stabilimento_controllo." - ".$tipo_riga_controllo;
                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                        fwrite($handle_log_file, $report_finale);
                                        fclose($handle_log_file);
                                    }
                            }
                            $row++;
                        }

                        ImportComponentiNonAggiornati();

                        PulisciTabellaCustom();

                        fclose($handle);

                        copy($path_ftp.$dir.'/'.$file, $path_ftp.$dir.'/'.$dir_old.date("YmdHis").'_'.$file); /* kpro@bid180420170930 */
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

function ImportImpianti($array_dati_riga){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $new_impianto = 0;

    $cod_azienda = $array_dati_riga[2];
    $cod_stabilimento = $array_dati_riga[3];
    $cod_impianto = $array_dati_riga[26];

    $anno = normalizzaStringaCsvPerImportCrm($array_dati_riga[11], "Numero", false);

    if($anno != 0){
        $data_attivazione_impianto = $anno."-01-01";
    }
    else{
        $data_attivazione_impianto = "1900-01-01";
    }

    $q_verifica_azienda = "SELECT acc.accountid
                        FROM {$table_prefix}_account acc
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = acc.accountid
                        WHERE ent.deleted = 0 AND acc.cod_cliente_gemma = '".$cod_azienda."'";
    $res_verifica_azienda = $adb->query($q_verifica_azienda);
    if($adb->num_rows($res_verifica_azienda) > 0){
        $id_azienda = $adb->query_result($res_verifica_azienda, 0, 'accountid');
        $id_azienda = html_entity_decode(strip_tags($id_azienda), ENT_QUOTES, $default_charset);

        $q_verifica_stabilimento = "SELECT stab.stabilimentiid,
                            stab.nome_stabilimento
                            FROM {$table_prefix}_stabilimenti stab
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = stab.stabilimentiid
                            WHERE ent.deleted = 0 
                            AND stab.azienda = ".$id_azienda."
                            AND stab.kp_cod_stab_gemma = '" . $cod_stabilimento . "'";
        $res_verifica_stabilimento = $adb->query($q_verifica_stabilimento);
        if($adb->num_rows($res_verifica_stabilimento) > 0){
            $id_stabilimento = $adb->query_result($res_verifica_stabilimento, 0, 'stabilimentiid');
            $id_stabilimento = html_entity_decode(strip_tags($id_stabilimento), ENT_QUOTES, $default_charset);

            $nome_stabilimento = $adb->query_result($res_verifica_stabilimento, 0, 'nome_stabilimento');
            $nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES, $default_charset);

            $nome_impianto = $cod_impianto." ".$nome_stabilimento;

            $q_verifica_impianto = "SELECT imp.impiantiid
                                FROM {$table_prefix}_impianti imp
                                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = imp.impiantiid
                                WHERE ent.deleted = 0
                                AND azienda = ".$id_azienda."
                                AND stabilimento = ".$id_stabilimento."
                                AND kp_gm_cod_impianto = '".$cod_impianto."'";
            $res_verifica_impianto = $adb->query($q_verifica_impianto);
            if($adb->num_rows($res_verifica_impianto) == 0){
                $impianto = CRMEntity::getInstance('Impianti');
                $impianto->column_fields['impianto_name'] = $nome_impianto;
                $impianto->column_fields['kp_gm_cod_impianto'] = $cod_impianto;
                $impianto->column_fields['azienda'] = $id_azienda;
                $impianto->column_fields['stabilimento'] = $id_stabilimento;
                $impianto->column_fields['data_attivazione_imp'] = $data_attivazione_impianto;
                $impianto->column_fields['stato_impianto'] = "Attivo";
                $impianto->column_fields['assigned_user_id'] = 1;
                $impianto->save('Impianti', $longdesc=true, $offline_update=false, $triggerEvent=false);

                $new_impianto = $impianto->id;
            }
            else{
                $new_impianto = $adb->query_result($res_verifica_impianto, 0, 'impiantiid');
                $new_impianto = html_entity_decode(strip_tags($new_impianto), ENT_QUOTES, $default_charset);

                $q_update_impianto = "UPDATE {$table_prefix}_impianti SET
                                    impianto_name = '".$nome_impianto."',
                                    data_attivazione_imp = '".$data_attivazione_impianto."'
                                    WHERE impiantiid = ".$new_impianto;
                $adb->query($q_update_impianto);
            }
        }
    }

    if ($new_impianto == 0 || $new_impianto == '' || $new_impianto == null) {
        $new_impianto = 0;
    }

    return $new_impianto;
}

function ImportComponenti($array_dati_riga, $id_crm_impianto, $tipo_componente_impianto){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $id_contratto = $array_dati_riga[0];
    $sezione_contratto = $array_dati_riga[1];
    $id_riga_mezzo = $array_dati_riga[4];
    $codice_mezzo = $array_dati_riga[5];
    $descrizione_mezzo = $array_dati_riga[6];
    $ubicazione = $array_dati_riga[25];
    $matricola = $array_dati_riga[9];
    $costruttore = $array_dati_riga[10];
    $anno = $array_dati_riga[11];
    $note = $array_dati_riga[7];
    $pos_cliente = $array_dati_riga[8];

    $mezzo = $array_dati_riga[12];
    $tipo = $array_dati_riga[13];
    $estinguente = $array_dati_riga[14];
    $carica = $array_dati_riga[15];
    $cl_fuoco = $array_dati_riga[16];
    $tipo_idrante = $array_dati_riga[19];
    $diametro_manichetta = $array_dati_riga[20];
    $tipo_lancia = $array_dati_riga[21];
    $lunghezza = $array_dati_riga[22];
    $tipo_apparato = $array_dati_riga[23];
    $sotto_tipo_apparato = $array_dati_riga[24];
    $car_testo_8 = $array_dati_riga[17];
    $car_testo_9 = $array_dati_riga[18];
    $reparto = $array_dati_riga[27];

    $prec_id_contratto = normalizzaStringaCsvPerImportCrm($array_dati_riga[28], "Testo", true);
    $prec_sezione_contratto = normalizzaStringaCsvPerImportCrm($array_dati_riga[29], "Testo", true);
    $prec_id_riga_mezzo = normalizzaStringaCsvPerImportCrm($array_dati_riga[30], "Testo", true);

    $anno_data = normalizzaStringaCsvPerImportCrm($anno, "Numero", false);

    if($anno_data != 0){
        $data_componente = $anno_data."-01-01";
    }
    else{
        $data_componente = "1900-01-01";
    }
    
    $q_verifica_componente = "SELECT comp.compimpiantoid
                        FROM {$table_prefix}_compimpianto comp
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = comp.compimpiantoid
                        WHERE ent.deleted = 0
                        AND comp.kp_gm_id_contratto = '".$id_contratto."'
                        AND comp.kp_gm_sez_contratto = '".$sezione_contratto."'
                        AND comp.kp_gm_id_riga_mezzo = '".$id_riga_mezzo."'";
    $res_verifica_componente = $adb->query($q_verifica_componente);
    if($adb->num_rows($res_verifica_componente) == 0){
        if($prec_id_contratto != '' && $prec_sezione_contratto != '' && $prec_id_riga_mezzo != ''){
            $q_verifica_componente_prec = "SELECT comp.compimpiantoid
                                FROM {$table_prefix}_compimpianto comp
                                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = comp.compimpiantoid
                                WHERE ent.deleted = 0
                                AND comp.kp_gm_id_contratto = '".$prec_id_contratto."'
                                AND comp.kp_gm_sez_contratto = '".$prec_sezione_contratto."'
                                AND comp.kp_gm_id_riga_mezzo = '".$prec_id_riga_mezzo."'";
            $res_verifica_componente_prec = $adb->query($q_verifica_componente_prec);
            if($adb->num_rows($res_verifica_componente_prec) > 0){
                $id_componente = $adb->query_result($res_verifica_componente_prec, 0, 'compimpiantoid');
                $id_componente = html_entity_decode(strip_tags($id_componente), ENT_QUOTES, $default_charset);

                $id_contratto = normalizzaStringaCsvPerImportCrm($id_contratto, "Testo", true);
                $sezione_contratto = normalizzaStringaCsvPerImportCrm($sezione_contratto, "Testo", true);
                $id_riga_mezzo = normalizzaStringaCsvPerImportCrm($id_riga_mezzo, "Testo", true);
                $codice_mezzo = normalizzaStringaCsvPerImportCrm($codice_mezzo, "Testo", true);
                $descrizione_mezzo = normalizzaStringaCsvPerImportCrm($descrizione_mezzo, "Testo", true);
                $ubicazione = normalizzaStringaCsvPerImportCrm($ubicazione, "Testo", true);
                $matricola = normalizzaStringaCsvPerImportCrm($matricola, "Testo", true);
                $costruttore = normalizzaStringaCsvPerImportCrm($costruttore, "Testo", true);
                $anno = normalizzaStringaCsvPerImportCrm($anno, "Testo", true);
                $note = normalizzaStringaCsvPerImportCrm($note, "Testo", true);
                $pos_cliente = normalizzaStringaCsvPerImportCrm($pos_cliente, "Testo", true);

                $mezzo = normalizzaStringaCsvPerImportCrm($mezzo, "Testo", true);
                $tipo = normalizzaStringaCsvPerImportCrm($tipo, "Testo", true);
                $estinguente = normalizzaStringaCsvPerImportCrm($estinguente, "Testo", true);
                $carica = normalizzaStringaCsvPerImportCrm($carica, "Testo", true);
                $cl_fuoco = normalizzaStringaCsvPerImportCrm($cl_fuoco, "Testo", true);
                $tipo_idrante = normalizzaStringaCsvPerImportCrm($tipo_idrante, "Testo", true);
                $diametro_manichetta = normalizzaStringaCsvPerImportCrm($diametro_manichetta, "Testo", true);
                $tipo_lancia = normalizzaStringaCsvPerImportCrm($tipo_lancia, "Testo", true);
                $lunghezza = normalizzaStringaCsvPerImportCrm($lunghezza, "Testo", true);
                $tipo_apparato = normalizzaStringaCsvPerImportCrm($tipo_apparato, "Testo", true);
                $sotto_tipo_apparato = normalizzaStringaCsvPerImportCrm($sotto_tipo_apparato, "Testo", true);
                $car_testo_8 = normalizzaStringaCsvPerImportCrm($car_testo_8, "Testo", true);
                $car_testo_9 = normalizzaStringaCsvPerImportCrm($car_testo_9, "Testo", true);
                $reparto = normalizzaStringaCsvPerImportCrm($reparto, "Testo", true);

                $q_update_componente = "UPDATE {$table_prefix}_compimpianto SET
                                    nome_componente = '".$descrizione_mezzo."',
                                    impianto = ".$id_crm_impianto.",
                                    data = '".$data_componente."',
                                    stato_componente = 'Attivo',
                                    kp_gm_matricola = '".$matricola."',
                                    kp_gm_ubicazione = '".$ubicazione."',
                                    kp_gm_costruttore = '".$costruttore."',
                                    kp_gm_note = '".$note."',
                                    kp_gm_anno = '".$anno."',
                                    kp_gm_tipo_impianto = '".$tipo_componente_impianto."',
                                    kp_gm_pos_cliente = '".$pos_cliente."',
                                    kp_gm_id_contratto = '".$id_contratto."',
                                    kp_gm_sez_contratto = '".$sezione_contratto."',
                                    kp_gm_id_riga_mezzo = '".$id_riga_mezzo."',
                                    kp_gm_codice_mezzo = '".$codice_mezzo."',
                                    kp_gm_mezzo_est = '".$mezzo."',
                                    kp_gm_tipo_est = '".$tipo."',
                                    kp_gm_esting_est = '".$estinguente."',
                                    kp_gm_carica_est = '".$carica."',
                                    kp_gm_cl_fuoco_est = '".$cl_fuoco."',
                                    kp_gm_tipo_idrante = '".$tipo_idrante."',
                                    kp_gm_diametro_man = '".$diametro_manichetta."',
                                    kp_gm_tipo_lancia = '".$tipo_lancia."',
                                    kp_gm_lunghezza = '".$lunghezza."',
                                    kp_gm_tipo_apparato = '".$tipo_apparato."',
                                    kp_gm_sub_tipo_app = '".$sotto_tipo_apparato."',
                                    kp_gm_cartesto8 = '".$car_testo_8."',
                                    kp_gm_cartesto9 = '".$car_testo_9."',
                                    kp_gm_reparto = '".$reparto."'
                                    WHERE compimpiantoid = ".$id_componente;
                $adb->query($q_update_componente);

                $report_finale = " 
 - Aggiornato Componente ".$id_contratto." - ".$sezione_contratto." - ".$id_riga_mezzo." -> precedente ".$prec_id_contratto." - ".$prec_sezione_contratto." - ".$prec_id_riga_mezzo;
                $handle_log_file=fopen($GLOBALS['path_logs'].$GLOBALS['dettaglio_logs_file_name'], "a+");
                fwrite($handle_log_file, $report_finale);
                fclose($handle_log_file);

                return 0;
            }
            else{
                $q_verifica_componente_prec_custom = "SELECT compimpiantoid
                                    FROM kp_import_componenti_gemma
                                    WHERE kp_aggiornato = 0
                                    AND kp_gm_id_contratto = '".$prec_id_contratto."'
                                    AND kp_gm_sez_contratto = '".$prec_sezione_contratto."'
                                    AND kp_gm_id_riga_mezzo = '".$prec_id_riga_mezzo."'";
                $res_verifica_componente_prec_custom = $adb->query($q_verifica_componente_prec_custom);
                if($adb->num_rows($res_verifica_componente_prec_custom) > 0){
                    $componente = CRMEntity::getInstance('CompImpianto');
                    $componente->column_fields['assigned_user_id'] = 1;
                    $componente->column_fields['nome_componente'] = $descrizione_mezzo;
                    $componente->column_fields['impianto'] = $id_crm_impianto;
                    $componente->column_fields['data'] = $data_componente;
                    $componente->column_fields['stato_componente'] = 'Attivo';
                    $componente->column_fields['kp_gm_matricola'] = $matricola;
                    $componente->column_fields['kp_gm_ubicazione'] = $ubicazione;
                    $componente->column_fields['kp_gm_costruttore'] = $costruttore;
                    $componente->column_fields['kp_gm_note'] = $note;
                    $componente->column_fields['kp_gm_anno'] = $anno;
                    $componente->column_fields['kp_gm_tipo_impianto'] = $tipo_componente_impianto;
                    $componente->column_fields['kp_gm_pos_cliente'] = $pos_cliente;
                    $componente->column_fields['kp_gm_id_contratto'] = $id_contratto;
                    $componente->column_fields['kp_gm_sez_contratto'] = $sezione_contratto;
                    $componente->column_fields['kp_gm_id_riga_mezzo'] = $id_riga_mezzo;
                    $componente->column_fields['kp_gm_codice_mezzo'] = $codice_mezzo;

                    $componente->column_fields['kp_gm_mezzo_est'] = $mezzo;
                    $componente->column_fields['kp_gm_tipo_est'] = $tipo;
                    $componente->column_fields['kp_gm_esting_est'] = $estinguente;
                    $componente->column_fields['kp_gm_carica_est'] = $carica;
                    $componente->column_fields['kp_gm_cl_fuoco_est'] = $cl_fuoco;
                    $componente->column_fields['kp_gm_tipo_idrante'] = $tipo_idrante;
                    $componente->column_fields['kp_gm_diametro_man'] = $diametro_manichetta;
                    $componente->column_fields['kp_gm_tipo_lancia'] = $tipo_lancia;
                    $componente->column_fields['kp_gm_lunghezza'] = $lunghezza;
                    $componente->column_fields['kp_gm_tipo_apparato'] = $tipo_apparato;
                    $componente->column_fields['kp_gm_sub_tipo_app'] = $sotto_tipo_apparato;
                    $componente->column_fields['kp_gm_cartesto8'] = $car_testo_8;
                    $componente->column_fields['kp_gm_cartesto9'] = $car_testo_9;
                    $componente->column_fields['kp_gm_reparto'] = $reparto;
                    $componente->save('CompImpianto', $longdesc=true, $offline_update=false, $triggerEvent=false);

                    $new_componente = $componente->id;

                    $report_finale = " 
 - Creato Componente Aggiornato ".$id_contratto." - ".$sezione_contratto." - ".$id_riga_mezzo." -> precedente ".$prec_id_contratto." - ".$prec_sezione_contratto." - ".$prec_id_riga_mezzo;
                    $handle_log_file=fopen($GLOBALS['path_logs'].$GLOBALS['dettaglio_logs_file_name'], "a+");
                    fwrite($handle_log_file, $report_finale);
                    fclose($handle_log_file);

                    $upd_componente_temporaneo = "UPDATE kp_import_componenti_gemma SET
                                                kp_aggiornato = 1
                                                WHERE kp_gm_id_contratto = '".$prec_id_contratto."'
                                                AND kp_gm_sez_contratto = '".$prec_sezione_contratto."'
                                                AND kp_gm_id_riga_mezzo = '".$prec_id_riga_mezzo."'";
                    $adb->query($upd_componente_temporaneo);

                    return 1;
                }
                else{
                    $report_finale = " 
 - Componente precedente da aggiornare non trovato ".$prec_id_contratto." - ".$prec_sezione_contratto." - ".$prec_id_riga_mezzo;
                    $handle_log_file=fopen($GLOBALS['path_logs'].$GLOBALS['dettaglio_logs_file_name'], "a+");
                    fwrite($handle_log_file, $report_finale);
                    fclose($handle_log_file);

                    return -1;
                }
            }
        }
        else{
            $q_crea_temporaneo = "INSERT INTO kp_import_componenti_gemma 
                                (nome_componente, 
                                impianto,  
                                data, 
                                stato_componente,  
                                kp_gm_id_contratto, 
                                kp_gm_sez_contratto, 
                                kp_gm_id_riga_mezzo, 
                                kp_gm_codice_mezzo, 
                                kp_gm_matricola, 
                                kp_gm_costruttore, 
                                kp_gm_anno, 
                                kp_gm_note, 
                                kp_gm_ubicazione, 
                                kp_gm_tipo_impianto, 
                                kp_gm_pos_cliente, 
                                kp_gm_mezzo_est, 
                                kp_gm_tipo_est, 
                                kp_gm_esting_est, 
                                kp_gm_carica_est, 
                                kp_gm_cl_fuoco_est, 
                                kp_gm_tipo_idrante, 
                                kp_gm_diametro_man, 
                                kp_gm_tipo_lancia, 
                                kp_gm_lunghezza, 
                                kp_gm_tipo_apparato, 
                                kp_gm_sub_tipo_app, 
                                kp_gm_cartesto8, 
                                kp_gm_cartesto9, 
                                kp_gm_reparto, 
                                kp_aggiornato
                                )
                                VALUES
                                ('{$descrizione_mezzo}', 
                                {$id_crm_impianto}, 
                                '{$data_componente}', 
                                'Attivo', 
                                '{$id_contratto}', 
                                '{$sezione_contratto}', 
                                '{$id_riga_mezzo}', 
                                '{$codice_mezzo}', 
                                '{$matricola}', 
                                '{$costruttore}', 
                                '{$anno}', 
                                '{$note}', 
                                '{$ubicazione}', 
                                '{$tipo_componente_impianto}', 
                                '{$pos_cliente}', 
                                '{$mezzo}', 
                                '{$tipo}', 
                                '{$estinguente}', 
                                '{$carica}', 
                                '{$cl_fuoco}', 
                                '{$tipo_idrante}', 
                                '{$diametro_manichetta}', 
                                '{$tipo_lancia}', 
                                '{$lunghezza}', 
                                '{$tipo_apparato}', 
                                '{$sotto_tipo_apparato}', 
                                '{$car_testo_8}', 
                                '{$car_testo_9}', 
                                '{$reparto}', 
                                0)";
            $adb->query($q_crea_temporaneo);

            $report_finale = " 
 - Creato Componente Temporaneo ".$id_contratto." - ".$sezione_contratto." - ".$id_riga_mezzo;
            $handle_log_file=fopen($GLOBALS['path_logs'].$GLOBALS['dettaglio_logs_file_name'], "a+");
            fwrite($handle_log_file, $report_finale);
            fclose($handle_log_file);

            return 1;
        }
    }
    else{
        $id_componente = $adb->query_result($res_verifica_componente, 0, 'compimpiantoid');
        $id_componente = html_entity_decode(strip_tags($id_componente), ENT_QUOTES, $default_charset);

        $id_contratto = normalizzaStringaCsvPerImportCrm($id_contratto, "Testo", true);
        $sezione_contratto = normalizzaStringaCsvPerImportCrm($sezione_contratto, "Testo", true);
        $id_riga_mezzo = normalizzaStringaCsvPerImportCrm($id_riga_mezzo, "Testo", true);
        $codice_mezzo = normalizzaStringaCsvPerImportCrm($codice_mezzo, "Testo", true);
        $descrizione_mezzo = normalizzaStringaCsvPerImportCrm($descrizione_mezzo, "Testo", true);
        $ubicazione = normalizzaStringaCsvPerImportCrm($ubicazione, "Testo", true);
        $matricola = normalizzaStringaCsvPerImportCrm($matricola, "Testo", true);
        $costruttore = normalizzaStringaCsvPerImportCrm($costruttore, "Testo", true);
        $anno = normalizzaStringaCsvPerImportCrm($anno, "Testo", true);
        $note = normalizzaStringaCsvPerImportCrm($note, "Testo", true);
        $pos_cliente = normalizzaStringaCsvPerImportCrm($pos_cliente, "Testo", true);

        $mezzo = normalizzaStringaCsvPerImportCrm($mezzo, "Testo", true);
        $tipo = normalizzaStringaCsvPerImportCrm($tipo, "Testo", true);
        $estinguente = normalizzaStringaCsvPerImportCrm($estinguente, "Testo", true);
        $carica = normalizzaStringaCsvPerImportCrm($carica, "Testo", true);
        $cl_fuoco = normalizzaStringaCsvPerImportCrm($cl_fuoco, "Testo", true);
        $tipo_idrante = normalizzaStringaCsvPerImportCrm($tipo_idrante, "Testo", true);
        $diametro_manichetta = normalizzaStringaCsvPerImportCrm($diametro_manichetta, "Testo", true);
        $tipo_lancia = normalizzaStringaCsvPerImportCrm($tipo_lancia, "Testo", true);
        $lunghezza = normalizzaStringaCsvPerImportCrm($lunghezza, "Testo", true);
        $tipo_apparato = normalizzaStringaCsvPerImportCrm($tipo_apparato, "Testo", true);
        $sotto_tipo_apparato = normalizzaStringaCsvPerImportCrm($sotto_tipo_apparato, "Testo", true);
        $car_testo_8 = normalizzaStringaCsvPerImportCrm($car_testo_8, "Testo", true);
        $car_testo_9 = normalizzaStringaCsvPerImportCrm($car_testo_9, "Testo", true);
        $reparto = normalizzaStringaCsvPerImportCrm($reparto, "Testo", true);

        $q_update_componente = "UPDATE {$table_prefix}_compimpianto SET
                            nome_componente = '".$descrizione_mezzo."',
                            impianto = ".$id_crm_impianto.",
                            data = '".$data_componente."',
                            stato_componente = 'Attivo',
                            kp_gm_matricola = '".$matricola."',
                            kp_gm_ubicazione = '".$ubicazione."',
                            kp_gm_costruttore = '".$costruttore."',
                            kp_gm_note = '".$note."',
                            kp_gm_anno = '".$anno."',
                            kp_gm_tipo_impianto = '".$tipo_componente_impianto."',
                            kp_gm_pos_cliente = '".$pos_cliente."',
                            kp_gm_id_contratto = '".$id_contratto."',
                            kp_gm_sez_contratto = '".$sezione_contratto."',
                            kp_gm_id_riga_mezzo = '".$id_riga_mezzo."',
                            kp_gm_codice_mezzo = '".$codice_mezzo."',
                            kp_gm_mezzo_est = '".$mezzo."',
                            kp_gm_tipo_est = '".$tipo."',
                            kp_gm_esting_est = '".$estinguente."',
                            kp_gm_carica_est = '".$carica."',
                            kp_gm_cl_fuoco_est = '".$cl_fuoco."',
                            kp_gm_tipo_idrante = '".$tipo_idrante."',
                            kp_gm_diametro_man = '".$diametro_manichetta."',
                            kp_gm_tipo_lancia = '".$tipo_lancia."',
                            kp_gm_lunghezza = '".$lunghezza."',
                            kp_gm_tipo_apparato = '".$tipo_apparato."',
                            kp_gm_sub_tipo_app = '".$sotto_tipo_apparato."',
                            kp_gm_cartesto8 = '".$car_testo_8."',
                            kp_gm_cartesto9 = '".$car_testo_9."',
                            kp_gm_reparto = '".$reparto."'
                            WHERE compimpiantoid = ".$id_componente;
        $adb->query($q_update_componente);

        $report_finale = " 
 - Aggiornato Componente ".$id_contratto." - ".$sezione_contratto." - ".$id_riga_mezzo;
        $handle_log_file=fopen($GLOBALS['path_logs'].$GLOBALS['dettaglio_logs_file_name'], "a+");
        fwrite($handle_log_file, $report_finale);
        fclose($handle_log_file);

        if($prec_id_contratto != '' && $prec_sezione_contratto != '' && $prec_id_riga_mezzo != ''){
            $q_verifica_componente_prec_custom = "SELECT compimpiantoid
                                FROM kp_import_componenti_gemma
                                WHERE kp_aggiornato = 0
                                AND kp_gm_id_contratto = '".$prec_id_contratto."'
                                AND kp_gm_sez_contratto = '".$prec_sezione_contratto."'
                                AND kp_gm_id_riga_mezzo = '".$prec_id_riga_mezzo."'";
            $res_verifica_componente_prec_custom = $adb->query($q_verifica_componente_prec_custom);
            if($adb->num_rows($res_verifica_componente_prec_custom) > 0){
                $upd_componente_temporaneo = "UPDATE kp_import_componenti_gemma SET
                                            kp_aggiornato = 1
                                            WHERE kp_gm_id_contratto = '".$prec_id_contratto."'
                                            AND kp_gm_sez_contratto = '".$prec_sezione_contratto."'
                                            AND kp_gm_id_riga_mezzo = '".$prec_id_riga_mezzo."'";
                $adb->query($upd_componente_temporaneo);
            }
        }

        return 0;
    }
}

function ImportComponentiNonAggiornati(){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $q_componenti_non_aggiornati = "SELECT * 
                                FROM kp_import_componenti_gemma
                                WHERE kp_aggiornato = 0";
    $res_componenti_non_aggiornati = $adb->query($q_componenti_non_aggiornati);
    $num_componenti_non_aggiornati = $adb->num_rows($res_componenti_non_aggiornati);
    if($num_componenti_non_aggiornati > 0){
        for($i = 0; $i < $num_componenti_non_aggiornati; $i++){
            $descrizione_mezzo = $adb->query_result($res_componenti_non_aggiornati, $i, 'nome_componente');
            $descrizione_mezzo = html_entity_decode(strip_tags($descrizione_mezzo), ENT_QUOTES, $default_charset);

            $id_crm_impianto = $adb->query_result($res_componenti_non_aggiornati, $i, 'impianto');
            $id_crm_impianto = html_entity_decode(strip_tags($id_crm_impianto), ENT_QUOTES, $default_charset);

            $data_componente = $adb->query_result($res_componenti_non_aggiornati, $i, 'data');
            $data_componente = html_entity_decode(strip_tags($data_componente), ENT_QUOTES, $default_charset);

            $matricola = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_matricola');
            $matricola = html_entity_decode(strip_tags($matricola), ENT_QUOTES, $default_charset);

            $ubicazione = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_ubicazione');
            $ubicazione = html_entity_decode(strip_tags($ubicazione), ENT_QUOTES, $default_charset);

            $costruttore = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_costruttore');
            $costruttore = html_entity_decode(strip_tags($costruttore), ENT_QUOTES, $default_charset);

            $note = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_note');
            $note = html_entity_decode(strip_tags($note), ENT_QUOTES, $default_charset);

            $anno = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_anno');
            $anno = html_entity_decode(strip_tags($anno), ENT_QUOTES, $default_charset);

            $tipo_componente_impianto = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_tipo_impianto');
            $tipo_componente_impianto = html_entity_decode(strip_tags($tipo_componente_impianto), ENT_QUOTES, $default_charset);

            $pos_cliente = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_pos_cliente');
            $pos_cliente = html_entity_decode(strip_tags($pos_cliente), ENT_QUOTES, $default_charset);

            $id_contratto = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_id_contratto');
            $id_contratto = html_entity_decode(strip_tags($id_contratto), ENT_QUOTES, $default_charset);

            $sezione_contratto = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_sez_contratto');
            $sezione_contratto = html_entity_decode(strip_tags($sezione_contratto), ENT_QUOTES, $default_charset);

            $id_riga_mezzo = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_id_riga_mezzo');
            $id_riga_mezzo = html_entity_decode(strip_tags($id_riga_mezzo), ENT_QUOTES, $default_charset);

            $codice_mezzo = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_codice_mezzo');
            $codice_mezzo = html_entity_decode(strip_tags($codice_mezzo), ENT_QUOTES, $default_charset);

            $mezzo = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_mezzo_est');
            $mezzo = html_entity_decode(strip_tags($mezzo), ENT_QUOTES, $default_charset);

            $tipo = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_tipo_est');
            $tipo = html_entity_decode(strip_tags($tipo), ENT_QUOTES, $default_charset);

            $estinguente = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_esting_est');
            $estinguente = html_entity_decode(strip_tags($estinguente), ENT_QUOTES, $default_charset);

            $carica = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_carica_est');
            $carica = html_entity_decode(strip_tags($carica), ENT_QUOTES, $default_charset);

            $cl_fuoco = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_cl_fuoco_est');
            $cl_fuoco = html_entity_decode(strip_tags($cl_fuoco), ENT_QUOTES, $default_charset);

            $tipo_idrante = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_tipo_idrante');
            $tipo_idrante = html_entity_decode(strip_tags($tipo_idrante), ENT_QUOTES, $default_charset);

            $diametro_manichetta = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_diametro_man');
            $diametro_manichetta = html_entity_decode(strip_tags($diametro_manichetta), ENT_QUOTES, $default_charset);

            $tipo_lancia = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_tipo_lancia');
            $tipo_lancia = html_entity_decode(strip_tags($tipo_lancia), ENT_QUOTES, $default_charset);

            $lunghezza = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_lunghezza');
            $lunghezza = html_entity_decode(strip_tags($lunghezza), ENT_QUOTES, $default_charset);

            $tipo_apparato = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_tipo_apparato');
            $tipo_apparato = html_entity_decode(strip_tags($tipo_apparato), ENT_QUOTES, $default_charset);

            $sotto_tipo_apparato = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_sub_tipo_app');
            $sotto_tipo_apparato = html_entity_decode(strip_tags($sotto_tipo_apparato), ENT_QUOTES, $default_charset);

            $car_testo_8 = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_cartesto8');
            $car_testo_8 = html_entity_decode(strip_tags($car_testo_8), ENT_QUOTES, $default_charset);

            $car_testo_9 = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_cartesto9');
            $car_testo_9 = html_entity_decode(strip_tags($car_testo_9), ENT_QUOTES, $default_charset);

            $reparto = $adb->query_result($res_componenti_non_aggiornati, $i, 'kp_gm_reparto');
            $reparto = html_entity_decode(strip_tags($reparto), ENT_QUOTES, $default_charset);

            $componente = CRMEntity::getInstance('CompImpianto');
            $componente->column_fields['assigned_user_id'] = 1;
            $componente->column_fields['nome_componente'] = $descrizione_mezzo;
            $componente->column_fields['impianto'] = $id_crm_impianto;
            $componente->column_fields['data'] = $data_componente;
            $componente->column_fields['stato_componente'] = 'Attivo';
            $componente->column_fields['kp_gm_matricola'] = $matricola;
            $componente->column_fields['kp_gm_ubicazione'] = $ubicazione;
            $componente->column_fields['kp_gm_costruttore'] = $costruttore;
            $componente->column_fields['kp_gm_note'] = $note;
            $componente->column_fields['kp_gm_anno'] = $anno;
            $componente->column_fields['kp_gm_tipo_impianto'] = $tipo_componente_impianto;
            $componente->column_fields['kp_gm_pos_cliente'] = $pos_cliente;
            $componente->column_fields['kp_gm_id_contratto'] = $id_contratto;
            $componente->column_fields['kp_gm_sez_contratto'] = $sezione_contratto;
            $componente->column_fields['kp_gm_id_riga_mezzo'] = $id_riga_mezzo;
            $componente->column_fields['kp_gm_codice_mezzo'] = $codice_mezzo;

            $componente->column_fields['kp_gm_mezzo_est'] = $mezzo;
            $componente->column_fields['kp_gm_tipo_est'] = $tipo;
            $componente->column_fields['kp_gm_esting_est'] = $estinguente;
            $componente->column_fields['kp_gm_carica_est'] = $carica;
            $componente->column_fields['kp_gm_cl_fuoco_est'] = $cl_fuoco;
            $componente->column_fields['kp_gm_tipo_idrante'] = $tipo_idrante;
            $componente->column_fields['kp_gm_diametro_man'] = $diametro_manichetta;
            $componente->column_fields['kp_gm_tipo_lancia'] = $tipo_lancia;
            $componente->column_fields['kp_gm_lunghezza'] = $lunghezza;
            $componente->column_fields['kp_gm_tipo_apparato'] = $tipo_apparato;
            $componente->column_fields['kp_gm_sub_tipo_app'] = $sotto_tipo_apparato;
            $componente->column_fields['kp_gm_cartesto8'] = $car_testo_8;
            $componente->column_fields['kp_gm_cartesto9'] = $car_testo_9;
            $componente->column_fields['kp_gm_reparto'] = $reparto;
            $componente->save('CompImpianto', $longdesc=true, $offline_update=false, $triggerEvent=false);

            $new_componente = $componente->id;

            $report_finale = " 
 - Creato Componente NON Aggiornato ".$id_contratto." - ".$sezione_contratto." - ".$id_riga_mezzo;
            $handle_log_file=fopen($GLOBALS['path_logs'].$GLOBALS['dettaglio_logs_file_name'], "a+");
            fwrite($handle_log_file, $report_finale);
            fclose($handle_log_file);
        }
    }
}

function PulisciTabellaCustom(){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $q_pulisci_tabella_custom = "TRUNCATE TABLE kp_import_componenti_gemma";
    $adb->query($q_pulisci_tabella_custom);

}

/*

0) IDCONTRATTO 
1) SEZIONECONTRATTO 
2) CODCLIENTE 
3) CODDEST 
4) IdrigaMezzo 
5) CodMezzo 
6) DescrizioneMezzo 
7) Note 
8) PosCliente 
9) Matricola 
10) Costruttore 
11) ANNO 
12) MEZZO 
13) TIPO 
14) Estinguente 
15) Carica 
16) CL_FUOCO 
17) CarTesto8 
18) CarTesto9 
19) Tipo_Idrante 
20) Diametro_Man 
21) Tipo_Lancia 
22) Lunghezza 
23) Tipo_Apparato 
24) SottoTipo_Apparato 
25) Ubicazione 
26) TIPO_RIGA 
27) Reparto 

*/

?>