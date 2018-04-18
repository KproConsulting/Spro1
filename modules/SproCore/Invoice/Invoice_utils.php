<?php

/* kpro@tom010416 */

function recuperaNumeroFattura($fattura){
    global $adb, $table_prefix,$current_user;
    
    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package fatturazioneConOdf
     * @version 1.0
     * 
     * Questo script recupera il numero di fattura
     */
    
    $q_fattura = "SELECT inv.invoice_number,
                inv.kp_business_unit,
                inv.kp_tipo_documento,
                acc.kp_fat_elettronica
                FROM {$table_prefix}_invoice inv
                INNER JOIN {$table_prefix}_account acc ON acc.accountid = inv.accountid
                WHERE inv.invoiceid = ".$fattura;
    $res_fattura = $adb->query($q_fattura);
    if($adb->num_rows($res_fattura)>0){
        $invoice_number = $adb->query_result($res_fattura, 0, 'invoice_number'); 
        $invoice_number = html_entity_decode(strip_tags($invoice_number), ENT_QUOTES,$default_charset);

        $tipo_documento = $adb->query_result($res_fattura, 0, 'kp_tipo_documento'); 
        $tipo_documento = html_entity_decode(strip_tags($tipo_documento), ENT_QUOTES,$default_charset);
        if($tipo_documento == null){
            $tipo_documento = '';
        }
        
        $business_unit = $adb->query_result($res_fattura, 0, 'kp_business_unit'); 
        $business_unit = html_entity_decode(strip_tags($business_unit), ENT_QUOTES,$default_charset);
        if($business_unit == '' || $business_unit == null){
            $business_unit = 0;
        }

        $fattura_elettronica = $adb->query_result($res_fattura, 0, 'kp_fat_elettronica'); 
        $fattura_elettronica = html_entity_decode(strip_tags($fattura_elettronica), ENT_QUOTES,$default_charset);
        if($fattura_elettronica == '1' || $fattura_elettronica == 1){
            $fattura_elettronica = '1';
        }
        else{
            $fattura_elettronica = '0';
        }
        
        if($invoice_number == "" || $invoice_number == null){

            if($tipo_documento == 'Fattura'){
                $id_modulo = '23';
            }
            else{
                $id_modulo = '23N';
            }
            
            $q_numeratore = "SELECT num.use_prefix, 
                            num.start_sequence, 
                            num.modulenumberingid
                            FROM {$table_prefix}_crmentityrel entrel
                            INNER JOIN {$table_prefix}_modulenumbering num ON num.modulenumberingid = entrel.crmid
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = num.modulenumberingid
                            WHERE ent.deleted = 0 AND num.select_module = '{$id_modulo}'
                            AND entrel.relcrmid = {$business_unit}
                            AND num.kp_fat_elettronica = '{$fattura_elettronica}'
                            UNION
                            SELECT num.use_prefix, 
                            num.start_sequence, 
                            num.modulenumberingid
                            FROM {$table_prefix}_crmentityrel entrel
                            INNER JOIN {$table_prefix}_modulenumbering num ON num.modulenumberingid = entrel.relcrmid
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = num.modulenumberingid
                            WHERE ent.deleted = 0 AND num.select_module = '{$id_modulo}'
                            AND entrel.crmid = {$business_unit}
                            AND num.kp_fat_elettronica = '{$fattura_elettronica}'";

            $res_numeratore = $adb->query($q_numeratore);
            if($adb->num_rows($res_numeratore)>0){
                $use_prefix = $adb->query_result($res_numeratore, 0, 'use_prefix'); 
                $use_prefix = html_entity_decode(strip_tags($use_prefix), ENT_QUOTES,$default_charset);

                $start_sequence = $adb->query_result($res_numeratore, 0, 'start_sequence'); 
                $start_sequence = html_entity_decode(strip_tags($start_sequence), ENT_QUOTES,$default_charset);
                
                $modulenumberingid = $adb->query_result($res_numeratore, 0, 'modulenumberingid'); 
                $modulenumberingid = html_entity_decode(strip_tags($modulenumberingid), ENT_QUOTES,$default_charset);
                
                $invoice_number = $use_prefix.$start_sequence;
							
                $upd_invoice = "UPDATE {$table_prefix}_invoice
                                SET invoice_number ='".$invoice_number."'
                                WHERE invoiceid =".$fattura;
                $adb->query($upd_invoice);
				
                $length_sequence = strlen($start_sequence);			
                $start_sequence = (int)$start_sequence;

                $start_sequence++;
                $start_sequence = str_pad($start_sequence, $length_sequence, "0", STR_PAD_LEFT);
                
                $upd_numeratore = "UPDATE {$table_prefix}_modulenumbering
                                    SET start_sequence ='".$start_sequence."'
                                    WHERE modulenumberingid =".$modulenumberingid;
                $adb->query($upd_numeratore);
                                
            }
            
        }
        
    }
 
}
    
function generaScadenzeFattura($fattura){
    global $adb, $table_prefix,$current_user;
    
    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package fatturazioneConOdf
     * @version 1.0
     * 
     * Questo script genera le scadenze della fattura
     */
	
    require_once('modules/SproCore/Scadenziario/Scadenziario_utils.php'); /* kpro@bid180420181220 */
    
    $q_dati_fattura = "SELECT 
                        inv.mod_pagamento mod_pagamento,
                        inv.invoicedate invoicedate,
                        inv.total total,
                        inv.accountid accountid,
                        inv.banca_pagamento banca_pagamento_pag,
                        inv.invoice_number invoice_number,
                        inv.commessa commessa,
                        inv.kp_business_unit kp_business_unit,
                        inv.invoicestatus invoicestatus,
                        inv.kp_banca_cliente kp_banca_cliente,
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
                        FROM {$table_prefix}_invoice inv
                        INNER JOIN {$table_prefix}_modpagamento modp ON modp.modpagamentoid = inv.mod_pagamento
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = inv.invoiceid
                        WHERE inv.invoiceid = ".$fattura;
    $res_dati_fattura = $adb->query($q_dati_fattura);
    
    if($adb->num_rows($res_dati_fattura)>0){
         
        $numero_scadenze = 0;
        
        $mod_pagamento = $adb->query_result($res_dati_fattura, 0, 'mod_pagamento'); 
        $mod_pagamento = html_entity_decode(strip_tags($mod_pagamento), ENT_QUOTES,$default_charset);
        
        $invoicedate = $adb->query_result($res_dati_fattura, 0, 'invoicedate'); 
        $invoicedate = html_entity_decode(strip_tags($invoicedate), ENT_QUOTES,$default_charset);
        
        $accountid = $adb->query_result($res_dati_fattura, 0, 'accountid'); 
        $accountid = html_entity_decode(strip_tags($accountid), ENT_QUOTES,$default_charset);
        
        $banca_pagamento_pag = $adb->query_result($res_dati_fattura, 0, 'banca_pagamento_pag'); 
        $banca_pagamento_pag = html_entity_decode(strip_tags($banca_pagamento_pag), ENT_QUOTES,$default_charset);
        
        $invoice_number = $adb->query_result($res_dati_fattura, 0, 'invoice_number'); 
        $invoice_number = html_entity_decode(strip_tags($invoice_number), ENT_QUOTES,$default_charset);
        
        $total = $adb->query_result($res_dati_fattura, 0, 'total'); 
        $total = html_entity_decode(strip_tags($total), ENT_QUOTES,$default_charset);
        
        $commessa = $adb->query_result($res_dati_fattura, 0, 'commessa'); 
        $commessa = html_entity_decode(strip_tags($commessa), ENT_QUOTES,$default_charset);
        if($commessa == null || $commessa == ""){
            $commessa = 0;
        }
        
        $business_unit = $adb->query_result($res_dati_fattura, 0, 'kp_business_unit'); 
        $business_unit = html_entity_decode(strip_tags($business_unit), ENT_QUOTES,$default_charset);
        if($business_unit == '' || $business_unit == null){
            $business_unit = 0;
        }

        $banca_cliente = $adb->query_result($res_dati_fattura, 0, 'kp_banca_cliente'); 
        $banca_cliente = html_entity_decode(strip_tags($banca_cliente), ENT_QUOTES,$default_charset);
        
        $invoicestatus = $adb->query_result($res_dati_fattura, 0, 'invoicestatus'); 
        $invoicestatus = html_entity_decode(strip_tags($invoicestatus), ENT_QUOTES,$default_charset);
        
        $nome_mod_pag = $adb->query_result($res_dati_fattura, 0, 'nome_mod_pag'); 
        $nome_mod_pag = html_entity_decode(strip_tags($nome_mod_pag), ENT_QUOTES,$default_charset);
        
        $per_pag_1 = $adb->query_result($res_dati_fattura, 0, 'per_pag_1'); 
        $per_pag_1 = html_entity_decode(strip_tags($per_pag_1), ENT_QUOTES,$default_charset);
        $array_per_pag[1] = $per_pag_1;
        if($per_pag_1 != "" && $per_pag_1 != null && $per_pag_1 != 0){
            $numero_scadenze = 1;
        }
        
        $per_pag_2 = $adb->query_result($res_dati_fattura, 0, 'per_pag_2'); 
        $per_pag_2 = html_entity_decode(strip_tags($per_pag_2), ENT_QUOTES,$default_charset);
        $array_per_pag[2] = $per_pag_2;
        if($per_pag_2 != "" && $per_pag_2 != null && $per_pag_2 != 0){
            $numero_scadenze = 2;
        }
        
        $per_pag_3 = $adb->query_result($res_dati_fattura, 0, 'per_pag_3'); 
        $per_pag_3 = html_entity_decode(strip_tags($per_pag_3), ENT_QUOTES,$default_charset);
        $array_per_pag[3] = $per_pag_3;
        if($per_pag_3 != "" && $per_pag_3 != null && $per_pag_3 != 0){
            $numero_scadenze = 3;
        }
        
        $per_pag_4 = $adb->query_result($res_dati_fattura, 0, 'per_pag_4'); 
        $per_pag_4 = html_entity_decode(strip_tags($per_pag_4), ENT_QUOTES,$default_charset);
        $array_per_pag[4] = $per_pag_4;
        if($per_pag_4 != "" && $per_pag_4 != null && $per_pag_4 != 0){
            $numero_scadenze = 4;
        }
        
        $per_pag_5 = $adb->query_result($res_dati_fattura, 0, 'per_pag_5'); 
        $per_pag_5 = html_entity_decode(strip_tags($per_pag_5), ENT_QUOTES,$default_charset);
        $array_per_pag[5] = $per_pag_5;
        if($per_pag_5 != "" && $per_pag_5 != null && $per_pag_5 != 0){
            $numero_scadenze = 5;
        }
        
        $scad_pag_1 = $adb->query_result($res_dati_fattura, 0, 'scad_pag_1'); 
        $scad_pag_1 = html_entity_decode(strip_tags($scad_pag_1), ENT_QUOTES,$default_charset);
        $array_scad_pag[1] = $scad_pag_1;
        
        $scad_pag_2 = $adb->query_result($res_dati_fattura, 0, 'scad_pag_2'); 
        $scad_pag_2 = html_entity_decode(strip_tags($scad_pag_2), ENT_QUOTES,$default_charset);
        $array_scad_pag[2] = $scad_pag_2;
        
        $scad_pag_3 = $adb->query_result($res_dati_fattura, 0, 'scad_pag_3'); 
        $scad_pag_3 = html_entity_decode(strip_tags($scad_pag_3), ENT_QUOTES,$default_charset);
        $array_scad_pag[3] = $scad_pag_3;
        
        $scad_pag_4 = $adb->query_result($res_dati_fattura, 0, 'scad_pag_4'); 
        $scad_pag_4 = html_entity_decode(strip_tags($scad_pag_4), ENT_QUOTES,$default_charset);
        $array_scad_pag[4] = $scad_pag_4;
        
        $scad_pag_5 = $adb->query_result($res_dati_fattura, 0, 'scad_pag_5'); 
        $scad_pag_5 = html_entity_decode(strip_tags($scad_pag_5), ENT_QUOTES,$default_charset);
        $array_scad_pag[5] = $scad_pag_5;
        
        $fine_mese = $adb->query_result($res_dati_fattura, 0, 'fine_mese'); 
        $fine_mese = html_entity_decode(strip_tags($fine_mese), ENT_QUOTES,$default_charset);
        if($fine_mese == "Data fattura"){
            $fine_mese = false;
        }
        else{
            $fine_mese = true;
        }

        $condizioni_pagamento = $adb->query_result($res_dati_fattura, 0, 'condizioni_pagamento'); 
        $condizioni_pagamento = html_entity_decode(strip_tags($condizioni_pagamento), ENT_QUOTES,$default_charset);
        
        $assegnatario = $adb->query_result($res_dati_fattura, 0, 'assegnatario'); 
        $assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES,$default_charset);
        
        $upd_scadenze = "UPDATE {$table_prefix}_scadenziario
                            SET aggiornato ='0'
                            WHERE invoice =".$fattura;
        $adb->query($upd_scadenze);
        
        for($i=1; $i<=$numero_scadenze; $i++){
            
            $nro_scadenza = $i;
            $importo_scadenza = ($total * $array_per_pag[$i])/100;
            $data_scadenza = calcolaDataScadenza($invoicedate,$array_scad_pag[$i],$fine_mese);
            
            $q_scadenza = "SELECT scad.scadenziarioid scadenziarioid,
                            scad.stato_scadenza_pag stato_scadenza_pag
                            FROM {$table_prefix}_scadenziario scad
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = scad.scadenziarioid
                            WHERE ent.deleted = 0 AND scad.invoice = ".$fattura." AND nro_scadenza = ".$nro_scadenza."
                            ORDER BY scad.scadenziarioid ASC";
            $res_scadenza = $adb->query($q_scadenza);
            if($adb->num_rows($res_scadenza)==0){
                
                $scadenziario = CRMEntity::getInstance('Scadenziario');
                $scadenziario->column_fields['assigned_user_id'] = $assegnatario;
                $scadenziario->column_fields['invoice'] = $fattura;
                $scadenziario->column_fields['azienda'] = $accountid;
                $scadenziario->column_fields['data_scadenza'] = $data_scadenza;
                $scadenziario->column_fields['tipo_scadenza_pag'] = 'Pagamento cliente';
                $scadenziario->column_fields['import'] = $importo_scadenza;
                if($invoicestatus == 'Paid' || $invoicestatus == 'Pagata Proforma'){
                    $scadenziario->column_fields['stato_scadenza_pag'] = 'Pagata';
                }
                else{
                    $scadenziario->column_fields['stato_scadenza_pag'] = 'Aperta';
                }
                $scadenziario->column_fields['nro_scadenza'] = $i;
                $scadenziario->column_fields['totale_scadenze'] = $numero_scadenze;
                $scadenziario->column_fields['banca_pagamento'] = $banca_pagamento_pag;
                $scadenziario->column_fields['mod_pagamento'] = $mod_pagamento;
                $scadenziario->column_fields['condizioni_pagamento'] = $condizioni_pagamento;
                if($commessa != 0){
                    $scadenziario->column_fields['commessa'] = $commessa;
                }
                $scadenziario->column_fields['kp_business_unit'] = $business_unit;
                $scadenziario->column_fields['kp_banca_cliente'] = $banca_cliente;
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
                                        banca_pagamento = '".$banca_pagamento_pag."',
                                        mod_pagamento = ".$mod_pagamento.",
                                        commessa = ".$commessa.",
                                        kp_business_unit = ".$business_unit.",
                                        kp_banca_cliente = '".$banca_cliente."',
                                        condizioni_pagamento = '".$condizioni_pagamento."',
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
                                    WHERE ent.deleted = 0 AND scad.aggiornato ='0' AND scad.invoice =".$fattura;
        $adb->query($upd_scadenze_vecchie);
                                
    }
 
}
		
?>
