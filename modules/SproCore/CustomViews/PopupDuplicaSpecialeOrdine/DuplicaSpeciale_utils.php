<?php

function DuplicaOrdineDiVendita($record, $data_ordine, $data_inizio_fatt_canone, $data_fine_fatt_canone){
    global $adb, $table_prefix, $current_user, $default_charset;

    $new_salesorder = 0;

    $q = "SELECT *
        FROM {$table_prefix}_salesorder so
        INNER JOIN {$table_prefix}_sobillads bill ON bill.sobilladdressid = so.salesorderid
        INNER JOIN {$table_prefix}_soshipads ship ON ship.soshipaddressid = so.salesorderid
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = so.salesorderid
        WHERE ent.deleted = 0 AND so.salesorderid = ".$record;
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){

        $array_colonne = array(
            'subject',
            'accountid',
            'kp_stabilimento',
            'potentialid',
            'quoteid',
            'contactid',
            'commessa',
            'kp_tipologia_ordine',
            'kp_business_unit',
            'kp_agente',
            'kp_rif_ordine_cli',
            'smownerid',
            'bill_country',
            'ship_country',
            'bill_pobox',
            'ship_pobox',
            'bill_state',
            'ship_state',
            'bill_city',
            'ship_city',
            'bill_street',
            'ship_street',
            'bill_code',
            'ship_code',
            'description',
            'kp_conto_corrente',
            'kp_banca_cliente',
            'mod_pagamento',
            'frequenza_fatturazione',
            'total',
            'adjustment',
            'subtotal',
            'discount_percent',
            'discount_amount',
            's_h_amount',
            'taxtype',
            'currency_id'
        );

        $salesorder = CRMEntity::getInstance('SalesOrder');
        $salesorder->column_fields['data_ordine'] = $data_ordine;
        $salesorder->column_fields['kp_data_inizio_fatt'] = $data_inizio_fatt_canone;
        if($data_fine_fatt_canone != ""){
            $salesorder->column_fields['kp_data_fine_fatt'] = $data_fine_fatt_canone;
        }
        $salesorder->column_fields['sostatus'] = 'Created';

        foreach($array_colonne as $nome_colonna){
            $nome_campo = GetFieldName($nome_colonna, 22);
            if($nome_campo != ""){
                $valore = $adb->query_result($res, 0, $nome_colonna);

                $salesorder->column_fields[$nome_campo] = $valore;

            }
        }

        $salesorder->save('SalesOrder', $longdesc=true, $offline_update=false, $triggerEvent=false);

        $new_salesorder = $salesorder->id;

        DuplicaRigheOrdineDiVendita("{$table_prefix}_inventoryproductrel", $record, $new_salesorder);

        DuplicaTotaliOrdineDiVendita("{$table_prefix}_inventorytotals", $record, $new_salesorder);

        $subtotal = $adb->query_result($res, 0, 'subtotal');
        $total = $adb->query_result($res, 0, 'total');
        $taxtype = $adb->query_result($res, 0, 'taxtype');

        $upd_so = "UPDATE {$table_prefix}_salesorder
                SET subtotal = ".$subtotal.", 
                total = ".$total.",
                taxtype = '".$taxtype."'
                WHERE salesorderid =".$new_salesorder;
        $adb->query($upd_so);
    }

    return $new_salesorder;
}

function DuplicaRigheOrdineDiVendita($tabella, $record, $new_salesorder){
    global $adb, $table_prefix, $current_user, $default_charset;

    $array_tabella = GetTableFieldMap($tabella);

    $q = "SELECT * 
        FROM {$tabella}
        WHERE id = ".$record;
    $res = $adb->query($q);
    $num = $adb->num_rows($res);
    if($num > 0){
        for($i = 0; $i < $num; $i++){
            $new_lineitem_id = GetTableSeq($tabella."_seq");
            $query_insert1 = "INSERT INTO {$tabella} (";
            $query_insert2 = " VALUES(";
            $cont = 0;
            foreach($array_tabella as $nome_colonna => $tipo){
                if($nome_colonna != 'lineitem_id'){
                    $valore = $adb->query_result($res, $i, $nome_colonna);
                }
                else{
                    $valore = $new_lineitem_id;
                }
                
                if($cont == 0){
                    $query_insert1 .= $nome_colonna;
                    $query_insert2 .= $new_salesorder;
                }
                else{
                    $query_insert1 .= ",".$nome_colonna;
                    if($valore != "" && $valore != null){
                        if (strpos($tipo, 'int') !== false || strpos($tipo, 'decimal') !== false) {
                            $query_insert2 .= ",'".$valore."'";
                        }
                        else{
                            $query_insert2 .= ",'".$valore."'";
                        }
                    }
                    else{
                        $query_insert2 .= ",NULL";
                    }
                }

                $cont++;
            }

            $query_insert = $query_insert1.")".$query_insert2.")";

            $adb->query($query_insert);

            UpdateTableSeq($tabella."_seq", $new_lineitem_id);
        }
    }
}

function DuplicaTotaliOrdineDiVendita($tabella, $record, $new_salesorder){
    global $adb, $table_prefix, $current_user, $default_charset;

    $array_tabella = GetTableFieldMap($tabella);

    $q = "SELECT * 
        FROM {$tabella}
        WHERE id = ".$record;
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $query_insert1 = "INSERT INTO {$tabella} (";
        $query_insert2 = " VALUES(";
        $cont = 0;
        foreach($array_tabella as $nome_colonna => $tipo){
            $valore = $adb->query_result($res, $i, $nome_colonna);
            
            if($cont == 0){
                $query_insert1 .= $nome_colonna;
                $query_insert2 .= $new_salesorder;
            }
            else{
                $query_insert1 .= ",".$nome_colonna;
                if($valore != "" && $valore != null){
                    if (strpos($tipo, 'int') !== false || strpos($tipo, 'decimal') !== false) {
                        $query_insert2 .= ",".$valore;
                    }
                    else{
                        $query_insert2 .= ",'".$valore."'";
                    }
                }
                else{
                    $query_insert2 .= ",NULL";
                }
            }

            $cont++;
        }

        $query_insert = $query_insert1.")".$query_insert2.")";

        $adb->query($query_insert);
    }
}

function DuplicaTicketDaOrdineDiVendita($record, $new_salesorder, $data_ordine, $data_consegna_ticket){
    global $adb, $table_prefix, $current_user, $default_charset;

    $risultato = 0;

    $q = "SELECT * 
        FROM {$table_prefix}_troubletickets tick
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
        WHERE ent.deleted = 0 AND tick.salesorder = ".$record;
    $res = $adb->query($q);
    $num = $adb->num_rows($res);
    if($num > 0){
        for($i = 0; $i < $num; $i++){
            $array_colonne = array(
                'title',
                'parent_id',
                'kp_stabilimento',
                'severity',
                'kp_tempo_previsto',
                'kp_fornitore',
                'smownerid',
                'kp_ripetitivo',
                'frequenza_fatturazione',
                'commessa',
                'da_fatturare',
                'servizio',
                'area_aziendale',
                'kp_business_unit',
                'kp_agente',
                'total_notaxes',
                'quantity',
                'listprice',
                'discount_percent',
                'discount_amount',
                'prezzo',
                'comment_line',
                'so_line_id',
                'description'
            );
    
            $ticket = CRMEntity::getInstance('HelpDesk');
            if($data_consegna_ticket != ""){
                $ticket->column_fields['kp_data_consegna'] = $data_consegna_ticket;
            }
            $ticket->column_fields['kp_data_elem_rif'] = $data_ordine;
            $ticket->column_fields['salesorder'] = $new_salesorder;
            $ticket->column_fields['ticketstatus'] = 'Open';
    
            foreach($array_colonne as $nome_colonna){
                $nome_campo = GetFieldName($nome_colonna, 13);
                if($nome_campo != ""){
                    $valore = $adb->query_result($res, $i, $nome_colonna); /* kpro@bid210520181600 */
    
                    $ticket->column_fields[$nome_campo] = $valore;

                }
            }
    
            $ticket->save('HelpDesk', $longdesc=true, $offline_update=false, $triggerEvent=false);

            $risultato++;
        }

    }
    
    return $risultato;
}

function DuplicaCanoniDaOrdineDiVendita($record, $new_salesorder, $data_ordine, $data_inizio_fatt_canone, $data_fine_fatt_canone){
    global $adb, $table_prefix, $current_user, $default_charset;

    $risultato = 0;

    $q = "SELECT * 
        FROM {$table_prefix}_canoni can
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = can.canoniid
        WHERE ent.deleted = 0 AND can.sales_order = ".$record;
    $res = $adb->query($q);
    $num = $adb->num_rows($res);
    if($num > 0){
        for($i = 0; $i < $num; $i++){
            $array_colonne = array(
                'canone_name',
                'account',
                'commessa',
                'servizio',
                'prezzo',
                'kp_agente',
                'kp_business_unit',
                'frequenza_fatturazione',
                'smownerid',
                'description',
                'salesorder_line_id'
            );

            list($anno_inizio_canone,$mese_inizio_canone,$giorno_inizio_canone) = explode("-",$data_inizio_fatt_canone);
            
            $mese_fatturazione = ltrim($mese_inizio_canone, '0');
            $anno_fatturazione = $anno_inizio_canone;

            $canoni = CRMEntity::getInstance('Canoni');
            if($data_fine_fatt_canone != ""){
                $canoni->column_fields['data_fine'] = $data_fine_fatt_canone;
            }
            $canoni->column_fields['data_inizio'] = $data_ordine;
            $canoni->column_fields['data_inizio_fatt'] = $data_inizio_fatt_canone;
            $canoni->column_fields['sales_order'] = $new_salesorder;
            $canoni->column_fields['stato_canone'] = 'Attivo';
            $canoni->column_fields['mese_fatturazione'] = $mese_fatturazione;
            $canoni->column_fields['kp_anno_fatt'] = $anno_fatturazione;
    
            foreach($array_colonne as $nome_colonna){
                $nome_campo = GetFieldName($nome_colonna, 89);
                if($nome_campo != ""){
                    $valore = $adb->query_result($res, $i, $nome_colonna); /* kpro@bid210520181600 */
    
                    $canoni->column_fields[$nome_campo] = $valore;

                }
            }
    
            $canoni->save('Canoni', $longdesc=true, $offline_update=false, $triggerEvent=false);

            $risultato++;
        }

    }
    
    return $risultato;
}

function GetTableSeq($tabella){
    global $adb, $table_prefix, $current_user, $default_charset;

    $q = "SELECT * 
        FROM {$tabella}";
    $res = $adb->query($q);

    $seq = $adb->query_result($res, 0, 'id');
    $seq++;

    return $seq;
}

function UpdateTableSeq($tabella, $new_lineitem_id){
    global $adb, $table_prefix, $current_user, $default_charset;

    $update = "UPDATE {$tabella}
        SET id = {$new_lineitem_id}";
    $adb->query($update);
}

function GetTableFieldMap($tabella){
    global $adb, $table_prefix, $current_user, $default_charset;

    $array_campi = array();

    $q = "DESCRIBE {$tabella}";
    $res = $adb->query($q);
    $num = $adb->num_rows($res);
    if($num > 0){
        for($i = 0; $i < $num; $i++){
            $fieldname = $adb->query_result($res, $i, 'field');
            $type = $adb->query_result($res, $i, 'type');

            $array_campi[$fieldname] = $type;
        }
    }

    return $array_campi;
}

function GetFieldName($nome_colonna, $modulo){
    global $adb, $table_prefix, $current_user, $default_charset;

    $q = "SELECT fieldname
        FROM {$table_prefix}_field
        WHERE tabid = {$modulo}
        AND columnname = '{$nome_colonna}'";
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $fieldname = $adb->query_result($res, 0, 'fieldname');
        $fieldname = html_entity_decode(strip_tags($fieldname), ENT_QUOTES,$default_charset);
        if($fieldname == null){
            $fieldname = "";
        }
    }
    else{
        $fieldname = "";
    }

    return $fieldname;
}
