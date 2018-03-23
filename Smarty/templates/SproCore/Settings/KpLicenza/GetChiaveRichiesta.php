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

if(isset($_POST['lista_moduli'])){

    $lista_moduli_encode = $_POST['lista_moduli'];

    $lista_moduli_decode = json_decode($lista_moduli_encode);

    $lista_moduli = array();
    
    foreach($lista_moduli_decode as $modulo){

        if($modulo->check == 1){
            $check = true;
        }
        else{
            $check = false; 
        }

        $lista_moduli[] = array("id" => $modulo->id,
                                "check" => $check);

    }

    $lista_programmi = array();

    if( isset($_POST['lista_programmi']) ){
        $lista_programmi_encode = $_POST['lista_programmi'];

        $lista_programmi_decode = json_decode($lista_programmi_encode);
        
        foreach($lista_programmi_decode as $programma){

            if($programma->check == 1){
                $check = true;
            }
            else{
                $check = false; 
            }

            $lista_programmi[] = array("id" => $programma->id,
                                        "numero_utenti" => $programma->numero_utenti,
                                        "check" => $check);

        }

    }

    $chive_di_richiesta = KpLicenza::getChiaveRichiestaAttivazione($lista_moduli, $lista_programmi);

    $rows = $chive_di_richiesta;

}

$json = json_encode($rows);
print $json;

?>