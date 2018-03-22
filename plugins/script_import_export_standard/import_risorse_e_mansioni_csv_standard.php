<?php

/* kpro@bid210220171400 */

/**
 * @author BideseJacopo
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

$report_finale = " 
ERRORI IMPORTAZIONE MANSIONI-RISORSA ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs."MansioniRisorse_import_error.txt", "w+");
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
                                
                                $codice_fiscale_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[0], "Testo", false);
                                $cognome_risorsa_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[2], "Testo", false);
                                $nome_azienda_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[4], "Testo", false);
                                $nome_stabilimento_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[5], "Testo", false);
                            
                                if($codice_fiscale_controllo != '' && $cognome_risorsa_controllo != '' 
                                && $nome_azienda_controllo != '' && $nome_stabilimento_controllo != ''){

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
".$nome_stabilimento_controllo.": Stabilimento non trovato";
                                                $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                fwrite($handle_log_file, $report_finale);
                                                fclose($handle_log_file);
                                            }
                                        }
                                        else{
                                            $errori++;

                                            $report_finale = "
".$nome_azienda_controllo.": Azienda non trovata";
                                            $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                            fwrite($handle_log_file, $report_finale);
                                            fclose($handle_log_file);
                                        }
                                    }
                                }
                                else{
                                    $errori++;

                                    $report_finale = "
Record privo dei campi obbligatori: ".$nome_azienda_controllo." - ".$codice_risorsa_controllo." - ".$cognome_risorsa_controllo." - ".$codice_fiscale_controllo;
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

function ControlloAzienda($dati_riga) {
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $nome_azienda = $dati_riga[4];

    $q_verifica_azienda = "SELECT acc.accountid
                        FROM {$table_prefix}_account acc
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = acc.accountid
                        WHERE ent.deleted = 0 AND acc.accountname = '" . $nome_azienda . "'";

    $res_verifica_azienda = $adb->query($q_verifica_azienda);
    if ($adb->num_rows($res_verifica_azienda) == 0) {
        $account = CRMEntity::getInstance('Accounts');
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

    $nome_stabilimento = $dati_riga[5];
    $nome_azienda = $dati_riga[4];

    $nome_stabilimento = $nome_azienda." - ".$nome_stabilimento;

    $q_verifica_stabilimento = "SELECT stab.stabilimentiid
                        FROM {$table_prefix}_stabilimenti stab
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = stab.stabilimentiid
                        WHERE ent.deleted = 0 
                        AND stab.azienda = ".$id_azienda_crm."
                        AND stab.nome_stabilimento = '" . $nome_stabilimento . "'";

    $res_verifica_stabilimento = $adb->query($q_verifica_stabilimento);
    if ($adb->num_rows($res_verifica_stabilimento) == 0) {
            $stabilimento = CRMEntity::getInstance('Stabilimenti');
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

    $codice_fiscale = $dati_riga[0];
    $codice_erp_risorsa = $dati_riga[1];
    $cognome = $dati_riga[2];
    $nome = $dati_riga[3];
    $data_assunzione = normalizzaStringaCsvPerImportCrm($dati_riga[6], "Data", false);
    $data_cessazione = normalizzaStringaCsvPerImportCrm($dati_riga[7], "Data", false);
    $cod_centro_di_costo = normalizzaStringaCsvPerImportCrm($dati_riga[8], "Testo", false);
    $desc_centro_di_costo = normalizzaStringaCsvPerImportCrm($dati_riga[9], "Testo", false);
    $email = $dati_riga[10];
    $telefono_ufficio = $dati_riga[11];
    $cellulare = $dati_riga[12];
    $titolo_di_studio = normalizzaStringaCsvPerImportCrm($dati_riga[13], "Testo", false);
    $desc_tipo_contratto = normalizzaStringaCsvPerImportCrm($dati_riga[14], "Testo", false);
    $inquadramento = $dati_riga[15];
    $sesso = normalizzaStringaCsvPerImportCrm($dati_riga[16], "Testo", false);
    $compleanno = normalizzaStringaCsvPerImportCrm($dati_riga[17], "Data", false);
    $stato = $dati_riga[18];
    $provincia = $dati_riga[19];
    $citta = $dati_riga[20];
    $cap = $dati_riga[21];
    $indirizzo = $dati_riga[22];
    $cittadinanza = $dati_riga[23];

    $campo_agg1 = $dati_riga[24];
    $campo_agg2 = $dati_riga[25];
    $campo_agg3 = $dati_riga[26];
    $campo_agg4 = $dati_riga[27];
    $campo_agg5 = $dati_riga[28];

    switch(strtolower($titolo_di_studio)){
        case "licenza elementare":
            $titolo_di_studio = 'Licenza elementare'; 
            break;
        case "licenza media":
            $titolo_di_studio = 'Licenza media';
            break;
        case "diploma di istruzione secondaria superiore":
            $titolo_di_studio = 'Diploma di istruzione secondaria superiore'; 
            break;
        case "diploma universitario":
            $titolo_di_studio = 'Diploma universitario';
            break;
        case "laurea di primo livello":
            $titolo_di_studio = 'Laurea di primo livello'; 
            break;
        case "laurea specialistica":
            $titolo_di_studio = 'Laurea specialistica';
            break;
        case "laurea specialistica a ciclo unico":
            $titolo_di_studio = 'Laurea specialistica a ciclo unico'; 
            break;
        case "master universitario di primo livello":
            $titolo_di_studio = 'Master universitario di primo livello';
            break;
        case "master universitario di secondo livello":
            $titolo_di_studio = 'Master universitario di secondo livello'; 
            break;
        case "diploma di specializzazione":
            $titolo_di_studio = 'Diploma di specializzazione';
            break;
        case "titolo di dottore di ricerca":
            $titolo_di_studio = 'Titolo di dottore di ricerca';
            break;
        default:
            $titolo_di_studio = '--Nessuno--';
    }

    switch(strtolower($desc_tipo_contratto)){
        case "interinale":
            $desc_tipo_contratto = 'Interinale'; 
            break;
        case "apprendistato":
            $desc_tipo_contratto = 'Apprendistato';
            break;
        case "indeterminato":
            $desc_tipo_contratto = 'Indeterminato'; 
            break;
        case "determinato":
            $desc_tipo_contratto = 'Determinato';
            break;
        default:
            $desc_tipo_contratto = '--Nessuno--';
    }

    switch(strtolower($sesso)){
        case "m":
            $sesso = 'M'; 
            break;
        case "f":
            $sesso = 'F';
            break;
        case "maschio":
            $sesso = 'M'; 
            break;
        case "femmina":
            $sesso = 'F';
            break;
        default:
            $sesso = '--Nessuno--';
    }

    ImportPicklistMultilinguaggio('kp_centro_di_costo','it_it',$cod_centro_di_costo,$desc_centro_di_costo);

    if($data_cessazione == '2999-12-31' || $data_cessazione == '' || $data_cessazione == null){
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
        $contact->column_fields['kp_matricola'] = $codice_erp_risorsa;
        $contact->column_fields['lastname'] = $cognome; 
        $contact->column_fields['firstname'] = $nome; 
        $contact->column_fields['account_id'] = $id_crm_azienda; 
        $contact->column_fields['stabilimento'] = $id_crm_stabilimento;	
        if($data_assunzione != ''){
            $contact->column_fields['data_assunzione'] = $data_assunzione;
        }
        if($data_cessazione != ''){
            $contact->column_fields['data_fine_rap'] = $data_cessazione; 
        }
        if($cod_centro_di_costo != ''){
            $contact->column_fields['kp_centro_di_costo'] = $cod_centro_di_costo;
        }
        $contact->column_fields['email'] = $email; 
        $contact->column_fields['phone'] = $telefono_ufficio;
        $contact->column_fields['mobile'] = $cellulare;
        $contact->column_fields['kp_titolo_studio'] = $titolo_di_studio;
        $contact->column_fields['tipo_contratto'] = $desc_tipo_contratto;  
        $contact->column_fields['kp_inquadramento'] = $inquadramento;  
        $contact->column_fields['kp_sesso'] = $sesso; 
        $contact->column_fields['birthday'] = $compleanno; 
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
        $contact->column_fields['kp_cittadinanza'] = $cittadinanza;       
        $contact->save('Contacts', $longdesc=true, $offline_update=false, $triggerEvent=false);

        $new_contact = $contact->id;

        ImportMansioniStandard($new_contact, $dati_riga);

        return 1;
    }
    else{
        $id_risorsa = $adb->query_result($res_verifica_risorsa, 0, 'contactid');
        $id_risorsa = html_entity_decode(strip_tags($id_risorsa), ENT_QUOTES, $default_charset);

        $codice_fiscale = normalizzaStringaCsvPerImportCrm($codice_fiscale, "Testo", true);
        $codice_erp_risorsa = normalizzaStringaCsvPerImportCrm($codice_erp_risorsa, "Testo", true);
        $cognome = normalizzaStringaCsvPerImportCrm($cognome, "Testo", true);
        $nome = normalizzaStringaCsvPerImportCrm($nome, "Testo", true);
        $email = normalizzaStringaCsvPerImportCrm($email, "Testo", true);
        $telefono_ufficio = normalizzaStringaCsvPerImportCrm($telefono_ufficio, "Testo", true);
        $cellulare = normalizzaStringaCsvPerImportCrm($cellulare, "Testo", true);
        $inquadramento = normalizzaStringaCsvPerImportCrm($inquadramento, "Testo", true);
        $stato = normalizzaStringaCsvPerImportCrm($stato, "Testo", true);
        $provincia = normalizzaStringaCsvPerImportCrm($provincia, "Testo", true);
        $citta = normalizzaStringaCsvPerImportCrm($citta, "Testo", true);
        $cap = normalizzaStringaCsvPerImportCrm($cap, "Testo", true);
        $indirizzo = normalizzaStringaCsvPerImportCrm($indirizzo, "Testo", true);
        $cittadinanza = normalizzaStringaCsvPerImportCrm($cittadinanza, "Testo", true);

        $q_update_risorsa = "UPDATE {$table_prefix}_contactdetails SET
                            kp_codice_fiscale = '".$codice_fiscale."',
                            kp_matricola = '".$codice_erp_risorsa."',
                            lastname = '".$cognome."',
                            firstname = '".$nome."',                            
                            accountid = ".$id_crm_azienda.",
                            stabilimento = ".$id_crm_stabilimento.",";
        if($data_assunzione != ''){
            $q_update_risorsa .= " data_assunzione = '".$data_assunzione."',";
        }
        if($data_cessazione != ''){
            $q_update_risorsa .= " data_fine_rap = '".$data_cessazione."',";
        }
        if($cod_centro_di_costo != ''){
            $q_update_risorsa .= " kp_centro_di_costo = '".$cod_centro_di_costo."',";
        }
        $q_update_risorsa .= " email = '".$email."',
                            phone = '".$telefono_ufficio."',
                            mobile = '".$cellulare."',
                            kp_titolo_studio = '".$titolo_di_studio."',
                            tipo_contratto = '".$desc_tipo_contratto."',
                            kp_inquadramento = '".$inquadramento."',
                            kp_sesso = '".$sesso."',
                            kp_cittadinanza = '".$cittadinanza."'                            
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

        ImportMansioniStandard($id_risorsa, $dati_riga);

        return 0;
    }
}

function ImportMansioniStandard($id_risorsa, $dati_riga){
    global $adb, $table_prefix, $default_charset;

    $mansione_principale = $dati_riga[29];
    $id_mansione = CreaMansioniStandard($mansione_principale);
    if($id_mansione != 0){
        $data_mansione_principale = normalizzaStringaCsvPerImportCrm($dati_riga[30], "Data", false);
        $res_mansione_risorsa = CreaMansioniRisorsaStandard($id_mansione, $id_risorsa, "Principale", $data_mansione_principale);
        if($res_mansione_risorsa == -1 || $res_mansione_risorsa == '-1'){
            $report_finale = " 
Errore nella creazione della mansione-risorsa: ".$mansione_principale." - ".$id_risorsa." Mansione-risorsa gia presente";
            $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
            fwrite($handle_log_file, $report_finale);
            fclose($handle_log_file);
        }
        else if($res_mansione_risorsa == -2 || $res_mansione_risorsa == '-2'){
            $report_finale = " 
Errore nella creazione della mansione-risorsa: ".$mansione_principale." - ".$id_risorsa." Risorsa non trovata";
            $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
            fwrite($handle_log_file, $report_finale);
            fclose($handle_log_file);
        }
    }
    else{
        $report_finale = " 
Errore nella creazione della mansione: ".$mansione_principale;
        $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
        fwrite($handle_log_file, $report_finale);
        fclose($handle_log_file);
    }

    $mansione_accessioria1 = $dati_riga[31];
    $id_mansione = CreaMansioniStandard($mansione_accessioria1);
    if($id_mansione != 0){
        $data_mansione_accessioria1 = normalizzaStringaCsvPerImportCrm($dati_riga[32], "Data", false);
        $res_mansione_risorsa = CreaMansioniRisorsaStandard($id_mansione, $id_risorsa, "Accessoria", $data_mansione_accessioria1);
        if($res_mansione_risorsa == -1 || $res_mansione_risorsa == '-1'){
            $report_finale = " 
Errore nella creazione della mansione-risorsa: ".$mansione_accessioria1." - ".$id_risorsa." Mansione-risorsa gia presente";
            $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
            fwrite($handle_log_file, $report_finale);
            fclose($handle_log_file);
        }
        else if($res_mansione_risorsa == -2 || $res_mansione_risorsa == '-2'){
            $report_finale = " 
Errore nella creazione della mansione-risorsa: ".$mansione_accessioria1." - ".$id_risorsa." Risorsa non trovata";
            $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
            fwrite($handle_log_file, $report_finale);
            fclose($handle_log_file);
        }
    }
    else{
        $report_finale = " 
Errore nella creazione della mansione: ".$mansione_accessioria1;
        $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
        fwrite($handle_log_file, $report_finale);
        fclose($handle_log_file);
    }

    $mansione_accessioria2 = $dati_riga[33];
    $id_mansione = CreaMansioniStandard($mansione_accessioria2);
    if($id_mansione != 0){
        $data_mansione_accessioria2 = normalizzaStringaCsvPerImportCrm($dati_riga[34], "Data", false);
        $res_mansione_risorsa = CreaMansioniRisorsaStandard($id_mansione, $id_risorsa, "Accessoria", $data_mansione_accessioria2);
        if($res_mansione_risorsa == -1 || $res_mansione_risorsa == '-1'){
            $report_finale = " 
Errore nella creazione della mansione-risorsa: ".$mansione_accessioria2." - ".$id_risorsa." Mansione-risorsa gia presente";
            $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
            fwrite($handle_log_file, $report_finale);
            fclose($handle_log_file);
        }
        else if($res_mansione_risorsa == -2 || $res_mansione_risorsa == '-2'){
            $report_finale = " 
Errore nella creazione della mansione-risorsa: ".$mansione_accessioria2." - ".$id_risorsa." Risorsa non trovata";
            $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
            fwrite($handle_log_file, $report_finale);
            fclose($handle_log_file);
        }
    }
    else{
        $report_finale = " 
Errore nella creazione della mansione: ".$mansione_accessioria2;
        $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
        fwrite($handle_log_file, $report_finale);
        fclose($handle_log_file);
    }

    $mansione_accessioria3 = $dati_riga[35];
    $id_mansione = CreaMansioniStandard($mansione_accessioria3);
    if($id_mansione != 0){
        $data_mansione_accessioria3 = normalizzaStringaCsvPerImportCrm($dati_riga[36], "Data", false);
        $res_mansione_risorsa = CreaMansioniRisorsaStandard($id_mansione, $id_risorsa, "Accessoria", $data_mansione_accessioria3);
        if($res_mansione_risorsa == -1 || $res_mansione_risorsa == '-1'){
            $report_finale = " 
Errore nella creazione della mansione-risorsa: ".$mansione_accessioria3." - ".$id_risorsa." Mansione-risorsa gia presente";
            $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
            fwrite($handle_log_file, $report_finale);
            fclose($handle_log_file);
        }
        else if($res_mansione_risorsa == -2 || $res_mansione_risorsa == '-2'){
            $report_finale = " 
Errore nella creazione della mansione-risorsa: ".$mansione_accessioria3." - ".$id_risorsa." Risorsa non trovata";
            $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
            fwrite($handle_log_file, $report_finale);
            fclose($handle_log_file);
        }
    }
    else{
        $report_finale = " 
Errore nella creazione della mansione: ".$mansione_accessioria3;
        $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
        fwrite($handle_log_file, $report_finale);
        fclose($handle_log_file);
    }

    $mansione_accessioria4 = $dati_riga[37];
    $id_mansione = CreaMansioniStandard($mansione_accessioria4);
    if($id_mansione != 0){
        $data_mansione_accessioria4 = normalizzaStringaCsvPerImportCrm($dati_riga[38], "Data", false);
        $res_mansione_risorsa = CreaMansioniRisorsaStandard($id_mansione, $id_risorsa, "Accessoria", $data_mansione_accessioria4);
        if($res_mansione_risorsa == -1 || $res_mansione_risorsa == '-1'){
            $report_finale = " 
Errore nella creazione della mansione-risorsa: ".$mansione_accessioria4." - ".$id_risorsa." Mansione-risorsa gia presente";
            $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
            fwrite($handle_log_file, $report_finale);
            fclose($handle_log_file);
        }
        else if($res_mansione_risorsa == -2 || $res_mansione_risorsa == '-2'){
            $report_finale = " 
Errore nella creazione della mansione-risorsa: ".$mansione_accessioria4." - ".$id_risorsa." Risorsa non trovata";
            $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
            fwrite($handle_log_file, $report_finale);
            fclose($handle_log_file);
        }
    }
    else{
        $report_finale = " 
Errore nella creazione della mansione: ".$mansione_accessioria4;
        $handle_log_file=fopen($GLOBALS['path_logs']."MansioniRisorse_import_error.txt", "a+");
        fwrite($handle_log_file, $report_finale);
        fclose($handle_log_file);
    }

}

function CreaMansioniStandard($nome_mansione){
    global $adb, $table_prefix, $default_charset;

    if($nome_mansione != "" && $nome_mansione != null){
        $q_controllo_mansione = "SELECT mans.mansioniid
                                FROM {$table_prefix}_mansioni mans
                                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = mans.mansioniid
                                WHERE ent.deleted = 0 AND mans.mansione_name = '".$nome_mansione."'";
        $res_controllo_mansione = $adb->query($q_controllo_mansione);
        if($adb->num_rows($res_controllo_mansione) == 0){
            $mansione = CRMEntity::getInstance('Mansioni');          
            $mansione->column_fields['assigned_user_id'] = 1;
            $mansione->column_fields['mansione_name'] = $nome_mansione; 
            $mansione->save('Mansioni', $longdesc=true, $offline_update=false, $triggerEvent=false);

            $id_mansione = $mansione->id;
        }
        else{
            $id_mansione = $adb->query_result($res_controllo_mansione, 0, 'mansioniid');
            $id_mansione = html_entity_decode(strip_tags($id_mansione), ENT_QUOTES, $default_charset);
        }
    }
    else{
        $id_mansione = 0;
    }

    if ($id_mansione == 0 || $id_mansione == '' || $id_mansione == null) {
        $id_mansione = 0;
    }

    return $id_mansione;

}

function CreaMansioniRisorsaStandard($id_mansione, $id_risorsa, $tipo_mansione, $data_inizio){
    global $adb, $table_prefix, $default_charset;

    $q_verifica_risorsa = "SELECT cont.contactid,
                        cont.accountid,
                        cont.stabilimento,
                        cont.data_assunzione
                        FROM {$table_prefix}_contactdetails cont
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = cont.contactid
                        WHERE ent.deleted = 0 AND cont.contactid = ".$id_risorsa;
    $res_verifica_risorsa = $adb->query($q_verifica_risorsa);
    if($adb->num_rows($res_verifica_risorsa) > 0){

        $q_verifica_mansione_risorsa = "SELECT mnsr.mansionirisorsaid
                                    FROM {$table_prefix}_mansionirisorsa mnsr
                                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = mnsr.mansionirisorsaid
                                    WHERE ent.deleted = 0
                                    AND mnsr.risorsa = ".$id_risorsa."
                                    AND mnsr.mansione = ".$id_mansione;
        $res_verifica_mansione_risorsa = $adb->query($q_verifica_mansione_risorsa);
        if($adb->num_rows($res_verifica_mansione_risorsa) == 0){
            $id_risorsa = $adb->query_result($res_verifica_risorsa, 0, 'contactid');
            $id_risorsa = html_entity_decode(strip_tags($id_risorsa), ENT_QUOTES, $default_charset);

            $id_azienda = $adb->query_result($res_verifica_risorsa, 0, 'accountid');
            $id_azienda = html_entity_decode(strip_tags($id_azienda), ENT_QUOTES, $default_charset);

            $id_stabilimento = $adb->query_result($res_verifica_risorsa, 0, 'stabilimento');
            $id_stabilimento = html_entity_decode(strip_tags($id_stabilimento), ENT_QUOTES, $default_charset);

            $data_assunzione = $adb->query_result($res_verifica_risorsa, 0, 'data_assunzione');
            $data_assunzione = html_entity_decode(strip_tags($data_assunzione), ENT_QUOTES, $default_charset);
            if($data_assunzione == null){
                $data_assunzione = "";
            }

            if($data_inizio == "" || $data_inizio == null){
                $data_inizio = $data_assunzione;
            }

            $mansione_risorsa = CRMEntity::getInstance('MansioniRisorsa');
            $mansione_risorsa->column_fields['risorsa'] = $id_risorsa;
            $mansione_risorsa->column_fields['mansione'] = $id_mansione;
            if($data_inizio != ""){
                $mansione_risorsa->column_fields['data_inizio'] = $data_inizio;
            }
            $mansione_risorsa->column_fields['tipo_mansione'] = $tipo_mansione;
            $mansione_risorsa->column_fields['azienda'] = $id_azienda;
            $mansione_risorsa->column_fields['stabilimento'] = $id_stabilimento;
            $mansione_risorsa->column_fields['stato_mansione'] = 'Attiva';
            $mansione_risorsa->column_fields['eredita_t_corso'] = 'No';
            $mansione_risorsa->column_fields['eredita_t_visita'] = 'No';
            $mansione_risorsa->column_fields['assigned_user_id'] = 1;
            $mansione_risorsa->save('MansioniRisorsa', $longdesc=true, $offline_update=false, $triggerEvent=false);

            $new_mansione_risorsa = $mansione_risorsa->id;

            /*$q_update_mansione_risorsa = "UPDATE {$table_prefix}_mansionirisorsa SET
                                        eredita_t_corso = 'No',
                                        eredita_t_visita = 'No'
                                        WHERE mansionirisorsaid = ".$new_mansione_risorsa;
            $adb->query($q_update_mansione_risorsa);*/

            return 1;
        }
        else{
            return -1;
        }

    }
    else{
        return -2;
    }

}

?>