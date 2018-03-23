/* kpro@tom31072017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

var jcheck_module_kpro;
var jpagina_principale;
var jpagina_genera_richiesta;
var jpagina_aggiorna_licenza;
var jbottone_aggiorna_licenza;
var jbottone_genera_richiesta;
var jbottone_torna_pagina_principale;
var jreadonly_chiave_richiesta;
var jbottone_attiva_licenza;
var jform_chiave_attivazione;
var jform_chiave_aggiorna_licenza;
var jbottone_pagina_aggiorna_licenza;
var jcheck_area_kpro;
var jcheck_prog_kpro;

var lista_moduli;
var lista_programmi;
var tab_selezionato;

jQuery(document).ready(function() {

    inizializzazione();

});

function inizializzazione() {

    jcheck_module_kpro = jQuery(".check_module_kpro");
    jpagina_principale = jQuery("#pagina_principale");
    jpagina_genera_richiesta = jQuery("#pagina_genera_richiesta");
    jpagina_aggiorna_licenza = jQuery("#pagina_aggiorna_licenza");
    jbottone_aggiorna_licenza = jQuery("#bottone_aggiorna_licenza");
    jbottone_genera_richiesta = jQuery("#bottone_genera_richiesta");
    jbottone_torna_pagina_principale = jQuery(".bottone_torna_pagina_principale");
    jreadonly_chiave_richiesta = jQuery("#readonly_chiave_richiesta");
    jbottone_attiva_licenza = jQuery("#bottone_attiva_licenza");
    jform_chiave_attivazione = jQuery("#form_chiave_attivazione");
    jform_chiave_aggiorna_licenza = jQuery("#form_chiave_aggiorna_licenza");
    jbottone_pagina_aggiorna_licenza = jQuery("#bottone_pagina_aggiorna_licenza");
    jcheck_area_kpro = jQuery(".check_area_kpro");
    jcheck_prog_kpro = jQuery(".check_prog_kpro");
    jcheck_area_prog_kpro = jQuery(".check_area_prog_kpro");

    tabSelezionato('moduli');

    jbottone_genera_richiesta.click(function() {

        generaRichiestaChiave();

    });

    jbottone_torna_pagina_principale.click(function() {

        tornaAllaPaginaPrincipale();

    });

    jbottone_pagina_aggiorna_licenza.click(function() {

        jpagina_genera_richiesta.hide();
        jpagina_principale.hide();
        jpagina_aggiorna_licenza.show();

    });

    jbottone_attiva_licenza.click(function() {

        inserisciLicenza(jform_chiave_attivazione.val());

    });

    jbottone_aggiorna_licenza.click(function() {

        inserisciLicenza(jform_chiave_aggiorna_licenza.val());

    });

    jcheck_area_kpro.change(function() {

        var area_id_temp = jQuery(this).prop("id");
        area_id_temp = area_id_temp.substring(5, area_id_temp.length);

        if (jQuery(this).prop("checked")) {

            jQuery(".check_area_" + area_id_temp).prop("checked", true);

        } else {

            jQuery(".check_area_" + area_id_temp).prop("checked", false);

        }

    });

    jcheck_area_prog_kpro.change(function() {

        var area_id_temp = jQuery(this).prop("id");
        area_id_temp = area_id_temp.substring(10, area_id_temp.length);

        if (jQuery(this).prop("checked")) {

            jQuery(".check_prog_" + area_id_temp).prop("checked", true);

        } else {

            jQuery(".check_prog_" + area_id_temp).prop("checked", false);

        }

    });

}

function tornaAllaPaginaPrincipale() {

    jpagina_aggiorna_licenza.hide();
    jpagina_genera_richiesta.hide();
    jpagina_principale.show();

}

function generaRichiestaChiave() {

    jreadonly_chiave_richiesta.val();

    jpagina_aggiorna_licenza.hide();
    jpagina_principale.hide();
    jpagina_genera_richiesta.show();

    lista_moduli = [];
    lista_programmi = [];

    jcheck_module_kpro = jQuery(".check_module_kpro");
    jcheck_prog_kpro = jQuery(".check_prog_kpro");

    jcheck_module_kpro.each(function() {

        var elemento_in_esame = {
            id: jQuery(this).prop("id"),
            check: jQuery(this).prop('checked')
        };

        lista_moduli.push(elemento_in_esame);

    });

    jcheck_prog_kpro.each(function() {

        var id_temp = jQuery(this).prop("id");
        id_temp = id_temp.substring(5, id_temp.length);

        var numero_utenti = 0;
        if (jQuery("#numero_utenti_pro_" + id_temp)) {
            numero_utenti = jQuery("#numero_utenti_pro_" + id_temp).val();
        }

        var elemento_in_esame = {
            id: id_temp,
            check: jQuery(this).prop('checked'),
            numero_utenti: numero_utenti
        };

        lista_programmi.push(elemento_in_esame);

    });

    //console.table(lista_moduli);

    var lista_moduli_json = JSON.stringify(lista_moduli);
    var lista_programmi_json = JSON.stringify(lista_programmi);

    var dati = {
        lista_moduli: lista_moduli_json,
        lista_programmi: lista_programmi_json
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/Settings/KpLicenza/GetChiaveRichiesta.php',
        dataType: 'json',
        async: true,
        method: 'POST',
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {

            jreadonly_chiave_richiesta.val(data);

        },
        fail: function() {

            console.error("Errore nel salvataggio");

            //location.reload();

        }
    });

}

function inserisciLicenza(licenza) {

    var dati = {
        dati: licenza
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/Settings/KpLicenza/SetChiaveAttivazione.php',
        dataType: 'json',
        async: true,
        method: 'POST',
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {

            tornaAllaPaginaPrincipale();

        },
        fail: function() {

            console.error("Errore nel salvataggio");

            //location.reload();

        }
    });

}

function tabSelezionato(tab) {

    tab_selezionato = tab;

    jQuery(".nav-item").each(function() {

        if (jQuery(this).attr("id") == ("li_" + tab_selezionato)) {

            jQuery(this).css("border-bottom", "3px solid red");

        } else {

            jQuery(this).css("border-bottom", "");

        }

    });

}