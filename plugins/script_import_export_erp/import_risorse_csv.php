<?php

/* kpro@bid02092016 */

/**
 * @author BideseJacopo
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package importExport
 * @version 1.0
 */

require_once('../import_export_utils/import_utils.php'); /* kpro@bid190420171000 */

include_once('/../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

$current_user->id = 1;

$path_ftp = "/home/erp/Import_Export/Export_da_Erp/";
$dir = 'Risorse';
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
IMPORTAZIONE RISORSE delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$logs_file_name, "w+");
fwrite($handle_log_file, $report_finale);
fclose($handle_log_file);

$report_finale = "
ERRORI IMPORTAZIONE RISORSE delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$error_logs_file_name, "w+");
fwrite($handle_log_file, $report_finale);
fclose($handle_log_file);

// connect and login to FTP server
$ftp_server = "";
$ftp_username = '';
$ftp_userpass = '';
$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
$login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass);

$local_file = $path_ftp.$dir.'/'.$data_per_nome_file_csv."_risorse_rochling.csv";
$server_file = "RISORSE_KPRO.csv";

// download server file
if (ftp_get($ftp_conn, $local_file, $server_file, FTP_BINARY)){
  
    ftp_close($ftp_conn);

    $report_finale = " 
Download file eseguito con successo!";
    $handle_log_file=fopen($path_logs.$logs_file_name, "a+");
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
                            //$file_csv_size = filesize($path_ftp.$dir . '/'. $file);
                            while (($array_dati_riga = fgetcsv($handle, 0, ";")) !== false) {
                                
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
                                    
                                    $codice_azienda_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[0], "Testo", false);
                                    $nome_azienda_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[4], "Testo", false);
                                    $codice_stabilimento_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[5], "Testo", false);
                                    $nome_stabilimento_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[6], "Testo", false);
                                    $codice_risorsa_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[1], "Testo", false);
                                    $cognome_risorsa_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[2], "Testo", false);
                                    $codice_fiscale_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[14], "Testo", false);
                                
                                    if($codice_azienda_controllo != '' && $nome_azienda_controllo != ''
                                    && $codice_stabilimento_controllo != '' && $nome_stabilimento_controllo != ''
                                    && $codice_risorsa_controllo != '' && $cognome_risorsa_controllo != '' && $codice_fiscale_controllo != ''){

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
                                            $data_cessazione_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[11], "Data", false);
                                            if($data_cessazione_controllo != "" && $data_cessazione_controllo > $data_corrente){

                                                $id_crm_azienda = ControlloAzienda($array_dati_riga); 
                                                if($id_crm_azienda != 0){

                                                    $id_crm_stabilimento = ControlloStabilimento($array_dati_riga, $id_crm_azienda);
                                                    if($id_crm_stabilimento != 0){

                                                        $res_controllo_risorsa = ControlloRisorsa($array_dati_riga, $id_crm_azienda, $id_crm_stabilimento);
                                                        if($res_controllo_risorsa == 1){
                                                            $record_creati++;
                                                        }
                                                        else{
                                                            $record_aggiornati++;
                                                        }
                                                    }
                                                    else{
                                                        $errori++;

                                                        $report_finale = "
".$codice_stabilimento_controllo." - ".$nome_stabilimento_controllo.": Errore durante la creazione/aggiornamento dello stabilimento";
                                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                        fwrite($handle_log_file, $report_finale);
                                                        fclose($handle_log_file);
                                                    }
                                                }
                                                else{
                                                    $errori++;

                                                    $report_finale = "
".$codice_azienda_controllo." - ".$nome_azienda_controllo.": Errore durante la creazione/aggiornamento dell'azienda";
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
Record privo dei campi obbligatori: ".$codice_azienda_controllo." - ".$nome_azienda_controllo." - ".$codice_stabilimento_controllo." - ".$nome_stabilimento_controllo." - ".$codice_risorsa_controllo." - ".$cognome_risorsa_controllo." - ".$codice_fiscale_controllo;
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
}
else{
    ftp_close($ftp_conn);

    $report_finale = " 
Errore nel download del file.";
    $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
    fwrite($handle_log_file, $report_finale);
    fclose($handle_log_file);
}

function ControlloAzienda($dati_riga) {
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $codice_azienda = $dati_riga[0];
    $nome_azienda = $dati_riga[4];

    $q_verifica_azienda = "SELECT acc.accountid
                        FROM {$table_prefix}_account acc
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = acc.accountid
                        WHERE ent.deleted = 0 AND acc.kp_cod_erp_azienda = '" . $codice_azienda . "'";

    $res_verifica_azienda = $adb->query($q_verifica_azienda);
    if ($adb->num_rows($res_verifica_azienda) == 0) {
        $account = CRMEntity::getInstance('Accounts');
        $account->column_fields['kp_cod_erp_azienda'] = $codice_azienda;
        $account->column_fields['accountname'] = $nome_azienda;
        $account->column_fields['assigned_user_id'] = 1;
        $account->save('Accounts', $longdesc=true, $offline_update=false, $triggerEvent=false);

        $new_account = $account->id;
    } else {
        $new_account = $adb->query_result($res_verifica_azienda, 0, 'accountid');
        $new_account = html_entity_decode(strip_tags($new_account), ENT_QUOTES, $default_charset);
    }

    if ($new_account == 0 || $new_account == '' || $new_account == null) {
        $new_account = 0;
    }

    return $new_account;
}

function ControlloStabilimento($dati_riga, $id_azienda_crm) {
    global $adb, $table_prefix, $default_charset;

    $codice_stabilimento = $dati_riga[5];
    $nome_stabilimento = $dati_riga[6];

    $q_verifica_stabilimento = "SELECT stab.stabilimentiid
                        FROM {$table_prefix}_stabilimenti stab
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = stab.stabilimentiid
                        WHERE ent.deleted = 0 
                        AND stab.azienda = ".$id_azienda_crm."
                        AND stab.kp_cod_erp_stabilim = '" . $codice_stabilimento . "'";

    $res_verifica_stabilimento = $adb->query($q_verifica_stabilimento);
    if ($adb->num_rows($res_verifica_stabilimento) == 0) {
            $stabilimento = CRMEntity::getInstance('Stabilimenti');
            $stabilimento->column_fields['kp_cod_erp_stabilim'] = $codice_stabilimento;
            $stabilimento->column_fields['nome_stabilimento'] = $nome_stabilimento;
            $stabilimento->column_fields['azienda'] = $id_azienda_crm;
            $stabilimento->column_fields['assigned_user_id'] = 1;
            $stabilimento->save('Stabilimenti', $longdesc=true, $offline_update=false, $triggerEvent=false);

            $new_stabilimento = $stabilimento->id;
    } else {
        $new_stabilimento = $adb->query_result($res_verifica_stabilimento, 0, 'stabilimentiid');
        $new_stabilimento = html_entity_decode(strip_tags($new_stabilimento), ENT_QUOTES, $default_charset);
    }

    if ($new_stabilimento == 0 || $new_stabilimento == '' || $new_stabilimento == null) {
        $new_stabilimento = 0;
    }

    return $new_stabilimento;
}

function ControlloRisorsa($dati_riga, $id_crm_azienda, $id_crm_stabilimento) {
    global $adb, $table_prefix, $default_charset;

    $codice_fiscale = $dati_riga[14];
    $codice_erp_risorsa = $dati_riga[1];
    $cognome = $dati_riga[2];
    $nome = $dati_riga[3];
    $data_assunzione = $dati_riga[7];
    $data_cessazione = $dati_riga[11];
    $compleanno = $dati_riga[8];
    $desc_tipo_contratto = $dati_riga[9];
    $email = $dati_riga[10];
    $sesso = $dati_riga[12];
    $stato = $dati_riga[15];
    $provincia = $dati_riga[16];
    $citta = $dati_riga[18];
    $indirizzo = $dati_riga[19];
    $numero_civico = $dati_riga[20];
    $indirizzo = $indirizzo . ', ' . $numero_civico;
    $cap = $dati_riga[21];
    $cod_centro_di_costo = normalizzaStringaCsvPerImportCrm($dati_riga[22], "Testo", false);
    $desc_centro_di_costo = normalizzaStringaCsvPerImportCrm($dati_riga[23], "Testo", false);

    ImportPicklistMultilinguaggio('kp_centro_di_costo','it_it',$cod_centro_di_costo,$desc_centro_di_costo);

    $data_assunzione = normalizzaStringaCsvPerImportCrm($dati_riga[7], "Data", false);
    $data_cessazione = normalizzaStringaCsvPerImportCrm($dati_riga[11], "Data", false);
    $compleanno = normalizzaStringaCsvPerImportCrm($dati_riga[8], "Data", false);

    if($data_cessazione == '2999-12-31'){
        $data_cessazione = '';
    }

    $q_verifica_risorsa = "SELECT cont.contactid
                        FROM {$table_prefix}_contactdetails cont
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = cont.contactid
                        WHERE ent.deleted = 0 AND cont.kp_codice_fiscale = '".$codice_fiscale."'";

    $res_verifica_risorsa = $adb->query($q_verifica_risorsa);
    if($adb->num_rows($res_verifica_risorsa) == 0){
        $contact = CRMEntity::getInstance('Contacts');          
        $contact->column_fields['assigned_user_id'] = 1;
        $contact->column_fields['kp_codice_fiscale'] = $codice_fiscale; 
        $contact->column_fields['kp_cod_erp_risorsa'] = $codice_erp_risorsa;
        $contact->column_fields['account_id'] = $id_crm_azienda; 
        $contact->column_fields['stabilimento'] = $id_crm_stabilimento; 	
        $contact->column_fields['lastname'] = $cognome; 
        $contact->column_fields['firstname'] = $nome; 
        $contact->column_fields['data_assunzione'] = $data_assunzione; 
        if($data_cessazione != ''){
            $contact->column_fields['data_fine_rap'] = $data_cessazione; 
        }
        $contact->column_fields['birthday'] = $compleanno; 
        $contact->column_fields['tipo_contratto'] = $desc_tipo_contratto; 
        $contact->column_fields['email'] = $email; 
        $contact->column_fields['kp_sesso'] = $sesso; 
        $contact->column_fields['othercountry'] = $stato; 
        $contact->column_fields['otherstate'] = $provincia; 
        $contact->column_fields['othercity'] = $citta; 
        $contact->column_fields['otherstreet'] = $indirizzo; 
        $contact->column_fields['otherzip'] = $cap; 
        $contact->column_fields['mailingcountry'] = $stato; 
        $contact->column_fields['mailingstate'] = $provincia; 
        $contact->column_fields['mailingcity'] = $citta; 
        $contact->column_fields['mailingstreet'] = $indirizzo; 
        $contact->column_fields['mailingzip'] = $cap;
        if($cod_centro_di_costo != ''){
            $contact->column_fields['kp_centro_di_costo'] = $cod_centro_di_costo;
        }
        $contact->save('Contacts', $longdesc=true, $offline_update=false, $triggerEvent=false);

        $new_contact = $contact->id;

        return 1;
    }
    else{
        $id_risorsa = $adb->query_result($res_verifica_risorsa, 0, 'contactid');
        $id_risorsa = html_entity_decode(strip_tags($id_risorsa), ENT_QUOTES, $default_charset);

        $codice_fiscale = normalizzaStringaCsvPerImportCrm($codice_fiscale, "Testo", true);
        $codice_erp_risorsa = normalizzaStringaCsvPerImportCrm($codice_erp_risorsa, "Testo", true);
        $cognome = normalizzaStringaCsvPerImportCrm($cognome, "Testo", true);
        $nome = normalizzaStringaCsvPerImportCrm($nome, "Testo", true);
        $desc_tipo_contratto = normalizzaStringaCsvPerImportCrm($desc_tipo_contratto, "Testo", true);
        $email = normalizzaStringaCsvPerImportCrm($email, "Testo", true);
        $sesso = normalizzaStringaCsvPerImportCrm($sesso, "Testo", true);
        $stato = normalizzaStringaCsvPerImportCrm($stato, "Testo", true);
        $provincia = normalizzaStringaCsvPerImportCrm($provincia, "Testo", true);
        $citta = normalizzaStringaCsvPerImportCrm($citta, "Testo", true);
        $indirizzo = normalizzaStringaCsvPerImportCrm($indirizzo, "Testo", true);
        $cap = normalizzaStringaCsvPerImportCrm($cap, "Testo", true);

        $q_update_risorsa = "UPDATE {$table_prefix}_contactdetails SET
                            firstname = '".$nome."',
                            lastname = '".$cognome."',
                            accountid = ".$id_crm_azienda.",
                            email = '".$email."',
                            tipo_contratto = '".$desc_tipo_contratto."',
                            data_assunzione = '".$data_assunzione."',";
        if($data_cessazione != ''){
            $q_update_risorsa .= " data_fine_rap = '".$data_cessazione."',";
        }
        $q_update_risorsa .= " stabilimento = ".$id_crm_stabilimento.",
                            kp_sesso = '".$sesso."',
                            kp_codice_fiscale = '".$codice_fiscale."',";
        if($cod_centro_di_costo != ''){
            $q_update_risorsa .= " kp_centro_di_costo = '".$cod_centro_di_costo."',";
        }
        $q_update_risorsa .= " kp_cod_erp_risorsa = '".$codice_erp_risorsa."'
                            WHERE contactid = ".$id_risorsa;
        $adb->query($q_update_risorsa);

        $q_update_risorsa2 = "UPDATE {$table_prefix}_contactsubdetails SET
                            birthday = '".$compleanno."'
                            WHERE contactsubscriptionid = ".$id_risorsa;
        $adb->query($q_update_risorsa2);

        $q_update_risorsa3 = "UPDATE {$table_prefix}_contactaddress SET
                            mailingcountry = '".$stato."',
                            othercountry = '".$stato."',
                            mailingstate = '".$provincia."',
                            otherstate = '".$provincia."',
                            mailingcity = '".$citta."',
                            othercity = '".$citta."',
                            mailingstreet = '".$indirizzo."',
                            otherstreet = '".$indirizzo."',
                            mailingzip = '".$cap."',
                            otherzip = '".$cap."'
                            WHERE contactaddressid = ".$id_risorsa;
        $adb->query($q_update_risorsa3);

        return 0;
    }
}

?>