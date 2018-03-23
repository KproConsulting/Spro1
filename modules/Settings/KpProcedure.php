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

$revisioneProcessi = 'disabled';

$query = "SELECT 
            revisione_processi
            FROM kp_settings_procedure";

$result_query = $adb->query($query);
$num_result = $adb->num_rows($result_query);

if( $num_result > 0 ){
    
    $revisioneProcessi = $adb->query_result($result_query, 0, 'revisione_processi');
	$revisioneProcessi = html_entity_decode(strip_tags($revisioneProcessi), ENT_QUOTES, $default_charset);
    if($revisioneProcessi == '1'){
        $revisioneProcessi = 'enabled';
    }

}

$smarty->assign("revisioneProcessi", $revisioneProcessi);

$smarty->display('SproCore/Settings/KpProcedure.tpl');

?>