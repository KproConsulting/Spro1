<?php

/* kpro@tom2412015 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2015, Kpro Consulting Srl
 * @package vteSicurezza
 * @version 1.0
 * 
 * Questa leggere tutte le mansioni-risorse e riporta i tipi corso e i tipi visita medica della relativa mansione sulla mansione-risorsa
 */

include_once('../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $current_user, $adb, $table_prefix;
session_start();

require_once('modules/SproCore/SproUtils/spro_utils.php');

die("Togliere Die!");

$q_mansioni_risorse = "SELECT mr.mansionirisorsaid mansionirisorsaid FROM {$table_prefix}_mansionirisorsa mr
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = mr.mansionirisorsaid
                        WHERE ent.deleted = 0";
$res_mansioni_risorse = $adb->query($q_mansioni_risorse);
$num_mansioni_risorse = $adb->num_rows($res_mansioni_risorse);
for($i=0; $i<$num_mansioni_risorse; $i++){	

    $mansionirisorsaid = $adb->query_result($res_mansioni_risorse,$i,'mansionirisorsaid');
    $mansionirisorsaid = html_entity_decode(strip_tags($mansionirisorsaid), ENT_QUOTES,$default_charset);
    
    recuperaTipiCorsoDallaMansione($mansionirisorsaid);
    
    recuperaTipiVisiteMedicheDallaMansione($mansionirisorsaid);

    recuperaCategoriePrivacyDallaMansione($mansionirisorsaid);
    
}

printf("Totale Mansioni-Risorsa elaborate: %s", $num_mansioni_risorse);
    
?>