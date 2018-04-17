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

if($contact_id != 0){
    
    $q_account = "SELECT accountid,
					kp_app_scadenziario
					FROM {$table_prefix}_contactdetails 
					WHERE contactid = ".$contact_id;
	$res_account = $adb->query($q_account);
			
	if($adb->num_rows($res_account) > 0){
		
		$azienda = $adb->query_result($res_account, 0, 'accountid');
		$azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES,$default_charset);
		
		$kp_app_scadenziario = $adb->query_result($res_account, 0, 'kp_app_scadenziario');
		$kp_app_scadenziario = html_entity_decode(strip_tags($kp_app_scadenziario), ENT_QUOTES,$default_charset);
		if($kp_app_scadenziario == '1'){
			$app_scadenziario = true;
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

?>

<html>
    <head>
        <title>Portale SPro</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        
         <link rel="icon" type="image/png" href="../../img/S-PRO-FAVICON.ico">
		<link rel="stylesheet" type="text/css" href="../../css/style_index.css">
		<!--<link rel="stylesheet" type="text/css" href="css/jquery-ui-kpro.css">-->
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link rel="stylesheet" href="../../css/material.min.css">
		<link type="text/css" rel="stylesheet" href="../../css/materialize.min.css"  media="screen,projection"/>
		<!--<link rel="stylesheet" href="../../css/bootstrap.min.css">-->
		<!--<link rel="stylesheet" href="../../css/bootstrap-theme.min.css">-->

        <script src="../../js/jquery-2.1.4.min.js"></script>  
		<!--<script src="js/jquery-ui.min.js"></script>-->
		<script src="../../js/material.min.js"></script>
		<script type="text/javascript" src="../../js/materialize.min.js"></script>
		<!--<script src="../../js/bootstrap.min.js" ></script>-->
        <script src="js/general_index.js"></script>  
		
		<link rel="stylesheet" type="text/css" href="../../css/jquery.datetimepicker.css">
        <script src="../../js/jquery.datetimepicker.full.js"></script>
		
		<!--Grid-->
		<link rel="stylesheet" type="text/css" href="../../codebase/fonts/font_roboto/roboto.css"/>
		<!--<link rel="stylesheet" type="text/css" href="../../codebase/dhtmlx.css"/>-->
		<link rel="stylesheet" type="text/css" href="../../codebase/dhtmlx_material.css"/>
        <script src="../../codebase/dhtmlx.js"></script>
        <!--Grid end-->

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
            var string_scadenziario = "<?php echo($string_scadenziario); ?>";           
            //Traduzioni end
            
            var contact_id = "<?php echo($contact_id); ?>";
            var aziendaid = "<?php echo($azienda); ?>";
            var accountname = "<?php echo($accountname); ?>";
            var indirizzo_crm = "<?php echo($site_URL); ?>";
            var default_language = "<?php echo($default_language); ?>";
			
        </script>   
    </head>
    <body>
         
        <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header mdl-layout--fixed-tabs">
			<header class="mdl-layout__header">
				
				<!-- title -->
				<div class="mdl-layout__header-row" style="width:100%; margin-left: 0px; margin-right: 0px; padding-left: 10px; padding-right: 10px;">
					
					<table id="table_header">
						<tr>
							<td id="td_menu"></td>
							<td id="td_titolo_pagina"><span id="titolo_pagina"></span></td>
							<td style="text-align: right; vertical-align: middle; padding-right: 10px">
								<image src="../../img/S-PRO-LOGO-HEADER-BLUE.png" style="max-widh:30px; max-height:30px" />
							</td>
							<td style="text-align: right; vertical-align: middle; padding-right: 10px; width: 100px;">
								<a href="../../login.php" id="button_logout" class="menu_head_button" title="<?php echo($string_esci); ?>"><image style="max-widh:30px; max-height:30px" id="logout" src='../../img/logout.png' /></a>
							</td>
						</tr>
					</table>
					
				</div>
				<!-- title end -->
			
				<!-- tabs -->
				<div class="mdl-layout__tab-bar mdl-js-ripple-effect">
					<a href="#fixed-tab-1" class="mdl-layout__tab is-active">Formazione</a>
					<a href="#fixed-tab-2" class="mdl-layout__tab">Visite Mediche</a>
				</div>
				<!-- tabs end -->
			
			</header>

			<!-- navigation-bar -->

			<?php include($portal_name.'/modules/navbar.php'); ?>
			
			<!-- navigation-bar end -->
			
			<main class="mdl-layout__content">
				
				<section class="mdl-layout__tab-panel is-active" id="fixed-tab-1" style="padding-top: 20px;">
					<div class="page-content">

						<table class="card striped" style="width: 98%; margin: auto;">
							<thead>	
								<tr>
									<th>
										<div class="input-field col s12">
											<input id="search_risorsa_sit_formazione" type="text" placeholder="Risorsa">
											<label for="search_risorsa_sit_formazione">Risorsa</label>
										</div>
									</th>
									<th>
										<div class="input-field col s12">
											<input id="search_mansione_sit_formazione" type="text" placeholder="Mansione">
											<label for="search_mansione_sit_formazione">Mansione</label>
										</div>
									</th>
									<th>
										<div class="input-field col s12">
											<input id="search_stabilimento_sit_formazione" type="text" placeholder="Stabilimento">
											<label for="search_stabilimento_sit_formazione">Stabilimento</label>
										</div>
									</th>
									<th>
										<div class="input-field col s12">
											<input id="search_tipo_corso_sit_formazione" type="text" placeholder="Tipo Corso">
											<label for="search_tipo_corso_sit_formazione">Tipo Corso</label>
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
								<tr><td colspan='10' style='text-align: center;'><em>Nessuna situazione formazione trovata!</em></td></tr>
							</tbody>
							
						</table>
			  
					</div>
				</section>
				
				<section class="mdl-layout__tab-panel" id="fixed-tab-2" style="padding-top: 20px;">
					<div class="page-content">
			  
						<table class="card striped" style="width: 98%; margin: auto;">
							<thead>	
								<tr>
									<th>
										<div class="input-field col s12">
											<input id="search_risorsa_sit_visite" type="text" placeholder="Risorsa">
											<label for="search_risorsa_sit_visite">Risorsa</label>
										</div>
									</th>
									<th>
										<div class="input-field col s12">
											<input id="search_mansione_sit_visite" type="text" placeholder="Mansione">
											<label for="search_mansione_sit_visite">Mansione</label>
										</div>
									</th>
									<th>
										<div class="input-field col s12">
											<input id="search_stabilimento_sit_visite" type="text" placeholder="Stabilimento">
											<label for="search_stabilimento_sit_visite">Stabilimento</label>
										</div>
									</th>
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
								<tr><td colspan='10' style='text-align: center;'><em>Nessuna situazione visite mediche trovata!</em></td></tr>
							</tbody>
							
						</table>
			  
					</div>
				</section>
				
				<div id="alert_offline" title="Offline" style="display: none; text-align: center;">
					<span style="text-align: center; font-weight: bold;"><?php echo($string_connessione_persa); ?></span>          	 
				</div>
				
			</main>
			
		</div>

	</body>
</html>
