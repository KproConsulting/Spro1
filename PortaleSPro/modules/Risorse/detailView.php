<!DOCTYPE html>

<?php

/* kpro@tom17062016 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package portaleVteSicurezza
 * @version 1.0
 */

require_once("../../PortalConfig.php");
include_once('../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_language;
session_start();

//print_r($_SESSION);die;

$contact_id = 0;
$default_language = "it_it";
if(isset($_SESSION['customer_sessionid'])){
	
    $contact_id = $_SESSION['customer_id'];
    $customer_account_id = $_SESSION['customer_account_id'];
    $sessionid = $_SESSION['customer_sessionid'];
    $default_language = $_SESSION['portal_login_language'];
  
}
else{
	$contact_id = 0;
}

require_once($portal_name.'/string/'.$default_language.'.php');

$app_risorse = false;

if($contact_id != 0){
    
    $q_account = "SELECT accountid,
					kp_app_risorse
					FROM {$table_prefix}_contactdetails 
					WHERE contactid = ".$contact_id;
	$res_account = $adb->query($q_account);
			
	if($adb->num_rows($res_account) > 0){
		
		$azienda = $adb->query_result($res_account, 0, 'accountid');
		$azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES,$default_charset);
		
		$kp_app_risorse = $adb->query_result($res_account, 0, 'kp_app_risorse');
		$kp_app_risorse = html_entity_decode(strip_tags($kp_app_risorse), ENT_QUOTES,$default_charset);
		if($kp_app_risorse == '1'){
			$app_risorse = true;
		}
		else{
			header("Location: ../../login.php"); 
		}
		
	}
	else{
	    header("Location: ../../login.php"); 
	}
	
}
else{
    header("Location: ../../login.php"); 
}

$q_azienda_name = "SELECT accountname 
					FROM {$table_prefix}_account 
					WHERE accountid = ".$azienda;
$res_azienda_name = $adb->query($q_azienda_name);
			
if($adb->num_rows($res_azienda_name)>0){
		
    $accountname = $adb->query_result($res_azienda_name, 0, 'accountname');
    $accountname = html_entity_decode(strip_tags($accountname), ENT_QUOTES,$default_charset);
		
}

if(isset($_REQUEST['record'])){
    $record = $_REQUEST['record'];
}
else{
    $record = 0; 
}

?>

<html>
    <head>
        <title>Portale SPro</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        
        <link rel="icon" type="image/png" href="../../img/S-PRO-FAVICON.ico">
        <link rel="stylesheet" type="text/css" href="../../css/style_detailView.css">
		<link rel="stylesheet" type="text/css" href="../../css/jquery-ui-kpro.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link type="text/css" rel="stylesheet" href="../../css/materialize.min.css"  media="screen,projection"/>
		<!--<link rel="stylesheet" href="../../css/material.min.css">-->
		
		<link rel="stylesheet" type="text/css" href="../../codebase/dhtmlx.css"/>
        <script src="../../codebase/dhtmlx.js"></script>

        <script src="../../js/jquery-2.1.4.min.js"></script>  
		<script src="../../js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="../../js/materialize.min.js"></script>
		<!--<script src="../../js/material.min.js"></script>-->
		<script src="js/general_detailView.js"></script>
		
		<link rel="stylesheet" type="text/css" href="../../css/jquery.datetimepicker.css">
        <script src="../../js/jquery.datetimepicker.full.js"></script>

		<!-- Boostrap DateTime -->
        <script type="text/javascript" src="../../js/moment-with-locales.min.js"></script>
        <link rel="stylesheet" href="../../css/bootstrap-material-datetimepicker.css" />
        <script type="text/javascript" src="../../js/bootstrap-material-datetimepicker.js"></script>
        <!-- Boostrap DateTime end -->
		
        <script type="text/JavaScript">
			
			//Traduzioni
            var string_salva = "<?php echo($string_salva); ?>";
            var string_chiudi = "<?php echo($string_chiudi); ?>";
            var string_prosegui = "<?php echo($string_prosegui); ?>";
            var string_annulla = "<?php echo($string_annulla); ?>";
            var string_termina = "<?php echo($string_termina); ?>";
            var string_risorse = "<?php echo($string_risorse); ?>";         
            var string_azioni = "<?php echo($string_azioni); ?>"; 
            var string_cognome = "<?php echo($string_cognome); ?>"; 
            var string_nome = "<?php echo($string_nome); ?>"; 
            var string_stabilimento = "<?php echo($string_stabilimento); ?>"; 
            var string_email = "<?php echo($string_email); ?>"; 
            var string_telefono = "<?php echo($string_telefono); ?>";    
            var string_stato = "<?php echo($string_stato); ?>";      
            //Traduzioni end
            
            var contact_id = "<?php echo($contact_id); ?>";
            var aziendaid = "<?php echo($azienda); ?>";
            var accountname = "<?php echo($accountname); ?>";
            var indirizzo_crm = "<?php echo($site_URL); ?>";
            var default_language = "<?php echo($default_language); ?>";
            var record = "<?php echo($record); ?>";
			
        </script>   
    </head>
    <body>
        <div id="general">
			
			<!-- sidebar -->
			
            <div id="nav_div">
                <div id="div_torna_indietro">
                    <table id="table_torna_indietro">
                        <tr id="tr_torna_a_precedente">
                            <td class="td_list_img_header">
                                <a id='torna_a_precedente' class='torna_a_precedente'><image class='immagine_indietro' src='../../img/indietro.png' /></a>
                            </td>
                            <td class="td_list_name_header">
                                <span id="torna_indietro">Indietro</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan = '2' class="td_logo" id="div_img_program">
                                <image style="max-widh:150px; max-height:150px" src="../../img/riquadro_risorse.png" />
                            </td>
                        </tr>
                    </table>
                </div> 
                
                <!-- pulsanti laterali -->
                <ul class="list">
					
                    <li id="button_page1" name="Dati Anagrafici">
						<table>
							<tr>
								<td class="td_list_img">
									<image src="img/contacts_40_40.png" />
									<a class="menu_list_number waves-effect waves-circle waves-light btn-floating secondary-content" style="display: none;"></a>
								</td>
								<td class="td_list_name">
									Dati Anagrafici
								</td>
							</tr>
						</table>
					</li>
                    
                    <li id="button_page2" name="Documenti">
						<table>
							<tr>
								<td class="td_list_img">
									<image src="img/documents_40_40.png" />
									<a id="icon_number_documenti" class="menu_list_number waves-effect waves-circle waves-light btn-floating secondary-content" style="display: none;"></a>
								</td>
								<td class="td_list_name">
									Documenti
								</td>
							</tr>
						</table>
					</li>
                    
                    <li id="button_page3" name="Formazione">
						<table>
							<tr>
								<td class="td_list_img">
									<image src="img/formazione_40_40.png" />
									<a id="icon_number_formazione" class="menu_list_number waves-effect waves-circle waves-light btn-floating secondary-content" style="display: none;"></a>
								</td>
								<td class="td_list_name">
									Formazione
								</td>
							</tr>
						</table>
					</li>
					
					<li id="button_page4" name="Visite Mediche">
						<table>
							<tr>
								<td class="td_list_img">
									<image src="img/visiteMediche_40_40.png" />
									<a id="icon_number_visite_mediche" class="menu_list_number waves-effect waves-circle waves-light btn-floating secondary-content" style="display: none; position: absolute; top: 60px; left: 30px;"></a>
								</td>
								<td class="td_list_name">
									Visite Mediche
								</td>
							</tr>
						</table>
					</li>
                
                </ul>
                <!-- pulsanti laterali end-->
                
            </div>
            
            <!-- sidebar end -->

            <div id="content"> 
				
				<!-- header -->
				
                <header	id="header_titolo">
                    <table id="table_header">
                        <tr>
                            <td id="td_menu_button"><button type="button" id="menu_button"><image src="../../img/menu_blue.png" /></button></td>
                            <td id="td_titolo_pagina"><span id="titolo_pagina"></span></td>
                            <td id="td_clock"><span id="clock"></span></td>
                        </tr>
                    </table>
                </header>
                
                <!-- header end -->
                
                <!-- panel -->

                <div class="panel" id="page1" data-footer="none" selected="true">         
					
					<table style="width: 100%;">
						<tr>
							<td style="width: 30%;"></td>
							<td style="text-align: center; width: 40%;">
								
								<!-- Barra di caricamento -->
								<!--<div id="caricamento" class="progress" style="display: none;">
									<div class="indeterminate"></div>
								</div>-->
								<!-- Barra di caricamento end -->
								
								<!-- Disco di caricamento -->
								<div id="caricamento" class="preloader-wrapper small active" style="display: none;">
									<div class="spinner-layer spinner-yellow-only">
										<div class="circle-clipper left">
											<div class="circle"></div>
										</div>
										<div class="gap-patch">
											<div class="circle"></div>
										</div>
										<div class="circle-clipper right">
											<div class="circle"></div>
										</div>
									</div>
								</div>
								<!-- Disco di caricamento end -->
								
							</td>
							<td style="text-align: right; width: 30%;">
								<button id="bottone_salva" class="bottone_salva btn waves-effect waves-light green" type="submit" name="action">Salva<i class="material-icons right">save</i></button>
								<button id="bottone_modifica" class="bottone_modifica btn waves-effect waves-light amber" type="submit" name="action">Modifica<i class="material-icons right">mode_edit</i></button>
							</td>
						</tr>
					</table>
					
					<ul class="collapsible popout" data-collapsible="expandable" style="width: 95%; margin: auto;">
						<li>
							<div class="collapsible-header active"><i class="material-icons">account_circle</i>Informazioni Anagrafiche</div>
							<div class="collapsible-body">
								
								<div class="row">
									<div class="input-field col s6">
										<i class="material-icons prefix">account_circle</i>
										<input id="form_cognome" type="text" class="validate">
										<label for="form_cognome">Cognome</label>
									</div>
									<div class="input-field col s6">
										<i class="material-icons prefix">account_circle</i>
										<input id="form_nome" type="text" class="validate">
										<label for="form_nome">Nome</label>
									</div>
								</div>
								<div class="row">
									<div class="input-field col s6">
										<i class="material-icons prefix">phone</i>
										<input id="form_telefono" type="tel" class="validate">
										<label for="form_telefono">Telefono</label>
									</div>
									<div class="input-field col s6">
										<i class="material-icons prefix">email</i>
										<input id="form_email" type="email" class="validate">
										<label for="form_email">Email</label>
									</div>
								</div>		
								<div class="row">
									<div class="input-field col s6">
										<i class="material-icons prefix">store</i>
										<select id="form_stabilimento" class="validate">
											<option value="0"></option>
										</select>
										<label>Stabilimento</label>										
									</div>
									<div class="input-field col s6">
										<i class="material-icons prefix">today</i>
										<label for="form_compleanno" class="">Compleanno</label>
										<input id="form_compleanno" type="text" class="campo_data_bootstrap validate" readonly >
									</div>
								</div>
								<div class="row">
									<div class="input-field col s6">
										<i class="material-icons prefix">today</i>
										<label for="form_data_assunzione" class="">Data Assunzione</label>
										<input id="form_data_assunzione" type="text" class="campo_data_bootstrap validate" readonly >								
									</div>
									<div class="input-field col s6">
										<i class="material-icons prefix">today</i>
										<label for="form_data_fine_rapporto" class="">Data Fine Rapporto</label>
										<input id="form_data_fine_rapporto" type="text" class="campo_data_bootstrap validate" readonly >
									</div>
								</div>
								
							</div>
						</li>
						<li>
							<div class="collapsible-header active"><i class="material-icons">place</i>Informazioni Indirizzo</div>
							<div class="collapsible-body">
								
								<div class="row">
									<div class="input-field col s6">
										<i class="material-icons prefix">place</i>
										<input id="form_stato" type="text" class="validate">
										<label for="form_stato">Stato</label>
									</div>
									<div class="input-field col s6">
										<i class="material-icons prefix">place</i>
										<input id="form_provincia" type="text" class="validate">
										<label for="form_provincia">Provincia</label>
									</div>
								</div>
								<div class="row">
									<div class="input-field col s6">
										<i class="material-icons prefix">place</i>
										<input id="form_citta" type="text" class="validate">
										<label for="form_citta">Citta'</label>
									</div>
									<div class="input-field col s6">
										<i class="material-icons prefix">place</i>
										<input id="form_via" type="text" class="validate">
										<label for="form_via">Via</label>
									</div>
								</div>
								<div class="row">
									<div class="input-field col s6">
										<i class="material-icons prefix">place</i>
										<input id="form_cap" type="text" class="validate">
										<label for="form_cap">CAP</label>
									</div>
								</div>
								
							</div>
						</li>
						<li>
							<div class="collapsible-header"><i class="material-icons">mode_edit</i>Note</div>
							<div class="collapsible-body">
								
								<div class="row">
									<div class="input-field col s12">
										<i class="material-icons prefix">mode_edit</i>
										<textarea id="form_descrizione" class="materialize-textarea"></textarea>
										<label for="form_descrizione">Descrizione</label>
									</div>
								</div>
							
							</div>
						</li>
					</ul>
					         
                </div>
                
                <div class="panel" id="page2" data-footer="none">                  
					<h5>Lista Documenti</h5>
					<hr />
					
					<div class="card" style="margin: auto;">
					
						<table class="striped" style="width: 100%;">
					        <thead>	
								<tr>
									<th>
										<div class="input-field col s6">
											<input id="search_nome_documento" type="text" placeholder="Nome documento">
											<label for="search_nome_documento">Nome Documento</label>
										</div>
									</th>
									<th>
										<div class="input-field col s6">
											<input id="search_data_documento" class="campo_data_bootstrap" type="text" placeholder="Data documento" readonly >
											<label for="search_data_documento">Data Documento</label>
										</div>
									</th>
									<th>
										<div class="input-field col s6">
											<input id="search_data_scadenza_documento" class="campo_data_bootstrap" type="text" placeholder="Data Scadenza documento" readonly >
											<label for="search_data_scadenza_documento">Data Scadenza Documento</label>
										</div>
									</th>
									<th>
										<div class="input-field col s6">
											<select multiple id="search_stato_documento">
												<option value="Valido senza scadenza" selected>Valido senza scadenza</option>
												<option value="In corso di validita" selected>In corso di validita</option>
												<option value="Scaduto" selected>Scaduto</option>
											</select>
											<label for="search_stato_documento">Stato Documento</label>
										</div>
									</th>
									<th style="width: 80px; text-align: center;">
									</th>
								</tr>	
					        </thead>
					
					        <tbody id="body_tabella_documenti">
								<tr><td colspan='5' style='text-align: center;'><em>Nessun documento trovato!</em></td></tr>
							</tbody>
							
					    </table>
				    
				    </div>
						
				</div>
				
				<div class="panel" id="page3" data-footer="none">   
					
					<div class="col s12">
						<ul class="tabs" id="tabs_page3">
							<li class="tab col s6"><a class="active" href="#pag_situazione_formazione">Situazione Formazione</a></li>
							<li class="tab col s6"><a href="#pag_formazione_eseguita">Formazione Eseguita</a></li>
						</ul>
					</div>
					
					<div id="pag_situazione_formazione" class="col s12">
						<div class="card" style="margin: auto;">
							
							<table class="striped" style="width: 100%;">
								<thead>	
									<tr>
										<th>
											<div class="input-field col s12">
												<input id="search_tipo_corso_sit" type="text" placeholder="Tipo Corso">
												<label for="search_tipo_corso_sit">Tipo Corso</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<input id="search_data_ultima_formazione" class="campo_data_bootstrap" type="text" placeholder="Data Ult. Formaz." readonly >
												<label for="search_data_ultima_formazione">Data Ult. Formaz.</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<input id="search_data_scadenza_sit_formazione" class="campo_data_bootstrap" type="text" placeholder="Data Scadenza" readonly >
												<label for="search_data_scadenza_sit_formazione">Data Scadenza</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<select multiple id="search_stato_sit_formazione">
													<option value="Non eseguita" selected>Non eseguita</option>
													<option value="Eseguita" selected>Eseguita</option>
													<option value="Scaduta" selected>Scaduta</option>
													<option value="In scadenza" selected>In scadenza</option>
													<option value="Eseguito corso base" selected>Eseguito corso base</option>
													<option value="Valida senza scadenza" selected>Valida senza scadenza</option>
													<option value="In corso di validita" selected>In corso di validita</option>
													<option value="Non eseguito corso base" selected>Non eseguito corso base</option>
													<option value="Non eseguita formazione precedente" selected>Non eseguita formazione precedente</option>
													<option value="Eseguire entro" selected>Eseguire entro</option> <!-- kpro@bid170420180940 -->
												</select>
												<label for="search_stato_sit_formazione">Stato</label>
											</div>
										</th>
									</tr>
								</thead>	
								
								<tbody id="body_tabella_situazione_formazione">
									<tr><td colspan='5' style='text-align: center;'><em>Nessuna situazione formazione trovata!</em></td></tr>
								</tbody>
								
							</table>
							
						</div>
					</div>
					
					<div id="pag_formazione_eseguita" class="col s12">
						<div class="card" style="margin: auto;">
							
							<table class="striped" style="width: 100%;">
								<thead>	
									<tr>
										<th>
											<div class="input-field col s12">
												<input id="search_nome_formazione" type="text" placeholder="Nome Formazione">
												<label for="search_nome_formazione">Nome Formazione</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<input id="search_tipo_corso" type="text" placeholder="Tipo Corso">
												<label for="search_tipo_corso">Tipo Corso</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<input id="search_data_formazione" class="campo_data_bootstrap" type="text" placeholder="Data Formazione" readonly >
												<label for="search_data_formazione">Data Formazione</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<input id="search_data_validita_formazione" class="campo_data_bootstrap" type="text" placeholder="Data Validita'" readonly >
												<label for="search_data_validita_formazione">Data Validita'</label>
											</div>
										</th>
									</tr>
								</thead>	
								
								<tbody id="body_tabella_formazione_eseguita">
									<tr><td colspan='5' style='text-align: center;'><em>Nessuna formazione eseguita trovata!</em></td></tr>
								</tbody>
								
							</table>
							
						</div>
					</div>
					
				</div>
				
				<div class="panel" id="page4" data-footer="none">   
					
					<div class="col s12">
						<ul class="tabs" id="tabs_page4">
							<li class="tab col s6"><a class="active" href="#pag_situazione_visite_mediche">Situazione Visite Mediche</a></li>
							<li class="tab col s6"><a href="#pag_visite_mediche_eseguite">Visite Mediche Eseguite</a></li>
						</ul>
					</div>
					
					<div id="pag_situazione_visite_mediche" class="col s12">
						<div class="card" style="margin: auto;">
							
							<table class="striped" style="width: 100%; ">
								<thead>	
									<tr>
										<th>
											<div class="input-field col s12">
												<input id="search_tipo_visita_sit" type="text" placeholder="Tipo Visita">
												<label for="search_tipo_visita_sit">Tipo Visita</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<input id="search_data_ultima_visita" class="campo_data_bootstrap" type="text" placeholder="Data Ult. Visita" readonly >
												<label for="search_data_ultima_visita">Data Ult. Visita</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<input id="search_data_scadenza_sit_visite" class="campo_data_bootstrap" type="text" placeholder="Data Scadenza" readonly >
												<label for="search_data_scadenza_sit_visite">Data Scadenza</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<select multiple id="search_stato_sit_visita">
													<option value="Non eseguita" selected>Non eseguita</option>
													<option value="Eseguita" selected>Eseguita</option>
													<option value="Scaduta" selected>Scaduta</option>
													<option value="In scadenza" selected>In scadenza</option>
												</select>
												<label for="search_stato_sit_visita">Stato</label>
											</div>
										</th>
									</tr>
								</thead>	
								
								<tbody id="body_tabella_situazione_visite_mediche">
									<tr><td colspan='5' style='text-align: center;'><em>Nessuna situazione visite mediche trovata!</em></td></tr>
								</tbody>
								
							</table>
							
						</div>
					</div>
					
					<div id="pag_visite_mediche_eseguite" class="col s12">
						<div class="card" style="margin: auto;">
							
							<table class="striped" style="width: 100%;">
								<thead>	
									<tr>
										<th>
											<div class="input-field col s12">
												<input id="search_nome_visita" type="text" placeholder="Nome Visita">
												<label for="search_nome_visita">Nome Visita</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<input id="search_tipo_visita" type="text" placeholder="Tipo Visita">
												<label for="search_tipo_visita">Tipo Visita</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<input id="search_data_visita" class="campo_data_bootstrap" type="text" placeholder="Data Visita" readonly >
												<label for="search_data_visita">Data Visita</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<input id="search_data_validita_visita" class="campo_data_bootstrap" type="text" placeholder="Data Validita'" readonly >
												<label for="search_data_validita_visita">Data Validita'</label>
											</div>
										</th>
									</tr>
								</thead>	
								
								<tbody id="body_tabella_visite_mediche_eseguite">
									<tr><td colspan='5' style='text-align: center;'><em>Nessuna visita medica trovata!</em></td></tr>
								</tbody>
								
							</table>
							
						</div>
					</div>
					
				</div>
				
				<!-- panel end -->
				
            </div>
            
        </div>
        
        <!-- popup -->
        
        <div id="alert_offline" class="modal" style="background-color: #ffcc00; vertical-align: middle; text-align: center; color: red; font-weight: bold;">
			<div class="modal-content">
				<span>Attenzione: Connessione Internet persa!</span>  
			</div>
		</div>
		
		<div id="alert_mandatory_field" class="modal" style="vertical-align: middle; text-align: center; color: red; font-weight: bold;">
		    <div class="modal-content">
				<h5>Compilare tutti i campi obbligatori!</h5>  
		    </div>
		    <div class="modal-footer">
				<a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Chiudi</a>
		    </div>
		</div>
		
		<!-- popup end -->
        
    </body>
</html>
