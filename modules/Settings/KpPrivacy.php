<?php

/* kpro@tom31072017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

require_once('include/utils/utils.php');
require_once('Smarty_setup.php');
global $app_strings;
global $mod_strings;
global $currentModule;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
global $current_language, $adb;

$smarty = new vtigerCRM_Smarty;

$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("THEME", $theme);


$check_table = "SHOW TABLES LIKE 'kp_settings_privacy'";

$result_check_table = $adb->query($check_table);
$num_check_table = $adb->num_rows($result_check_table);

if( $num_check_table == 0 ){

    $create_table = "CREATE TABLE IF NOT EXISTS `kp_settings_privacy` (
                        `probabilita_1` longtext,
                        `probabilita_2` longtext,
                        `probabilita_3` longtext,
                        `probabilita_4` longtext,
                        `probabilita_5` longtext,
                        `magnitudo_1` longtext,
                        `magnitudo_2` longtext,
                        `magnitudo_3` longtext,
                        `magnitudo_4` longtext,
                        `magnitudo_5` longtext) 
                    ENGINE=InnoDB DEFAULT CHARSET=utf8";

    $adb->query($create_table);

}


$query = "SELECT 
            probabilita_1,
            probabilita_2,
            probabilita_3,
            probabilita_4,
            probabilita_5,
            magnitudo_1,
            magnitudo_2,
            magnitudo_3,
            magnitudo_4,
            magnitudo_5
            FROM kp_settings_privacy";

$result_query = $adb->query($query);
$num_result = $adb->num_rows($result_query);

if( $num_result > 0 ){
    
    $probabilita_1 = $adb->query_result($result_query, 0, 'probabilita_1');
	$probabilita_1 = html_entity_decode(strip_tags($probabilita_1), ENT_QUOTES, $default_charset);
    if($probabilita_1 == null){
        $probabilita_1 = '';
    }

    $probabilita_2 = $adb->query_result($result_query, 0, 'probabilita_2');
	$probabilita_2 = html_entity_decode(strip_tags($probabilita_2), ENT_QUOTES, $default_charset);
    if($probabilita_2 == null){
        $probabilita_2 = '';
    }

    $probabilita_3 = $adb->query_result($result_query, 0, 'probabilita_3');
	$probabilita_3 = html_entity_decode(strip_tags($probabilita_3), ENT_QUOTES, $default_charset);
    if($probabilita_3 == null){
        $probabilita_3 = '';
    }

    $probabilita_4 = $adb->query_result($result_query, 0, 'probabilita_4');
	$probabilita_4 = html_entity_decode(strip_tags($probabilita_4), ENT_QUOTES, $default_charset);
    if($probabilita_4 == null){
        $probabilita_4 = '';
    }

    $probabilita_5 = $adb->query_result($result_query, 0, 'probabilita_5');
	$probabilita_5 = html_entity_decode(strip_tags($probabilita_5), ENT_QUOTES, $default_charset);
    if($probabilita_5 == null){
        $probabilita_5 = '';
    }

    $magnitudo_1 = $adb->query_result($result_query, 0, 'magnitudo_1');
	$magnitudo_1 = html_entity_decode(strip_tags($magnitudo_1), ENT_QUOTES, $default_charset);
    if($magnitudo_1 == null){
        $magnitudo_1 = '';
    }

    $magnitudo_2 = $adb->query_result($result_query, 0, 'magnitudo_2');
	$magnitudo_2 = html_entity_decode(strip_tags($magnitudo_2), ENT_QUOTES, $default_charset);
    if($magnitudo_2 == null){
        $magnitudo_2 = '';
    }

    $magnitudo_3 = $adb->query_result($result_query, 0, 'magnitudo_3');
	$magnitudo_3 = html_entity_decode(strip_tags($magnitudo_3), ENT_QUOTES, $default_charset);
    if($magnitudo_3 == null){
        $magnitudo_3 = '';
    }

    $magnitudo_4 = $adb->query_result($result_query, 0, 'magnitudo_4');
	$magnitudo_4 = html_entity_decode(strip_tags($magnitudo_4), ENT_QUOTES, $default_charset);
    if($magnitudo_4 == null){
        $magnitudo_4 = '';
    }

    $magnitudo_5 = $adb->query_result($result_query, 0, 'magnitudo_5');
	$magnitudo_5 = html_entity_decode(strip_tags($magnitudo_5), ENT_QUOTES, $default_charset);
    if($magnitudo_5 == null){
        $magnitudo_5 = '';
    }

}

$smarty->assign("form_probabilita_1", $probabilita_1);
$smarty->assign("form_probabilita_2", $probabilita_2);
$smarty->assign("form_probabilita_3", $probabilita_3);
$smarty->assign("form_probabilita_4", $probabilita_4);
$smarty->assign("form_probabilita_5", $probabilita_5);

$smarty->assign("form_magnitudo_1", $magnitudo_1);
$smarty->assign("form_magnitudo_2", $magnitudo_2);
$smarty->assign("form_magnitudo_3", $magnitudo_3);
$smarty->assign("form_magnitudo_4", $magnitudo_4);
$smarty->assign("form_magnitudo_5", $magnitudo_5);

$smarty->display('SproCore/Settings/KpPrivacy.tpl');

?>