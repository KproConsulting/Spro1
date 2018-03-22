<?php
/* kpro@tom140220181508 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2018, Kpro Consulting Srl
 */

global $currentModule;
global $mod_strings;

$record = vtlib_purify($_REQUEST['record']);
$module = vtlib_purify($_REQUEST['module']);
$return_module = vtlib_purify($_REQUEST['return_module']);
$return_action = vtlib_purify($_REQUEST['return_action']);
$return_id = vtlib_purify($_REQUEST['return_id']);
$parenttab = getParentTab();

$url = "index.php?module=$return_module&action=$return_action&record=$return_id&parenttab=$parenttab&relmodule=$module";

if(!isset($_REQUEST['record'])) die($mod_strings['ERR_DELETE_RECORD']);

$focus = CRMEntity::getInstance($currentModule);

$focus->unlinkRelationship($record, $return_module, $return_id);

$parenttab = getParentTab();
$url .= getBasic_Advance_SearchURL();

if(isset($_REQUEST['activity_mode'])){
	$url .= '&activity_mode='.vtlib_purify($_REQUEST['activity_mode']);
}

header("Location: $url");
?>