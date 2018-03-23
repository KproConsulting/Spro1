<?php

/* kpro@tom18072017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

include_once('../../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

require_once('modules/SproCore/SproUtils/KpLicenza.php');

$rows = array();

if (!isset($_SESSION['authenticated_user_id'])) {

    $json = json_encode($rows);
    print $json;
    die;

    header("Location: ". $site_URL."/index.php");
	die; 
}
$current_user->id = $_SESSION['authenticated_user_id'];

if(isset($_POST['dati'])){

    $dati = $_POST['dati'];

    KpLicenza::registraChiaveAttivazione($dati);

    $rows[] = array('result' => 'ok');

}

$json = json_encode($rows);
print $json;

?>