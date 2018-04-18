<?php

/* kpro@bid18042018 */

function calcolaDataScadenza($data, $numero_giorni, $fine_mese){
    global $adb, $table_prefix,$current_user;
    
    if($numero_giorni % 30 == 0){
        $numero_mesi = $numero_giorni / 30;
    }
    else{
        $numero_mesi = 0;
    }
    
    if($fine_mese){
        $array_data = explode('-', $data);
        $anno = $array_data[0];
        $mese = $array_data[1];
        $giorno = $array_data[2];
        
        $data_temp = $anno."-".$mese."-01";
        $data_temp = date_create($data_temp);
        if($numero_mesi > 0){
            date_add($data_temp, date_interval_create_from_date_string($numero_mesi." months"));
        }
        else{
            date_add($data_temp, date_interval_create_from_date_string($numero_giorni." days"));
        }
        $data_temp = date_format($data_temp,"Y-m-d");
        $array_data_temp = explode('-', $data_temp);
        $anno_temp = $array_data_temp[0];
        $mese_temp = $array_data_temp[1];
        
        $ultimo_giorno_mese_temp = date("t", strtotime($data_temp));
        if($ultimo_giorno_mese_temp < $giorno){
            $giorno = $ultimo_giorno_mese_temp;
        }
        
        $data_scadenza = $anno_temp."-".$mese_temp."-".$giorno;
    }
    else{
        $data = date_create($data);
        if($numero_mesi > 0){
            date_add($data, date_interval_create_from_date_string($numero_mesi." months"));
        }
        else{
            date_add($data, date_interval_create_from_date_string($numero_giorni." days"));
        }
        $data_scadenza = date_format($data,"Y-m-d");
    }
    
    return $data_scadenza; 
}
		
?>
