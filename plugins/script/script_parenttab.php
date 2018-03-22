<?php
require('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
require_once('vtlib/Vtecrm/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix;
session_start();

/*
SDK::unsetFile('Settings', 'menuSettings'); //menuSettingsArea
SDK::unsetClass('AreaManager'); //AreaManagerSDKClass -- modules/SDK/src/modules/Area/AreaSDKClass.php
SDK::unsetClass('VTEPageHeader'); //VTEPageHeaderArea -- modules/SDK/src/PageHeaderArea.php
*/

//Per rinominare i parent tab (tabella vte_parenttab)
//SDK::setLanguageEntries('APP_STRINGS', 'Inventory', array('it_it'=>'Ulteriori','en_us'=>'Others'));
         
require_once('vtlib/Vtiger/Menu.php');

/*//Per creare il parent tab Anagrafiche
$adb->query("INSERT INTO ".$table_prefix."_parenttab VALUES (10,'Anagrafiche',10,0,0)");

$menu_anagrafiche = Vtiger_Menu::getInstance('Anagrafiche');
$menu_anagrafiche->addModule(Vtiger_Module::getInstance('Accounts'));
$menu_anagrafiche->addModule(Vtiger_Module::getInstance('Stabilimenti'));
$menu_anagrafiche->addModule(Vtiger_Module::getInstance('Contacts'));
$menu_anagrafiche->addModule(Vtiger_Module::getInstance('Mansioni'));
$menu_anagrafiche->addModule(Vtiger_Module::getInstance('MansioniRisorsa'));
$menu_anagrafiche->addModule(Vtiger_Module::getInstance('Vendors'));

$menu_pianificazioni = Vtiger_Menu::getInstance('ProjectPlan');
$menu_pianificazioni->addModule(Vtiger_Module::getInstance('ProjectResources'));
$menu_pianificazioni->addModule(Vtiger_Module::getInstance('ProjectTimecards'));
$menu_pianificazioni->addModule(Vtiger_Module::getInstance('RelazioniOp'));
$menu_pianificazioni->addModule(Vtiger_Module::getInstance('TaskResources'));

$adb->query("INSERT INTO ".$table_prefix."_parenttab VALUES (11,'Formazione',11,0,0)");

$menu_formazione = Vtiger_Menu::getInstance('Formazione');
$menu_formazione->addModule(Vtiger_Module::getInstance('TipiCorso'));
$menu_formazione->addModule(Vtiger_Module::getInstance('KpFormazione'));
$menu_formazione->addModule(Vtiger_Module::getInstance('KpPartecipFormaz'));
$menu_formazione->addModule(Vtiger_Module::getInstance('SituazFormaz'));
$menu_formazione->addModule(Vtiger_Module::getInstance('KpTemplateQuestionari'));
$menu_formazione->addModule(Vtiger_Module::getInstance('KpDomande'));
$menu_formazione->addModule(Vtiger_Module::getInstance('KpQuestionari'));

$adb->query("INSERT INTO ".$table_prefix."_parenttab VALUES (12,'Visite Mediche',12,0,0)");

$menu_vis_med = Vtiger_Menu::getInstance('Visite Mediche');
$menu_vis_med->addModule(Vtiger_Module::getInstance('TipiVisitaMed'));
$menu_vis_med->addModule(Vtiger_Module::getInstance('StoricoVisite'));
$menu_vis_med->addModule(Vtiger_Module::getInstance('VisiteMediche'));
$menu_vis_med->addModule(Vtiger_Module::getInstance('SituazVisiteMed'));
$menu_vis_med->addModule(Vtiger_Module::getInstance('EsitiVisiteMediche'));

$adb->query("INSERT INTO ".$table_prefix."_parenttab VALUES (13,'Impianti',13,0,0)");

$menu_impianti = Vtiger_Menu::getInstance('Impianti');
$menu_impianti->addModule(Vtiger_Module::getInstance('TipiVerifiche'));
$menu_impianti->addModule(Vtiger_Module::getInstance('CheckLists'));
$menu_impianti->addModule(Vtiger_Module::getInstance('Impianti'));
$menu_impianti->addModule(Vtiger_Module::getInstance('CompImpianto'));
$menu_impianti->addModule(Vtiger_Module::getInstance('Manutenzioni'));
$menu_impianti->addModule(Vtiger_Module::getInstance('SituazCheckList'));
$menu_impianti->addModule(Vtiger_Module::getInstance('TempiManutenzioni'));
$menu_impianti->addModule(Vtiger_Module::getInstance('FermiImpianto'));
$menu_impianti->addModule(Vtiger_Module::getInstance('MatSostituiti'));
$menu_impianti->addModule(Vtiger_Module::getInstance('KpRigheManutenzioni'));
$menu_impianti->addModule(Vtiger_Module::getInstance('EsitiManutenzioni'));

$adb->query("INSERT INTO ".$table_prefix."_parenttab VALUES (14,'DPI',14,0,0)");

$menu_dpi = Vtiger_Menu::getInstance('DPI');
$menu_dpi->addModule(Vtiger_Module::getInstance('Contacts'));
$menu_dpi->addModule(Vtiger_Module::getInstance('Products'));
$menu_dpi->addModule(Vtiger_Module::getInstance('ConsegnaDPI'));
$menu_dpi->addModule(Vtiger_Module::getInstance('ListaDPI'));

$adb->query("INSERT INTO ".$table_prefix."_parenttab VALUES (15,'Non Conformita',15,0,0)");

$menu_non_conf = Vtiger_Menu::getInstance('Non Conformita');
$menu_non_conf->addModule(Vtiger_Module::getInstance('Contacts'));
$menu_non_conf->addModule(Vtiger_Module::getInstance('Richiami'));
$menu_non_conf->addModule(Vtiger_Module::getInstance('NonConformita'));
$menu_non_conf->addModule(Vtiger_Module::getInstance('AzioniCorrettive'));

$adb->query("INSERT INTO ".$table_prefix."_parenttab VALUES (16,'DVR',16,0,0)");

$menu_dvr = Vtiger_Menu::getInstance('DVR');
$menu_dvr->addModule(Vtiger_Module::getInstance('KpAreeStabilimento'));
$menu_dvr->addModule(Vtiger_Module::getInstance('KpRischiDVR'));
$menu_dvr->addModule(Vtiger_Module::getInstance('KpAttivitaDVR'));
$menu_dvr->addModule(Vtiger_Module::getInstance('KpRilevazioniRischi'));
$menu_dvr->addModule(Vtiger_Module::getInstance('KpInterventiRR'));
$menu_dvr->addModule(Vtiger_Module::getInstance('KpRilevazRischiRig'));
$menu_dvr->addModule(Vtiger_Module::getInstance('KpInterventiRRRighe'));

$adb->query("INSERT INTO ".$table_prefix."_parenttab VALUES (17,'Qualita',17,0,0)");

$menu_qualita = Vtiger_Menu::getInstance('Qualita');
$menu_qualita->addModule(Vtiger_Module::getInstance('KpRuoli'));
$menu_qualita->addModule(Vtiger_Module::getInstance('KpOrganigrammi'));
$menu_qualita->addModule(Vtiger_Module::getInstance('KpProcedure'));
$menu_qualita->addModule(Vtiger_Module::getInstance('NonConformita'));
$menu_qualita->addModule(Vtiger_Module::getInstance('Richiami'));
$menu_qualita->addModule(Vtiger_Module::getInstance('AzioniCorrettive'));
$menu_qualita->addModule(Vtiger_Module::getInstance('Richiami'));

$adb->query("INSERT INTO ".$table_prefix."_parenttab VALUES (18,'Privacy',18,0,0)");

$menu_privacy = Vtiger_Menu::getInstance('Privacy');
$menu_privacy->addModule(Vtiger_Module::getInstance('KpCategoriePrivacy'));
$menu_privacy->addModule(Vtiger_Module::getInstance('KpMinaccePrivacy'));
$menu_privacy->addModule(Vtiger_Module::getInstance('KpMisurePrivacy'));
$menu_privacy->addModule(Vtiger_Module::getInstance('KpLettereNomina'));
$menu_privacy->addModule(Vtiger_Module::getInstance('KpRilRischiPrivacy'));
$menu_privacy->addModule(Vtiger_Module::getInstance('KpIntRidRischiPrivacy'));
$menu_privacy->addModule(Vtiger_Module::getInstance('KpSitMinaccePrivacy'));

$adb->query("INSERT INTO ".$table_prefix."_parenttab VALUES (19,'Acquisti',19,0,0)");

$menu_acquisti = Vtiger_Menu::getInstance('Acquisti');
$menu_acquisti->addModule(Vtiger_Module::getInstance('PriceBooks'));
$menu_acquisti->addModule(Vtiger_Module::getInstance('Vendors'));
$menu_acquisti->addModule(Vtiger_Module::getInstance('PurchaseOrder'));
$menu_acquisti->addModule(Vtiger_Module::getInstance('Scadenziario'));
           
create_tab_data_file();

SDK::setLanguageEntries('APP_STRINGS', 'Anagrafiche', array('it_it'=>'Anagrafiche','en_us'=>'Anagrafiche'));
SDK::setLanguageEntries('APP_STRINGS', 'Formazione', array('it_it'=>'Formazione','en_us'=>'Formazione'));
SDK::setLanguageEntries('APP_STRINGS', 'Visite Mediche', array('it_it'=>'Visite Mediche','en_us'=>'Visite Mediche'));
SDK::setLanguageEntries('APP_STRINGS', 'Impianti', array('it_it'=>'Impianti','en_us'=>'Impianti'));
SDK::setLanguageEntries('APP_STRINGS', 'DPI', array('it_it'=>'DPI','en_us'=>'DPI'));
SDK::setLanguageEntries('APP_STRINGS', 'Non Conformita', array('it_it'=>'Non Conformita','en_us'=>'Non Conformita'));
SDK::setLanguageEntries('APP_STRINGS', 'DVR', array('it_it'=>'DVR','en_us'=>'DVR'));
SDK::setLanguageEntries('APP_STRINGS', 'Qualita', array('it_it'=>'Qualità','en_us'=>'Quality'));
SDK::setLanguageEntries('APP_STRINGS', 'Privacy', array('it_it'=>'Privacy','en_us'=>'Privacy'));
SDK::setLanguageEntries('APP_STRINGS', 'Acquisti', array('it_it'=>'Acquisti','en_us'=>'Acquisti'));

*/

?>