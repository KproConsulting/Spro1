<?php

/* kpro@tom17112016 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */
 
include_once('../../../../config.inc.php');
global $site_URL;

$custom_site_URL_name = $site_URL;

$server_con_https = false;
 
$nome_programma_firma = "GestioneFirmaGrafometricaPartecipazioniCorso";

$defaultTablet = "tdWacomSTU430";	

$cartella_crm_documento = 28;

$pdf_template_id = 20;

$pdf_template_relmodule = "KpPartecipFormaz";



$custom_site_URL = "http://".$custom_site_URL_name;

if($server_con_https){
	$custom_site_URL_https = "https://".$custom_site_URL_name;
}
else{
	$custom_site_URL_https = $custom_site_URL;
}

$url_certificato = $custom_site_URL."/modules/SproCore/CustomViews/".$nome_programma_firma."/certificato/Firma GrafoCerta (FEA) Demo.txt";

$pdf_temp_name_coda = "_template_temporaneo.pdf";

$pdf_temp_url_radice = $site_URL."/modules/SproCore/CustomViews/".$nome_programma_firma."/temp/";

$upload_url = $site_URL."/modules/SproCore/CustomViews/".$nome_programma_firma."/UploadFirma.php";

$pdf_signed_name_coda = "_signed.pdf";

$pdf_signed_url_radice = $site_URL."/modules/SproCore/CustomViews/".$nome_programma_firma."/signed/";

$template_url = $site_URL."/modules/SproCore/CustomViews/".$nome_programma_firma."/template_fct/template_firma.fct";
	
$radice_nome_documento = "PDF Firmato ";

$path_cartella_pdf_firmati = "modules/SproCore/CustomViews/".$nome_programma_firma."/signed/";

$path_cartella_temporanea = "modules/SproCore/CustomViews/".$nome_programma_firma."/temp/";

?>