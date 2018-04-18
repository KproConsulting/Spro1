<?php

/* kpro@tom010416 */
    
function generaScadenzePurchaseOrder($purchase_order){
    global $adb, $table_prefix,$current_user;
    
    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package fatturazioneConOdf
     * @version 1.0
     * 
     * Questo script genera le scadenze del Purchase Order
     */
    
    require_once('modules/SproCore/Scadenziario/Scadenziario_utils.php'); /* kpro@bid180420181220 */

    $q_dati_ordine = "SELECT 
                        po.mod_pagamento mod_pagamento,
                        po.kp_data_oda podate,
                        po.total total,
                        po.vendorid vendorid,
                        po.purchaseorder_no purchaseorder_no,
                        po.commessa commessa,
                        po.kp_business_unit kp_business_unit,
                        po.banca_pagamento banca_pagamento,
                        po.kp_data_fatt_fornit data_fattura_fornitore,
                        modp.nome_mod_pag nome_mod_pag,
                        modp.per_pag_1 per_pag_1,
                        modp.per_pag_2 per_pag_2,
                        modp.per_pag_3 per_pag_3,
                        modp.per_pag_4 per_pag_4,
                        modp.per_pag_5 per_pag_5,
                        modp.scad_pag_1 scad_pag_1,
                        modp.scad_pag_2 scad_pag_2,
                        modp.scad_pag_3 scad_pag_3,
                        modp.scad_pag_4 scad_pag_4,
                        modp.scad_pag_5 scad_pag_5,
                        modp.fine_mese fine_mese,
                        modp.condizioni_pagamento condizioni_pagamento,
                        ent.smownerid assegnatario
                        FROM {$table_prefix}_purchaseorder po
                        INNER JOIN {$table_prefix}_modpagamento modp ON modp.modpagamentoid = po.mod_pagamento
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = po.purchaseorderid
                        WHERE po.purchaseorderid = ".$purchase_order;
    $res_dati_ordine = $adb->query($q_dati_ordine);
    
    if($adb->num_rows($res_dati_ordine)>0){
         
        $numero_scadenze = 0;
        
        $mod_pagamento = $adb->query_result($res_dati_ordine, 0, 'mod_pagamento'); 
        $mod_pagamento = html_entity_decode(strip_tags($mod_pagamento), ENT_QUOTES,$default_charset);
        
        $podate = $adb->query_result($res_dati_ordine, 0, 'podate'); 
        $podate = html_entity_decode(strip_tags($podate), ENT_QUOTES,$default_charset);
        
        $commessa = $adb->query_result($res_dati_ordine, 0, 'commessa'); 
        $commessa = html_entity_decode(strip_tags($commessa), ENT_QUOTES,$default_charset);
        if($commessa == null || $commessa == ""){
            $commessa = 0;
        }
        
        $businessunit = $adb->query_result($res_dati_ordine,0,'kp_business_unit');
        $businessunit = html_entity_decode(strip_tags($businessunit), ENT_QUOTES,$default_charset);
        if($businessunit == null || $businessunit == ""){
            $businessunit = 0;
        }

        $data_fattura_fornitore = $adb->query_result($res_dati_ordine, 0, 'data_fattura_fornitore'); 
        $data_fattura_fornitore = html_entity_decode(strip_tags($data_fattura_fornitore), ENT_QUOTES,$default_charset);
        if($data_fattura_fornitore == null || $data_fattura_fornitore == "0000-00-00"){
            $data_fattura_fornitore = "";
        }

        $vendorid = $adb->query_result($res_dati_ordine, 0, 'vendorid'); 
        $vendorid = html_entity_decode(strip_tags($vendorid), ENT_QUOTES,$default_charset);
        
        $banca_pagamento = $adb->query_result($res_dati_ordine, 0, 'banca_pagamento'); 
        $banca_pagamento = html_entity_decode(strip_tags($banca_pagamento), ENT_QUOTES,$default_charset);

        $purchaseorder_no = $adb->query_result($res_dati_ordine, 0, 'purchaseorder_no'); 
        $purchaseorder_no = html_entity_decode(strip_tags($purchaseorder_no), ENT_QUOTES,$default_charset);
        
        $total = $adb->query_result($res_dati_ordine, 0, 'total'); 
        $total = html_entity_decode(strip_tags($total), ENT_QUOTES,$default_charset);
        
        $nome_mod_pag = $adb->query_result($res_dati_ordine, 0, 'nome_mod_pag'); 
        $nome_mod_pag = html_entity_decode(strip_tags($nome_mod_pag), ENT_QUOTES,$default_charset);
        
        $per_pag_1 = $adb->query_result($res_dati_ordine, 0, 'per_pag_1'); 
        $per_pag_1 = html_entity_decode(strip_tags($per_pag_1), ENT_QUOTES,$default_charset);
        $array_per_pag[1] = $per_pag_1;
        if($per_pag_1 != "" && $per_pag_1 != null && $per_pag_1 != 0){
            $numero_scadenze = 1;
        }
        
        $per_pag_2 = $adb->query_result($res_dati_ordine, 0, 'per_pag_2'); 
        $per_pag_2 = html_entity_decode(strip_tags($per_pag_2), ENT_QUOTES,$default_charset);
        $array_per_pag[2] = $per_pag_2;
        if($per_pag_2 != "" && $per_pag_2 != null && $per_pag_2 != 0){
            $numero_scadenze = 2;
        }
        
        $per_pag_3 = $adb->query_result($res_dati_ordine, 0, 'per_pag_3'); 
        $per_pag_3 = html_entity_decode(strip_tags($per_pag_3), ENT_QUOTES,$default_charset);
        $array_per_pag[3] = $per_pag_3;
        if($per_pag_3 != "" && $per_pag_3 != null && $per_pag_3 != 0){
            $numero_scadenze = 3;
        }
        
        $per_pag_4 = $adb->query_result($res_dati_ordine, 0, 'per_pag_4'); 
        $per_pag_4 = html_entity_decode(strip_tags($per_pag_4), ENT_QUOTES,$default_charset);
        $array_per_pag[4] = $per_pag_4;
        if($per_pag_4 != "" && $per_pag_4 != null && $per_pag_4 != 0){
            $numero_scadenze = 4;
        }
        
        $per_pag_5 = $adb->query_result($res_dati_ordine, 0, 'per_pag_5'); 
        $per_pag_5 = html_entity_decode(strip_tags($per_pag_5), ENT_QUOTES,$default_charset);
        $array_per_pag[5] = $per_pag_5;
        if($per_pag_5 != "" && $per_pag_5 != null && $per_pag_5 != 0){
            $numero_scadenze = 5;
        }
        
        $scad_pag_1 = $adb->query_result($res_dati_ordine, 0, 'scad_pag_1'); 
        $scad_pag_1 = html_entity_decode(strip_tags($scad_pag_1), ENT_QUOTES,$default_charset);
        $array_scad_pag[1] = $scad_pag_1;
        
        $scad_pag_2 = $adb->query_result($res_dati_ordine, 0, 'scad_pag_2'); 
        $scad_pag_2 = html_entity_decode(strip_tags($scad_pag_2), ENT_QUOTES,$default_charset);
        $array_scad_pag[2] = $scad_pag_2;
        
        $scad_pag_3 = $adb->query_result($res_dati_ordine, 0, 'scad_pag_3'); 
        $scad_pag_3 = html_entity_decode(strip_tags($scad_pag_3), ENT_QUOTES,$default_charset);
        $array_scad_pag[3] = $scad_pag_3;
        
        $scad_pag_4 = $adb->query_result($res_dati_ordine, 0, 'scad_pag_4'); 
        $scad_pag_4 = html_entity_decode(strip_tags($scad_pag_4), ENT_QUOTES,$default_charset);
        $array_scad_pag[4] = $scad_pag_4;
        
        $scad_pag_5 = $adb->query_result($res_dati_ordine, 0, 'scad_pag_5'); 
        $scad_pag_5 = html_entity_decode(strip_tags($scad_pag_5), ENT_QUOTES,$default_charset);
        $array_scad_pag[5] = $scad_pag_5;
        
        $fine_mese = $adb->query_result($res_dati_ordine, 0, 'fine_mese'); 
        $fine_mese = html_entity_decode(strip_tags($fine_mese), ENT_QUOTES,$default_charset);
        if($fine_mese == "Data fattura"){
            $fine_mese = false;
        }
        else{
            $fine_mese = true;
        }

        $condizioni_pagamento = $adb->query_result($res_dati_ordine, 0, 'condizioni_pagamento'); 
        $condizioni_pagamento = html_entity_decode(strip_tags($condizioni_pagamento), ENT_QUOTES,$default_charset);
        
        $assegnatario = $adb->query_result($res_dati_ordine, 0, 'assegnatario'); 
        $assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES,$default_charset);
        
        $upd_scadenze = "UPDATE {$table_prefix}_scadenziario
                            SET aggiornato ='0'
                            WHERE purchaseorder =".$purchase_order;
        $adb->query($upd_scadenze);
        
        for($i=1; $i<=$numero_scadenze; $i++){
            
            $nro_scadenza = $i;
            $importo_scadenza = ($total * $array_per_pag[$i])/100;
            if($data_fattura_fornitore == ""){
                $data_scadenza = calcolaDataScadenza($podate, $array_scad_pag[$i], $fine_mese);
            }
            else{
                $data_scadenza = calcolaDataScadenza($data_fattura_fornitore, $array_scad_pag[$i], $fine_mese);
            }
            
            $q_scadenza = "SELECT scad.scadenziarioid scadenziarioid,
                            scad.stato_scadenza_pag stato_scadenza_pag
                            FROM {$table_prefix}_scadenziario scad
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = scad.scadenziarioid
                            WHERE ent.deleted = 0 AND scad.purchaseorder = ".$purchase_order." AND nro_scadenza = ".$nro_scadenza."
                            ORDER BY scad.scadenziarioid ASC";
            $res_scadenza = $adb->query($q_scadenza);
            if($adb->num_rows($res_scadenza)==0){
                
                $scadenziario = CRMEntity::getInstance('Scadenziario');
                $scadenziario->column_fields['assigned_user_id'] = $assegnatario;
                $scadenziario->column_fields['purchaseorder'] = $purchase_order;
                $scadenziario->column_fields['fornitore'] = $vendorid;
                $scadenziario->column_fields['data_scadenza'] = $data_scadenza;
                $scadenziario->column_fields['tipo_scadenza_pag'] = 'Pagamento fornitore';
                $scadenziario->column_fields['import'] = $importo_scadenza;
                $scadenziario->column_fields['stato_scadenza_pag'] = 'Prevista';
                $scadenziario->column_fields['nro_scadenza'] = $i;
                $scadenziario->column_fields['totale_scadenze'] = $numero_scadenze;
                $scadenziario->column_fields['commessa'] = $commessa;
                $scadenziario->column_fields['mod_pagamento'] = $mod_pagamento;
                $scadenziario->column_fields['condizioni_pagamento'] = $condizioni_pagamento;
                $scadenziario->column_fields['banca_pagamento'] = $banca_pagamento;
                $scadenziario->column_fields['kp_business_unit'] = $businessunit;
                $scadenziario->column_fields['aggiornato'] = '1';
                $scadenziario->save('Scadenziario', $longdesc=true, $offline_update=false, $triggerEvent=false);
                
            }
            else{
                
                $scadenziarioid = $adb->query_result($res_scadenza, 0, 'scadenziarioid');
                $scadenziarioid = html_entity_decode(strip_tags($scadenziarioid), ENT_QUOTES,$default_charset);
                
                $stato_scadenza_pag = $adb->query_result($res_scadenza, 0, 'stato_scadenza_pag');
                $stato_scadenza_pag = html_entity_decode(strip_tags($stato_scadenza_pag), ENT_QUOTES,$default_charset);
                
                if($stato_scadenza_pag != "Pagata"){
                    $upd_scadenza = "UPDATE {$table_prefix}_scadenziario SET
                                        data_scadenza = '".$data_scadenza."',
                                        import = ".$importo_scadenza.",
                                        totale_scadenze = ".$numero_scadenze.",
                                        mod_pagamento = ".$mod_pagamento.",
                                        condizioni_pagamento = '".$condizioni_pagamento."',
                                        banca_pagamento = '".$banca_pagamento."',
                                        kp_business_unit = ".$businessunit.",
                                        aggiornato = '1'
                                        WHERE scadenziarioid = ".$scadenziarioid;
                    $adb->query($upd_scadenza);
                }
                else{
                    $upd_scadenza = "UPDATE {$table_prefix}_scadenziario SET
                                        aggiornato = '1'
                                        WHERE scadenziarioid = ".$scadenziarioid;
                    $adb->query($upd_scadenza);
                }
                
            }
            
        }
        
        $upd_scadenze_vecchie = "UPDATE {$table_prefix}_crmentity ent
                                    INNER JOIN {$table_prefix}_scadenziario scad ON scad.scadenziarioid = ent.crmid
                                    SET ent.deleted = 1
                                    WHERE ent.deleted = 0 AND scad.aggiornato ='0' AND scad.purchaseorder =".$purchase_order;
        $adb->query($upd_scadenze_vecchie);
                                
    }
 
}
		
?>
