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

$app_fatture = false;

if($contact_id != 0){
    
    $q_account = "SELECT accountid,
					kp_app_fatture
					FROM {$table_prefix}_contactdetails 
					WHERE contactid = ".$contact_id;
	$res_account = $adb->query($q_account);
			
	if($adb->num_rows($res_account) > 0){
		
		$azienda = $adb->query_result($res_account, 0, 'accountid');
		$azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES,$default_charset);
		
		$kp_app_fatture = $adb->query_result($res_account, 0, 'kp_app_fatture');
		$kp_app_fatture = html_entity_decode(strip_tags($kp_app_fatture), ENT_QUOTES,$default_charset);
		if($kp_app_fatture == '1'){
			$app_fatture = true;
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
            var string_fatture = "<?php echo($string_fatture); ?>";            
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
				<div class="mdl-layout__header-row" style="width:100%; margin: 0px; padding-left: 10px; padding-right: 0px">
					
					<table id="table_header">
						<tr>
							<td id="td_menu"></td>
							<td id="td_titolo_pagina"><span id="titolo_pagina">Resp. Linea > Documenti</span></td>
							<td id="td_caricamento_pagina" style="text-align: center;"><div class="caricamento mdl-spinner mdl-js-spinner is-active" style="display: none;"></div></td>
							<td style="text-align: right; vertical-align: middle; padding-right: 10px">
								<image src="../../img/S-PRO-LOGO-HEADER-BLUE.png" style="max-widh:30px; max-height:30px" />
							</td>
							<td style="text-align: right; vertical-align: middle; padding-right: 10px; width: 100px;">
								<a href="../../login.php" id="button_logout" class="menu_head_button" title="Esci"><image style="max-widh:30px; max-height:30px" id="logout" src='../../img/logout.png' /></a> <!-- kpro@bid130420181425 -->
							</td>
						</tr>
					</table>
					
				</div>
				<!-- title end -->
			
				<!-- tabs -->
				<!--<div class="mdl-layout__tab-bar mdl-js-ripple-effect">
					<a href="#fixed-tab-1" class="mdl-layout__tab is-active">Tab 1</a>
					<a href="#fixed-tab-2" class="mdl-layout__tab">Tab 2</a>
					<a href="#fixed-tab-3" class="mdl-layout__tab">Tab 3</a>
				</div>-->
				<!-- tabs end -->
			
			</header>

			<!-- navigation-bar -->

			<?php include($portal_name.'/modules/navbar.php'); ?>
			
			<!-- navigation-bar end -->
			
			<main class="mdl-layout__content">
				
				<div class="card" style="width: 98%; margin-left: 1%; margin-top: 20px;">

					<table class="striped" style="width: 100%;">
						<thead>	
							<tr>

								<th>
								</th>

								<th>
									<div class="input-field col s6">
										<input id="search_numero_fattura" type="text" placeholder="Numero">
										<label for="search_numero_fattura">Numero</label>
									</div>
								</th>
								<th>
									<div class="input-field col s6">
										<input id="search_soggetto_fattura" type="text" placeholder="Soggetto">
										<label for="search_soggetto_fattura">Soggetto</label>
									</div>
								</th>
								<th>
									<div class="input-field col s6">
										<input id="search_data_fattura" class="campo_data_bootstrap" type="text" placeholder="Data Fattura" readonly >
										<label for="search_data_fattura">Data Fattura</label>
									</div>
								</th>
								<th>
									<div class="input-field col s6">
										<select id="search_stato_fattura">
											<option value="all" selected></option>
											<option value="Emessa">Emessa</option>
											<option value="Pagata">Pagata</option>
										</select>
										<label for="search_stato_fattura">Stato</label>
									</div>
								</th>
							</tr>	
						</thead>
				
						<tbody id="body_tabella_lista_fatture">
							<tr><td colspan='5' style='text-align: center;'><em>Nessuna fattura trovata!</em></td></tr>
						</tbody>
						
					</table>
			
				</div>
				
			</main>

			<!-- Popup -->

			<div id="alert_offline" class="modal" style="background-color: #ffcc00; vertical-align: middle; text-align: center; color: red; font-weight: bold;">
                <div class="modal-content">
                    <span>Attenzione: Connessione Internet persa!</span>  
                </div>
            </div>
			
			<!-- Popup end -->
			
		</div>

	</body>
</html>
