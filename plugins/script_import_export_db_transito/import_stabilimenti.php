<?php

/* kpro@tom20042017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

require_once('Classi/KpImportStabilimenti.php');

include_once('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $current_user, $adb, $table_prefix, $default_charset;
session_start();
ini_set('memory_limit','1024M');

$current_user->id = 1;

$import = new KpImportStabilimenti("Stabilimenti", "import_export", "erp_destinazioni_diverse");
$import->setPhpErrorLog(false); //Imposto il debug php
$import->setDebug(true);     //Imposto il debug del codice
$import->setFullLog(false); //Imposto la creazione di un file di log esteso
$import->setClearDbTransito(true); //Imposto la cancellazione dei record dopo che sono stati processati
$import->setDbTransitoKeys( array('data_export' => 'string', 'codice_erp_azienda' => 'string', 'codice_dest_diversa' => 'string') );   //Imposto la chiave del DataBase di transito
$import->setCampiObbligatori( array('azienda', 'kp_codice_erp', 'nome_stabilimento') ); //imposto il fieldname dei campi obbligatori nel CRM
$import->setLogFile(__DIR__."/Logs/", "stabilimenti_import_log.txt");
$import->setErrorLogFile(__DIR__."/Logs/", "stabilimenti_import_error.txt");

//$import->createMapsFile(); //Esegue la creazione del file di mappatura dei campi
$import->run(); //Esegue l'import dei dati


?>