
function bottoneRecuperaTipiCorso(crmid){
    
    /* kpro@tom2412015 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2015, Kpro Consulting Srl
     * @package vteSicurezza
     * @version 1.0
     * 
     * Questa leggere riporta i tipi corso della relativa mansione sulla mansione-risorsa
     */
    
    //openPopup('modules/SproCore/SproUtils/recupero_tipi_corso.php?record='+crmid,'','','auto','50','32',''); 
    if(confirm("Attenzione!! Se si continua i tipi corso della Mansione-Risorsa verranno allineati con quelli della relativa Mansione perdendo eventuali modifiche; continuare?")){
        $('status').style.display='inline';		
        var res = getFile('modules/SproCore/SproUtils/recupero_tipi_corso.php?record='+crmid);
        $('status').style.display='none';
        window.open('index.php?module=MansioniRisorsa&action=DetailView&record='+crmid, '_self');
        alert('Operazione eseguita'); 
    }
    
}

function bottoneRecuperaTipiVisitaMedica(crmid){
    
    /* kpro@tom2412015 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2015, Kpro Consulting Srl
     * @package vteSicurezza
     * @version 1.0
     * 
     * Questa leggere riporta i tipi visita medica della relativa mansione sulla mansione-risorsa
     */
    
    //openPopup('modules/SproCore/SproUtils/recupero_tipi_visita_medica.php?record='+crmid,'','','auto','50','32',''); 
    
    if(confirm("Attenzione!! Se si continua i tipi vista medica della Mansione-Risorsa verranno allineati con quelli della relativa Mansione perdendo eventuali modifiche; continuare?")){
        $('status').style.display='inline';		
        var res = getFile('modules/SproCore/SproUtils/recupero_tipi_visita_medica.php?record='+crmid);
        $('status').style.display='none';
        window.open('index.php?module=MansioniRisorsa&action=DetailView&record='+crmid, '_self');
        alert('Operazione eseguita'); 
    }
    
}

function bottoneRecuperaTipiCorsoETipiVisitaMedica(crmid){ 
    
    /* kpro@tom2412015 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2015, Kpro Consulting Srl
     * @package vteSicurezza
     * @version 1.0
     * 
     * Questa leggere riporta i tipi corso e i tipi visita medica della relativa mansione sulla mansione-risorsa
     */
    
    if(confirm("Attenzione!! Se si continua i tipi corso e i tipi vista medica della Mansione-Risorsa verranno allineati con quelli della relativa Mansione perdendo eventuali modifiche; continuare?")){
        $('status').style.display='inline';	
        var res = getFile('modules/SproCore/SproUtils/recupero_tipi_corso.php?record='+crmid);
        var res = getFile('modules/SproCore/SproUtils/recupero_tipi_visita_medica.php?record='+crmid);
        $('status').style.display='none';
        window.open('index.php?module=MansioniRisorsa&action=DetailView&record='+crmid, '_self');
        alert('Operazione eseguita'); 
    }
    
}

