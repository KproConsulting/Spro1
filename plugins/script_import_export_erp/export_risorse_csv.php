<?php

/* kpro@bid09092016 */

/**
 * @author BideseJacopo
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package importExport
 * @version 1.0
 */

require_once('../import_export_utils/export_utils.php'); /* kpro@bid190420171000 */

include_once('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

$path_ftp = "/home/erp/Import_Export/Export_da_Vte_Sicurezza/";
$dir = 'Risorse';

$path_logs = "logs/";
$logs_file_name = $dir . "_export_log.txt";
$error_logs_file_name = $dir . "_export_error.txt";
$csv_filename = date('YmdHis') . '_vte_sicurezza_export_' . $dir . '.csv';

$array_intestazione_csv = array();
$array_dati_csv = array();

$data_inizio_importazione = date("Y-m-d H:i:s");

$report_finale = " 
ESPORTAZIONE RISORSE delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$logs_file_name, "w+");
fwrite($handle_log_file, $report_finale);
fclose($handle_log_file);

$q_dati_export = "SELECT cont.kp_codice_fiscale,
                cont.kp_cod_erp_risorsa,
                cont.lastname,
                cont.firstname,
                acc.kp_cod_erp_azienda,
                acc.accountname,
                stab.kp_cod_erp_stabilim,
                stab.nome_stabilimento,
                cont.data_assunzione,
                cont.data_fine_rap,
                contsub.birthday,
                cont.tipo_contratto,
                cont.email,
                cont.kp_sesso,
                contadd.mailingcountry,
                contadd.mailingstate,
                contadd.mailingcity,
                contadd.mailingstreet,
                contadd.mailingzip
                FROM {$table_prefix}_contactdetails cont
                INNER JOIN {$table_prefix}_contactsubdetails contsub ON contsub.contactsubscriptionid = cont.contactid
                INNER JOIN {$table_prefix}_contactaddress contadd ON contadd.contactaddressid = cont.contactid
                INNER JOIN {$table_prefix}_account acc ON acc.accountid = cont.accountid
                INNER JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = cont.stabilimento
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = cont.contactid
                INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = acc.accountid
                INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = stab.stabilimentiid
                WHERE ent.deleted = 0 AND ent1.deleted = 0 AND ent2.deleted = 0";
$res_dati_export = $adb->query($q_dati_export);
$num_dati_export = $adb->num_rows($res_dati_export);
if ($num_dati_export > 0) {
    $array_intestazione_csv = array(
        'Codice Fiscale', 'Codice ERP Risorsa', 'Cognome', 'Nome', 'Codice ERP Azienda', 'Descrizione Azienda', 'Codice ERP Stabilimento', 'Descrizione Stabilimento', 'Data Assunzione', 'Data Cessazione', 'Compleanno', 'Tipo Contratto', 'Email', 'Sesso', 'Stato', 'Provincia', 'Citta', 'Indirizzo', 'N Civico', 'CAP'
    );
    for ($i = 0; $i < $num_dati_export; $i++) {
        $codice_fiscale = $adb->query_result($res_dati_export, $i, 'kp_codice_fiscale');
        $codice_fiscale = normalizzaStringaCsvPerExportCrm($codice_fiscale, 'Testo');

        $codice_erp_risorsa = $adb->query_result($res_dati_export, $i, 'kp_cod_erp_risorsa');
        $codice_erp_risorsa = normalizzaStringaCsvPerExportCrm($codice_erp_risorsa, 'Testo');

        $cognome = $adb->query_result($res_dati_export, $i, 'lastname');
        $cognome = normalizzaStringaCsvPerExportCrm($cognome, 'Testo');

        $nome = $adb->query_result($res_dati_export, $i, 'firstname');
        $nome = normalizzaStringaCsvPerExportCrm($nome, 'Testo');

        $codice_erp_azienda = $adb->query_result($res_dati_export, $i, 'kp_cod_erp_azienda');
        $codice_erp_azienda = normalizzaStringaCsvPerExportCrm($codice_erp_azienda, 'Testo');

        $descrizione_azienda = $adb->query_result($res_dati_export, $i, 'accountname');
        $descrizione_azienda = normalizzaStringaCsvPerExportCrm($descrizione_azienda, 'Testo');

        $codice_erp_stabilimento = $adb->query_result($res_dati_export, $i, 'kp_cod_erp_stabilim');
        $codice_erp_stabilimento = normalizzaStringaCsvPerExportCrm($codice_erp_stabilimento, 'Testo');

        $descrizione_stabilimento = $adb->query_result($res_dati_export, $i, 'nome_stabilimento');
        $descrizione_stabilimento = normalizzaStringaCsvPerExportCrm($descrizione_stabilimento, 'Testo');

        $data_assunzione = $adb->query_result($res_dati_export, $i, 'data_assunzione');
        $data_assunzione = normalizzaStringaCsvPerExportCrm($data_assunzione, 'Data');

        $data_cessazione = $adb->query_result($res_dati_export, $i, 'data_fine_rap');
        $data_cessazione = normalizzaStringaCsvPerExportCrm($data_cessazione, 'Data');

        $compleanno = $adb->query_result($res_dati_export, $i, 'birthday');
        $compleanno = normalizzaStringaCsvPerExportCrm($compleanno, 'Data');

        $tipo_contratto = $adb->query_result($res_dati_export, $i, 'tipo_contratto');
        $tipo_contratto = normalizzaStringaCsvPerExportCrm($tipo_contratto, 'Testo');

        $email = $adb->query_result($res_dati_export, $i, 'email');
        $email = normalizzaStringaCsvPerExportCrm($email, 'Testo');

        $sesso = $adb->query_result($res_dati_export, $i, 'kp_sesso');
        $sesso = normalizzaStringaCsvPerExportCrm($sesso, 'Testo');

        $stato = $adb->query_result($res_dati_export, $i, 'mailingcountry');
        $stato = normalizzaStringaCsvPerExportCrm($stato, 'Testo');

        $provincia = $adb->query_result($res_dati_export, $i, 'mailingstate');
        $provincia = normalizzaStringaCsvPerExportCrm($provincia, 'Testo');

        $citta = $adb->query_result($res_dati_export, $i, 'mailingcity');
        $citta = normalizzaStringaCsvPerExportCrm($citta, 'Testo');

        $indirizzo = $adb->query_result($res_dati_export, $i, 'mailingstreet');
        $indirizzo = normalizzaStringaCsvPerExportCrm($indirizzo, 'Testo');
        $n_civico = '';

        if ($indirizzo != '') {
            $array_indirizzo = explode(',', $indirizzo);
            if (count($array_indirizzo) == 2) {
                $indirizzo = addslashes(trim($array_indirizzo[0]));
                $n_civico = addslashes(trim($array_indirizzo[1]));
            }
        }

        $cap = $adb->query_result($res_dati_export, $i, 'mailingzip');
        $cap = normalizzaStringaCsvPerExportCrm($cap, 'Testo');

        $cont = 0;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $codice_fiscale;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $codice_erp_risorsa;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $cognome;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $nome;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $codice_erp_azienda;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $descrizione_azienda;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $codice_erp_stabilimento;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $descrizione_stabilimento;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $data_assunzione;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $data_cessazione;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $compleanno;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $tipo_contratto;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $email;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $sesso;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $stato;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $provincia;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $citta;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $indirizzo;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $n_civico;
        $cont++;
        $nome_posizione_intestazione = $array_intestazione_csv[$cont];

        $array_dati_csv[$i][$nome_posizione_intestazione] = $cap;
    }

    array2csv($array_intestazione_csv, $array_dati_csv, $path_ftp.$dir, $csv_filename);
    
    $data_fine_importazione = date("Y-m-d H:i:s");

    $report_finale = " terminato alle ".$data_fine_importazione.": record esportati ".$num_dati_export;
    $handle_log_file=fopen($path_logs.$logs_file_name, "a+");
    fwrite($handle_log_file, $report_finale);
    fclose($handle_log_file);
}

function array2csv($array_intestazione, $array_dati, $path, $csv_filename) {
    if (count($array_intestazione) == 0 || count($array_dati) == 0) {
        die();
    } else {
        $df = fopen($path.'/'.$csv_filename, 'w+');
        fputcsv($df, array_values($array_intestazione), ';', ' ');
        foreach ($array_dati as $row) {
            fputcsv($df, array_values($row), ';', ' ');
        }
        fclose($df);
    }
}

?>