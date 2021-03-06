<?php

/* kpro@tom09062017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

class KpSDK {

	var $kprosdk_log_folder = "modules/SproCore/SDK/Logs/";
	var $kprosdk_log_filename = "logs.txt";

	static function log($message) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione gestisce i log
		 */

		$focus = new KpSDK();
		
		$data_corrente = date("Y-m-d H:i:s");

		$text = "
- ".$data_corrente.": ".$message;
		$log_file = fopen($focus->kprosdk_log_folder.$focus->kprosdk_log_filename, "a+");
		fwrite($log_file, $text);
		fclose($log_file);

		printf("<br /> - ".$data_corrente.": ".$message);

	}

	static function registraModuloDaFile($nome_modulo, $vtlib_filename, $sdk_filename = "") {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione gestisce la creazione di nuovi moduli verificando che questi già non siano presenti nel CRM
		 * e che vi siano tutti i file necessari per la corretta creazione
		 */

		if( $nome_modulo != "" ){

			$vtlib_filename_resente = self::filePresente("plugins/script/".$vtlib_filename);

			$modulo_file_presente = self::filePresente("modules/".$nome_modulo."/".$nome_modulo.".php");

			if( $sdk_filename != "" ){

				$sdk_filename_resente = self::filePresente("modules/SDK/src/".$sdk_filename);

			}
			else{

				$sdk_filename_resente = false;

			}

			if( !self::esisteModulo($nome_modulo) ){

				if( $vtlib_filename_resente && $modulo_file_presente ){

					//Esegui la registrazione del modulo

					include_once("plugins/script/".$vtlib_filename);
					@unlink("plugins/script/".$vtlib_filename);	//Elimina il file vtlib di registrazione del modulo

					if( $sdk_filename_resente ){

						include_once("modules/SDK/src/".$sdk_filename);
						@unlink("modules/SDK/src/".$sdk_filename);

					}

					self::log("Creato modulo ".$nome_modulo);

				}
				else{

					if( !$vtlib_filename_resente && $modulo_file_presente ){

						self::log("Impossibile creare modulo ".$nome_modulo." --> Motivo: manca il file plugins/script/".$vtlib_filename);

					}
					elseif( $vtlib_filename_resente && !$modulo_file_presente ){

						self::log("Impossibile creare modulo ".$nome_modulo." --> Motivo: manca il file modules/".$nome_modulo."/".$nome_modulo.".php");

					}
					elseif( !$vtlib_filename_resente && !$modulo_file_presente ){

						self::log("Impossibile creare modulo ".$nome_modulo." --> Motivo: manca il file plugins/script/".$vtlib_filename." e il file modules/".$nome_modulo."/".$nome_modulo.".php");

					}

					if( $vtlib_filename_resente ){

						@unlink("plugins/script/".$vtlib_filename);

					}

					if( $sdk_filename_resente ){

						@unlink("modules/SDK/src/".$sdk_filename);

					}

				}

			}
			else{

				if( $vtlib_filename_resente ){

					@unlink("plugins/script/".$vtlib_filename);

				}

				if( $sdk_filename_resente ){

					@unlink("modules/SDK/src/".$sdk_filename);

				}

				self::log("Impossibile creare modulo ".$nome_modulo." --> Motivo: Modulo già esistente");

			}

		}
		else{

			self::log("Impossibile creare modulo --> Motivo: Non è stato indicato il nome del modulo");

		}

	}
 	
	static function esisteModulo($nome_modulo) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione verifica l'esistenza o meno di un modulo nel CRM
		 */

		$result = false;

		$query = "SELECT 
					tabid 
					FROM {$table_prefix}_tab 
					WHERE NAME = '".$nome_modulo."'";
		
		$result_query = $adb->query($query);
   		$num_result = $adb->num_rows($result_query);

		if($num_result > 0){

			$result = true;

		}

		return $result;

	}

	static function filePresente($file) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione verifica l'esistenza o meno di un determinato file
		 */

		$result = false;

		if( is_file($file) ){

			$result = true;

		}

		return $result;

	}

	static function registraCampo($nome_modulo, $blocco, $nome_campo, $label_campo, $uitype, $columntype, $typeofdata, $readonly = "1", $helpinfo = "", $picklist = "") {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione gestisce la creazione di nuovi campi verificando che questi già non siano presenti nel CRM
		 */
		
		if( !self::esisteCampoNelModulo($nome_modulo, $nome_campo) && $uitype != "10" ){

			if( $blocco == "" ){

				$blocco = self::getBloccoModulo($nome_modulo);

			}
			
			$modulo = Vtiger_Module::getInstance( $nome_modulo );
			$block = Vtiger_Block::getInstance($blocco, $modulo);
			$field = new Vtiger_Field();
			$field->readonly = 1;
			$field->name = $nome_campo;
			$field->label= $label_campo;
			$field->table = $modulo->basetable;

			if( $readonly != '' ){
				$field->readonly = $readonly;
			}

			if( $columntype != '' ){
				$field->columntype = $columntype;
			}
			else{
				$field->columntype = 'C(255)';
			}
			
			if ( $typeofdata != '' ){
				$field->typeofdata = $typeofdata;
			}
			else{
				$field->typeofdata = 'V~O';
			}
			
			if( $uitype != '' ){
				$field->uitype = $uitype;
			}
			else{
				$field->uitype = 1;
			}
			
			$field->displaytype = 1;
			
			$field->masseditable = 1;
			
			$field->quickcreate = 0;

			if($picklist != ''){
			
				if( !self::esistePickingList($nome_campo) ){

					$field->setPicklistValues($picklist);
					
				}
				
			}

			if ($helpinfo != '') {
				$field->helpinfo = $helpinfo;
			}
			
			$block->addField($field);

			if($uitype == 7 || $uitype == 71){
			
				self::correzioneDbType($nome_modulo, $nome_campo, $columntype);
				
			}

			self::log("Creato Campo ".$nome_campo." nel modulo ".$nome_modulo);

		}
		else{

			if($uitype == "10"){

				self::log("Impossibile creare il campo ".$nome_campo." nel modulo ".$nome_modulo." --> Motivo: Usare la funzione KpSDK::registraCampoRelazionato per registrare i campi relazionati");
			
			}
			else{

				self::log("Impossibile creare il campo ".$nome_campo." nel modulo ".$nome_modulo." --> Motivo: Campo già esistente nel modulo");
				
			}
		}

	}

	static function registraCampoRelazionato($nome_modulo, $blocco, $nome_campo, $label_campo, $typeofdata, $readonly = "1", $helpinfo = "", $relatedModules = "", $relatedModulesAction = "") {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione gestisce la creazione di nuovi campi Relazionati verificando che questi già non siano presenti nel CRM
		 */
		
		if( !self::esisteCampoNelModulo($nome_modulo, $nome_campo) ){

			if( $blocco == "" ){

				$blocco = self::getBloccoModulo($nome_modulo);

			}
			
			$modulo = Vtiger_Module::getInstance( $nome_modulo );
			$block = Vtiger_Block::getInstance($blocco, $modulo);
			$field = new Vtiger_Field();
			$field->readonly = 1;
			$field->name = $nome_campo;
			$field->label= $label_campo;
			$field->table = $modulo->basetable;

			if( $readonly != '' ){
				$field->readonly = $readonly;
			}

			$field->columntype = 'int(19)';
			
			if ( $typeofdata != '' ){
				$field->typeofdata = $typeofdata;
			}
			else{
				$field->typeofdata = 'I~O';
			}
			
			$field->uitype = 10;
			
			$field->displaytype = 1;
			
			$field->masseditable = 1;
			
			$field->quickcreate = 0;

			if ($helpinfo != '') {
				$field->helpinfo = $helpinfo;
			}
			
			$block->addField($field);
			
			if ($relatedModules != ''){
				$field->setRelatedModules($relatedModules);
				if (!empty($relatedModulesAction)) {
					foreach ($relatedModules as $relmod) {
						$relinst = Vtiger_Module::getInstance($relmod);
						$relinst->setRelatedList($modulo, $nome_modulo, $relatedModulesAction[$relmod], 'get_dependents_list');
					}
				}
			}

			self::log("Creato Campo Relazionato ".$nome_campo." nel modulo ".$nome_modulo);

		}
		else{

			self::log("Impossibile creare il campo relazionato ".$nome_campo." nel modulo ".$nome_modulo." --> Motivo: Campo già esistente nel modulo");

		}

	}

	static function esisteCampoNelModulo($nome_modulo, $nome_campo) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione verifica l'esistenza o meno di un campo all'interno di un determinato modulo
		 */

		$result = true;

		$tabid_modulo = self::getModuloTabid($nome_modulo);
		
		if( $tabid_modulo != 0 ){

			$query = "SELECT 
						fieldid 
						FROM {$table_prefix}_field 
						WHERE (columnname = '".$nome_campo."' OR fieldname = '".$nome_campo."') AND tabid = ".$tabid_modulo;
			
			$result_query = $adb->query($query);
			$num_result = $adb->num_rows($result_query);

			if( $num_result == 0 ){
				
				$result = false;
				
			}

		}

		return $result;

	}

	static function getModuloTabid($nome_modulo) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione restituisce il tabid di un modulo
		 */

		$result = 0;
	
		$query = "SELECT 
					tabid 
					FROM {$table_prefix}_tab 
					WHERE name = '".$nome_modulo."'"; 
			
		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){
			
			$tabid = $adb->query_result($result_query, 0, 'tabid');
			$tabid = html_entity_decode(strip_tags($tabid), ENT_QUOTES, $default_charset);

			$result = $tabid;
			
		}

		return $result;

	}

	static function esistePickingList($nome_campo) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione verifica l'esistenza o meno di una picking-list all'interno del CRM
		 */

		$result = true;

		$query = "SELECT 
					fieldid 
					FROM {$table_prefix}_field 
					WHERE fieldname = '".$nome_campo."' AND uitype IN (15, 33)";
		
		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result == 0 ){
				
			$result = false;
			
		}

		return $result;

	}

	static function correzioneDbType($nome_modulo, $nome_campo, $tipo_campo){
    	global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione corregge il type dei campi numerici
		 */

		$tabid_modulo = self::getModuloTabid($nome_modulo);

		if( $tabid_modulo != 0 ){

			$query = "SELECT 
						tablename
						FROM {$table_prefix}_field 
						WHERE columnname = '".$nome_campo."' AND tabid = ".$tabid_modulo;
			
			$result_query = $adb->query($query);
			$num_result = $adb->num_rows($result_query);

			if( $num_result > 0 ){
				
				$tablename = $adb->query_result($result_query, 0, 'tablename');
				$tablename = html_entity_decode(strip_tags($tablename), ENT_QUOTES, $default_charset);
				
			}

			if($tablename != ""){
		
				$update = "ALTER TABLE ".$tablename." CHANGE ".$nome_campo." ".$nome_campo." ".$tipo_campo;
				
				$adb->query($update);
				
				printf("\nAggiornato campo: %s", $campo);
				
			}

		}

	}

	static function getBloccoModulo($nome_modulo){
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Recupera il primo blocco del modulo
		 */

		$result = "";

		$tabid_modulo = self::getModuloTabid($nome_modulo);

		if( $tabid_modulo != 0 ){

			$query = "SELECT 
						blocklabel 
						FROM {$table_prefix}_blocks 
						WHERE tabid = ".$tabid_modulo." 
						ORDER BY blockid ASC";
			
			$result_query = $adb->query($query);
			$num_result = $adb->num_rows($result_query);

			if( $num_result > 0 ){
				
				$blocklabel = $adb->query_result($result_query, 0, 'blocklabel');
				$blocklabel = html_entity_decode(strip_tags($blocklabel), ENT_QUOTES, $default_charset);

				$result = $blocklabel;
				
			}

		}

		return $result;

	}

	static function registraRelated($nome_modulo1, $nome_modulo2, $nome_related = "", $azioni = "", $tipo_related = ""){
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione gestisce la creazione di nuove relate verificando che queste già non siano presenti nel CRM
		 */

		if( !self::esisteRelated($nome_modulo1, $nome_modulo2) ){

			$modulo1Instance = Vtiger_Module::getInstance($nome_modulo1);
			$modulo2Instance = Vtiger_Module::getInstance($nome_modulo2);

			if($nome_related == ""){
				$nome_related = $nome_modulo2;
			}

			if($tipo_related == ""){
				$tipo_related = self::getTipoRelatedStandard($nome_modulo1, $nome_modulo2);
			}

			if($azioni == ""){
				$azioni = array("ADD", "SELECT");
			}

			$modulo1Instance->setRelatedList($modulo2Instance, $nome_related, $azioni, $tipo_related);

			self::log("Creata related tra il modulo ".$nome_modulo1." e il modulo ".$nome_modulo2);
		
		}
		else{

			self::log("Impossibile creare la related tra il modulo ".$nome_modulo1." e il modulo ".$nome_modulo2." --> Motivo: Related già esistente");
		
		}
	
	}

	static function esisteRelated($nome_modulo1, $nome_modulo2) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione verifica l'esistenza o meno di una related all'interno del CRM
		 */

		$result = true;

		$tabid_modulo1 = self::getModuloTabid($nome_modulo1);
		$tabid_modulo2 = self::getModuloTabid($nome_modulo2);

		if( $tabid_modulo1 != 0 && $tabid_modulo2 != 0){

			$query = "SELECT 
						relation_id 
						FROM {$table_prefix}_relatedlists 
						WHERE tabid = ".$tabid_modulo1." AND related_tabid = ".$tabid_modulo2;
			
			$result_query = $adb->query($query);
			$num_result = $adb->num_rows($result_query);

			if( $num_result == 0 ){
					
				$result = false;
				
			}

		}

		return $result;

	}

	static function getTipoRelatedStandard($nome_modulo1, $nome_modulo2){
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione restituisce alcuni tipi related particolari
		 */

		$result = "get_related_list";

		/*if( $nome_modulo2 == "Products" ){
			$result = "get_products";
			return $result;
		}*/

		if( $nome_modulo2 == "Documents" ){
			$result = "get_attachments";
			return $result;
		}

		if( $nome_modulo2 == "Messages" ){
			$result = "get_messages_list";
			return $result;
		}

		if( $nome_modulo2 == "Calendar" ){
			$result = "get_activities";
			return $result;
		}

		/*if( $nome_modulo2 == "HelpDesk" ){
			$result = "get_tickets";
			return $result;
		}*/

		if( $nome_modulo2 == "Campaigns" ){
			$result = "get_campaigns_newsletter";
			return $result;
		}

		/*if( $nome_modulo2 == "Contacts" ){
			$result = "get_contacts";
			return $result;
		}

		if( $nome_modulo2 == "SalesOrder" ){
			$result = "get_salesorder";
			return $result;
		}

		if( $nome_modulo2 == "Invoice" ){
			$result = "get_invoices";
			return $result;
		}*/

		if( $nome_modulo2 == "Users" ){
			$result = "get_users";
			return $result;
		}

		if( $nome_modulo2 == "Sms" ){
			$result = "get_sms";
			return $result;
		}

		if( $nome_modulo2 == "Fax" ){
			$result = "get_faxes";
			return $result;
		}

		/*if( $nome_modulo2 == "Timecards" ){
			$result = "get_timecards";
			return $result;
		}

		if( $nome_modulo2 == "Newsletter" ){
			$result = "get_newsletter";
			return $result;
		}

		if( $nome_modulo2 == "Services" ){
			$result = "get_services";
			return $result;
		}*/

		if( $nome_modulo1 == "Documents" ){
			$result = "get_documents_dependents_list";
			return $result;
		}

		return $result;

	}

	static function registraModuloNelCalendario($nome_modulo) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione aggiunge nel calendatio la possibilità di relazionare un evento o un compito a tale modulo
		 */

		$query = "SELECT 
					fie.fieldid fieldid 
					FROM {$table_prefix}_tab tab
					INNER JOIN {$table_prefix}_field fie ON fie.tabid = tab.tabid
					WHERE tab.name = 'Calendar' AND fie.fieldname = 'parent_id'";

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){
			
			$fieldid = $adb->query_result($result_query, 0, 'fieldid');
			$fieldid = html_entity_decode(strip_tags($fieldid), ENT_QUOTES, $default_charset);

			$query_verifica = "SELECT *FROM 
								{$table_prefix}_fieldmodulerel 
								WHERE module = 'Calendar' AND relmodule = '".$nome_modulo."' AND fieldid = ".$fieldid;
			
			$result_query_verifica = $adb->query($query_verifica);
			$num_result_verifica = $adb->num_rows($result_query_verifica);

			if( $adb->num_rows($num_result_verifica) == 0 ){

				$insert = "INSERT INTO {$table_prefix}_fieldmodulerel
							(fieldid, module, relmodule)
							VALUES 
							(".$fieldid.", 'Calendar', '".$nome_modulo."')";
				$adb->query($insert);
		
			}

		}

	}

	static function getFiltroId($nome_modulo, $nome_filtro = "All") {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione recupera l'id del filtro
		 */

		$result = 0;

		$query = "SELECT 
					cvid
					FROM {$table_prefix}_customview 
					WHERE viewname = '".$nome_filtro."' AND entitytype = '".$nome_modulo."'";

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){

			$cvid = $adb->query_result($result_query, 0, 'cvid');
			$cvid = html_entity_decode(strip_tags($cvid), ENT_QUOTES, $default_charset);

			$result = $cvid;

		}
		
		return $result;

	}

	static function getCampoId($modulo, $nome_campo) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione recupera l'id del campo
		 */

		$result = 0;

		$query = "SELECT 
					fieldid
					FROM {$table_prefix}_field
					WHERE fieldname = '".$nome_campo."' AND tabid = ".$modulo;
		
		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){

			$fieldid = $adb->query_result($result_query, 0, 'fieldid');
			$fieldid = html_entity_decode(strip_tags($fieldid), ENT_QUOTES, $default_charset);

			$result = $fieldid;

		}
		
		return $result;

	}

	static function creaFiltro($nome_modulo, $nome_filtro = "All", $elenco_campi = array()) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione crea/ ricrea il filtro secondo le specifiche
		 */

		$tabid_modulo = self::getModuloTabid($nome_modulo);

		$modulo = Vtiger_Module::getInstance($tabid_modulo);

		if( $nome_filtro == "" ){

			$nome_filtro = "All";

		}

		$id_filtro = self::getFiltroId($nome_modulo, $nome_filtro);

		$filtro = Vtiger_Filter::getInstance($id_filtro);

		if( $modulo && $filtro ){

			$filtro->deleteForModule($modulo);

		}

		if( $modulo ){
			
			$nuovo_filtro = new Vtiger_Filter();
			$nuovo_filtro->name = $nome_filtro;
			if($nome_filtro == "All"){
				$nuovo_filtro->isdefault = true;
			}
			$modulo->addFilter($nuovo_filtro);

			if( count($elenco_campi) == 0 ){
				$lista_campi_principali = self::getCampoPrincipaleModulo($tabid_modulo);
				$lista_campi_base = array("createdtime", "modifiedtime", "assigned_user_id");
				$elenco_campi = array_merge($lista_campi_principali, $lista_campi_base);
			}

			$posizione = 1;

			foreach( $elenco_campi as $nome_campo ){

				$id_campo = self::getCampoId($tabid_modulo, $nome_campo);
				$campo = Vtiger_Field::getInstance($id_campo);

				if($campo){

					$nuovo_filtro->addField($campo, $posizione);
					$posizione++;

				}

			}

			self::log("Creato filtro ".$nome_filtro." per il modulo ".$nome_modulo);

		}
		else{

			self::log("Impossibile creare filtro ".$nome_filtro." --> Motivo: Modulo ".$nome_modulo." non esistente");

		}

	}

	static function aggiungiAlFiltro($nome_modulo, $nome_filtro = "All", $elenco_campi = array()) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione aggiunge i campi al filtro secondo le specifiche
		 */

		$tabid_modulo = self::getModuloTabid($nome_modulo);

		$modulo = Vtiger_Module::getInstance($tabid_modulo);

		if( $nome_filtro == "" ){

			$nome_filtro = "All";

		}

		$id_filtro = self::getFiltroId($nome_modulo, $nome_filtro);

		$filtro = Vtiger_Filter::getInstance($id_filtro);

		if( $modulo && $filtro ){

			$posizione = self::getPosizioneLiberaFiltro($id_filtro);
			
			foreach( $elenco_campi as $nome_campo ){

				$id_campo = self::getCampoId($tabid_modulo, $nome_campo);
				$campo = Vtiger_Field::getInstance($id_campo);

				if($campo){

					$filtro->addField($campo, $posizione);
					$posizione++;

				}

			}

			self::log("Aggiunto campi al filtro ".$nome_filtro." per il modulo ".$nome_modulo);

		}
		else{

			if( !$modulo && !$filtro ){
				self::log("Impossibile aggiungere campi al filtro ".$nome_filtro." --> Motivo: Modulo ".$nome_modulo." e filtro ".$nome_filtro." non esistenti");
			}
			elseif( !$modulo ){
				self::log("Impossibile aggiungere campi al filtro ".$nome_filtro." --> Motivo: Modulo ".$nome_modulo." non esistente");
			}
			elseif( !$filtro ){
				self::log("Impossibile aggiungere campi al filtro ".$nome_filtro." --> Motivo: Filtro ".$nome_filtro." non esistente");
			}

		}

	}

	static function aggiornaFiltro($nome_modulo, $nome_filtro = "All", $elenco_campi = array()) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione aggiorna il filtro secondo le specifiche
		 */

		$tabid_modulo = self::getModuloTabid($nome_modulo);

		$modulo = Vtiger_Module::getInstance($tabid_modulo);

		if( $nome_filtro == "" ){

			$nome_filtro = "All";

		}

		$id_filtro = self::getFiltroId($nome_modulo, $nome_filtro);

		$filtro = Vtiger_Filter::getInstance($id_filtro);

		if( $modulo && $filtro ){

			self::svuotaFiltro($id_filtro);

			$posizione = 1;
			
			foreach( $elenco_campi as $nome_campo ){

				$id_campo = self::getCampoId($tabid_modulo, $nome_campo);
				$campo = Vtiger_Field::getInstance($id_campo);

				if($campo){

					$filtro->addField($campo, $posizione);
					$posizione++;

				}

			}

			self::log("Aggiornato il filtro ".$nome_filtro." per il modulo ".$nome_modulo);

		}
		else{

			if( !$modulo && !$filtro ){
				self::log("Impossibile aggiornare il filtro ".$nome_filtro." --> Motivo: Modulo ".$nome_modulo." e filtro ".$nome_filtro." non esistenti");
			}
			elseif( !$modulo ){
				self::log("Impossibile aggiornare il filtro ".$nome_filtro." --> Motivo: Modulo ".$nome_modulo." non esistente");
			}
			elseif( !$filtro ){
				self::log("Impossibile aggiornare il filtro ".$nome_filtro." --> Motivo: Filtro ".$nome_filtro." non esistente");
			}

		}

	}

	static function svuotaFiltro($filtro) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione svuota il filtro
		 */

		$delete = "DELETE FROM {$table_prefix}_cvcolumnlist 
					WHERE cvid = ".$filtro;
		
		$adb->query($delete);

	}

	static function getPosizioneLiberaFiltro($filtro) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione recupera la prima posizione libera del filtro
		 */

		$result = 1;

		$query = "SELECT 
					COALESCE( MAX(columnindex), 0) ultima_posizione 
					FROM {$table_prefix}_cvcolumnlist 
					WHERE cvid = ".$filtro;
		
		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){

			$ultima_posizione = $adb->query_result($result_query, 0, 'ultima_posizione');
			$ultima_posizione = html_entity_decode(strip_tags($ultima_posizione), ENT_QUOTES, $default_charset);

			$result = $ultima_posizione + 1;

		}
		
		return $result;

	}

	static function getCampoPrincipaleModulo($modulo) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione recupera il campo principale del modulo
		 */

		$result = array();

		$query = "SELECT 
					fieldname 
					FROM {$table_prefix}_entityname 
					WHERE tabid = ".$modulo;
		
		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){

			$fieldname = $adb->query_result($result_query, 0, 'fieldname');
			$fieldname = html_entity_decode(strip_tags($fieldname), ENT_QUOTES, $default_charset);

			$result = split(",", $fieldname); ;

		}

		return $result;

	}

	static function registraModulo($nome_modulo, $label_modulo, $label_modulo_singolare, $tipo_campo, $nome_campo, $label_campo, $privilegi = "Private", $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione gestisce la creazione di nuovi moduli verificando che questi già non siano presenti nel CRM
		 * e che vi siano tutti i file necessari per la corretta creazione; diversamente dalla funzione di registrazione modulo da file
		 * tale funzione effettua la creazio del modulo senza necessita di file vtlib o SDK
		 */

		if( $nome_modulo != "" ){

			$modulo_file_presente = self::filePresente("modules/".$nome_modulo."/".$nome_modulo.".php");

			if( !self::esisteModulo($nome_modulo) ){

				if( $modulo_file_presente ){

					//Esegui la registrazione del modulo

					global $enterprise_current_build;

					// Create module instance and save it first
					$module = new Vtiger_Module();
					$module->name = $nome_modulo;
					$module->save();

					// Initialize all the tables required
					$module->initTables();

					// Add the module to the Menu (entry point from UI)
					$menu = Vtiger_Menu::getInstance('Inventory');
					$menu->addModule($module);

					// Add panels (only for VTE >= 16)
					if ( $enterprise_current_build >= 1405 ) {
						$panel1 = new Vtiger_Panel();
						$panel1->label = 'LBL_PANEL_MAIN';
						$module->addPanel($panel1);
					}

					// Add the basic module block
					$block1 = new Vtiger_Block();
					$block1->label = "LBL_".strtoupper($nome_modulo)."_INFORMATION";
					$module->addBlock($block1);

					// Add custom block (required to support Custom Fields)
					$block2 = new Vtiger_Block();
					$block2->label = 'LBL_CUSTOM_INFORMATION';
					$module->addBlock($block2);

					// Add description block (required to support Description)
					$block3 = new Vtiger_Block();
					$block3->label = 'LBL_DESCRIPTION_INFORMATION';
					$module->addBlock($block3);

					/** Create required fields and add to the block */
					if( $tipo_campo == "Numeratore" ){
						$field1 = new Vtiger_Field();
						$field1->name = $nome_campo;
						$field1->table = $module->basetable;
						$field1->column = $nome_campo;
						$field1->label= $label_campo;
						$field1->columntype = 'varchar(255)';
						$field1->uitype = 4;
						$field1->typeofdata = 'V~O';
						$field1->quickcreate = 3;
						$block1->addField($field1);
					}
					else{
						$field1 = new Vtiger_Field();
						$field1->name = $nome_campo;
						$field1->table = $module->basetable;
						$field1->column = $nome_campo;
						$field1->label= $label_campo;
						$field1->columntype = 'varchar(255)';
						$field1->uitype = 1;
						$field1->typeofdata = 'V~M';
						$field1->quickcreate = 0;
						$block1->addField($field1);
					}

					// Set at-least one field to identifier of module record
					$module->setEntityIdentifier($field1);

					$field2 = new Vtiger_Field();
					$field2->name = 'description';
					$field2->table = $table_prefix.'_crmentity';
					$field2->label = 'Description';
					$field2->uitype = 19;
					$field2->typeofdata = 'V~O';
					$block3->addField($field2); 

					/** Common fields that should be in every module, linked to vtiger CRM core table */

					$field3 = new Vtiger_Field();
					$field3->name = 'assigned_user_id';
					$field3->label = 'Assigned To';
					$field3->table = $table_prefix.'_crmentity';
					$field3->column = 'smownerid';
					$field3->uitype = 53;
					$field3->typeofdata = 'V~M';
					$field3->quickcreate = 0;
					$block1->addField($field3);

					$field4 = new Vtiger_Field();
					$field4->name = 'createdtime';
					$field4->label= 'Created Time';
					$field4->table = $table_prefix.'_crmentity';
					$field4->column = 'createdtime';
					$field4->uitype = 70;
					$field4->typeofdata = 'T~O';
					$field4->displaytype= 2;
					$block1->addField($field4);

					$field5 = new Vtiger_Field();
					$field5->name = 'modifiedtime';
					$field5->label= 'Modified Time';
					$field5->table = $table_prefix.'_crmentity';
					$field5->column = 'modifiedtime';
					$field5->uitype = 70;
					$field5->typeofdata = 'T~O';
					$field5->displaytype= 2;
					$block1->addField($field5);
					
					self::creaFiltro($nome_modulo);

					if( $privilegi == "public" ){
						$module->setDefaultSharing('Public');
					}
					else{
						$module->setDefaultSharing('Private');
					}

					if( $import_export ){
						$module->enableTools(Array('Import', 'Export'));
					}

					if( !$merge ){
						$module->disableTools('Merge');
					}

					//Per aggiungere il supporto ai webservices
					$module->initWebservice();

					$SDKdir = 'modules/SDK/'; 
					$moduleInstance = Vtiger_Module::getInstance('SDK'); 
					if ( !empty($moduleInstance) ) { 
						
						SDK::clearSessionValues();

						SDK::setLanguageEntries($nome_modulo, "LBL_".strtoupper($nome_modulo)."_INFORMATION", array('it_it' => 'Informazione '.$label_modulo_singolare,'en_us' => 'Informazione '.$label_modulo_singolare));
						SDK::setLanguageEntries($nome_modulo, "SINGLE_".$nome_modulo." Informazioni", array('it_it' => 'Informazione '.$label_modulo_singolare,'en_us' => 'Informazione '.$label_modulo_singolare));
						SDK::setLanguageEntries($nome_modulo, "SINGLE_".$nome_modulo, array('it_it' => $label_modulo_singolare,'en_us'=> $label_modulo_singolare));
						SDK::setLanguageEntries($nome_modulo, $nome_modulo, array('it_it' => $label_modulo,'en_us' => $label_modulo));
						SDK::setLanguageEntries($nome_modulo, "LBL_PANEL_MAIN", array('it_it'=>'Informazioni','en_us'=>'Informazioni'));

						$ModCommentsModuleInstance = Vtiger_Module::getInstance('ModComments');
						if ($ModCommentsModuleInstance) {
							$ModCommentsFocus = CRMEntity::getInstance('ModComments');
							$ModCommentsFocus->addWidgetTo($nome_modulo);
						}

						$ChangeLogModuleInstance = Vtiger_Module::getInstance('ChangeLog');
						if ($ChangeLogModuleInstance) {
							$ChangeLogFocus = CRMEntity::getInstance('ChangeLog');
							$ChangeLogFocus->enableWidget($nome_modulo);
						}

						$ModNotificationsModuleInstance = Vtiger_Module::getInstance('ModNotifications');
						if ($ModNotificationsModuleInstance) {
							$ModNotificationsCommonFocus = CRMEntity::getInstance('ModNotifications');
							$ModNotificationsCommonFocus->addWidgetTo($nome_modulo);
						}

						$MyNotesModuleInstance = Vtiger_Module::getInstance('MyNotes');
						if ($MyNotesModuleInstance) {
							$MyNotesCommonFocus = CRMEntity::getInstance('MyNotes');
							$MyNotesCommonFocus->addWidgetTo($nome_modulo);
						}

						if( $calendario ){
							self::registraRelated($nome_modulo, "Calendar", "Activities", array("ADD", "SELECT"), "get_activities");
							self::registraModuloNelCalendario($nome_modulo);
						}

						if( $processi ){
							$ProcessesFocus = CRMEntity::getInstance('Processes');
							$ProcessesFocus->enable($nome_modulo);
						}

						if( $homeview ){
							require_once('include/utils/ModuleHomeView.php');
							$MHW = ModuleHomeView::install($nome_modulo);
						}

						if( $messaggi ){
							self::registraRelated($nome_modulo, "Messages", "Messages", array("ADD"), "get_messages_list");
						}

						if( $documenti ){
							self::registraRelated($nome_modulo, "Documents", "Documents", array("ADD", "SELECT"), "get_attachments");
							self::registraRelated("Documents", $nome_modulo, $nome_modulo, array("ADD", "SELECT"), "get_documents_dependents_list");
						}

					}

					self::log("Creato modulo ".$nome_modulo);

				}
				else{

					self::log("Impossibile creare modulo ".$nome_modulo." --> Motivo: manca il file modules/".$nome_modulo."/".$nome_modulo.".php");

				}

			}
			else{

				self::log("Impossibile creare modulo ".$nome_modulo." --> Motivo: Modulo già esistente");

			}

		}
		else{

			self::log("Impossibile creare modulo --> Motivo: Non è stato indicato il nome del modulo");

		}

	}

	static function registraPulsante($nome_modulo, $nome_pulsante, $tipo_pulsante, $funzione, $icona = "") {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione gestisce la creazione di nuovi pulsanti verificando che questi già non siano presenti nel CRM
		 */

		$tipo_pulsante_options = array("index", "ListView", "DetailView", "Menu ALTRO");

		if( in_array($tipo_pulsante, $tipo_pulsante_options) ) {

			if( $tipo_pulsante == "Menu ALTRO" ){

				self::registraPulsanteMenuAltro($nome_modulo, $nome_pulsante, $tipo_pulsante, $funzione);

			}
			else{

				self::registraPulsanteHeader($nome_modulo, $nome_pulsante, $tipo_pulsante, $funzione, $icona);

			}

		}

	}

	static function registraPulsanteHeader($nome_modulo, $nome_pulsante, $tipo_pulsante, $funzione, $icona) { 
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione gestisce la creazione di nuovi pulsanti nell'Header verificando che questi già non siano presenti nel CRM
		 */

		if( !self::esistePulsanteHeader($nome_modulo, $nome_pulsante, $tipo_pulsante) ){
			
			$id = $adb->getUniqueID("sdk_menu_contestual");
			
			$insert = "INSERT INTO sdk_menu_contestual
						(id, module, action, title, onclick, image)
						VALUES
						(".$id.", '".$nome_modulo."', '".$tipo_pulsante."', '".$nome_pulsante."', '".$funzione."', '".$icona."')";
			$adb->query($insert);
		
			self::log("Creato pulsante ".$nome_pulsante." in ".$tipo_pulsante." del modulo ".$nome_modulo);

		}
		else{

			self::log("Impossibile creare pulsante ".$nome_pulsante." in ".$tipo_pulsante." del modulo ".$nome_modulo." --> Motivo: Pulsante già esistente");

		}

	}

	static function esistePulsanteHeader($nome_modulo, $nome_pulsante, $tipo_pulsante) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione verifica l'esistenza o meno di un pulsante header nel CRM
		 */

		$result = false;

		$query = "SELECT 
					id 
					FROM sdk_menu_contestual 
					WHERE module = '".$nome_modulo."' AND action = '".$tipo_pulsante."' AND title = '".$nome_pulsante."'";
		
		$result_query = $adb->query($query);
   		$num_result = $adb->num_rows($result_query);

		if($num_result > 0){

			$result = true;

		}

		return $result;

	}

	static function registraPulsanteMenuAltro($nome_modulo, $nome_pulsante, $tipo_pulsante, $funzione) { 
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione gestisce la creazione di nuovi pulsanti nel menu altro verificando che questi già non siano presenti nel CRM
		 */

		$tabid_modulo = self::getModuloTabid($nome_modulo);

		if( !self::esistePulsanteMenuAltro($tabid_modulo, $nome_pulsante) && $tabid_modulo != 0 ){

			Vtiger_Link::addLink($tabid_modulo, 'DETAILVIEWBASIC', $nome_pulsante, $funzione);

			self::log("Creato pulsante ".$nome_pulsante." in ".$tipo_pulsante." del modulo ".$nome_modulo);

		}
		else{

			self::log("Impossibile creare pulsante ".$nome_pulsante." in ".$tipo_pulsante." del modulo ".$nome_modulo." --> Motivo: Pulsante già esistente");

		}

	}

	static function esistePulsanteMenuAltro($modulo, $nome_pulsante) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione verifica l'esistenza o meno di un pulsante nel menu altro nel CRM
		 */

		$result = false;

		$query = "SELECT 
					linkid 
					FROM {$table_prefix}_links 
					WHERE tabid = ".$modulo." AND linktype = 'DETAILVIEWBASIC' AND linklabel = '".$nome_pulsante."'";
		
		$result_query = $adb->query($query);
   		$num_result = $adb->num_rows($result_query);

		if($num_result > 0){

			$result = true;

		}

		return $result;

	}

	static function registraFile($file) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione gestisce la registrazione di file verificando che questi già non siano presenti nel CRM
		 */

		if( !self::fileGiaRegistrato($file) ){

			$moduleInstance = Vtiger_Module::getInstance('SDK');

			Vtiger_Link::addLink($moduleInstance->id, 'HEADERSCRIPT', 'SDKScript', $file);

		}
		else{

			self::log("Impossibile registrare il file ".$file." --> Motivo: File già registrato");

		}

	}

	static function fileGiaRegistrato($file) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione verifica se un file è già registrato o meno
		 */

		$result = false;

		$query = "SELECT 
					linkid 
					FROM {$table_prefix}_links 
					WHERE linktype = 'HEADERSCRIPT' AND linkurl = '".$file."'";
		
		$result_query = $adb->query($query);
   		$num_result = $adb->num_rows($result_query);

		if($num_result > 0){

			$result = true;

		}

		return $result;

	}

	static function registraEstensioneClasse($nome_modulo, $tipo_estenzione = "Standard", $percorso_core = "modules/SproCore/") {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione gestisce la registrazione l'estenzione di classe verificando che questi già non siano presenti nel CRM
		 */

		$tipo_estenzione_options = array("Standard", "Custom");

		if( !in_array($tipo_estenzione, $tipo_estenzione_options) ) {

			$tipo_estenzione = "Standard";

		}

		$tabid_modulo = self::getModuloTabid($nome_modulo);

		if( $tabid_modulo != 0 ){

			if( $tipo_estenzione == "Standard" ){

				self::registraEstensioneClasseStandard($nome_modulo, $percorso_core);

			}
			else{

				self::registraEstensioneClasseCustom($nome_modulo, $percorso_core);

			}

		}
		else{

			self::log("Impossibile estendere la classe del modulo ".$nome_modulo." --> Motivo: Modulo non esistente");

		}

	}

	static function registraEstensioneClasseStandard($nome_modulo, $percorso_core) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione gestisce la registrazione l'estenzione di classe standard verificando che questi già non siano presenti nel CRM
		 */
		
		$estenzione_classe_standard = self::getEstensioneClasse($nome_modulo);

		if( !$estenzione_classe_standard["esiste"] ){

			if ( !is_dir($percorso_core.$nome_modulo) ) {
				
				mkdir($percorso_core.$nome_modulo, 0755);
				chown($percorso_core.$nome_modulo, "www-data");
				chgrp($percorso_core.$nome_modulo, "www-data");

			}

			if ( !file_exists($percorso_core.$nome_modulo."/Class".$nome_modulo."Kp.php") ) {

				self::generaFileEstensioneStandard($nome_modulo, $percorso_core);
			
			}

			$classid = $adb->getUniqueID("sdk_class");

			$insert = "INSERT INTO sdk_class
						(id, extends, module, src)
						VALUES
						(".$classid.", '".$nome_modulo."', '".$nome_modulo."Kp', '".$percorso_core.$nome_modulo."/Class".$nome_modulo."Kp.php')";
			$adb->query($insert);

			self::log("Registrata estensione della classe ".$nome_modulo);

		}
		else{

			self::log("Impossibile estendere la classe del modulo ".$nome_modulo." --> Motivo: Classe già estesa");

		}

	}

	static function getEstensioneClasse($nome_modulo) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione verifica l'esistenza o meno di una estensione di classe all'interno del CRM
		 */

		$result = "";

		$query = "SELECT 
					id,
					module,
					src
					FROM sdk_class 
					WHERE extends = '".$nome_modulo."'";
		
		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){

			$esiste = true;
				
			$id = $adb->query_result($result_query, 0, 'id');
			$id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

			$module = $adb->query_result($result_query, 0, 'module');
			$module = html_entity_decode(strip_tags($module), ENT_QUOTES, $default_charset);

			$src = $adb->query_result($result_query, 0, 'src');
			$src = html_entity_decode(strip_tags($src), ENT_QUOTES, $default_charset);
			
		}
		else{

			$esiste = false;
				
			$id = 0;

			$module = "";

			$src = "";

		}

		$result = array("esiste" => $esiste,
						"id" => $id,
						"module" => $module,
						"src" => $src);

		return $result;

	}

	static function generaFileEstensioneStandard($nome_modulo, $percorso_core) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione crea il file di estensione della classe standard
		 */

		$nome_file = "Class".$nome_modulo."Kp.php";
		$percorso_file = $percorso_core.$nome_modulo;

		$testo_file = "<?php \n\n";
		$testo_file .= "/* kpro@".date("YmdHis")." */ \n\n";
		$testo_file .= "/** \n";
		$testo_file .= " * @copyright (c) ".date("Y").", Kpro Consulting Srl \n";
		$testo_file .= " * \n";
		$testo_file .= " * Estensione classe ".$nome_modulo." \n";
		$testo_file .= " */ \n\n";
		$testo_file .= "require_once('modules/".$nome_modulo."/".$nome_modulo.".php'); \n\n";
		$testo_file .= "class ".$nome_modulo."Kp extends ".$nome_modulo." { \n\n";

		$testo_file .= "\n\n";

		$testo_file .= "} \n\n";
		$testo_file .= "?>";
		
		$class_file = fopen($percorso_file."/".$nome_file, "x+");
		fwrite($class_file, $testo_file);
		fclose($class_file);

		chown($percorso_file."/".$nome_file, "www-data");
		chgrp($percorso_file."/".$nome_file, "www-data");
		chmod($percorso_file."/".$nome_file, 0755);

	}

	static function generaFileEstensioneCustom($nome_modulo) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione crea il file di estensione della classe standard
		 */

		$estenzione_classe_standard = self::getEstensioneClasse($nome_modulo);

		$nome_file = "Class".$nome_modulo."KpC.php";
		$percorso_file = "modules/SDK/src/".$nome_modulo;

		$testo_file = "<?php \n\n";
		$testo_file .= "/* kpro@".date("YmdHis")." */ \n\n";
		$testo_file .= "/** \n";
		$testo_file .= " * @copyright (c) ".date("Y").", Kpro Consulting Srl \n";
		$testo_file .= " * \n";
		$testo_file .= " * Estensione custom classe ".$estenzione_classe_standard["module"]." \n";
		$testo_file .= " */ \n\n";
		$testo_file .= "require_once('".$estenzione_classe_standard["src"]."'); \n\n";
		$testo_file .= "class ".$nome_modulo."KpC extends ".$estenzione_classe_standard["module"]." { \n\n";

		$testo_file .= "\n\n";

		$testo_file .= "} \n\n";
		$testo_file .= "?>";
		
		$class_file = fopen($percorso_file."/".$nome_file, "x+");
		fwrite($class_file, $testo_file);
		fclose($class_file);

		chown($percorso_file."/".$nome_file, "www-data");
		chgrp($percorso_file."/".$nome_file, "www-data");
		chmod($percorso_file."/".$nome_file, 0755);

	}

	static function registraEstensioneClasseCustom($nome_modulo, $percorso_core) {
		global $adb, $table_prefix, $default_charset;

		/* kpro@tom09062017 */

		/**
		 * @author Tomiello Marco
		 * @copyright (c) 2017, Kpro Consulting Srl
		 *
		 * Questa funzione gestisce la registrazione l'estenzione di classe custom verificando che questi già non siano presenti nel CRM
		 */
		
		$estenzione_classe_standard = self::getEstensioneClasse($nome_modulo);

		if( $estenzione_classe_standard["esiste"] ){

			$estenzione_classe_custom = self::getEstensioneClasse($estenzione_classe_standard["module"]);

			if( !$estenzione_classe_custom["esiste"] ){

				if ( !is_dir("modules/SDK/src/".$nome_modulo) ) {
				
					mkdir("modules/SDK/src/".$nome_modulo, 0755);
					chown("modules/SDK/src/".$nome_modulo, "www-data");
					chgrp("modules/SDK/src/".$nome_modulo, "www-data");

				}

				if ( !file_exists("modules/SDK/src/".$nome_modulo."/Class".$nome_modulo."KpC.php") ) {

					self::generaFileEstensioneCustom($nome_modulo);
				
				}

				$classid = $adb->getUniqueID("sdk_class");

				$insert = "INSERT INTO sdk_class
							(id, extends, module, src)
							VALUES
							(".$classid.", '".$estenzione_classe_standard["module"]."', '".$nome_modulo."KpC', 'modules/SDK/src/".$nome_modulo."/Class".$nome_modulo."KpC.php')";
				$adb->query($insert);

				self::log("Registrata estensione custom della classe ".$nome_modulo);

			}
			else{

				self::log("Impossibile estendere in modo custom la classe del modulo ".$nome_modulo." --> Motivo: Classe custom già estesa");

			}

		}
		else{

			self::registraEstensioneClasseStandard($nome_modulo, $percorso_core);
			self::registraEstensioneClasseCustom($nome_modulo, $percorso_core);

		}

	}

	/* kpro@bid19062017 */
	static function registraModuleHomeCustom($nome_modulo, $nome_tab, $traduzione_nome_tab, $src) {
		global $adb, $table_prefix;

		$tabid_modulo = self::getModuloTabid($nome_modulo);

		if( $tabid_modulo != 0 && $nome_tab != "" && $nome_tab != null && $traduzione_nome_tab != "" && $traduzione_nome_tab != null && $src != "" && $src != null){

			$modulehome_custom = self::getModuleHomeCustom($tabid_modulo, $nome_tab);

			if( !$modulehome_custom["esiste"] ){

				$query_seq = "SELECT * 
					FROM {$table_prefix}_modulehome_seq";
				$res_seq = $adb->query($query_seq);
				$seq = $adb->query_result($res_seq, 0, 'id');

				$modulehome_standard = self::getModuleHomeStandard($tabid_modulo, $nome_tab, null, $seq);

				if( $modulehome_standard[0]["esiste"] == 'no_tabs'){

					self::registraModuleHomeStandard($nome_modulo, $nome_tab, null, "KpSDK");

					$modulehome_standard = self::getModuleHomeStandard($tabid_modulo, $nome_tab, null, $seq);

					if( $modulehome_standard[0]["esiste"] == 'new_tabs'){

						$insert = "INSERT INTO kp_modulehome
									(tabid, name, src)
									VALUES
									(".$tabid_modulo.", '".$nome_tab."', '".$src."')";
						$adb->query($insert);

						$moduleInstance = Vtiger_Module::getInstance('SDK'); 
						if ( !empty($moduleInstance) ) { 
							
							SDK::clearSessionValues();

							SDK::setLanguageEntries('APP_STRINGS', $nome_tab, array('it_it'=>$traduzione_nome_tab,'en_us'=>$traduzione_nome_tab));

						}

						self::log("Registrata tab ".$traduzione_nome_tab);
					}
					else{
						self::log("Impossibile registrare il tab ".$traduzione_nome_tab." --> Motivo: Tab standard già presenti");
					}
				}
				else{
					self::log("Impossibile registrare il tab ".$traduzione_nome_tab." --> Motivo: Esiste già un tab STANDARD con lo stesso nome nello stesso modulo per almeno un utente");
				}			
			}
			else{
				self::log("Impossibile registrare il tab ".$traduzione_nome_tab." --> Motivo: Esiste già un tab CUSTOM con lo stesso nome nello stesso modulo");
			}
		}
		else{
			self::log("Impossibile registrare il tab --> Motivo: Parametri mancanti");
		}
	}

	static function registraModuleHomeStandard($nome_modulo, $nome_tab, $userid, $chiamato_da) {
		global $adb, $table_prefix;
		
		if (!empty($userid)) {

			$users = array($userid);
		} else {

			$users = array();

			$q_users = "SELECT id 
					FROM {$table_prefix}_users";
			$res_users = $adb->query($q_users);

			if ($adb->num_rows($res_users) > 0) {

				while($row = $adb->fetchByAssoc($res_users)) {

					$users[] = $row['id'];		
				}
			}
		}

		if (!empty($nome_modulo)) {

			$tabid_modulo = self::getModuloTabid($nome_modulo);

			if( $tabid_modulo != 0){

				$tabs = array($tabid_modulo);	
			}
		}
		else{
			$q_tabs = "SELECT tabid
					FROM kp_modulehome
					GROUP BY tabid";
			$res_tabs = $adb->query($q_tabs);

			if($adb->num_rows($res_tabs) > 0){

				while($row = $adb->fetchByAssoc($res_tabs)) {

					$tabs[] = $row['tabid'];			
				}
			}
		}

		if (!empty($nome_tab)) {

			foreach($tabs as $tabid) {

				$tab_names[$tabid][] = $nome_tab;
	
			}
		}
		else{
			foreach($tabs as $tabid) {

				$q_names = "SELECT name
						FROM kp_modulehome
						WHERE tabid = ".$tabid;
				$res_names = $adb->query($q_names);

				if($adb->num_rows($res_names) > 0){

					while($row = $adb->fetchByAssoc($res_names)) {
						$tab_names[$tabid][] = $row['name'];		

					}
				}
			}
		}

		$query_seq = "SELECT * 
			FROM {$table_prefix}_modulehome_seq";
		$res_seq = $adb->query($query_seq);
		$seq = $adb->query_result($res_seq, 0, 'id');

		foreach($tabs as $tabid) {

			foreach($tab_names[$tabid] as $name){

				foreach($users as $userid) {

					$modulehome_standard = self::getModuleHomeStandard($tabid, $name, $userid, $seq);

					if( $modulehome_standard[0]["esiste"] == 'no_tabs' ){

						$modhomeid = $adb->getUniqueID("{$table_prefix}_modulehome");

						$insert = "INSERT INTO {$table_prefix}_modulehome 
								(modhomeid,userid,tabid,name) 
								VALUES 
								(".$modhomeid.",".$userid.",".$tabid.",'".$name."')";
						$adb->query($insert);

						if($chiamato_da == 'Users'){
							self::log("CREAZIONE UTENTE ID ".$userid.": Registrata tab ".$name." nella tabella ".$tabid);
						}
					}
					else{
						if($chiamato_da == 'Users'){
							self::log("CREAZIONE UTENTE ID ".$userid.": Impossibile registrare il tab ".$name." nella tabella ".$tabid." --> Motivo: Tab STANDARD già presente");
						}
					}
				}
			}
		}
	}

	static function getModuleHomeStandard($tabid_modulo, $nome_tab, $userid, $seq) {
		global $adb, $table_prefix, $default_charset;

		$result = "";

		$query = "SELECT * 
				FROM {$table_prefix}_modulehome
				WHERE tabid = {$tabid_modulo}
				AND name = '{$nome_tab}'";
		if (!empty($userid)) {
			$query .= " AND userid = ".$userid;
		}
		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){

			$is_old_tab = false;

			for($i = 0; $i < $num_result; $i++){
				
				$modhomeid = $adb->query_result($result_query, $i, 'modhomeid');
				$modhomeid = html_entity_decode(strip_tags($modhomeid), ENT_QUOTES, $default_charset);
				
				$userid = $adb->query_result($result_query, $i, 'userid');
				$userid = html_entity_decode(strip_tags($userid), ENT_QUOTES, $default_charset);

				$tabid = $adb->query_result($result_query, $i, 'tabid');
				$tabid = html_entity_decode(strip_tags($tabid), ENT_QUOTES, $default_charset);

				$name = $adb->query_result($result_query, $i, 'name');
				$name = html_entity_decode(strip_tags($name), ENT_QUOTES, $default_charset);

				if($modhomeid <= $seq){
					$is_old_tab = true;
				}

				$result[] = array("esiste" => "new_tabs",
							"modhomeid" => $modhomeid,
							"userid" => $userid,
							"tabid" => $tabid,
							"name" => $name);
			}

			if($is_old_tab){
				$result = array();
				$result[] = array("esiste" => "old_tabs",
						"modhomeid" => 0,
						"userid" => 0,
						"tabid" => 0,
						"name" => "");
			}
			
		}
		else{

			$result[] = array("esiste" => "no_tabs",
						"modhomeid" => 0,
						"userid" => 0,
						"tabid" => 0,
						"name" => "");

		}

		return $result;

	}

	static function getModuleHomeCustom($tabid_modulo, $nome_tab) {
		global $adb, $table_prefix, $default_charset;

		$result = "";

		$query = "SELECT * 
				FROM kp_modulehome
				WHERE tabid = {$tabid_modulo}
				AND name = '{$nome_tab}'";
		
		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){

			$esiste = true;
				
			$kpmodhomeid = $adb->query_result($result_query, 0, 'kpmodhomeid');
			$kpmodhomeid = html_entity_decode(strip_tags($kpmodhomeid), ENT_QUOTES, $default_charset);

			$tabid = $adb->query_result($result_query, 0, 'tabid');
			$tabid = html_entity_decode(strip_tags($tabid), ENT_QUOTES, $default_charset);

			$name = $adb->query_result($result_query, 0, 'name');
			$name = html_entity_decode(strip_tags($name), ENT_QUOTES, $default_charset);

			$src = $adb->query_result($result_query, 0, 'src');
			$src = html_entity_decode(strip_tags($src), ENT_QUOTES, $default_charset);
			
		}
		else{

			$esiste = false;

			$kpmodhomeid = 0;
				
			$tabid = 0;

			$name = "";

			$src = "";

		}

		$result = array("esiste" => $esiste,
						"kpmodhomeid" => $kpmodhomeid,
						"tabid" => $tabid,
						"name" => $name,
						"src" => $src);

		return $result;

	}
	/* kpro@bid19062017 end */

	/* kpro@bid04082017 */

	static function registraStoricoStati($nome_modulo, $nome_campo_stato, $nome_campo_storico) {
		global $adb, $table_prefix;

		$tabid_modulo = self::getModuloTabid($nome_modulo);

		if( $tabid_modulo != 0 && $nome_campo_stato != "" && $nome_campo_stato != null && $nome_campo_storico != "" && $nome_campo_storico != null){

			$esistenza_gestione_stati_custom = self::controlloGestioneStatiCustom($tabid_modulo);

			if( !$esistenza_gestione_stati_custom ){

				$esistenza_gestione_stati_standard = self::controlloGestioneStatiStandard($nome_modulo, $nome_campo_stato);

				if( $esistenza_gestione_stati_standard ){

					$controllo_campo_storico = self::controlloCampoStorico($tabid_modulo, $nome_campo_storico);

					if( $controllo_campo_storico){

						$insert = "INSERT INTO kp_transitions_fields
									(tabid, module, field, history_field)
									VALUES
									(".$tabid_modulo.", '".$nome_modulo."', '".$nome_campo_stato."', '".$nome_campo_storico."')";
						$adb->query($insert);

						self::log("Registrato storico stati per il modulo ".$nome_modulo." nel campo ".$nome_campo_storico);
					}
					else{
						self::log("Impossibile registrare lo storico stati --> Motivo: Il campo storico deve appartenere al modulo ".$nome_modulo." e deve avere uitype 19, 21 o 210");
					}			
				}
				else{
					self::log("Impossibile registrare lo storico stati --> Motivo: Il campo non è gestito dalla Gestione Stati standard");
				}
			}
			else{
				self::log("Impossibile registrare lo storico stati --> Motivo: Storico stati già registrato per il modulo ".$nome_modulo);
			}
		}
		else{
			self::log("Impossibile registrare lo storico stati --> Motivo: Parametri mancanti");
		}
	}

	static function controlloGestioneStatiCustom($tabid_modulo){
		global $adb, $table_prefix;

		$query = "SELECT * 
				FROM kp_transitions_fields
				WHERE tabid = ".$tabid_modulo;
		$result_query = $adb->query($query);
		if($adb->num_rows($result_query) > 0){
			return true;
		}
		else{
			return false;
		}

	}

	static function controlloGestioneStatiStandard($nome_modulo, $nome_campo_stato){
		global $adb, $table_prefix;

		$query = "SELECT * 
				FROM tbl_s_transitions_fields
				WHERE module = '{$nome_modulo}' 
				AND field = '{$nome_campo_stato}'";
		$result_query = $adb->query($query);
		if($adb->num_rows($result_query) > 0){
			return true;
		}
		else{
			return false;
		}

	}

	static function controlloCampoStorico($tabid_modulo, $nome_campo_storico){
		global $adb, $table_prefix;

		$query = "SELECT *
            FROM {$table_prefix}_field
            WHERE tabid = {$tabid_modulo}
            AND columnname = '{$nome_campo_storico}'
			AND uitype IN (19,21,210)";
		$result_query = $adb->query($query);
		if($adb->num_rows($result_query) > 0){
			return true;
		}
		else{
			return false;
		}

	}

	/* kpro@bid04082017 end */

	/* kpro@tom06102017 */

	static function aggiungiAPickingList($nome_campo, $array_valori){
		global $adb, $table_prefix;

		if( self::checkIfPickingListEsistente($nome_campo) ){

			foreach($array_valori as $valore){

				if( !self::checkIfValorePresenteInPickingList($nome_campo, $valore) ){

					self::setValoreInPickingList($nome_campo, $valore);
					
					self::log("Aggiunto alla picking list ".$nome_campo." il valore ".$valore);

				}

			}

		}
		else{

			self::log("Impossibile aggiungere il valore alla picking-list ".$nome_campo." --> Motivo: picking-list non esistente");

		}


	}

	static function checkIfPickingListEsistente($nome_campo){
		global $adb, $table_prefix;

		$result = false;

		$query = "SELECT 
					fieldid
					FROM {$table_prefix}_field 
					WHERE uitype IN (15, 33) AND fieldname = '".$nome_campo."'";

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){

			$result = true;

		}

		return $result;

	}

	static function checkIfValorePresenteInPickingList($nome_campo, $valore){
		global $adb, $table_prefix;
		
		$result = false;

		$query = "SELECT 
					*
					FROM {$table_prefix}_".$nome_campo."
					WHERE ".$nome_campo." = '".$valore."'";

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){

			$result = true;

		}

		return $result;

	}

	static function setValoreInPickingList($nome_campo, $valore){
		global $adb, $table_prefix;
		
		$seq = self::getPickingListSeq( $nome_campo );

		$new_seq = $seq + 1;

		self::setPickingListSeq( $nome_campo, $new_seq );

		$value_seq = self::getPickingListValueSeq();
		
		$new_value_seq = $value_seq + 1;

		self::setPickingListValueSeq( $new_value_seq );

		if($nome_campo == 'invoicestatus'){
			$nome_campo_id = 'inovicestatusid';
		}
		else{
			$nome_campo_id = $nome_campo.'id';
		}

		$insert = "INSERT INTO {$table_prefix}_".$nome_campo." 
					(".$nome_campo_id.", ".$nome_campo.", presence, picklist_valueid)
					VALUES
					(".$new_seq.", '".$valore."', 0, ".$new_value_seq.")";

		$adb->query($insert);

	}

	static function getPickingListSeq($nome_campo){
		global $adb, $table_prefix;

		$result = "";

		$query = "SELECT 
					id 
					FROM {$table_prefix}_".$nome_campo."_seq";

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){
					
			$id = $adb->query_result($result_query, 0, 'id');
			$id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

		}
		else{

			$id = 0;

		}

		$result = $id;

		return $result;

	}

	static function setPickingListSeq($nome_campo, $seq){
		global $adb, $table_prefix;

		$update = "UPDATE {$table_prefix}_".$nome_campo."_seq SET
					id = ".$seq;

		$adb->query($update);

	}

	static function getPickingListValueSeq(){
		global $adb, $table_prefix;

		$result = "";

		$query = "SELECT 
					id 
					FROM {$table_prefix}_picklistvalues_seq";

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){
					
			$id = $adb->query_result($result_query, 0, 'id');
			$id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

		}
		else{

			$id = 0;

		}

		$result = $id;

		return $result;

	}

	static function setPickingListValueSeq($seq){
		global $adb, $table_prefix;

		$update = "UPDATE {$table_prefix}_picklistvalues_seq SET
					id = ".$seq;

		$adb->query($update);

	}

	/* kpro@tom06102017 end */

	/* kpro@bid17112017 */

	static function sostituisciFileStandard($nome_modulo, $nome_file_standard){
		global $adb, $table_prefix, $root_directory;

		$nome_file_custom = $nome_file_standard.'Kp';

		$tabid_modulo = self::getModuloTabid($nome_modulo);
		
		if( $tabid_modulo != 0 && $nome_file_standard != "" && $nome_file_standard != null){

			$esistenza_file_standard = self::controlloEsistenzaFile($nome_modulo, $nome_file_standard.'.php');

			if( $esistenza_file_standard ){

				$esistenza_set_file = self::controlloEsistenzaSetFile($nome_modulo, $nome_file_standard);

				if( !$esistenza_set_file ){

					$query_seq = "SELECT * 
							FROM sdk_file_seq";
					$res_seq = $adb->query($query_seq);
					$seq = $adb->query_result($res_seq, 0, 'id');
					
					$insert = "INSERT INTO sdk_file 
						(fileid, module, file, new_file)
						VALUES ({$seq}, '{$nome_modulo}', '{$nome_file_standard}', '{$nome_file_custom}')";
					$adb->query($insert);

					$seq++;

					$update_seq = "UPDATE sdk_file_seq
								SET id = {$seq}";
					$adb->query($update_seq);

					self::log("Sostituito file ".$nome_file_standard." con ".$nome_file_custom." ");

					$esistenza_file_custom = self::controlloEsistenzaFile($nome_modulo, $nome_file_custom.'.php');

					if(!$esistenza_file_custom){

						$percorso_file = $root_directory.'modules/'.$nome_modulo;
						$file_standard = $percorso_file.'/'.$nome_file_standard.'.php';
						$file_custom = $percorso_file.'/'.$nome_file_custom.'.php';

						if (copy($file_standard, $file_custom)) {
							chown($file_custom, "www-data");
							chgrp($file_custom, "www-data");
							chmod($file_custom, 0755);

							self::log("Creato file ".$nome_file_custom." ");
						}
					}
				}
				else{
					self::log("Impossibile sostituire il file --> Motivo: Il file ".$nome_file_standard." del modulo ".$nome_modulo." è già stato sostituito da un'altro file");
				}
			}
			else{
				self::log("Impossibile sostituire il file --> Motivo: Il file ".$nome_file_standard." non esiste nel modulo ".$nome_modulo);
			}
		}
		else{
			self::log("Impossibile sostituire il file --> Motivo: Parametri mancanti");
		}
	}

	static function controlloEsistenzaFile($nome_modulo, $nome_file){
		global $adb, $table_prefix, $root_directory;

		$filename = $root_directory.'modules/'.$nome_modulo.'/'.$nome_file;

		if (file_exists($filename)) {
			return true;
		} else {
			return false;
		}
	}

	static function controlloEsistenzaSetFile($nome_modulo, $nome_file_standard){
		global $adb, $table_prefix;

		$q = "SELECT * 
			FROM sdk_file
			WHERE module = '{$nome_modulo}'
			AND file = '{$nome_file_standard}'";
		$res = $adb->query($q);
		if($adb->num_rows($res) > 0){
			return true;
		}
		else{
			return false;
		}
	}

	/* kpro@bid17112017 end */

}