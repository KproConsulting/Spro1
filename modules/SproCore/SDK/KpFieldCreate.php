<?php

/* kpro@tom26082016 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */

global $adb, $table_prefix;

if(!isset($fields) || empty($fields) || !is_array($fields)){
	die('$fields empty');
}

//Esempio: $fields[] = array('module'=>'Corsi','block'=>'Informazioni Corso','name'=>'prodotto','label'=>'Prodotto','uitype'=>'10','columntype'=>'INT(19)','typeofdata'=>'I~O','relatedModules'=>array('Products'),'relatedModulesAction'=>array('Products'=>array('ADD','SELECT')));

foreach ($fields as $arr){
	$Vtiger_Utils_Log = true;
	
	if(!kpVerificaEsistenzaCampo($arr['module'], $arr['name'])){
	
		$modulo = Vtiger_Module::getInstance($arr['module']);
		$block = Vtiger_Block::getInstance($arr['block'],$modulo);
		$field = new Vtiger_Field();
		$field->readonly = 1;
		$field->name = $arr['name'];
		$field->label= $arr['label'];
		$field->table = $modulo->basetable;
		
		if($arr['readonly'] != ''){
			$field->readonly = $arr['readonly'];
		}

		if($arr['presence'] != ''){
			$field->presence = $arr['presence'];
		}
		
		//column type
		if ($arr['columntype'] != ''){
			$field->columntype = $arr['columntype'];
		}
		else{
			$field->columntype = 'C(255)';
		}
		
		//type of data
		if ($arr['typeofdata'] != ''){
			$field->typeofdata = $arr['typeofdata'];
		}
		else{
			$field->typeofdata = 'V~O';
		}
		
		//uitype
		if ($arr['uitype'] != ''){
			$field->uitype = $arr['uitype'];
		}
		else{
			$field->uitype = 1;
		}
		
		//displaytype
		if ($arr['displaytype'] != ''){
			$field->displaytype = $arr['displaytype'];
		}
		else{
			$field->displaytype = 1;
		}
		
		if ($arr['masseditable'] != ''){
			$field->masseditable = $arr['masseditable'];
		}
		else{
			$field->masseditable = 0;
		}
		
		$field->quickcreate = 3;
		
		//se picklist aggiungo i valori
		if($arr['picklist'] != ''){
			
			if(kpVerificaEsistenzaPickingList($arr['name'])){
				
				printf("La pickinglist %s risulta gia' esistente nel crm (ma non nel modulo in esame) pertanto e' stato creato solo il campo\n", $arr['name']);
				
			}
			else{
				
				$field->setPicklistValues($arr['picklist']);
				
			}
			
		}
		
		if ($arr['helpinfo'] != '') {
			$field->helpinfo = $arr['helpinfo'];
		}
			
		$block->addField($field);
		
		if ($arr['relatedModules'] != ''){
			$field->setRelatedModules($arr['relatedModules']);
			if (!empty($arr['relatedModulesAction'])) {
				foreach ($arr['relatedModules'] as $relmod) {
					$relinst = Vtiger_Module::getInstance($relmod);
					$relinst->setRelatedList($modulo, $arr['module'], $arr['relatedModulesAction'][$relmod], 'get_dependents_list');
				}
			}
		}
		if ($arr['sdk_uitype'] != '') {
			$newtype = intval($arr['sdk_uitype']);
			$adb->pquery("update ".$table_prefix."_field set uitype = ? where columnname = ?", array($newtype, $arr['name']));
		}
		
		if($arr['uitype'] == 7 || $arr['uitype'] == 71){
			
			kpCorrezioneDbType($arr['module'], $arr['name'], $arr['columntype']);
			
		}
	
	}
	else{
		
		printf("Il campo %s risulta gia' esistente nel modulo %s \n", $arr['name'], $arr['module']);
		
	}	
	
}

function kpCorrezioneDbType($nome_modulo, $nome_campo, $tipo_campo){
    global $adb, $table_prefix, $current_user;
    
    /* kpro@tom26082016 */

	/**
	 * @author Tomiello Marco
	 * @copyright (c) 2016, Kpro Consulting Srl
	 * 
	 * Questa funzione permette di correggere il tipo campo nel database;
	 */
	 
	$tabid = 0;
	$tablename = "";
	
	$q_tabid = "select 
				tabid 
				from {$table_prefix}_tab 
				where name = '".$nome_modulo."'"; 
	$res_tabid = $adb->query($q_tabid);
    if($adb->num_rows($res_tabid) > 0){
        
        $tabid = $adb->query_result($res_tabid, 0, 'tabid');
        $tabid = html_entity_decode(strip_tags($tabid), ENT_QUOTES, $default_charset);
        
    }
    
    if($tabid != "" && $tabid != 0){
		
		$q_nome_tabella = "select 
							tablename
							from {$table_prefix}_field 
							where columnname = '".$nome_campo."' and tabid = ".$tabid;
		
		$res_nome_tabella = $adb->query($q_nome_tabella);
		if($adb->num_rows($res_nome_tabella) > 0){
			
			$tablename = $adb->query_result($res_nome_tabella, 0, 'tablename');
			$tablename = html_entity_decode(strip_tags($tablename), ENT_QUOTES, $default_charset);
			
		}
		
		if($tablename != ""){
		
			$q_change = "ALTER TABLE ".$tablename." CHANGE ".$nome_campo." ".$nome_campo." ".$tipo_campo;
			
			$adb->query($q_change);
			
			printf("\nAggiornato campo: %s", $campo);
			
		}
		
	}
        
} 

function kpVerificaEsistenzaCampo($nome_modulo, $nome_campo){
    global $adb, $table_prefix, $current_user;
	
	/* kpro@tom26082016 */

	/**
	 * @author Tomiello Marco
	 * @copyright (c) 2016, Kpro Consulting Srl
	 * 
	 * Questa funzione permette verificare se il campo in questione esiste gia' nel modulo
	 */
	
	$tabid = 0;
	
	$q_tabid = "select 
				tabid 
				from {$table_prefix}_tab 
				where name = '".$nome_modulo."'"; 
	$res_tabid = $adb->query($q_tabid);
    if($adb->num_rows($res_tabid) > 0){
        
        $tabid = $adb->query_result($res_tabid, 0, 'tabid');
        $tabid = html_entity_decode(strip_tags($tabid), ENT_QUOTES, $default_charset);
        
    }
    
    if($tabid != "" && $tabid != 0){
		
		$q_fieldid = "select 
						fieldid 
						from {$table_prefix}_field 
						where (columnname = '".$nome_campo."' or fieldname = '".$nome_campo."') and tabid = ".$tabid;
		$res_fieldid = $adb->query($q_fieldid);
		if($adb->num_rows($res_fieldid) > 0){
			
			return true;
			
		}
		else{
			
			return false;
			
		}
		
	}
	
	return true;
    
}

function kpVerificaEsistenzaPickingList($nome_campo){
    global $adb, $table_prefix, $current_user;
	
	/* kpro@tom26082016 */

	/**
	 * @author Tomiello Marco
	 * @copyright (c) 2016, Kpro Consulting Srl
	 * 
	 * Questa funzione permette verificare se nel crm esiste gia' una picking list con quel nome
	 */
	
	
	$q_fieldid = "select 
					fieldid 
					from {$table_prefix}_field 
					where fieldname = '".$nome_campo."' and uitype in (15, 33)";
	$res_fieldid = $adb->query($q_fieldid);
	if($adb->num_rows($res_fieldid) > 0){
		
		return true;
		
	}
	else{
		
		return false;
		
	}
    
}

?>
