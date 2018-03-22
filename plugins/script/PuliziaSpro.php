<?php

/* kpro@tom180120171100 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 * 
 * Script per pulire i dati dal CRM
 */

printf("Togliere Die!"); die;

require('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
require_once('vtlib/Vtecrm/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix;
session_start();

//Pulisce le mail in coda di uscita
$q_pulizia_mail_in_coda = "DELETE FROM {$table_prefix}_emails_send_queue";

$adb->query($q_pulizia_mail_in_coda);

//Pulisce le mail
$q_pulizia_mail = "DELETE FROM {$table_prefix}_messages";

$adb->query($q_pulizia_mail);

//Pulisce gli account mail
$q_pulizia_mail_account = "DELETE FROM {$table_prefix}_messages_account";

$adb->query($q_pulizia_mail_account);

//Pulisce gli allegati delle mail
$q_pulizia_allegati_mail = "DELETE FROM {$table_prefix}_messages_attach";

$adb->query($q_pulizia_allegati_mail);

//Pulisce la configurazione del server mail
$q_pulizia_server_mail = "DELETE FROM {$table_prefix}_systems";

$adb->query($q_pulizia_server_mail);

//Pulisce lo storico accessi
$q_pulizia_storico_accessi = "DELETE FROM {$table_prefix}_loginhistory";

$adb->query($q_pulizia_storico_accessi);

$q_update_storico_accessi_seq = "UPDATE {$table_prefix}_loginhistory_seq SET id = 1";

$adb->query($q_update_storico_accessi_seq);

//Pulisce gli utenti ad eccezione dell'utente admin e dell'utente sicurezza
$q_pulizia_utenti = "DELETE FROM {$table_prefix}_users
                        WHERE id NOT IN (1, 4)";

$adb->query($q_pulizia_utenti);

//Pulizia moduli
$lista_moduli_da_pulire = array();
$lista_moduli_da_pulire[] = "Accounts";
$lista_moduli_da_pulire[] = "AttivWorkflow";
$lista_moduli_da_pulire[] = "AzioniCorrettive";
$lista_moduli_da_pulire[] = "Calendar";
$lista_moduli_da_pulire[] = "Canoni";
$lista_moduli_da_pulire[] = "ChangeLog";
$lista_moduli_da_pulire[] = "CheckLists";
$lista_moduli_da_pulire[] = "Commesse";
$lista_moduli_da_pulire[] = "CompImpianto";
$lista_moduli_da_pulire[] = "ConfWorkflow";
$lista_moduli_da_pulire[] = "ConsegnaDPI";
$lista_moduli_da_pulire[] = "Contacts";
$lista_moduli_da_pulire[] = "DistintaServizi";
$lista_moduli_da_pulire[] = "Documents";
$lista_moduli_da_pulire[] = "Documents Attachment";
$lista_moduli_da_pulire[] = "EsitiManutenzioni";
$lista_moduli_da_pulire[] = "EsitiVisiteMediche";
$lista_moduli_da_pulire[] = "FermiImpianto";
$lista_moduli_da_pulire[] = "Formazione";
$lista_moduli_da_pulire[] = "GestioneAvvisi";
$lista_moduli_da_pulire[] = "GestioneRifiutiHead";
$lista_moduli_da_pulire[] = "GestioneRifiutiLine";
$lista_moduli_da_pulire[] = "HelpDesk";
$lista_moduli_da_pulire[] = "Impianti";
$lista_moduli_da_pulire[] = "Invoice";
$lista_moduli_da_pulire[] = "KpAreeStabilimento";
$lista_moduli_da_pulire[] = "KpAttivitaDVR";
$lista_moduli_da_pulire[] = "KpFormazione";
$lista_moduli_da_pulire[] = "KpGiorniPartForm";
$lista_moduli_da_pulire[] = "KpIndiciPostazioni";
$lista_moduli_da_pulire[] = "KpInterventiRR";
$lista_moduli_da_pulire[] = "KpInterventiRRRighe";
$lista_moduli_da_pulire[] = "KpPartecipFormaz";
$lista_moduli_da_pulire[] = "KpRigheManutenzioni";
$lista_moduli_da_pulire[] = "KpRilevazioniRischi";
$lista_moduli_da_pulire[] = "KpRischiDVR";
$lista_moduli_da_pulire[] = "KpValutazioniOCRA";
$lista_moduli_da_pulire[] = "ListaDPI";
$lista_moduli_da_pulire[] = "ListaPartecip";
//$lista_moduli_da_pulire[] = "Mansioni";
$lista_moduli_da_pulire[] = "MansioniRisorsa";
$lista_moduli_da_pulire[] = "Manutenzioni";
$lista_moduli_da_pulire[] = "MatSostituiti";
$lista_moduli_da_pulire[] = "Messages";
$lista_moduli_da_pulire[] = "ModComments";
$lista_moduli_da_pulire[] = "ModNotifications";
$lista_moduli_da_pulire[] = "ModPagamento";
$lista_moduli_da_pulire[] = "ModuleNumbering";
$lista_moduli_da_pulire[] = "Myfiles";
$lista_moduli_da_pulire[] = "Myfiles Attachment";
$lista_moduli_da_pulire[] = "NonConformita";
$lista_moduli_da_pulire[] = "Normative";
$lista_moduli_da_pulire[] = "OdF";
$lista_moduli_da_pulire[] = "Potentials";
$lista_moduli_da_pulire[] = "PriceBooks";
$lista_moduli_da_pulire[] = "Processi";
$lista_moduli_da_pulire[] = "Products";
$lista_moduli_da_pulire[] = "Products Image";
$lista_moduli_da_pulire[] = "ProjectMilestone";
$lista_moduli_da_pulire[] = "ProjectPlan";
$lista_moduli_da_pulire[] = "ProjectResources";
$lista_moduli_da_pulire[] = "ProjectTask";
$lista_moduli_da_pulire[] = "ProjectTimecards";
$lista_moduli_da_pulire[] = "PurchaseOrder";
$lista_moduli_da_pulire[] = "Quotes";
$lista_moduli_da_pulire[] = "RelazioniOp";
$lista_moduli_da_pulire[] = "Richiami";
$lista_moduli_da_pulire[] = "Ruoli";
$lista_moduli_da_pulire[] = "SalesOrder";
$lista_moduli_da_pulire[] = "Scadenziario";
$lista_moduli_da_pulire[] = "Services";
$lista_moduli_da_pulire[] = "SituazCheckList";
$lista_moduli_da_pulire[] = "SituazFormaz";
$lista_moduli_da_pulire[] = "SituazVisiteMed";
$lista_moduli_da_pulire[] = "Stabilimenti";
$lista_moduli_da_pulire[] = "StoricoFormazione";
$lista_moduli_da_pulire[] = "StoricoVisite";
$lista_moduli_da_pulire[] = "TaskResources";
$lista_moduli_da_pulire[] = "TecniciManutentori";
$lista_moduli_da_pulire[] = "TempiManutenzioni";
$lista_moduli_da_pulire[] = "Timecards";
//$lista_moduli_da_pulire[] = "TipiCorso";
$lista_moduli_da_pulire[] = "TipiDocumenti";
$lista_moduli_da_pulire[] = "TipiVerifiche";
//$lista_moduli_da_pulire[] = "TipiVisitaMed";
$lista_moduli_da_pulire[] = "Users Attachment";
$lista_moduli_da_pulire[] = "Vendors";
$lista_moduli_da_pulire[] = "Venduto";
$lista_moduli_da_pulire[] = "VisiteMediche";
$lista_moduli_da_pulire[] = "Visitreport";
//$lista_moduli_da_pulire[] = "KpProcedure";
//$lista_moduli_da_pulire[] = "KpEntitaProcedure";
//$lista_moduli_da_pulire[] = "KpRuoli";
$lista_moduli_da_pulire[] = "KpRevisioniProcedure";
$lista_moduli_da_pulire[] = "KpNotificheRevProc";
//$lista_moduli_da_pulire[] = "KpCategoriePrivacy";
$lista_moduli_da_pulire[] = "KpLettereNomina";
//$lista_moduli_da_pulire[] = "KpMinaccePrivacy";
//$lista_moduli_da_pulire[] = "KpMisurePrivacy";
$lista_moduli_da_pulire[] = "KpRilRischiPrivacy";
$lista_moduli_da_pulire[] = "KpRigheRilRischiPriva";
$lista_moduli_da_pulire[] = "KpIntRidRischiPrivacy";
$lista_moduli_da_pulire[] = "KpRigIntRidRiscPrivac";
$lista_moduli_da_pulire[] = "KpSitMinaccePrivacy";
$lista_moduli_da_pulire[] = "KpVariazioniTicket";
$lista_moduli_da_pulire[] = "KpGiorniFormazione";
$lista_moduli_da_pulire[] = "KpModuliFormazione";
$lista_moduli_da_pulire[] = "KpSollecitiPagamenti";
$lista_moduli_da_pulire[] = "KpAule";
$lista_moduli_da_pulire[] = "KpAgenti";
$lista_moduli_da_pulire[] = "KpBusinessUnit";
$lista_moduli_da_pulire[] = "KpProvvigioni";
$lista_moduli_da_pulire[] = "KpTabelleProvvigional";
$lista_moduli_da_pulire[] = "KpConsegnaDocumenti";
//$lista_moduli_da_pulire[] = "KpBanche";
$lista_moduli_da_pulire[] = "KpContiCorrenti";
$lista_moduli_da_pulire[] = "KpRischiQualita";
$lista_moduli_da_pulire[] = "KpSalesOrderLine";
$lista_moduli_da_pulire[] = "KpTemplateQuestionari";
$lista_moduli_da_pulire[] = "KpDomande";
$lista_moduli_da_pulire[] = "KpDomandeQuestionari";
$lista_moduli_da_pulire[] = "KpQuestionari";
$lista_moduli_da_pulire[] = "KpRisposteQuestionari";

$lista_cartelle_documenti_da_NON_pulire = array(40);
$lista_allegati_documenti_da_NON_pulire = GetAllegatiDaNonPulire($lista_cartelle_documenti_da_NON_pulire);

foreach($lista_moduli_da_pulire as $modulo){
    
    if($modulo == 'Documents' && !empty($lista_cartelle_documenti_da_NON_pulire)){
        $q_pulizia_modulo = "UPDATE {$table_prefix}_crmentity ent
                            INNER JOIN {$table_prefix}_notes note ON note.notesid = ent.crmid
                            SET ent.deleted = 1
                            WHERE ent.setype = '".$modulo."'
                            AND note.folderid NOT IN (".implode(',', $lista_cartelle_documenti_da_NON_pulire).")";
        $adb->query($q_pulizia_modulo);
    }
    elseif($modulo == 'Documents Attachment' && !empty($lista_allegati_documenti_da_NON_pulire)){
        $q_pulizia_modulo = "UPDATE {$table_prefix}_crmentity SET
                                deleted = 1
                                WHERE setype = '".$modulo."'
                                AND crmid NOT IN (".implode(',', $lista_allegati_documenti_da_NON_pulire).")";
        $adb->query($q_pulizia_modulo);
    }
    else{
        $q_pulizia_modulo = "UPDATE {$table_prefix}_crmentity SET
                                deleted = 1
                                WHERE setype = '".$modulo."'";
        $adb->query($q_pulizia_modulo);
    }

}

svuotaCestino();

function GetAllegatiDaNonPulire($array_cartelle){
    global $adb, $table_prefix;

    $result = array();

    if(!empty($array_cartelle)){
        $q_recupera_allegati = "SELECT ent.crmid
                            FROM {$table_prefix}_crmentity ent 
                            INNER JOIN {$table_prefix}_seattachmentsrel att ON att.attachmentsid = ent.crmid
                            INNER JOIN {$table_prefix}_notes note ON note.notesid = att.crmid
                            WHERE ent.setype = 'Documents Attachment' 
                            AND note.folderid IN (".implode(',', $array_cartelle).")";
        $res_recupera_allegati = $adb->query($q_recupera_allegati);
        $num_recupera_allegati = $adb->num_rows($res_recupera_allegati);
        if($num_recupera_allegati > 0){
            for($i = 0; $i < $num_recupera_allegati; $i++){
                $id_allegato = $adb->query_result($res_recupera_allegati, $i, 'crmid');
                if($id_allegato != 0 && $id_allegato != '' && $id_allegato != null){
                    $result[] = $id_allegato;
                }
            }
        }

    }

    return $result;
}

function svuotaCestino(){
    global $adb, $table_prefix;

    require_once('modules/Documents/storage/StorageBackendUtils.php');
    
    $res = $adb->query('SELECT setype FROM '.$table_prefix.'_crmentity WHERE deleted=1 GROUP BY setype');

    $adb->query('DELETE FROM '.$table_prefix.'_crmentity WHERE deleted = 1');
    //TODO Related records for the module records deleted from vtiger_crmentity has to be deleted. 
    //It needs lookup in the related tables and needs to be removed if doesn't have a reference record in vtiger_crmentity
    
    $adb->query('DELETE FROM '.$table_prefix.'_relatedlists_rb');

    $SBU = StorageBackendUtils::getInstance();
    $SBU->deleteOrphanAttachments();

    while($row = $adb->fetchByAssoc($res)){
        $focus = CRMEntity::getInstance($row['setype']);
        foreach($focus->tab_name_index as $table=>$key){
            if($table == $table_prefix.'_crmentity') continue;
            
            $query="DELETE $table FROM $table INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = $table.$key WHERE deleted=1";
            
            $adb->query($query);
        }
    }

}

?>