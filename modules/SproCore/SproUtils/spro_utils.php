<?php

function recuperaTipiCorsoDallaMansione($risorsa_mansione_id){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom2412015 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2015, Kpro Consulting Srl
     * @package vteSicurezza
     * @version 1.0
     * 
     * Questa funzione recupera dalla mansione relazionata alla risorsa i relativi tipi corso
     */
    
    $delete = "DELETE FROM {$table_prefix}_crmentityrel
                WHERE crmid = ".$risorsa_mansione_id." AND module = 'MansioniRisorsa' AND relmodule = 'TipiCorso'";
    $adb->query($delete);
    
    $delete = "DELETE FROM {$table_prefix}_crmentityrel
                WHERE relcrmid = ".$risorsa_mansione_id." AND module = 'TipiCorso' AND relmodule = 'MansioniRisorsa'";
    $adb->query($delete);
    
    $record_scritti = 0;
    
    $q_mansione = "SELECT mansione FROM {$table_prefix}_mansionirisorsa 
                    WHERE mansionirisorsaid = ".$risorsa_mansione_id;
    $res_mansione = $adb->query($q_mansione);
    if($adb->num_rows($res_mansione)>0){
        
        $mansione_id = $adb->query_result($res_mansione,0,'mansione');
        $mansione_id = html_entity_decode(strip_tags($mansione_id), ENT_QUOTES,$default_charset);
                
        $q_tipi_corso = "(SELECT rel1.relcrmid tipo_corso FROM {$table_prefix}_crmentityrel rel1
                            INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = rel1.relcrmid
                            WHERE ent1.deleted = 0 AND rel1.crmid = ".$mansione_id." AND rel1.relmodule = 'TipiCorso')
                            UNION
                            (SELECT rel2.crmid tipo_corso FROM {$table_prefix}_crmentityrel rel2
                            INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = rel2.crmid
                            WHERE ent2.deleted = 0 AND rel2.relcrmid = ".$mansione_id." AND rel2.module = 'TipiCorso')";
        
        $res_tipi_corso = $adb->query($q_tipi_corso);
        $num_tipi_corso = $adb->num_rows($res_tipi_corso);
        for($i=0; $i<$num_tipi_corso; $i++){	

            $tipo_corso = $adb->query_result($res_tipi_corso,$i,'tipo_corso');
            $tipo_corso = html_entity_decode(strip_tags($tipo_corso), ENT_QUOTES,$default_charset);
            
            $q_ver_esistenza = "(SELECT * FROM {$table_prefix}_crmentityrel rel1
                                    INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = rel1.relcrmid
                                    WHERE ent1.deleted = 0 AND rel1.crmid = ".$risorsa_mansione_id." AND rel1.relcrmid = ".$tipo_corso.")
                                    UNION
                                    (SELECT * FROM {$table_prefix}_crmentityrel rel2
                                    INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = rel2.crmid
                                    WHERE ent2.deleted = 0 AND rel2.relcrmid = ".$risorsa_mansione_id." AND rel2.crmid = ".$tipo_corso.")";
            $res_ver_esistenza = $adb->query($q_ver_esistenza);
            if($adb->num_rows($res_ver_esistenza)==0){
                
                $insert_relazione = "INSERT INTO {$table_prefix}_crmentityrel
                                        (crmid, module, relcrmid, relmodule)
                                        VALUES (".$risorsa_mansione_id.", 'MansioniRisorsa', ".$tipo_corso.", 'TipiCorso')";
                $adb->query($insert_relazione);
                
                $record_scritti++;
                
            }
            
        }    
        
    }
    
    return $record_scritti;
    
}

function recuperaTipiVisiteMedicheDallaMansione($risorsa_mansione_id){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom2412015 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2015, Kpro Consulting Srl
     * @package vteSicurezza
     * @version 1.0
     * 
     * Questa funzione recupera dalla mansione relazionata alla risorsa i relativi tipi di visite mediche
     */
    
    $delete = "DELETE FROM {$table_prefix}_crmentityrel
                WHERE crmid = ".$risorsa_mansione_id." AND module = 'MansioniRisorsa' AND relmodule = 'TipiVisitaMed'";
    $adb->query($delete);
    
    $delete = "DELETE FROM {$table_prefix}_crmentityrel
                WHERE relcrmid = ".$risorsa_mansione_id." AND module = 'TipiVisitaMed' AND relmodule = 'MansioniRisorsa'";
    $adb->query($delete);
    
    $record_scritti = 0;
    
    $q_mansione = "SELECT mansione FROM {$table_prefix}_mansionirisorsa 
                    WHERE mansionirisorsaid = ".$risorsa_mansione_id;
    $res_mansione = $adb->query($q_mansione);
    if($adb->num_rows($res_mansione)>0){
        
        $mansione_id = $adb->query_result($res_mansione,0,'mansione');
        $mansione_id = html_entity_decode(strip_tags($mansione_id), ENT_QUOTES,$default_charset);
                
        $q_tipi_visite = "(SELECT rel1.relcrmid tipo_visita FROM {$table_prefix}_crmentityrel rel1
                            INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = rel1.relcrmid
                            WHERE ent1.deleted = 0 AND rel1.crmid = ".$mansione_id." AND rel1.relmodule = 'TipiVisitaMed')
                            UNION
                            (SELECT rel2.crmid tipo_visita FROM {$table_prefix}_crmentityrel rel2
                            INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = rel2.crmid
                            WHERE ent2.deleted = 0 AND rel2.relcrmid = ".$mansione_id." AND rel2.module = 'TipiVisitaMed')";
        
        $res_tipi_visite = $adb->query($q_tipi_visite);
        $num_tipi_visite = $adb->num_rows($res_tipi_visite);
        for($i=0; $i<$num_tipi_visite; $i++){	

            $tipo_visita = $adb->query_result($res_tipi_visite,$i,'tipo_visita');
            $tipo_visita = html_entity_decode(strip_tags($tipo_visita), ENT_QUOTES,$default_charset);
            
            $q_ver_esistenza = "(SELECT * FROM {$table_prefix}_crmentityrel rel1
                                    INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = rel1.relcrmid
                                    WHERE ent1.deleted = 0 AND rel1.crmid = ".$risorsa_mansione_id." AND rel1.relcrmid = ".$tipo_visita.")
                                    UNION
                                    (SELECT * FROM {$table_prefix}_crmentityrel rel2
                                    INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = rel2.crmid
                                    WHERE ent2.deleted = 0 AND rel2.relcrmid = ".$risorsa_mansione_id." AND rel2.crmid = ".$tipo_visita.")";
            $res_ver_esistenza = $adb->query($q_ver_esistenza);
            if($adb->num_rows($res_ver_esistenza)==0){
                
                $insert_relazione = "INSERT INTO {$table_prefix}_crmentityrel
                                        (crmid, module, relcrmid, relmodule)
                                        VALUES (".$risorsa_mansione_id.", 'MansioniRisorsa', ".$tipo_visita.", 'TipiVisitaMed')";
                $adb->query($insert_relazione);
                
                $record_scritti++;
                
            }
            
        }    
        
    }
    
    return $record_scritti;
    
}

function recuperaCategoriePrivacyDallaMansione($risorsa_mansione_id){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom01092017 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     * 
     * Questa funzione recupera dalla mansione relazionata alla risorsa le relative categorie Privacy
     */
    
    $delete = "DELETE FROM {$table_prefix}_crmentityrel
                WHERE crmid = ".$risorsa_mansione_id." AND module = 'MansioniRisorsa' AND relmodule = 'KpCategoriePrivacy'";
    $adb->query($delete);
    
    $delete = "DELETE FROM {$table_prefix}_crmentityrel
                WHERE relcrmid = ".$risorsa_mansione_id." AND module = 'KpCategoriePrivacy' AND relmodule = 'MansioniRisorsa'";
    $adb->query($delete);
    
    $record_scritti = 0;
    
    $q_mansione = "SELECT mansione FROM {$table_prefix}_mansionirisorsa 
                    WHERE mansionirisorsaid = ".$risorsa_mansione_id;
    $res_mansione = $adb->query($q_mansione);
    if($adb->num_rows($res_mansione)>0){
        
        $mansione_id = $adb->query_result($res_mansione, 0, 'mansione');
        $mansione_id = html_entity_decode(strip_tags($mansione_id), ENT_QUOTES,$default_charset);
                
        $q_tipi_visite = "(SELECT rel1.relcrmid categoria_privacy FROM {$table_prefix}_crmentityrel rel1
                            INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = rel1.relcrmid
                            WHERE ent1.deleted = 0 AND rel1.crmid = ".$mansione_id." AND rel1.relmodule = 'KpCategoriePrivacy')
                            UNION
                            (SELECT rel2.crmid categoria_privacy FROM {$table_prefix}_crmentityrel rel2
                            INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = rel2.crmid
                            WHERE ent2.deleted = 0 AND rel2.relcrmid = ".$mansione_id." AND rel2.module = 'KpCategoriePrivacy')";
        
        $res_tipi_visite = $adb->query($q_tipi_visite);
        $num_tipi_visite = $adb->num_rows($res_tipi_visite);
        for($i=0; $i<$num_tipi_visite; $i++){	

            $categoria_privacy = $adb->query_result($res_tipi_visite,$i,'categoria_privacy');
            $categoria_privacy = html_entity_decode(strip_tags($categoria_privacy), ENT_QUOTES,$default_charset);
            
            $q_ver_esistenza = "(SELECT * FROM {$table_prefix}_crmentityrel rel1
                                    INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = rel1.relcrmid
                                    WHERE ent1.deleted = 0 AND rel1.crmid = ".$risorsa_mansione_id." AND rel1.relcrmid = ".$categoria_privacy.")
                                    UNION
                                    (SELECT * FROM {$table_prefix}_crmentityrel rel2
                                    INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = rel2.crmid
                                    WHERE ent2.deleted = 0 AND rel2.relcrmid = ".$risorsa_mansione_id." AND rel2.crmid = ".$categoria_privacy.")";
            $res_ver_esistenza = $adb->query($q_ver_esistenza);
            if($adb->num_rows($res_ver_esistenza)==0){
                
                $insert_relazione = "INSERT INTO {$table_prefix}_crmentityrel
                                        (crmid, module, relcrmid, relmodule)
                                        VALUES (".$risorsa_mansione_id.", 'MansioniRisorsa', ".$categoria_privacy.", 'KpCategoriePrivacy')";
                $adb->query($insert_relazione);
                
                $record_scritti++;
                
            }
            
        }    
        
    }
    
    return $record_scritti;
    
}

function calcolaSituazioneFormazione(){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom010220170902 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */
	 
	printf("<br />Calcolo situazione formazione iniziato!");

	aggiornaStatoMansioniRisorseNonAttive();
	 
	$default_in_scadenza = 0;
	 
	$lista_aziende = getAziendePerSituazioneFormazione();
	
	foreach($lista_aziende as $azienda){
		
		printf("<br />--- Azienda: ".$azienda['accountid']);
		
		$giorni_in_scadenza = getGiorniInScadenzaAzienda($azienda['accountid'], $default_in_scadenza);
		
		printf(" Giorni in scadenza: ".$giorni_in_scadenza);
		
		calcolaSituazioneFormazioneAzienda($azienda['accountid'], $giorni_in_scadenza);
		
	}
	
	printf("<br />Calcolo situazione formazione terminato!");
    
}

function getAziendePerSituazioneFormazione(){
    global $adb, $table_prefix, $current_user;
	
	/* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package situazioneFormazione
     * @version 1.0
     */
	
	$result = array();
	
	$data_corrente = date("Y-m-d");
	
	$q_account = "SELECT 
					cont.accountid accountid
					FROM {$table_prefix}_mansionirisorsa mr
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = mr.mansionirisorsaid
                    INNER JOIN {$table_prefix}_contactdetails cont ON cont.contactid = mr.risorsa
                    WHERE ent.deleted = 0 AND mr.stato_mansione = 'Attiva'
					AND (cont.data_fine_rap IS NULL OR cont.data_fine_rap = '' OR cont.data_fine_rap < '".$data_corrente."')
                    GROUP BY cont.accountid";
					
	//printf("<br />Query lista Aziende: <br /> %s", $q_account); die;
	
    $res_account = $adb->query($q_account);
    $num_account = $adb->num_rows($res_account);

    for($i=0; $i<$num_account; $i++){		

        $account = $adb->query_result($res_account,$i,'accountid');
        $account = html_entity_decode(strip_tags($account), ENT_QUOTES,$default_charset);
		
		$result[] = array('accountid' => $account);
		
	}
	
	return $result;
	
}

function getGiorniInScadenzaAzienda($azienda, $default){
    global $adb, $table_prefix, $current_user;
	
	/* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	
	$result = $default;
	
	$q_avvisi = "SELECT gestioneavvisiid,
					tipo_avviso, 
					stabilimento, 
					giorni_tra_avvisi, 
					giorni_in_scadenza, 
					data_ultimo_avviso, 
					indirizzo_mittente, 
					nome_mittente 
					FROM {$table_prefix}_gestioneavvisi
					INNER JOIN {$table_prefix}_crmentity ON crmid = gestioneavvisiid
					WHERE deleted = 0 AND tipo_avviso = 'Formazione' AND stabilimento = ".$azienda;
	$res_avvisi = $adb->query($q_avvisi);
	if($adb->num_rows($res_avvisi)>0){
		
		$giorni_in_scadenza = $adb->query_result($res_avvisi,0,'giorni_in_scadenza');
		$giorni_in_scadenza = html_entity_decode(strip_tags($giorni_in_scadenza), ENT_QUOTES,$default_charset);
		
		if($giorni_in_scadenza == null || $giorni_in_scadenza == ''){
			
			$giorni_in_scadenza = 0;
			
		}
		
	}
	else{
		
		$giorni_in_scadenza = $default;
		
	}
	
	$result = $giorni_in_scadenza;
	
	return $result;
		
}

function calcolaSituazioneFormazioneAzienda($account, $giorni_in_scadenza){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package situazioneFormazione
     * @version 1.0
     */
    
    $q_vecchi = "UPDATE {$table_prefix}_situazformaz SET
                    aggiornato = '0'
                    WHERE azienda = ".$account;
    $adb->query($q_vecchi);
	
	$lista_risorse = getRisorseAzienda($account);
	
	foreach($lista_risorse as $risorsa){
		
		printf("<br />----- Risorsa: ".$risorsa['risorsaid']);
		
		calcolaSituazioneFormazioneRisorsa($risorsa['risorsaid'], $giorni_in_scadenza);
		aggiornaSituazioneFormazioneRisorsaInAnagrafica($risorsa['risorsaid']);
		
	}
	
	$upd = "UPDATE {$table_prefix}_crmentity ent
			INNER JOIN {$table_prefix}_situazformaz sitform ON sitform.situazformazid = ent.crmid
			SET
			ent.deleted = 1
			WHERE sitform.aggiornato != '1' AND sitform.azienda = ".$account;
	$adb->query($upd);
    
}

function getRisorseAzienda($azienda){
    global $adb, $table_prefix, $current_user;
	
	/* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	
	$result = array();
	
	$data_corrente = date("Y-m-d");
	
	$q_risorse = "SELECT 
					mr.risorsa risorsa
					FROM {$table_prefix}_mansionirisorsa mr
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = mr.mansionirisorsaid
                    INNER JOIN {$table_prefix}_contactdetails cont ON cont.contactid = mr.risorsa
                    WHERE ent.deleted = 0 AND mr.stato_mansione = 'Attiva' AND (cont.data_fine_rap IS NULL OR cont.data_fine_rap = '' OR cont.data_fine_rap > '".$data_corrente."' OR cont.data_fine_rap = '0000-00-00') AND cont.accountid = ".$azienda;
	
	$res_risorse = $adb->query($q_risorse);
	$num_risorse = $adb->num_rows($res_risorse);
	
	for($i = 0; $i < $num_risorse; $i++){	

		$risorsa = $adb->query_result($res_risorse, $i, 'risorsa');
		$risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES, $default_charset);
		
		$result[] = array('risorsaid' => $risorsa);
		
	}
	
	return $result;
	
}

function calcolaSituazioneFormazioneRisorsa($risorsa, $giorni_in_scadenza){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	 
	$lista_mansioni_risorsa = getMansioniRisorsa($risorsa);
	
	foreach($lista_mansioni_risorsa as $mansione_risorsa){
		
		printf("<br />------- Mansione-Risorsa: ".$mansione_risorsa['mansionirisorsaid']);
		
		calcolaSituazioneFormazioneMansioneRisorsa($risorsa, $mansione_risorsa['mansionirisorsaid'], $giorni_in_scadenza);
		
	}
      
}

function getMansioniRisorsa($risorsa){
    global $adb, $table_prefix, $current_user;
	
	/* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	
	$result = array();
	
	$q_mansioni_risorse = "SELECT 
							manris.mansionirisorsaid mansionirisorsaid 
                            FROM {$table_prefix}_mansionirisorsa manris
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = manris.mansionirisorsaid
                            WHERE ent.deleted = 0 AND manris.stato_mansione = 'Attiva' AND manris.risorsa = ".$risorsa;
    $res_mansioni_risorse = $adb->query($q_mansioni_risorse);
    $num_mansioni_risorse = $adb->num_rows($res_mansioni_risorse);
    
	for($i = 0; $i < $num_mansioni_risorse; $i++){
        
        $mansionirisorsaid = $adb->query_result($res_mansioni_risorse, $i, 'mansionirisorsaid');
        $mansionirisorsaid = html_entity_decode(strip_tags($mansionirisorsaid), ENT_QUOTES, $default_charset);
		
		$result[] = array('mansionirisorsaid' => $mansionirisorsaid);
		
	}
	
	return $result;
	
}

function calcolaSituazioneFormazioneMansioneRisorsa($risorsa, $mansionirisorsaid, $giorni_in_scadenza){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	 
	$lista_tipi_corso = getTipiCorsoMansioniRisorsa($mansionirisorsaid);
	
	foreach($lista_tipi_corso as $tipo_corso){
		
		printf("<br />--------- Tipo Corso: ".$tipo_corso['tipocorsoid']);
		
		calcolaSituazioneFormazioneTipoCorso($risorsa, $mansionirisorsaid, $tipo_corso['tipocorsoid'], $giorni_in_scadenza);
		
	}
    
}

function getTipiCorsoMansioniRisorsa($mansionirisorsa){
    global $adb, $table_prefix, $current_user;
	
	/* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	
	$result = array();
	
	$q_tipi_corso = "SELECT *FROM 
                    ((SELECT rel1.relcrmid tipo_corso,
                    tc1.aggiornamento_di aggiornamento_di
                    FROM {$table_prefix}_crmentityrel rel1
                    INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = rel1.relcrmid
                    INNER JOIN {$table_prefix}_tipicorso tc1 ON tc1.tipicorsoid = rel1.relcrmid
                    WHERE ent1.deleted = 0 AND rel1.crmid = ".$mansionirisorsa." AND rel1.relmodule = 'TipiCorso')
                    UNION
                    (SELECT rel2.crmid tipo_corso,
                    tc2.aggiornamento_di aggiornamento_di
                    FROM {$table_prefix}_crmentityrel rel2
                    INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = rel2.crmid
                    INNER JOIN {$table_prefix}_tipicorso tc2 ON tc2.tipicorsoid = rel2.crmid
                    WHERE ent2.deleted = 0 AND rel2.relcrmid = ".$mansionirisorsa." AND rel2.module = 'TipiCorso')) AS t
                    ORDER BY t.aggiornamento_di DESC";
    //printf($q_tipi_corso);                

    $res_tipi_corso = $adb->query($q_tipi_corso);
    $num_tipi_corso = $adb->num_rows($res_tipi_corso);
    for($i=0; $i<$num_tipi_corso; $i++){	

        $tipo_corso = $adb->query_result($res_tipi_corso,$i,'tipo_corso');
        $tipo_corso = html_entity_decode(strip_tags($tipo_corso), ENT_QUOTES,$default_charset);
		
		$result[] = array('tipocorsoid' => $tipo_corso);
		
	}
	
	return $result;
	
}

function calcolaSituazioneFormazioneTipoCorso($risorsa,$mansionirisorsaid,$tipo_corso,$giorni_in_scadenza){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
    
    $corso_di_aggiornamento = verificaSeAggiornamentoDiAltroTipoCorso($tipo_corso);
    printf(", Corso di aggiornamento: ".$corso_di_aggiornamento);
    
    if($corso_di_aggiornamento == "no"){
		
        calcolaSituazioneTipoCorsoBase($risorsa, $mansionirisorsaid, $tipo_corso, $giorni_in_scadenza, "si");
		
    }
    elseif($corso_di_aggiornamento == "si"){
		
        calcolaSituazioneTipoCorsoAggiornamento($risorsa,$mansionirisorsaid,$tipo_corso,$giorni_in_scadenza, "si");
		
    }
    
}

function verificaSeAggiornamentoDiAltroTipoCorso($tipo_corso){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
    
    $q_aggiornamento_di = "SELECT tc.tipicorsoid tipicorsoid 
                            FROM {$table_prefix}_tipicorso tc
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tc.tipicorsoid
                            WHERE ent.deleted = 0 AND tc.aggiornamento_di = ".$tipo_corso;
    //printf("  %s  ", $q_aggiornamento_di);                         
                          
    $res_aggiornamento_di = $adb->query($q_aggiornamento_di);
    if($adb->num_rows($res_aggiornamento_di)>0){
        
        $aggiornamento_di = "si";
        
    } 
    else{
        
        $aggiornamento_di = "no";
        
    }
    
    return $aggiornamento_di;
    
}

function getDatiTipoCorso($tipo_corso){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	 
	$result = "";
	 
	$q_tipo_corso = "SELECT 
                        tipicorso_name,
                        durata_corso,
                        aggiornamento_di,
                        formaz_scaglionata,
                        anni_rinnovo
                        FROM {$table_prefix}_tipicorso
                        WHERE tipicorsoid = ".$tipo_corso;
    $res_tipo_corso = $adb->query($q_tipo_corso);
    if($adb->num_rows($res_tipo_corso)>0){
        
        $tipicorso_name = $adb->query_result($res_tipo_corso,0,'tipicorso_name');
        $tipicorso_name = html_entity_decode(strip_tags($tipicorso_name), ENT_QUOTES,$default_charset);
        
        $durata_corso = $adb->query_result($res_tipo_corso,0,'durata_corso');
        $durata_corso = html_entity_decode(strip_tags($durata_corso), ENT_QUOTES,$default_charset);
        if($durata_corso == null || $durata_corso == ''){
            $durata_corso = 0;
        }
        
        $aggiornato_da = $adb->query_result($res_tipo_corso,0,'aggiornamento_di');
        $aggiornato_da = html_entity_decode(strip_tags($aggiornato_da), ENT_QUOTES,$default_charset);
        if($aggiornato_da == null || $aggiornato_da == ''){
            $aggiornato_da = 0;
        }
        
        $formaz_scaglionata = $adb->query_result($res_tipo_corso,0,'formaz_scaglionata');
        $formaz_scaglionata = html_entity_decode(strip_tags($formaz_scaglionata), ENT_QUOTES,$default_charset);
        if($formaz_scaglionata == '1'){
            $formaz_scaglionata = "si";
        }
        else{
            $formaz_scaglionata = "no";
        }
        
        $anni_rinnovo = $adb->query_result($res_tipo_corso,0,'anni_rinnovo');
        $anni_rinnovo = html_entity_decode(strip_tags($anni_rinnovo), ENT_QUOTES,$default_charset);
        if($anni_rinnovo == null || $anni_rinnovo == ''){
            $anni_rinnovo = 0;
        }
		
		$result = array('tipicorso_name' => $tipicorso_name,
						'durata_corso' => $durata_corso,
						'aggiornato_da' => $aggiornato_da,
						'formaz_scaglionata' => $formaz_scaglionata,
						'anni_rinnovo' => $anni_rinnovo);

    }
	
	return $result;
	 
}

function calcolaSituazioneTipoCorsoBase($risorsa, $mansionirisorsaid, $tipo_corso, $giorni_in_scadenza, $aggiorna){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	 
	$dati_tipo_corso = getDatiTipoCorso($tipo_corso);
        
    printf(", Nome: ".$dati_tipo_corso['tipicorso_name'].", Durata: ".$dati_tipo_corso['durata_corso'].", Scaglionato: ".$dati_tipo_corso['formaz_scaglionata']);
    
    $lista_formazione_eseguita = getFormazioneEseguitaRisorsaTipoCorso($risorsa, $tipo_corso, $dati_tipo_corso['aggiornato_da'], $giorni_in_scadenza);
 
	if(count($lista_formazione_eseguita) == 0){
		
		$stato_formazione = 'Non eseguita';
		$data_formazione = '';
		$validita_formazione = '';
		$durata_formazione = 0;
		$nota_stato = "Nota stato situazione formazione: La formazione NON e' stata eseguita in quanto per tale tipo corso e tale risorsa non risultano partecipazioni a corsi di formazione.";
		
	}
	else{
		
		$stato_formazione = $lista_formazione_eseguita[0]['stato_formazione'];
		$data_formazione = $lista_formazione_eseguita[0]['data_formazione'];
		$validita_formazione = $lista_formazione_eseguita[0]['data_scad_for'];
		$durata_formazione = $lista_formazione_eseguita[0]['tot_ore_effet'];
		$nota_stato = $lista_formazione_eseguita[0]['nota_stato'];
		
	}
	
	printf("<br />----------- Stato Formazione: %s <br />----------- Partecipazione ID: %s <br />----------- Data Formazione: %s <br />----------- Validita Formazione: %s <br />----------- Nota: %s", $stato_formazione, $partecipazioneid, $data_formazione, $durata_formazione, $nota_stato);
            
    if($aggiorna == "si"){
		
		setSituazioneFormazione($tipo_corso, $risorsa, $mansionirisorsaid, $durata_formazione, $data_formazione, $validita_formazione, $stato_formazione, $nota_stato, "", "no", "no", $lista_formazione_eseguita);
        
    }
    
}

function setSituazioneFormazione($tipo_corso, $risorsa, $mansionirisorsaid, $durata_formazione, $data_formazione, $validita_formazione, $stato_formazione, $nota_stato, $validita_formazione_prec, $formazione_scaglionata, $finestra_antecedente, $lista_formazione_eseguita){
	global $adb, $table_prefix, $current_user;
	
	if($durata_formazione == null || $durata_formazione == ''){
		$durata_formazione = 0;
	}
	
	if($formazione_scaglionata == "si"){
		$formazione_scaglionata = '1';
	}
	else{
		$formazione_scaglionata = '0';
	}
	
	if($finestra_antecedente == "si"){
		$finestra_antecedente = '1';
	}
	else{
		$finestra_antecedente = '0';
	}
	
	$dati_tipo_corso = getDatiTipoCorso($tipo_corso);
	
	if($dati_tipo_corso['durata_corso'] == null || $dati_tipo_corso['durata_corso'] == ''){
		$dati_tipo_corso['durata_corso'] = 0;
	}
	
	$dati_mansione_risorsa = getDatiMansioneRisorsa($mansionirisorsaid);
    
	$situazformazid = 0;
	
	if($formazione_scaglionata == '1'){
		
		$q_verifica = "SELECT situazformazid FROM {$table_prefix}_situazformaz
						INNER JOIN {$table_prefix}_crmentity ON crmid = situazformazid
						WHERE deleted = 0 AND validita_formazione = '".$validita_formazione."' AND tipo_corso = ".$tipo_corso." AND mansione_risorsa = ".$mansionirisorsaid;
		
	}
	else{
		
		$q_verifica = "SELECT situazformazid FROM {$table_prefix}_situazformaz
						INNER JOIN {$table_prefix}_crmentity ON crmid = situazformazid
						WHERE deleted = 0 AND tipo_corso = ".$tipo_corso." AND mansione_risorsa =".$mansionirisorsaid;
						
	}
	
	$res_verifica = $adb->query($q_verifica);
	if($adb->num_rows($res_verifica)>0){
		
		$situazformazid = $adb->query_result($res_verifica,0,'situazformazid');
		$situazformazid = html_entity_decode(strip_tags($situazformazid), ENT_QUOTES,$default_charset);
		
		$upd = "UPDATE {$table_prefix}_situazformaz SET
				tipo_corso = ".$tipo_corso.",
				data_formazione = '".$data_formazione."',
				validita_formazione = '".$validita_formazione."',
				azienda = ".$dati_mansione_risorsa['accountid'].",
				stato_formazione = '".$stato_formazione."',
				risorsa = ".$risorsa.",
				mansione = ".$dati_mansione_risorsa['mansione'].",
				mansione_risorsa = ".$mansionirisorsaid.",
				stabilimento = ".$dati_mansione_risorsa['stabilimento'].",
				ore_previste = ".$dati_tipo_corso['durata_corso'].",
				ore_effettuate = ".$durata_formazione.",  
				data_prec_scadenza = '".$validita_formazione_prec."',
				kp_corso_scaglionat = '".$formazione_scaglionata."',
				kp_finestra_antec = '".$finestra_antecedente."',
				aggiornato = '1'
				WHERE situazformazid = ".$situazformazid;
		$adb->query($upd);
		
		$nota_stato = addslashes($nota_stato);
		
		$upd_ent = "UPDATE {$table_prefix}_crmentity SET
					description = '".$nota_stato."'
					WHERE crmid = ".$situazformazid;
		
		$adb->query($upd_ent);
		
		pulisciRelatedPartecipazioniSituazioneFormazione($situazformazid);
			
		/*//Codice di verifica query
		if($risorsa == 359 && $tipo_corso ==  160){
			printf("<br />".$upd);die;
		}*/
		
	}
	else{
		
		$new_situazione_formazione = CRMEntity::getInstance('SituazFormaz'); 
		$new_situazione_formazione->column_fields['assigned_user_id'] = 1;
		$new_situazione_formazione->column_fields['creator'] = 1;
		if($mansionirisorsaid != "" && $mansionirisorsaid != 0){
			$new_situazione_formazione->column_fields['mansione_risorsa'] = $mansionirisorsaid;
		}
		if($risorsa != "" && $risorsa != 0){
			$new_situazione_formazione->column_fields['risorsa'] = $risorsa;
		}
		if($dati_mansione_risorsa['mansione'] != "" && $dati_mansione_risorsa['mansione'] != 0){
			$new_situazione_formazione->column_fields['mansione'] = $dati_mansione_risorsa['mansione'];
		}
		if($tipo_corso != "" && $tipo_corso != 0){
			$new_situazione_formazione->column_fields['tipo_corso'] = $tipo_corso;
		}
		if($data_formazione != ""){
			$new_situazione_formazione->column_fields['data_formazione'] = $data_formazione;
		}
		if($validita_formazione != ""){
			$new_situazione_formazione->column_fields['validita_formazione'] = $validita_formazione;
		}
		if($stato_formazione != ""){
			$new_situazione_formazione->column_fields['stato_formazione'] = $stato_formazione;
		}
		if($dati_mansione_risorsa['accountid']!= "" && $dati_mansione_risorsa['accountid'] != 0){
			$new_situazione_formazione->column_fields['azienda'] = $dati_mansione_risorsa['accountid'];
		}
		if($dati_mansione_risorsa['stabilimento']!= "" && $dati_mansione_risorsa['stabilimento'] != 0){
			$new_situazione_formazione->column_fields['stabilimento'] = $dati_mansione_risorsa['stabilimento'];
		}
		if($$dati_tipo_corso['durata_corso'] != "" && $dati_tipo_corso['durata_corso'] != 0){
			$new_situazione_formazione->column_fields['ore_previste'] = $dati_tipo_corso['durata_corso'];
		}
		if($durata_formazione != "" && $durata_formazione != 0){
			$new_situazione_formazione->column_fields['ore_effettuate'] = $durata_formazione;
		}
		if($nota_stato != ""){
			$new_situazione_formazione->column_fields['description'] = $nota_stato;
		}
		if($validita_formazione_prec != ""){
			$new_situazione_formazione->column_fields['data_prec_scadenza'] = $validita_formazione_prec;
		}
		if($formazione_scaglionata == '1'){
			$new_situazione_formazione->column_fields['kp_corso_scaglionat'] = $formazione_scaglionata;
		}
		if($finestra_antecedente == '1'){
			$new_situazione_formazione->column_fields['kp_finestra_antec'] = $finestra_antecedente;
		}
		$new_situazione_formazione->column_fields['aggiornato'] = '1';
		$new_situazione_formazione->save('SituazFormaz', $longdesc=true, $offline_update=false, $triggerEvent=false);

		$situazformazid = $new_situazione_formazione->id;	
		
		/*//Codice di verifica query
		if($risorsa == 361 && $tipo_corso ==  17057){
			printf("<br />".$nota_stato);die;
		}*/
		
	}
	
	if(count($lista_formazione_eseguita) > 0 && $situazformazid != "" && $situazformazid != 0){
		
		foreach($lista_formazione_eseguita as $formazione_eseguita){
			
			setRelatedPartecipazioniSituazioneFormazione($situazformazid, $formazione_eseguita['partecipformazid']);
			
		}
		
	}
	
}

function pulisciRelatedPartecipazioniSituazioneFormazione($situazioneformazione){
	global $adb, $table_prefix, $current_user;
    
    /* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	 
	$delete = "DELETE FROM {$table_prefix}_crmentityrel
				WHERE module = 'SituazFormaz' AND relmodule = 'KpPartecipFormaz' AND crmid = ".$situazioneformazione;
	$adb->query($delete);
	
	$delete2 = "DELETE FROM {$table_prefix}_crmentityrel
				WHERE module = 'KpPartecipFormaz' AND relmodule = 'SituazFormaz' AND relcrmid = ".$situazioneformazione;
	$adb->query($delete2);
	
	/*//Codice di verifica query
	if($situazioneformazione == 16078){
		printf("<br />".$delete);die;
	}*/
	
}

function setRelatedPartecipazioniSituazioneFormazione($situazioneformazione, $partecipazione){
	global $adb, $table_prefix, $current_user;
    
    /* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	 
	$insert = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule)
				VALUES (".$situazioneformazione.", 'SituazFormaz', ".$partecipazione.", 'KpPartecipFormaz')";
	$adb->query($insert);
	
}

function getDatiMansioneRisorsa($mansionirisorsa){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	 
	$result = "";
	
	$q_dati = "SELECT 
				manris.mansione mansione,
				ris.accountid accountid,
				ris.stabilimento stabilimento
				FROM {$table_prefix}_mansionirisorsa manris
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = manris.mansionirisorsaid
				INNER JOIN {$table_prefix}_contactdetails ris ON ris.contactid = manris.risorsa
				WHERE ent.deleted = 0 AND manris.mansionirisorsaid = ".$mansionirisorsa;
	$res_dati = $adb->query($q_dati);
	if($adb->num_rows($res_dati)>0){
		
		$mansione = $adb->query_result($res_dati,0,'mansione');
		$mansione = html_entity_decode(strip_tags($mansione), ENT_QUOTES,$default_charset);
		
		$accountid = $adb->query_result($res_dati,0,'accountid');
		$accountid = html_entity_decode(strip_tags($accountid), ENT_QUOTES,$default_charset);
		
		$stabilimento = $adb->query_result($res_dati,0,'stabilimento');
		$stabilimento = html_entity_decode(strip_tags($stabilimento), ENT_QUOTES,$default_charset);
		if($stabilimento == null || $stabilimento == ''){
			$stabilimento = 0;
		}
		
		$result = array('mansione' => $mansione,
						'accountid' => $accountid,
						'stabilimento' => $stabilimento);
		
	} 
	
	return $result;
	
}

function getFormazioneEseguitaRisorsaTipoCorso($risorsa, $tipo_corso, $aggiornato_da, $giorni_in_scadenza){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	 
	$result = array();
	 
	$q_formazione = "SELECT
						part.kppartecipformazid kppartecipformazid,
						part.kp_nome_partecipaz nome_partecipaz,
						part.kp_risorsa risorsa,
						part.kp_tipo_corso tipo_corso,
						part.kp_formazione formazione,
						part.kp_data_formazione data_formazione,
						part.kp_data_scad_for data_scad_for,
						part.kp_tot_ore_formazio tot_ore_formazio,
						part.kp_tot_ore_effet tot_ore_effet,
						part.kp_stato_partecip stato_partecip,
						part.kp_azienda azienda,
						part.kp_stabilimento stabilimento
						FROM {$table_prefix}_kppartecipformaz part
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = part.kppartecipformazid
						WHERE ent.deleted = 0 AND part.kp_risorsa = ".$risorsa." AND part.kp_tipo_corso = ".$tipo_corso." AND part.kp_stato_partecip IN ('Eseguita', 'Eseguita parzialmente')
						ORDER BY part.kp_data_formazione DESC";
	
	/*//Codice di verifica query
	if($risorsa == 359 && $tipo_corso ==  160){
		printf("<br />".$q_formazione);die;
	}*/
		
	$res_formazione = $adb->query($q_formazione);
    $num_formazione = $adb->num_rows($res_formazione);
    for($i = 0; $i < $num_formazione; $i++){
		
		$partecipformazid = $adb->query_result($res_formazione, $i, 'kppartecipformazid');
        $partecipformazid = html_entity_decode(strip_tags($partecipformazid), ENT_QUOTES, $default_charset);
		
		$nome_partecipaz = $adb->query_result($res_formazione, $i, 'nome_partecipaz');
        $nome_partecipaz = html_entity_decode(strip_tags($nome_partecipaz), ENT_QUOTES, $default_charset);
		
		$data_formazione = $adb->query_result($res_formazione, $i, 'data_formazione');
        $data_formazione = html_entity_decode(strip_tags($data_formazione), ENT_QUOTES, $default_charset);
		
		$data_scad_for = $adb->query_result($res_formazione, $i, 'data_scad_for');
        $data_scad_for = html_entity_decode(strip_tags($data_scad_for), ENT_QUOTES, $default_charset);
		list($anno, $mese, $giorno) = explode("-", $data_scad_for);
		$data_scad_for_inv = date("d-m-Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		
		$tot_ore_formazio = $adb->query_result($res_formazione, $i, 'tot_ore_formazio');
        $tot_ore_formazio = html_entity_decode(strip_tags($tot_ore_formazio), ENT_QUOTES, $default_charset);
		
		$tot_ore_effet = $adb->query_result($res_formazione, $i, 'tot_ore_effet');
        $tot_ore_effet = html_entity_decode(strip_tags($tot_ore_effet), ENT_QUOTES, $default_charset);
		
		$stato_partecip = $adb->query_result($res_formazione, $i, 'stato_partecip');
        $stato_partecip = html_entity_decode(strip_tags($stato_partecip), ENT_QUOTES, $default_charset);
		
		$data_corrente = date("Y-m-d");
		$data_corrente_inv = date("d-m-Y");
		list($anno, $mese, $giorno) = explode("-", $data_corrente);
		$in_scadenza = date("Y-m-d", mktime(0, 0, 0, $mese, (int)$giorno + $giorni_in_scadenza, $anno));
		$in_scadenza_inv = date("d-m-Y", mktime(0, 0, 0, $mese, (int)$giorno + $giorni_in_scadenza, $anno));
		
		if($aggiornato_da != null && $aggiornato_da != '' && $aggiornato_da != 0){
            $stato_formazione = 'Eseguita';
			$nota_stato = "Nota stato situazione formazione: La formazione e' stata eseguita e il tipo corso non sara' da ripetere in quanto sara' aggiornato da un altro tipo corso.";
        }
        elseif($data_scad_for == '2099-12-31' || $data_scad_for == '2999-12-31'){
			//$data_scad_for = '2099-12-31';	//kpro@tom06102017
			$data_scad_for = '';	//kpro@tom06102017
            $stato_formazione = 'Valida senza scadenza';
			$nota_stato = "Nota stato situazione formazione: La formazione e' 'Valida senza scadenza' in quanto l'ultimo corso eseguito ha data scadenza pari a '31-12-2099' oppure '31-12-2999'.";
        }
        elseif($data_scad_for > $data_corrente && $data_scad_for <= $in_scadenza){
            $stato_formazione = 'In scadenza';
			$nota_stato = "Nota stato situazione formazione: La formazione e' 'In scadenza' in quanto la data della scadenza dell'ultima formazione eseguita (".$data_scad_for_inv.") risulta compresa tra la data corrente (".$data_corrente_inv.") e la data in cui andra' 'In scadenza'(".$in_scadenza_inv.").";
        }
        elseif($data_scad_for >= $in_scadenza){
            $stato_formazione = 'In corso di validita';
			$nota_stato = "Nota stato situazione formazione: La formazione e' 'In corso di validita' in quanto la data della scadenza dell'ultima formazione eseguita (".$data_scad_for_inv.") risulta maggiore della data ".$in_scadenza_inv." in cui andra' 'In scadenza'.";
        }
        else{
            $stato_formazione = 'Scaduta';
			$nota_stato = "Nota stato situazione formazione: La formazione e' 'Scaduta' in quanto la data della scadenza dell'ultima formazione eseguita (".$data_scad_for_inv.") risulta inferiore alla data odierna (".$data_corrente_inv.").";
        }
		
		$result[] = array('partecipformazid' => $partecipformazid,
							'nome_partecipaz' => $nome_partecipaz,
							'data_formazione' => $data_formazione,
							'data_scad_for' => $data_scad_for,
							'tot_ore_formazio' => $tot_ore_formazio,
							'tot_ore_effet' => $tot_ore_effet,
							'stato_formazione' => $stato_formazione,
							'nota_stato' => $nota_stato);
		
	}
	
	return $result;
	
}

function calcolaSituazioneTipoCorsoAggiornamento($risorsa, $mansionirisorsaid, $tipo_corso, $giorni_in_scadenza, $aggiorna){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
    
    $a_data = '';
    $da_data = '';
    $da_data_prec = "";
    $fino_a_data_prec = "";
    $eseguita_formazione_precedente = "false";
        
    $dati_tipo_corso = getDatiTipoCorso($tipo_corso);
        
    printf(", Nome: ".$dati_tipo_corso['tipicorso_name'].", Durata: ".$dati_tipo_corso['durata_corso'].", Scaglionato: ".$dati_tipo_corso['formaz_scaglionata']);
    
    $dati_situazione_formazione_precedente = getFormazionePrecedente($risorsa, $mansionirisorsaid, $tipo_corso, $giorni_in_scadenza);
	
	if($dati_situazione_formazione_precedente['stato_formazione'] == "Non eseguita"){
		
        $stato_formazione = "Non eseguito corso base";
		$data_formazione = '';
		$validita_formazione = '';
		$durata_formazione = 0;
		$lista_formazione_eseguita = array(); 
		$nota_stato = "Nota stato situazione formazione: La formazione risulta non eseguita in quanto non e' stato eseguito il corso base.";
		
		$eseguita_formazione_precedente = "false";
		
		/*if($aggiorna == "si"){
		
			setSituazioneFormazione($tipo_corso, $risorsa, $mansionirisorsaid, $durata_formazione, $data_formazione, $validita_formazione, $stato_formazione, $nota_stato, "", "no", "no", $lista_formazione_eseguita);
			
		}*/
		
    }
	else{
		
		$eseguita_formazione_precedente = "true";
		//Devo quindi verificare lo stato della formazione in base operando in modo diverso a seconda che sia scaglionato o meno
        $data_precedente_scadenza = $dati_situazione_formazione_precedente['validita_formazione'];
		
		if($dati_tipo_corso['formaz_scaglionata'] == "no"){
            
			//Verifico se ha eseguito la formazione
			$lista_formazione_eseguita = getFormazioneEseguitaRisorsaTipoCorso($risorsa, $tipo_corso, $dati_tipo_corso['aggiornato_da'], $giorni_in_scadenza);
			
			if(count($lista_formazione_eseguita) == 0){
		
				$stato_formazione = 'Eseguire entro';	//Kpro@tom160420181414
				$data_formazione = '';
				$validita_formazione = $dati_situazione_formazione_precedente["validita_formazione"];	//Kpro@tom160420181414
				$durata_formazione = 0;
				$nota_stato = "Nota stato situazione formazione: La formazione NON e' stata eseguita in quanto per tale tipo corso e tale risorsa non risultano partecipazioni a corsi di formazione.";
				
			}
			else{
				
				$stato_formazione = $lista_formazione_eseguita[0]['stato_formazione'];
				$data_formazione = $lista_formazione_eseguita[0]['data_formazione'];
				$validita_formazione = $lista_formazione_eseguita[0]['data_scad_for'];
				$durata_formazione = $lista_formazione_eseguita[0]['tot_ore_effet'];
				$nota_stato = $lista_formazione_eseguita[0]['nota_stato'];
				
			}
			
			printf("<br />----------- Stato Formazione: %s <br />----------- Partecipazione ID: %s <br />----------- Data Formazione: %s <br />----------- Validita Formazione: %s <br />----------- Nota: %s", $stato_formazione, $partecipazioneid, $data_formazione, $durata_formazione, $nota_stato);	
			
			if($aggiorna == "si" && $eseguita_formazione_precedente == "true"){

				setSituazioneFormazione($tipo_corso, $risorsa, $mansionirisorsaid, $durata_formazione, $data_formazione, $validita_formazione, $stato_formazione, $nota_stato, $data_precedente_scadenza, "no", "no", $lista_formazione_eseguita);
				
			}
			
		}
		elseif($dati_tipo_corso['formaz_scaglionata'] == "si"){
			
            $data_scadenza_corso_base = $data_precedente_scadenza;
			
			calcolaFormazioneScaglionataRisorsaTipoCorso($risorsa, $tipo_corso, $mansionirisorsaid, $dati_tipo_corso['aggiornato_da'], $data_scadenza_corso_base, $giorni_in_scadenza, $dati_tipo_corso['durata_corso'], $dati_tipo_corso['anni_rinnovo'], "si");
			
		}
		
	}
    
}  

function calcolaFormazioneScaglionataRisorsaTipoCorso($risorsa, $tipo_corso, $mansionirisorsaid, $aggiornato_da, $data_scadenza_corso_base, $giorni_in_scadenza, $durata_corso, $anni_rinnovo, $aggiorna){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	
	$data_corrente = date("Y-m-d");
	$stato_formazione = 'Non eseguita';
	$data_formazione = "";
	$validita_formazione = "";
	$durata_formazione = 0;
	$da_data = '';
    $fino_a_data = $data_scadenza_corso_base;
	$fino_a_data_prec = "";
	
	while($fino_a_data < $data_corrente){
		
		if($da_data == ''){
			
			$da_data = $fino_a_data;
			
		}else{
			
			list($anno, $mese, $giorno) = explode("-", $da_data);
			$da_data = date("Y-m-d", mktime(0, 0, 0, $mese, $giorno, (int)$anno + $anni_rinnovo));
			
		}
		
		$fino_a_data_prec = $fino_a_data;
		
		list($anno, $mese, $giorno) = explode("-", $da_data);
        $fino_a_data = date("Y-m-d", mktime(0, 0, 0, $mese, $giorno, (int)$anno + $anni_rinnovo));
		
	}
	
	//Codice di controllo
	/*if($risorsa == 362 && $tipo_corso == 22817){
		printf("<br />Data scaglione precedente: %s", $data_scadenza_corso_base); die;
	}*/

	if($fino_a_data_prec != ""){
		
		list($anno, $mese, $giorno) = explode("-", $fino_a_data_prec);
        $da_data_prec = date("Y-m-d", mktime(0, 0, 0, $mese, $giorno, (int)$anno - $anni_rinnovo));
		
		$da_data_inv = date("d-m-Y", mktime(0, 0, 0, $mese, $giorno, (int)$anno - $anni_rinnovo));
		
		$da_data_prec_inv = date("d-m-Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		
		$lista_formazione_eseguita = getFormazioneScaglionataEseguitaRisorsaTipoCorso($risorsa, $tipo_corso, $giorni_in_scadenza, $da_data_prec, $fino_a_data_prec, $durata_corso, "si");
		
		if(count($lista_formazione_eseguita) == 0){

			/* kpro@tom06102017 */

			if( $fino_a_data_prec <= date("Y-m-d") ){
		
				$stato_formazione = 'Non eseguita';

			}
			else{

				$stato_formazione = 'Eseguire entro';

			}

			/* kpro@tom06102017 end */

			$data_formazione = $da_data_prec;
			$validita_formazione = $fino_a_data_prec;
			$durata_formazione = 0;
			$validita_formazione_prec = $da_data_prec;
			$nota_stato = "Nota stato situazione formazione: La formazione NON e' stata eseguita in quanto per tale tipo corso e tale risorsa non risultano partecipazioni a corsi di formazione all'interno della finestra temporale in esame (".$da_data_inv." - ".$da_data_prec_inv.").";
			$nota_stato .= "<br />Nota date situazione formazione scaglionata: La data della formazione e la data di scadenza delle formazioni scaglionate non sono dettate dalle partecipazioni; sono bensi' calcolate a partire dalla data di scadenza del corso base (".$data_scadenza_corso_base.") sommando gli anni di rinnovo del tipo corso (".$anni_rinnovo.").";
			
		}
		else{
			
			$stato_formazione = $lista_formazione_eseguita[0]['stato_formazione'];
			$data_formazione = $da_data_prec;
			$validita_formazione = $fino_a_data_prec;
			$validita_formazione_prec = $da_data_prec;
			$durata_formazione = $lista_formazione_eseguita[0]['tot_ore_effettuate'];
			$nota_stato = $lista_formazione_eseguita[0]['nota_stato'];
			
		}
		
		if($stato_formazione == "Eseguita"){
			
			$eseguito_scaglione_precedente = "si";
			
		}
		else{
			
			$eseguito_scaglione_precedente = "no";
			
		}
		
		printf("<br />----------- Stato Formazione: %s <br />----------- Partecipazione ID: %s <br />----------- Data Formazione: %s <br />----------- Validita Formazione: %s <br />----------- Nota: %s", $stato_formazione, $partecipazioneid, $data_formazione, $validita_formazione, $nota_stato);
		
		if($aggiorna == "si"){
			
			setSituazioneFormazione($tipo_corso, $risorsa, $mansionirisorsaid, $durata_formazione, $data_formazione, $validita_formazione, $stato_formazione, $nota_stato, $validita_formazione_prec, "si", "si", $lista_formazione_eseguita);
			
		}
		
	}
	else{
		
		$eseguito_scaglione_precedente = "si";
		
	}
	
	/*if($risorsa == 362 && $tipo_corso == 22817){
		printf("<br />Eseguito scaglione precedente: %s", $eseguito_scaglione_precedente); die;
	}*/
	
	if($da_data == ""){
		
		//Se il campo $da_data è vuoto significa che non esiste uno scaglione temporale antecedente a oggi quindi devo considerare
		//come scadenza la scadenza del corso base
		$fino_a_data = $data_scadenza_corso_base;
		
	}
	else{
		
		list($anno, $mese, $giorno) = explode("-", $da_data);
		
		$da_data_inv = date("d-m-Y", mktime(0, 0, 0, $mese, $giorno, $anno));
			
		$fino_a_data = date("Y-m-d", mktime(0, 0, 0, $mese, $giorno, (int)$anno + $anni_rinnovo));
		
		$fino_a_data_inv = date("d-m-Y", mktime(0, 0, 0, $mese, $giorno, (int)$anno + $anni_rinnovo));
		
	}
	
	/*if($risorsa == 362 && $tipo_corso == 22817){
		printf("<br />Calcola formazione scaglionata da data: %s, a data: %s", $da_data, $fino_a_data); die;
	}*/
	
	$lista_formazione_eseguita = getFormazioneScaglionataEseguitaRisorsaTipoCorso($risorsa, $tipo_corso, $giorni_in_scadenza, $da_data, $fino_a_data, $durata_corso, $eseguito_scaglione_precedente);
	
	if(count($lista_formazione_eseguita) == 0){
		
		if($eseguito_scaglione_precedente != "si"){
			
			$stato_formazione = "Non eseguita formazione precedente";
			$nota_stato = "Nota stato situazione formazione: Non risulta ultimata la formazione dello scaglione temporale precedente a quello in esame (".$da_data_inv." - ".$fino_a_data_inv.").";
	
		}
		else{

			/* kpro@tom06102017 */
			
			if( $fino_a_data <= date("Y-m-d") ){

				$stato_formazione = 'Non eseguita';

			}
			else{

				$stato_formazione = 'Eseguire entro';

			}

			/* kpro@tom06102017 end */

			$nota_stato = "Nota stato situazione formazione: La formazione NON e' stata eseguita in quanto per tale tipo corso e tale risorsa non risultano partecipazioni a corsi di formazione all'interno della finestra temporale in esame (".$da_data_inv." - ".$fino_a_data_inv.").";

		}
		$nota_stato .= "<br />Nota date situazione formazione scaglionata: La data della formazione e la data di scadenza delle formazioni scaglionate non sono dettate dalle partecipazioni; sono bensi' calcolate a partire dalla data di scadenza del corso base (".$data_scadenza_corso_base.") sommando gli anni di rinnovo del tipo corso (".$anni_rinnovo.").";
		$data_formazione = $da_data;
		$validita_formazione = $fino_a_data;
		$durata_formazione = 0;
		$validita_formazione_prec = $da_data;
		
	}
	else{
		
		$stato_formazione = $lista_formazione_eseguita[0]['stato_formazione'];
		$data_formazione = $da_data;
		$validita_formazione = $fino_a_data;
		$validita_formazione_prec = $da_data;
		$durata_formazione = $lista_formazione_eseguita[0]['tot_ore_effettuate'];
		$nota_stato = $lista_formazione_eseguita[0]['nota_stato'];
		
	}
	
	printf("<br />----------- Stato Formazione: %s <br />----------- Partecipazione ID: %s <br />----------- Data Formazione: %s <br />----------- Validita Formazione: %s <br />----------- Nota: %s", $stato_formazione, $partecipazioneid, $data_formazione, $validita_formazione, $nota_stato);
		
	if($aggiorna == "si"){
		
		setSituazioneFormazione($tipo_corso, $risorsa, $mansionirisorsaid, $durata_formazione, $data_formazione, $validita_formazione, $stato_formazione, $nota_stato, $validita_formazione_prec, "si", "no", $lista_formazione_eseguita);
		
	}
	 
}  

function getFormazioneScaglionataEseguitaRisorsaTipoCorso($risorsa, $tipo_corso, $giorni_in_scadenza, $da_data, $a_data, $durata_corso, $eseguita_precedente){
    global $adb, $table_prefix, $current_user; 
	
	/* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	
	$result = array();
	
	$tot_ore_effettuate = 0;
	
	list($anno, $mese, $giorno) = explode("-", $da_data);
    $da_data_inv = date("d-m-Y", mktime(0, 0, 0, $mese, $giorno, $anno));
	
	list($anno, $mese, $giorno) = explode("-", $a_data);
	$a_data_inv = date("d-m-Y", mktime(0, 0, 0, $mese, $giorno, $anno));
	
	$data_corrente = date("Y-m-d");
	list($anno, $mese, $giorno) = explode("-", $data_corrente);
	$in_scadenza = date("Y-m-d", mktime(0, 0, 0,$mese, (int)$giorno + $giorni_in_scadenza, $anno));

	$q_tot_ore_formazione = "SELECT 
							COALESCE(SUM(part.kp_tot_ore_effet), 0) tot_ore_effet
							FROM {$table_prefix}_kppartecipformaz part
							INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = part.kppartecipformazid
							WHERE ent.deleted = 0 AND part.kp_stato_partecip IN ('Eseguita', 'Eseguita parzialmente') AND part.kp_risorsa = ".$risorsa." AND part.kp_tipo_corso = ".$tipo_corso."
							AND part.kp_data_formazione > '".$da_data."' AND part.kp_data_formazione <= '".$a_data."'
							ORDER BY part.kp_data_formazione DESC";
	
	/*//Codice di verifica
	if($risorsa == 363 && $tipo_corso == 17058 && $a_data == '2020-12-28'){
		printf("<br />".$q_tot_ore_formazione); die;
	}*/
	
	$res_tot_ore_formazione = $adb->query($q_tot_ore_formazione);
	
    if($adb->num_rows($res_tot_ore_formazione) > 0){	

        $tot_ore_effettuate = $adb->query_result($res_tot_ore_formazione, 0, 'tot_ore_effet');
        $tot_ore_effettuate = html_entity_decode(strip_tags($tot_ore_effettuate), ENT_QUOTES, $default_charset);
		
	}
	
	if($eseguita_precedente != "si"){
		
		$stato_formazione = "Non eseguita formazione precedente";
		$nota_stato = "Nota stato situazione formazione: Non risulta ultimata la formazione dello scaglio temporale precedente a quello in esame (".$da_data_inv." - ".$a_data_inv.").";
		$nota_stato .= "<br />Nota date situazione formazione scaglionata: La data della formazione e la data di scadenza delle formazioni scaglionate non sono dettate dalle partecipazioni; sono bensi' calcolate a partire dalla precedente scadenza (".$da_data_inv.") sommando gli anni di rinnovo del tipo corso.";
		
	}
	elseif($tot_ore_effettuate >= $durata_corso){
		
		$stato_formazione = "Eseguita";
		$nota_stato = "Nota stato situazione formazione: La formazione e' stata eseguita in quanto tutte le ore richieste (".$durata_corso.") per tale tipo corso e tale risorsa sono state effettuate all'interno della finestra temporale in esame (".$da_data_inv." - ".$a_data_inv.").";
		$nota_stato .= "<br />Nota date situazione formazione scaglionata: La data della formazione e la data di scadenza delle formazioni scaglionate non sono dettate dalle partecipazioni; sono bensi' calcolate a partire dalla precedente scadenza (".$da_data_inv.") sommando gli anni di rinnovo del tipo corso.";
			 
	}
	elseif($a_data > $data_corrente && $a_data <= $in_scadenza){
		
		$stato_formazione = "In scadenza";
		$nota_stato = "Nota stato situazione formazione: La formazione ha stato 'In scadenza' in quanto sono state eseguite solo ".$tot_ore_effettuate." delle ore richieste (".$durata_corso.") per tale tipo corso e tale risorsa all'interno della finestra temporale in esame (".$da_data_inv." - ".$a_data_inv.").";
		$nota_stato .= "<br />Nota date situazione formazione scaglionata: La data della formazione e la data di scadenza delle formazioni scaglionate non sono dettate dalle partecipazioni; sono bensi' calcolate a partire dalla precedente scadenza (".$da_data_inv.") sommando gli anni di rinnovo del tipo corso.";
			 
	}
	elseif($a_data > $in_scadenza){
		
		$stato_formazione = "In corso di validita";
		$nota_stato = "Nota stato situazione formazione: La formazione ha stato 'In corso di validita' in quanto sono state eseguite solo ".$tot_ore_effettuate." delle ore richieste (".$durata_corso.") per tale tipo corso e tale risorsa all'interno della finestra temporale in esame (".$da_data_inv." - ".$a_data_inv.").";
		$nota_stato .= "<br />Nota date situazione formazione scaglionata: La data della formazione e la data di scadenza delle formazioni scaglionate non sono dettate dalle partecipazioni; sono bensi' calcolate a partire dalla precedente scadenza (".$da_data_inv.") sommando gli anni di rinnovo del tipo corso.";
			 
	}
	elseif($a_data <= $data_corrente){
		
		$stato_formazione = "Non eseguita";
		$nota_stato = "Nota stato situazione formazione: La formazione ha stato 'Non eseguita' in quanto sono state eseguite solo ".$tot_ore_effettuate." delle ore richieste (".$durata_corso.") per tale tipo corso e tale risorsa all'interno della finestra temporale in esame (".$da_data_inv." - ".$a_data_inv.").";
		$nota_stato .= "<br />Nota date situazione formazione scaglionata: La data della formazione e la data di scadenza delle formazioni scaglionate non sono dettate dalle partecipazioni; sono bensi' calcolate a partire dalla precedente scadenza (".$da_data_inv.") sommando gli anni di rinnovo del tipo corso.";
		
	}
	else{
		
		$stato_formazione = "Non eseguita";
		$nota_stato = "";
		
	}
	
	/*//Codice di verifica
	if($risorsa == 363 && $tipo_corso == 17058 && $a_data == '2020-12-28'){
		printf("<br />Stato Formazione: ".$stato_formazione); die;
	}*/
	
	$q_formazione = "SELECT 
						part.kppartecipformazid partecipformazid,
						part.kp_nome_partecipaz nome_partecipaz,
						part.kp_formazione formazione,
						part.kp_data_formazione data_formazione,
						part.kp_data_scad_for data_scad_for,
						part.kp_tot_ore_formazio tot_ore_formazio,
						part.kp_tot_ore_effet tot_ore_effet,
						part.kp_stato_partecip stato_partecip
						FROM {$table_prefix}_kppartecipformaz part
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = part.kppartecipformazid
						WHERE ent.deleted = 0 AND part.kp_stato_partecip IN ('Eseguita', 'Eseguita parzialmente') AND part.kp_risorsa = ".$risorsa." AND part.kp_tipo_corso = ".$tipo_corso."
						AND part.kp_data_formazione > '".$da_data."' AND part.kp_data_formazione <= '".$a_data."'
						ORDER BY part.kp_data_formazione DESC";
	
	$res_formazione = $adb->query($q_formazione);
	$num_formazione = $adb->num_rows($res_formazione);
	for($i = 0; $i < $num_formazione; $i++){
		
		$partecipformazid = $adb->query_result($res_formazione, $i, 'partecipformazid');
        $partecipformazid = html_entity_decode(strip_tags($partecipformazid), ENT_QUOTES, $default_charset);
		
		$nome_partecipaz = $adb->query_result($res_formazione, $i, 'nome_partecipaz');
        $nome_partecipaz = html_entity_decode(strip_tags($nome_partecipaz), ENT_QUOTES, $default_charset);
		
		$formazione = $adb->query_result($res_formazione, $i, 'formazione');
        $formazione = html_entity_decode(strip_tags($formazione), ENT_QUOTES, $default_charset);
		
		$data_formazione = $adb->query_result($res_formazione, $i, 'data_formazione');
        $data_formazione = html_entity_decode(strip_tags($data_formazione), ENT_QUOTES, $default_charset);
		
		$data_scad_for = $adb->query_result($res_formazione, $i, 'data_scad_for');
        $data_scad_for = html_entity_decode(strip_tags($data_scad_for), ENT_QUOTES, $default_charset);
		
		$tot_ore_formazione_corso = $adb->query_result($res_formazione, $i, 'tot_ore_formazio');
        $tot_ore_formazione_corso = html_entity_decode(strip_tags($tot_ore_formazione_corso), ENT_QUOTES, $default_charset);
		
		$tot_ore_effettuate_corso = $adb->query_result($res_formazione, $i, 'tot_ore_effet');
        $tot_ore_effettuate_corso = html_entity_decode(strip_tags($tot_ore_effettuate_corso), ENT_QUOTES, $default_charset);
		
		$result[] = array('partecipformazid' => $partecipformazid,
							'nome_partecipaz' => $nome_partecipaz,
							'formazione' => $formazione,
							'data_formazione' => $data_formazione,
							'data_scad_for' => $data_scad_for,
							'tot_ore_formazione_corso' => $tot_ore_formazione_corso,
							'tot_ore_effettuate_corso' => $tot_ore_effettuate_corso,
							'tot_ore_effettuate' => $tot_ore_effettuate,
							'stato_formazione' => $stato_formazione,
							'nota_stato' => $nota_stato);
		
	}
	
	return $result;
	
}

function getFormazionePrecedente($risorsa, $mansionirisorsaid, $tipo_corso_aggiornamento, $giorni_in_scadenza){
    global $adb, $table_prefix, $current_user; 
	
	/* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	 
	$result = "";
	
	$dati_tipo_corso_precedente = getTipoCorsoPrecedente($mansionirisorsaid, $tipo_corso_aggiornamento);
	
	printf("<br />----------- Stato Formazione Precedente: Tipo Corso: %s", $dati_tipo_corso_precedente);
	
	if($dati_tipo_corso_precedente != "" && $dati_tipo_corso_precedente != 0){
		
		$dati_situazione_formazione_prec = getSituazioneFormazionePrecedente($risorsa, $mansionirisorsaid, $dati_tipo_corso_precedente, $giorni_in_scadenza);
		
		$validita_formazione = $dati_situazione_formazione_prec['validita_formazione'];
		
		$data_formazione = $dati_situazione_formazione_prec['data_formazione'];
		
		$stato_formazione = $dati_situazione_formazione_prec['stato_formazione'];
		
		$ore_previste = $dati_situazione_formazione_prec['ore_previste'];
		
		$ore_effettuate = $dati_situazione_formazione_prec['ore_effettuate'];
		
	}
	else{
		
		$validita_formazione = "";
		
		$data_formazione = "";
		
		$stato_formazione = "Non eseguita";
		
		$ore_previste = 0;
		
		$ore_effettuate = 0;
						
	}
	
	$result = array('validita_formazione' => $validita_formazione,
					'data_formazione' => $data_formazione,
					'stato_formazione' => $stato_formazione,
					'ore_previste' => $ore_previste,
					'ore_effettuate' => $ore_effettuate);
	
	printf(" Validita Formazione: %s, Data Formazione: %s, Stato Formazione: %s", $validita_formazione, $data_formazione, $stato_formazione);
	
	return $result;
	
}

function getTipoCorsoPrecedente($mansionirisorsa, $tipo_corso_aggiornamento){
	global $adb, $table_prefix, $current_user; 
	
	/* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	
	$result = 0;
	
	$q_tipi_corso = "SELECT *FROM 
                    ((SELECT rel1.relcrmid tipo_corso,
                    tc1.aggiornamento_di aggiornamento_di
                    FROM {$table_prefix}_crmentityrel rel1
                    INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = rel1.relcrmid
                    INNER JOIN {$table_prefix}_tipicorso tc1 ON tc1.tipicorsoid = rel1.relcrmid
                    WHERE ent1.deleted = 0 AND rel1.crmid = ".$mansionirisorsa." AND rel1.relmodule = 'TipiCorso' AND tc1.aggiornamento_di = ".$tipo_corso_aggiornamento.")
                    UNION
                    (SELECT rel2.crmid tipo_corso,
                    tc2.aggiornamento_di aggiornamento_di
                    FROM {$table_prefix}_crmentityrel rel2
                    INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = rel2.crmid
                    INNER JOIN {$table_prefix}_tipicorso tc2 ON tc2.tipicorsoid = rel2.crmid
                    WHERE ent2.deleted = 0 AND rel2.relcrmid = ".$mansionirisorsa." AND rel2.module = 'TipiCorso' AND tc2.aggiornamento_di = ".$tipo_corso_aggiornamento.")) AS t
                    ORDER BY t.aggiornamento_di DESC";
    //printf($q_tipi_corso);
                    
    $res_tipi_corso = $adb->query($q_tipi_corso);
	
    if($adb->num_rows($res_tipi_corso)>0){	

        $tipo_corso = $adb->query_result($res_tipi_corso, 0, 'tipo_corso');
        $tipo_corso = html_entity_decode(strip_tags($tipo_corso), ENT_QUOTES, $default_charset);
		
		$result = $tipo_corso;
		
	}
	
	return $result;
	
}

function getSituazioneFormazionePrecedente($risorsa, $mansionirisorsa, $tipo_corso, $giorni_in_scadenza){
	global $adb, $table_prefix, $current_user; 
	
	/* kpro@tom27122016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */
	
    $result = "";	
	
	$q_verifica_siturazione_prec = "SELECT 
									sitform.validita_formazione validita_formazione,
									sitform.data_formazione data_formazione,
									sitform.stato_formazione stato_formazione,
									sitform.ore_previste ore_previste,
									sitform.ore_effettuate ore_effettuate
									FROM {$table_prefix}_situazformaz sitform
									INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sitform.situazformazid
									WHERE ent.deleted = 0 AND sitform.aggiornato = '1' AND sitform.mansione_risorsa = ".$mansionirisorsa." AND tipo_corso = ".$tipo_corso;
	//printf($q_verifica_siturazione_prec);
									
	$res_verifica_siturazione_prec = $adb->query($q_verifica_siturazione_prec);
	if($adb->num_rows($res_verifica_siturazione_prec)==0){
		
		calcolaSituazioneFormazioneTipoCorso($risorsa, $mansionirisorsa, $tipo_corso, $giorni_in_scadenza);
		
	}
	
	$res_verifica_siturazione_prec = $adb->query($q_verifica_siturazione_prec);
	if($adb->num_rows($res_verifica_siturazione_prec)>0){
		
		$validita_formazione = $adb->query_result($res_verifica_siturazione_prec,0,'validita_formazione');
		$validita_formazione = html_entity_decode(strip_tags($validita_formazione), ENT_QUOTES,$default_charset);
		if($validita_formazione == null){
			$validita_formazione = '';
		}
		
		$data_formazione = $adb->query_result($res_verifica_siturazione_prec,0,'data_formazione');
		$data_formazione = html_entity_decode(strip_tags($data_formazione), ENT_QUOTES,$default_charset);
		if($data_formazione == null){
			$data_formazione = '';
		}
		
		$stato_formazione = $adb->query_result($res_verifica_siturazione_prec,0,'stato_formazione');
		$stato_formazione = html_entity_decode(strip_tags($stato_formazione), ENT_QUOTES,$default_charset);
		
		$ore_previste = $adb->query_result($res_verifica_siturazione_prec,0,'ore_previste');
		$ore_previste = html_entity_decode(strip_tags($ore_previste), ENT_QUOTES,$default_charset);
		if($ore_previste == null || $ore_previste == ''){
			$ore_previste = 0;
		}
		
		$ore_effettuate = $adb->query_result($res_verifica_siturazione_prec,0,'ore_effettuate');
		$ore_effettuate = html_entity_decode(strip_tags($ore_effettuate), ENT_QUOTES,$default_charset);
		if($ore_effettuate == null || $ore_effettuate == ''){
			$ore_effettuate = 0;
		}
		
		$result = array('validita_formazione' => $validita_formazione,
						'data_formazione' => $data_formazione,
						'stato_formazione' => $stato_formazione,
						'ore_previste' => $ore_previste,
						'ore_effettuate' => $ore_effettuate);
		
	}
	
	return $result;
	
}

function calcolaSituazioneDocumenti(){
    global $adb, $table_prefix, $current_user;
    
    $q_documento = "SELECT notesid FROM {$table_prefix}_notes
                    INNER JOIN {$table_prefix}_crmentity ON crmid = notesid
                    WHERE deleted = 0";

    $res_documento = $adb->query($q_documento);
    $num_documento = $adb->num_rows($res_documento);

    for($i=0; $i<$num_documento; $i++){
        
        $notesid = $adb->query_result($res_documento,$i,'notesid');
        $notesid = html_entity_decode(strip_tags($notesid), ENT_QUOTES,$default_charset);
        
        calcolaSituazioneDocumento($notesid);
        calcolaAziendeRelazionateAlDocumento($notesid);
        calcolaStabilimentiRelazionateAlDocumento($notesid);
        
    }
    
}

function calcolaSituazioneDocumento($documento){
    global $adb, $table_prefix, $current_user;
    
    $data_corrente = date("Y-m-d");
    
    $q_documento = "SELECT data_scadenza FROM {$table_prefix}_notes
                    INNER JOIN {$table_prefix}_crmentity ON crmid = notesid
                    WHERE notesid = ".$documento;
    $res_documento = $adb->query($q_documento);
    if($adb->num_rows($res_documento)>0){
        
        $data_scadenza = $adb->query_result($res_documento,0,'data_scadenza');
        $data_scadenza = html_entity_decode(strip_tags($data_scadenza), ENT_QUOTES,$default_charset);
        
        if($data_scadenza != null && $data_scadenza != ''){
        
            if($data_scadenza == '2099-12-31' || $data_scadenza == '2999-12-31'){
                $stato_documento = 'Valido senza scadenza';
            }
            elseif($data_scadenza <= $data_corrente){
                $stato_documento= 'Scaduto';
            }
            elseif($data_scadenza > $data_corrente){
                $stato_documento= 'In corso di validita';
            }
    
        }
        else{
            $stato_documento = 'Valido senza scadenza';
        }
        
        $udp_documento = "UPDATE {$table_prefix}_notes SET
                            stato_documento = '".$stato_documento."'	
                            WHERE notesid =".$documento;
        $adb->query($udp_documento);

    }
    
}

function calcolaAziendeRelazionateAlDocumento($documento){
    global $adb, $table_prefix, $current_user;
    
    $lista_account = "";
	
    $q_account_rel = "SELECT noterel.crmid accountid, acc.accountname accountname FROM {$table_prefix}_notes note
                        INNER JOIN {$table_prefix}_senotesrel noterel ON noterel.notesid = note.notesid
                        INNER JOIN {$table_prefix}_account acc ON noterel.crmid = acc.accountid
                        WHERE noterel.relmodule = 'Accounts' AND note.notesid =".$documento;

    $res_account_rel = $adb->query($q_account_rel);
    $num_account_rel = $adb->num_rows($res_account_rel);

    for($i=0; $i<$num_account_rel; $i++){	

        $accountname = $adb->query_result($res_account_rel,$i,'accountname');
        $accountname = html_entity_decode(strip_tags($accountname), ENT_QUOTES,$default_charset);
        $accountname = addslashes($accountname);
        
        if($lista_account == ""){
            $lista_account = $accountname;
        }
        else{
            $lista_account .= ", ".$accountname;
        }

    }
    
    $udp_documento = "UPDATE {$table_prefix}_notes SET
                        nome_azienda = '".$lista_account."'	
                        WHERE notesid =".$documento;
    $adb->query($udp_documento);

}

function calcolaStabilimentiRelazionateAlDocumento($documento){
    global $adb, $table_prefix, $current_user;
    
    $lista_stabilimenti = "";
    
    $q_stabilimento_rel = "SELECT noterel.crmid stabilimentoid, stab.nome_stabilimento stabilimentoname FROM {$table_prefix}_notes note
                            INNER JOIN {$table_prefix}_senotesrel noterel ON noterel.notesid = note.notesid
                            INNER JOIN {$table_prefix}_stabilimenti stab ON noterel.crmid = stab.stabilimentiid
                            WHERE noterel.relmodule = 'Stabilimenti' AND note.notesid =".$documento;
	
    $res_stabilimento_rel = $adb->query($q_stabilimento_rel);
    $num_stabilimento_rel = $adb->num_rows($res_stabilimento_rel);

    for($i=0; $i<$num_stabilimento_rel; $i++){	

        $stabilimentoname = $adb->query_result($res_stabilimento_rel,$i,'stabilimentoname');
        $stabilimentoname = html_entity_decode(strip_tags($stabilimentoname), ENT_QUOTES,$default_charset);
        $stabilimentoname = addslashes($stabilimentoname);
        
        if($lista_stabilimenti == ""){
            $lista_stabilimenti = $stabilimentoname;
        }
        else{
            $lista_stabilimenti .= ", ".$stabilimentoname;
        }

    }
    
    $udp_documento = "UPDATE {$table_prefix}_notes SET
                        nome_stabilimento = '".$lista_stabilimenti."'	
                        WHERE notesid =".$documento;
    $adb->query($udp_documento);

}

function aggiornaSituazioneFormazioneRisorsaInAnagrafica($risorsa){
    global $adb, $table_prefix, $current_user;
    
    $situazione_form_contatto = 'Eseguita';
		
    $q_sit_form = "SELECT situazformazid,
                    tipo_corso,
                    stato_formazione 
                    FROM {$table_prefix}_situazformaz
                    INNER JOIN {$table_prefix}_crmentity ON crmid = situazformazid
                    WHERE deleted = 0 AND risorsa = ".$risorsa;

    $res_sit_form = $adb->query($q_sit_form);
    $num_sit_form = $adb->num_rows($res_sit_form);

    for($y=0; $y<$num_sit_form; $y++){
        $situazformazid = $adb->query_result($res_sit_form,$y,'situazformazid');
        $situazformazid = html_entity_decode(strip_tags($situazformazid), ENT_QUOTES,$default_charset);
        $situazformazid = addslashes($situazformazid);
        
        $stato_formazione = $adb->query_result($res_sit_form,$y,'stato_formazione');
        $stato_formazione = html_entity_decode(strip_tags($stato_formazione), ENT_QUOTES,$default_charset);
        $stato_formazione = addslashes($stato_formazione);
        
        $tipo_corso = $adb->query_result($res_sit_form,$y,'tipo_corso');
        $tipo_corso = html_entity_decode(strip_tags($tipo_corso), ENT_QUOTES,$default_charset);
        $tipo_corso = addslashes($tipo_corso);

        if($stato_formazione == 'Non eseguita' || $stato_formazione == 'Scaduta'){
            $situazione_form_contatto = 'Da eseguire';
        }
        elseif($stato_formazione == 'In scadenza' && $situazione_form_contatto == 'Eseguita'){
            $situazione_form_contatto = 'In scadenza';
        }

    }

    $upd_cont = "UPDATE {$table_prefix}_contactdetails SET
                    sit_form_cont = '".$situazione_form_contatto."'
                    WHERE contactid = ".$risorsa;
    $adb->query($upd_cont);
     
}

function aggiornaStatoMansioniRisorseNonAttive(){
	global $adb, $table_prefix, $current_user;

	/* kpro@tom010220170902 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */

	$data_corrente = date("Y-m-d");

	$lista_risorse_non_attive = getRisorseNonAttiveAllaData($data_corrente);

	foreach($lista_risorse_non_attive as $risorsa){

		//printf("\nRisorsa %s", $risorsa["contactid"]));

		$lista_mansioni_risorse = getMansioniRisorsa($risorsa["contactid"]);

		foreach($lista_mansioni_risorse as $mansione_risorsa){

			setMansioneRisorsaNonAttiva($mansione_risorsa["mansionirisorsaid"], $risorsa["data_fine_rap"]);

		}

	}

}

function getRisorseNonAttiveAllaData($data){
	global $adb, $table_prefix, $current_user;

	/* kpro@tom010220170902 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */

	$result = array();

	$q_query = "SELECT 
				cont.contactid contactid,
				cont.data_fine_rap data_fine_rap
				FROM {$table_prefix}_contactdetails cont
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = cont.contactid
				WHERE ent.deleted = 0 AND (cont.data_fine_rap != '' AND cont.data_fine_rap != '0000-00-00' AND cont.data_fine_rap <= '".$data."')";
	
	$res_query = $adb->query($q_query);
    $num_result = $adb->num_rows($res_query);

    for($i = 0; $i < $num_result; $i++){

        $contactid = $adb->query_result($res_query, $i,'contactid');
        $contactid = html_entity_decode(strip_tags($contactid), ENT_QUOTES, $default_charset);
        //$contactid = addslashes($contactid);

		$data_fine_rap = $adb->query_result($res_query, $i,'data_fine_rap');
        $data_fine_rap = html_entity_decode(strip_tags($data_fine_rap), ENT_QUOTES, $default_charset);
        //$contactid = addslashes($contactid);

		$result[] = array("contactid" => $contactid,
							"data_fine_rap" => $data_fine_rap);

	}

	return $result;

}

function setMansioneRisorsaNonAttiva($mansionerisorsaid, $data_fine){
	global $adb, $table_prefix, $current_user;

	/* kpro@tom010220170902 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */

	$upd = "UPDATE {$table_prefix}_mansionirisorsa SET
			stato_mansione = 'Non attiva',
			data_fine = '".$data_fine."'
			WHERE stato_mansione != 'Non attiva' AND mansionirisorsaid = ".$mansionerisorsaid;
	$adb->query($upd);

}


?>