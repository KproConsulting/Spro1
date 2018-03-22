<?php

/* kpro@tom2412015 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2015, Kpro Consulting Srl
 * @package vteSicurezza
 * @version 1.0
 * 
 * Questa leggere riporta i tipi visita medica della relativa mansione sulla mansione-risorsa
 */

include_once('../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $current_user, $adb, $table_prefix;
session_start();

require_once('modules/SproCore/SproUtils/spro_utils.php');

if(isset($_REQUEST['record'])){
    $record = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_REQUEST['record']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $record = substr($record,0,100);
    
    recuperaTipiVisiteMedicheDallaMansione($record);

}
    
?>