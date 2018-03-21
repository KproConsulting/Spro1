<?php

/* kpro@tom17102016 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */

function verificaPresenzaPdfFirmato($record){
	global $adb, $table_prefix, $current_user, $site_URL, $default_charset, $root_directory;

	$file_url = "";

	$name = "PDF Rapportino Manutenzione ".$record;

	$q_file = "SELECT 
				att.attachmentsid attachmentsid,
				att.name name,
				att.path path
				FROM {$table_prefix}_notes notes 
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notes.notesid
				INNER JOIN {$table_prefix}_senotesrel rel ON rel.notesid = notes.notesid
				INNER JOIN {$table_prefix}_seattachmentsrel serel ON serel.crmid = notes.notesid 
				INNER JOIN {$table_prefix}_attachments att ON att.attachmentsid = serel.attachmentsid
				WHERE ent.deleted = 0 AND rel.crmid = ".$record." AND notes.title LIKE '%".$name."%'";

	$res_file = $adb->query($q_file);
	if($adb->num_rows($res_file) > 0){

		$attachmentsid = $adb->query_result($res_file, 0, 'attachmentsid');
		$attachmentsid = html_entity_decode(strip_tags($attachmentsid), ENT_QUOTES,$default_charset);

		$name = $adb->query_result($res_file, 0, 'name');
		$name = html_entity_decode(strip_tags($name), ENT_QUOTES,$default_charset);

		$path = $adb->query_result($res_file, 0, 'path');
		$path = html_entity_decode(strip_tags($path), ENT_QUOTES,$default_charset);
		
		$file_url = $site_URL."/".$path.$attachmentsid."_".$name;

	}

	return $file_url;

}

function verificaPresenzaPdfTemporaneoFirmato($record){
	global $adb, $table_prefix, $current_user, $site_URL, $default_charset, $root_directory;

	$file_url = "";

	$file_path = $root_directory."modules/SproCore/CustomPortals/PortaleManutenzioni/temp/";
    $name = $record."_template_firmato_temporaneo";

    if(file_exists($file_path.$name.".pdf")) { 
        $file_url = $site_URL."/modules/SproCore/CustomPortals/PortaleManutenzioni/temp/".$name.".pdf";
    } 
	
	return $file_url;

}

function verificaPresenzaPdfTemporaneo($record){
	global $adb, $table_prefix, $current_user, $site_URL, $default_charset, $root_directory;

	$file_url = "";

	$file_path = $root_directory."modules/SproCore/CustomPortals/PortaleManutenzioni/temp/";
    $name = $record."_template_temporaneo";

    if(file_exists($file_path.$name.".pdf")) { 
        $file_url = $site_URL."/modules/SproCore/CustomPortals/PortaleManutenzioni/temp/".$name.".pdf";
    } 
	
	return $file_url;

}

function cancellaPdfTemplateTemporaneo($record){
	global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

	$upload_file_path = $root_directory."modules/SproCore/CustomPortals/PortaleManutenzioni/temp/";
    $name = $record."_template_temporaneo";

    if(file_exists($upload_file_path.$name.".pdf")) { 
        @unlink($upload_file_path.$name.".pdf");
    } 

}

function cancellaPdfTemplateFirmatoTemporaneo($record){
	global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

	$upload_file_path = $root_directory."modules/SproCore/CustomPortals/PortaleManutenzioni/temp/";
    $name = $record."_template_firmato_temporaneo";

    if(file_exists($upload_file_path.$name.".pdf")) { 
        @unlink($upload_file_path.$name.".pdf");
    } 

}

function generaPdfTemplateTemporaneo($record){

	require_once("modules/PDFMaker/InventoryPDF.php");
	require_once("include/mpdf/mpdf.php"); 

	$templateid = 12;
    $relmodule = 'Manutenzioni';
    $language = 'it_it';
    $record = $record;

    $upload_file_path = $root_directory."modules/SproCore/CustomPortals/PortaleManutenzioni/temp/";
    $name = $record."_template_temporaneo";

    if(file_exists($upload_file_path.$name.".pdf")) { 
        @unlink($upload_file_path.$name.".pdf");
    } 

    $focus = CRMEntity::getInstance($relmodule);
    $focus->retrieve_entity_info($record, $relmodule);
    $focus->id = $record;

    $PDFContents = array();
    $TemplateContent = array();

    $PDFContent = PDFContent::getInstance($templateid, $relmodule, $focus, $language); 
    $pdf_content = $PDFContent->getContent();    

    $header_html = $pdf_content["header"];
    $body_html = $pdf_content["body"];
    $footer_html = $pdf_content["footer"];

    $body_html = str_replace("#firma_tecnico#", "", $body_html);
    $body_html = str_replace("#firma_responsabile#", "", $body_html);

    $Settings = $PDFContent->getSettings();
    if($name==""){    
        $name = $PDFContent->getFilename();
    }

    if($Settings["orientation"] == "landscape"){
        $format = $Settings["format"]."-L";
    }
    else{
        $format = $Settings["format"];
    }

    $ListViewBlocks = array();
    if(strpos($body_html,"#LISTVIEWBLOCK_START#") !== false && strpos($body_html,"#LISTVIEWBLOCK_END#") !== false){
        preg_match_all("|#LISTVIEWBLOCK_START#(.*)#LISTVIEWBLOCK_END#|sU", $body_html, $ListViewBlocks, PREG_PATTERN_ORDER);
    }		

    if (count($ListViewBlocks) > 0){
					
        $TemplateContent[$templateid] = $pdf_content;
        $TemplateSettings[$templateid] = $Settings;

        $num_listview_blocks = count($ListViewBlocks[0]);
        for($i = 0; $i < $num_listview_blocks; $i++){
            $ListViewBlock[$templateid][$i] = $ListViewBlocks[0][$i];
            $ListViewBlockContent[$templateid][$i][$record][] = $ListViewBlocks[1][$i];
        }   
    }
    else{
        if (!isset($mpdf)){           
            $mpdf=new mPDF('',$format,'','Arial',$Settings["margin_left"],$Settings["margin_right"],0,0,$Settings["margin_top"],$Settings["margin_bottom"]);  
            $mpdf->SetAutoFont();
            @$mpdf->SetHTMLHeader($header_html);
        }
        else{
            @$mpdf->SetHTMLHeader($header_html);
            @$mpdf->WriteHTML('<pagebreak sheet-size="'.$format.'" margin-left="'.$Settings["margin_left"].'mm" margin-right="'.$Settings["margin_right"].'mm" margin-top="0mm" margin-bottom="0mm" margin-header="'.$Settings["margin_top"].'mm" margin-footer="'.$Settings["margin_bottom"].'mm" />');
        }     
        @$mpdf->SetHTMLFooter($footer_html);
        @$mpdf->WriteHTML($body_html);
    }

    if (count($TemplateContent)> 0){

        foreach($TemplateContent AS $templateid => $TContent){
            $header_html = $TContent["header"];
            $body_html = $TContent["body"];
            $footer_html = $TContent["footer"];

            $Settings = $TemplateSettings[$templateid];

            foreach($ListViewBlock[$templateid] AS $id => $text){
                $replace = "";
                foreach($Records as $record){  
                    $replace .= implode("",$ListViewBlockContent[$templateid][$id][$record]);   
                }

                $body_html = str_replace($text,$replace,$body_html);
            }

            if ($Settings["orientation"] == "landscape"){
                $format = $Settings["format"]."-L";
            }
            else{
                $format = $Settings["format"];
            }


            if (!isset($mpdf)){           
                $mpdf=new mPDF('',$format,'','Arial',$Settings["margin_left"],$Settings["margin_right"],0,0,$Settings["margin_top"],$Settings["margin_bottom"]);  
                $mpdf->SetAutoFont();
                @$mpdf->SetHTMLHeader($header_html);
            }
            else{
                @$mpdf->SetHTMLHeader($header_html);
                @$mpdf->WriteHTML('<pagebreak sheet-size="'.$format.'" margin-left="'.$Settings["margin_left"].'mm" margin-right="'.$Settings["margin_right"].'mm" margin-top="0mm" margin-bottom="0mm" margin-header="'.$Settings["margin_top"].'mm" margin-footer="'.$Settings["margin_bottom"].'mm" />');
            }     
            @$mpdf->SetHTMLFooter($footer_html);
            @$mpdf->WriteHTML($body_html);
        }
    }

    /*Questa parte servirebbe se volessi salvare il documento sul mio computer
    $mpdf->Output('cache/'.$name.'.pdf');

    @ob_clean();
    header('Content-Type: application/pdf');
    header("Content-length: ".filesize("./cache/$name.pdf"));
    header("Cache-Control: private");
    header("Content-Disposition: attachment; filename=$name.pdf");
    header("Content-Description: PHP Generated Data");
    echo fread(fopen("./cache/$name.pdf", "r"),filesize("./cache/$name.pdf"));

    @unlink("cache/$name.pdf");*/

    //$upload_file_path = decideFilePath();     //Con questo sistema il crm deciderà in automatico la cartella in cui salvare il pdf

    if($name!=""){
        $file_name = $name.".pdf";
    }

    @$mpdf->Output($upload_file_path.$file_name);

    $result = $upload_file_path.$file_name;

}

?>