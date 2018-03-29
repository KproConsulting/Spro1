<?php

/* kpro@bid210420170930 */

/**
 * @author Bidese Jacopo
 * @copyright (c) 2017, Kpro Consulting Srl
 * @package KpImportCustom
 * @version 1.0
 */

require_once('../../../../plugins/import_export_utils/import_utils.php');

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

$current_user->id = 1;

$rows = array();
if(isset($_REQUEST['server_filename'])){
	$file = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_REQUEST['server_filename']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$file = substr($file,0,255);

    $extension = pathinfo($file, PATHINFO_EXTENSION);

	$filename_no_ext = basename($file,'.'.$extension);

    $path = dirname(__FILE__)."/temp";

    $path_logs = dirname(__FILE__)."/logs/";
    $error_logs_file_name = $filename_no_ext."_error_log.txt";

    $errori = 0;
    $record_processati = 0;

    $aziende_create = 0;
    $stabilimenti_creati = 0;
    $risorse_create = 0;
    $risorse_aggiornate = 0;
    $mansioni_create = 0;
    $mansioni_risorsa_create = 0;

    $data_inizio_importazione = date("Y-m-d H:i:s");

    $report_finale = "
ERRORI IMPORTAZIONE RISORSE-MANSIONI delle ".$data_inizio_importazione;
    $handle_log_file=fopen($path_logs.$error_logs_file_name, "w+");
    fwrite($handle_log_file, $report_finale);
    fclose($handle_log_file);

    $report_finale = "
DETTAGLIO IMPORTAZIONE RISORSE-MANSIONI delle ".$data_inizio_importazione;
    $handle_log_file=fopen($path_logs.$filename_no_ext."_dettaglio_log.txt", "w+");
    fwrite($handle_log_file, $report_finale);
    fclose($handle_log_file);

    if (is_dir($path)) {
        try {
            if ($dh = opendir($path)) {
                if(file_exists($path. '/' .$file)) {
                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                    
                    if ($ext == 'csv' || $ext == 'CSV') {
                        $row = 1;
                        RimuoviCapoRigaCSVperImport($path . '/' . $file);
                        if (($handle = fopen($path . '/' . $file, "r")) !== false) {
                            
                            while (($array_dati_riga = fgetcsv($handle, 0, ";")) !== false) {
                                
                                $array_codici_fiscali = array();
                                if($row == 1){
                                    
                                }
                                else if ($row > 1) {
                                    $record_processati++;
                                    $risorsa_gia_passata = false;
                                    
                                    for ($i = 0; $i < count($array_dati_riga); $i++) {
                                        if ($array_dati_riga[$i] != null) {
                                            $array_dati_riga[$i] = trim(rimuoviApiciStringaCsvPerImportCrm($array_dati_riga[$i]));
                                        } else {
                                            $array_dati_riga[$i] = '';
                                        }
                                    }

                                    $report_finale = "
- Riga ".$row;
                                    $handle_log_file=fopen($path_logs.$filename_no_ext."_dettaglio_log.txt", "a+");
                                    fwrite($handle_log_file, $report_finale);
                                    fclose($handle_log_file);
                                    
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
                                            
                                            $id_crm_azienda = ControlloAzienda($array_dati_riga); 
                                            if($id_crm_azienda != 0){

                                                $report_finale = " -> Azienda";
                                                $handle_log_file=fopen($path_logs.$filename_no_ext."_dettaglio_log.txt", "a+");
                                                fwrite($handle_log_file, $report_finale);
                                                fclose($handle_log_file);

                                                $id_crm_stabilimento = ControlloStabilimento($array_dati_riga, $id_crm_azienda);
                                                if($id_crm_stabilimento != 0){

                                                    $report_finale = " -> Stabilimento";
                                                    $handle_log_file=fopen($path_logs.$filename_no_ext."_dettaglio_log.txt", "a+");
                                                    fwrite($handle_log_file, $report_finale);
                                                    fclose($handle_log_file);

                                                    $res_controllo_risorsa = ControlloRisorsa($array_dati_riga, $id_crm_azienda, $id_crm_stabilimento);
                                                    if($res_controllo_risorsa == 1){
                                                        $risorse_create++;

                                                        $report_finale = " -> Creata Risorsa";
                                                        $handle_log_file=fopen($path_logs.$filename_no_ext."_dettaglio_log.txt", "a+");
                                                        fwrite($handle_log_file, $report_finale);
                                                        fclose($handle_log_file);
                                                    }
                                                    else{
                                                        $risorse_aggiornate++;

                                                        $report_finale = " -> Aggiornata Risorsa";
                                                        $handle_log_file=fopen($path_logs.$filename_no_ext."_dettaglio_log.txt", "a+");
                                                        fwrite($handle_log_file, $report_finale);
                                                        fclose($handle_log_file);
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

                            unlink($path.'/'.$file);
                        }
                    }
                }
                closedir($dh);
            }
        } catch (Exception $e) {
            print($e);
        }
    }

    $rows[] = array(
        "Processati"=>$record_processati,
        "Aziende create"=>$aziende_create,
        "Stabilimenti creati"=>$stabilimenti_creati,
        "Risorse create"=>$risorse_create,
        "Risorse aggiornate"=>$risorse_aggiornate,
        "Mansioni create"=>$mansioni_create,
        "Mansioni-Risorsa create"=>$mansioni_risorsa_create,
        "Errori"=>$errori
    );
}

$json = json_encode($rows);
print $json;

function ControlloAzienda($dati_riga) {
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $nome_azienda = $dati_riga[4];
    $nome_azienda_controllo = addslashes($nome_azienda);
    
    $q_verifica_azienda = "SELECT acc.accountid
                        FROM {$table_prefix}_account acc
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = acc.accountid
                        WHERE ent.deleted = 0 AND acc.accountname LIKE '" . $nome_azienda_controllo . "'";

    $res_verifica_azienda = $adb->query($q_verifica_azienda);
    
    if ($adb->num_rows($res_verifica_azienda) == 0) {
        $account = CRMEntity::getInstance('Accounts');
        $account->column_fields['accountname'] = $nome_azienda;
        $account->column_fields['kp_tasse'] = 'ALIQUOTA IVA 22%'; /* kpro@bid290320180900 */
        $account->column_fields['assigned_user_id'] = 1;
        $account->save('Accounts', $longdesc=true, $offline_update=false, $triggerEvent=false);
        
        $new_account = $account->id;

        $GLOBALS['aziende_create']++;
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
    $nome_stabilimento_controllo = addslashes($nome_stabilimento);
    $nome_azienda = $dati_riga[4];

    //$nome_stabilimento = $nome_azienda." - ".$nome_stabilimento;

    $q_verifica_stabilimento = "SELECT stab.stabilimentiid
                        FROM {$table_prefix}_stabilimenti stab
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = stab.stabilimentiid
                        WHERE ent.deleted = 0
                        AND stab.azienda = ".$id_azienda_crm."
                        AND stab.nome_stabilimento LIKE '" . $nome_stabilimento_controllo . "'";

    $res_verifica_stabilimento = $adb->query($q_verifica_stabilimento);
    if ($adb->num_rows($res_verifica_stabilimento) == 0) {
            $stabilimento = CRMEntity::getInstance('Stabilimenti');
            $stabilimento->column_fields['nome_stabilimento'] = $nome_stabilimento;
            $stabilimento->column_fields['azienda'] = $id_azienda_crm;
            $stabilimento->column_fields['assigned_user_id'] = 1;
            $stabilimento->save('Stabilimenti', $longdesc=true, $offline_update=false, $triggerEvent=false);

            $new_stabilimento = $stabilimento->id;

            $GLOBALS['stabilimenti_creati']++;
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

    $numero_colonna_partenza = 29;
    $numero_colonne_record = 2;
    $numero_record = 5; /* kpro@bid290320181050 */
    $i = 0;
    while($i < $numero_record){
        $numero_colonna = $numero_colonna_partenza + ($i * $numero_colonne_record);

        $mansione = $dati_riga[$numero_colonna];
        $numero_colonna++;
        $data_mansione = normalizzaStringaCsvPerImportCrm($dati_riga[$numero_colonna], "Data", false);

        $id_mansione = CreaMansioniStandard($mansione);
        if($id_mansione != 0){  
            if($i == 0){          
                $res_mansione_risorsa = CreaMansioniRisorsaStandard($id_mansione, $id_risorsa, "Principale", $data_mansione);
            }
            else{
                $res_mansione_risorsa = CreaMansioniRisorsaStandard($id_mansione, $id_risorsa, "Accessoria", $data_mansione);
            }
            if($res_mansione_risorsa == -1 || $res_mansione_risorsa == '-1'){
                $report_finale = " 
    Errore nella creazione della mansione-risorsa: ".$mansione." - ".$id_risorsa." Mansione-risorsa gia presente";
                $handle_log_file=fopen($GLOBALS['path_logs'].$error_logs_file_name, "a+");
                fwrite($handle_log_file, $report_finale);
                fclose($handle_log_file);
            }
            else if($res_mansione_risorsa == -2 || $res_mansione_risorsa == '-2'){
                $report_finale = " 
    Errore nella creazione della mansione-risorsa: ".$mansione." - ".$id_risorsa." Risorsa non trovata";
                $handle_log_file=fopen($GLOBALS['path_logs'].$error_logs_file_name, "a+");
                fwrite($handle_log_file, $report_finale);
                fclose($handle_log_file);
            }
        }
        else{
            $report_finale = " 
    Errore nella creazione della mansione: ".$mansione;
            $handle_log_file=fopen($GLOBALS['path_logs'].$error_logs_file_name, "a+");
            fwrite($handle_log_file, $report_finale);
            fclose($handle_log_file);
        }

        $i++;
    }
}

function CreaMansioniStandard($nome_mansione){
    global $adb, $table_prefix, $default_charset;

    if($nome_mansione != "" && $nome_mansione != null){
        $q_controllo_mansione = "SELECT mans.mansioniid
                                FROM {$table_prefix}_mansioni mans
                                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = mans.mansioniid
                                WHERE ent.deleted = 0 AND mans.mansione_name LIKE '".$nome_mansione."'";
        $res_controllo_mansione = $adb->query($q_controllo_mansione);
        if($adb->num_rows($res_controllo_mansione) == 0){
            $mansione = CRMEntity::getInstance('Mansioni');          
            $mansione->column_fields['assigned_user_id'] = 1;
            $mansione->column_fields['mansione_name'] = $nome_mansione; 
            $mansione->save('Mansioni', $longdesc=true, $offline_update=false, $triggerEvent=false);

            $id_mansione = $mansione->id;

            $GLOBALS['mansioni_create']++;
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
            $mansione_risorsa->column_fields['eredita_t_corso'] = 'Si';
            $mansione_risorsa->column_fields['eredita_t_visita'] = 'Si';
            $mansione_risorsa->column_fields['kp_eredita_c_privacy'] = 'Si';
            $mansione_risorsa->column_fields['assigned_user_id'] = 1;
            $mansione_risorsa->save('MansioniRisorsa', $longdesc=true, $offline_update=false, $triggerEvent=false);

            $new_mansione_risorsa = $mansione_risorsa->id;

            $q_update_mansione_risorsa = "UPDATE {$table_prefix}_mansionirisorsa SET
                                            eredita_t_corso = 'No',
                                            eredita_t_visita = 'No',
                                            kp_eredita_c_privacy = 'No'
                                            WHERE mansionirisorsaid = ".$new_mansione_risorsa;
            $adb->query($q_update_mansione_risorsa);

            $GLOBALS['mansioni_risorsa_create']++;

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