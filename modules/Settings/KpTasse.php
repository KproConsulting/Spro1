<?php

/* kpro@bid29112017 */

/**
 * @author Bidese Jacopo
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
global $current_language, $adb, $table_prefix;

$smarty = new vtigerCRM_Smarty;

$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("THEME", $theme);

$check_table = "SHOW TABLES LIKE 'kp_settings_tasse'";

$result_check_table = $adb->query($check_table);
$num_check_table = $adb->num_rows($result_check_table);

if( $num_check_table == 0 ){

    $create_table = "CREATE TABLE IF NOT EXISTS `kp_settings_tasse` (
            `id_configurazione` varchar(255) NOT NULL,
            `id_tassa` int(10) NOT NULL,
            `aggiungi_a_totale` varchar(1) NOT NULL,
            `calcola_su_totale_e_tasse` varchar(1) NOT NULL,
            `attivo` varchar(1) NOT NULL,
            PRIMARY KEY (`id_configurazione`,`id_tassa`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $adb->query($create_table);
}

AggiornaTabellaTasseKp();

$lista_configurazioni_tasse = array();
$array_numero_tasse = array();
$numero_tasse = 0;

$q_conf_tasse = "SELECT pk.code AS id_configurazione,
                pk.value AS nome_configurazione,
                tax.taxid,
                tax.taxname,
                tax.taxlabel,
                tax.percentage,
                kp.aggiungi_a_totale,
                kp.calcola_su_totale_e_tasse,
                kp.attivo
                FROM tbl_s_picklist_language pk
                INNER JOIN kp_settings_tasse kp ON kp.id_configurazione = pk.code
                INNER JOIN {$table_prefix}_inventorytaxinfo tax ON tax.taxid = kp.id_tassa
                WHERE pk.language = 'it_it'
                AND pk.field = 'kp_conf_tassazione'
                AND tax.deleted = 0";
$res_conf_tasse = $adb->query($q_conf_tasse);
$num_conf_tasse = $adb->num_rows($res_conf_tasse);
if($num_conf_tasse > 0){
    for($i = 0; $i < $num_conf_tasse; $i++){
        $id_configurazione = $adb->query_result($res_conf_tasse, $i, 'id_configurazione');
        $id_configurazione = html_entity_decode(strip_tags($id_configurazione), ENT_QUOTES, $default_charset);

        $nome_configurazione = $adb->query_result($res_conf_tasse, $i, 'nome_configurazione');
        $nome_configurazione = html_entity_decode(strip_tags($nome_configurazione), ENT_QUOTES, $default_charset);

        $taxid = $adb->query_result($res_conf_tasse, $i, 'taxid');
        $taxid = html_entity_decode(strip_tags($taxid), ENT_QUOTES, $default_charset);

        $taxname = $adb->query_result($res_conf_tasse, $i, 'taxname');
        $taxname = html_entity_decode(strip_tags($taxname), ENT_QUOTES, $default_charset);

        $taxlabel = $adb->query_result($res_conf_tasse, $i, 'taxlabel');
        $taxlabel = html_entity_decode(strip_tags($taxlabel), ENT_QUOTES, $default_charset);

        $percentage = $adb->query_result($res_conf_tasse, $i, 'percentage');
        $percentage = html_entity_decode(strip_tags($percentage), ENT_QUOTES, $default_charset);

        $aggiungi_a_totale = $adb->query_result($res_conf_tasse, $i, 'aggiungi_a_totale');
        $aggiungi_a_totale = html_entity_decode(strip_tags($aggiungi_a_totale), ENT_QUOTES, $default_charset);

        $calcola_su_totale_e_tasse = $adb->query_result($res_conf_tasse, $i, 'calcola_su_totale_e_tasse');
        $calcola_su_totale_e_tasse = html_entity_decode(strip_tags($calcola_su_totale_e_tasse), ENT_QUOTES, $default_charset);

        $attivo = $adb->query_result($res_conf_tasse, $i, 'attivo');
        $attivo = html_entity_decode(strip_tags($attivo), ENT_QUOTES, $default_charset);

        $tassa_gia_passata = false;
        
        if (empty($array_numero_tasse)) {
            $array_numero_tasse[] = $taxname;
        } else {
            if (in_array($taxname, $array_numero_tasse)) {
                $tassa_gia_passata = true;
            } else {
                $array_numero_tasse[] = $taxname;
            }
        }

        if(!$tassa_gia_passata){
            $numero_tasse++;
        }

        $lista_configurazioni_tasse[] = array(
            "id_configurazione" => $id_configurazione,
            "nome_configurazione" => $nome_configurazione,
            "taxid" => $taxid,
            "taxname" => $taxname,
            "taxlabel" => $taxlabel,
            "percentage" => $percentage,
            "aggiungi_a_totale" => $aggiungi_a_totale,
            "calcola_su_totale_e_tasse" => $calcola_su_totale_e_tasse,
            "attivo" => $attivo
        );
    }
}

$tabella_configurazioni_tasse = "<table width=100% class='table table-striped'>";
$tabella_configurazioni_tasse .= "<tr>";
$tabella_configurazioni_tasse .= "<td></td>";
$tabella_configurazioni_tasse .= "<td></td>";
$tabella_configurazioni_tasse .= "<td></td>";
$tabella_configurazioni_tasse .= "<td style='text-align: center;'>Aggiungi a totale</td>";
$tabella_configurazioni_tasse .= "<td style='text-align: center;'>Calcola su totale e tasse</td>";
$tabella_configurazioni_tasse .= "</tr>";

$id_configurazione = "";

foreach($lista_configurazioni_tasse as $configurazione){

    $tabella_configurazioni_tasse .= "<tr>";

    if($configurazione["id_configurazione"] != $id_configurazione){

        $id_configurazione = $configurazione["id_configurazione"]; 

        $tabella_configurazioni_tasse .= "<td style='vertical-align: middle;' rowspan='".$numero_tasse."'>";
        $tabella_configurazioni_tasse .= "<label>";
        $tabella_configurazioni_tasse .= "<b><span style='vertical-align: middle; margin-left: 10px;' >".$configurazione["nome_configurazione"]."</span></b></label>";
        $tabella_configurazioni_tasse .= "</b></td>";

    }
    elseif($configurazione["id_configurazione"] == $id_configurazione && $configurazione["id_configurazione"] == ""){

        $tabella_configurazioni_tasse .= "<td style='vertical-align: middle;'><b>";
        $tabella_configurazioni_tasse .= $configurazione["nome_configurazione"];
        $tabella_configurazioni_tasse .= "</b></td>";

    }

    $tabella_configurazioni_tasse .= "<td style='width: 40px;'>";
    $tabella_configurazioni_tasse .= "<div class='checkbox'>";
    $tabella_configurazioni_tasse .= "<label>";
    if($configurazione["attivo"] == '1'){
        $tabella_configurazioni_tasse .= "<input type='checkbox' id='form_tassa_".$configurazione["id_configurazione"]."_".$configurazione["taxid"]."' value='".$configurazione["taxid"]."' checked>";
    }
    else{
        $tabella_configurazioni_tasse .= "<input type='checkbox' id='form_tassa_".$configurazione["id_configurazione"]."_".$configurazione["taxid"]."' value='".$configurazione["taxid"]."'>";
    }
    $tabella_configurazioni_tasse .= "</label>";
    $tabella_configurazioni_tasse .= "</div>";
    $tabella_configurazioni_tasse .= "</td>";

    $tabella_configurazioni_tasse .= "<td style='vertical-align: middle;'>".$configurazione["taxlabel"]." (".$configurazione["percentage"]." %)</td>";
    
    $tabella_configurazioni_tasse .= "<td style='text-align: center;'>";
    if($configurazione["attivo"] == '1'){
        $tabella_configurazioni_tasse .= "<div class='checkbox' id='div_aggiungi_a_totale_".$configurazione["id_configurazione"]."_".$configurazione["taxid"]."'>";
        $tabella_configurazioni_tasse .= "<label>";
        if($configurazione["aggiungi_a_totale"] == '1'){
            $tabella_configurazioni_tasse .= "<input type='checkbox' id='form_aggiungi_a_totale_".$configurazione["id_configurazione"]."_".$configurazione["taxid"]."' value='".$configurazione["aggiungi_a_totale"]."' checked>";
        }
        else{
            $tabella_configurazioni_tasse .= "<input type='checkbox' id='form_aggiungi_a_totale_".$configurazione["id_configurazione"]."_".$configurazione["taxid"]."' value='".$configurazione["aggiungi_a_totale"]."'>";
        }
    }
    else{
        $tabella_configurazioni_tasse .= "<div class='checkbox disabled' id='div_aggiungi_a_totale_".$configurazione["id_configurazione"]."_".$configurazione["taxid"]."'>";
        $tabella_configurazioni_tasse .= "<label>";
        if($configurazione["aggiungi_a_totale"] == '1'){
            $tabella_configurazioni_tasse .= "<input type='checkbox' id='form_aggiungi_a_totale_".$configurazione["id_configurazione"]."_".$configurazione["taxid"]."' value='".$configurazione["aggiungi_a_totale"]."' disabled checked>";
        }
        else{
            $tabella_configurazioni_tasse .= "<input type='checkbox' id='form_aggiungi_a_totale_".$configurazione["id_configurazione"]."_".$configurazione["taxid"]."' value='".$configurazione["aggiungi_a_totale"]."' disabled>";
        }
    }
    $tabella_configurazioni_tasse .= "</label>";
    $tabella_configurazioni_tasse .= "</div>";
    $tabella_configurazioni_tasse .= "</td>";

    $tabella_configurazioni_tasse .= "<td style='text-align: center;'>";
    if($configurazione["attivo"] == '1'){
        $tabella_configurazioni_tasse .= "<div class='checkbox' id='div_calcola_su_totale_e_tasse_".$configurazione["id_configurazione"]."_".$configurazione["taxid"]."'>";
        $tabella_configurazioni_tasse .= "<label>";
        if($configurazione["calcola_su_totale_e_tasse"] == '1'){
            $tabella_configurazioni_tasse .= "<input type='checkbox' id='form_calcola_su_totale_e_tasse_".$configurazione["id_configurazione"]."_".$configurazione["taxid"]."' value='".$configurazione["calcola_su_totale_e_tasse"]."' checked>";
        }
        else{
            $tabella_configurazioni_tasse .= "<input type='checkbox' id='form_calcola_su_totale_e_tasse_".$configurazione["id_configurazione"]."_".$configurazione["taxid"]."' value='".$configurazione["calcola_su_totale_e_tasse"]."'>";
        }
    }
    else{
        $tabella_configurazioni_tasse .= "<div class='checkbox disabled' id='div_calcola_su_totale_e_tasse_".$configurazione["id_configurazione"]."_".$configurazione["taxid"]."'>";
        $tabella_configurazioni_tasse .= "<label>";
        if($configurazione["calcola_su_totale_e_tasse"] == '1'){
            $tabella_configurazioni_tasse .= "<input type='checkbox' id='form_calcola_su_totale_e_tasse_".$configurazione["id_configurazione"]."_".$configurazione["taxid"]."' value='".$configurazione["calcola_su_totale_e_tasse"]."' disabled checked>";
        }
        else{
            $tabella_configurazioni_tasse .= "<input type='checkbox' id='form_calcola_su_totale_e_tasse_".$configurazione["id_configurazione"]."_".$configurazione["taxid"]."' value='".$configurazione["calcola_su_totale_e_tasse"]."' disabled>";
        }
    }
    $tabella_configurazioni_tasse .= "</label>";
    $tabella_configurazioni_tasse .= "</div>";
    $tabella_configurazioni_tasse .= "</td>";

    $tabella_configurazioni_tasse .= "</tr>";

}

$tabella_configurazioni_tasse .= "</table>";

$smarty->assign("tabella_configurazioni_tasse", $tabella_configurazioni_tasse);

$smarty->display('SproCore/Settings/KpTasse.tpl');

function AggiornaTabellaTasseKp(){
    global $current_language, $adb, $table_prefix;

    $q_configurazioni = "SELECT pk.code
                    FROM tbl_s_picklist_language pk
                    WHERE pk.language = 'it_it'
                    AND pk.field = 'kp_conf_tassazione'";
    $res_configurazioni = $adb->query($q_configurazioni);
    $num_configurazioni = $adb->num_rows($res_configurazioni);
    if($num_configurazioni > 0){
        for($i = 0; $i < $num_configurazioni; $i++){
            $id_configurazione = $adb->query_result($res_configurazioni, $i, 'code');
            $id_configurazione = html_entity_decode(strip_tags($id_configurazione), ENT_QUOTES, $default_charset);

            $q_tasse = "SELECT tax.taxid
                    FROM {$table_prefix}_inventorytaxinfo tax
                    WHERE tax.deleted = 0";
            $res_tasse = $adb->query($q_tasse);
            $num_tasse = $adb->num_rows($res_tasse);
            if($num_tasse > 0){
                for($j = 0; $j < $num_tasse; $j++){
                    $id_tassa = $adb->query_result($res_tasse, $j, 'taxid');
                    $id_tassa = html_entity_decode(strip_tags($id_tassa), ENT_QUOTES, $default_charset);

                    $check_presenza = "SELECT * 
                                    FROM kp_settings_tasse
                                    WHERE id_configurazione = '{$id_configurazione}'
                                    AND id_tassa = ".$id_tassa;
                    $res_check_presenza = $adb->query($check_presenza);
                    if($adb->num_rows($res_check_presenza) == 0){
                        $q_insert = "INSERT INTO 
                                    (id_configurazione,id_tassa,aggiungi_a_totale,calcola_su_totale_e_tasse,attivo)
                                    VALUES ('{$id_configurazione}',{$id_tassa},'0','0','0')";
                        $adb->query($q_insert);
                    }
                }
            }
        }
    }
}

?>