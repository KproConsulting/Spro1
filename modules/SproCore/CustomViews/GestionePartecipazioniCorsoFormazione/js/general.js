/* kpro@tom14122016 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */

var jtitolo_pagina;
var jclock;

var timer_check_offline;
var readonly = false;
var myclock = "";
var now = "";
var isOnline = true;

var altezza_schermo = '';
var larghezza_schermo = '';

var jalert_offline;
var jcaricamento;
var jpopup_salvataggio_in_corso;
var jcaricamento_popup;

//Elementi personalizzati

var jbody_tabella_partecipanti;
var jbottone_chiudi_scheda;
var jsearch_nome_partecipante;
var jsearch_nome_azienda;
var jsearch_nome_stabilimento;
var jsearch_ore;
var jbottone_add;
var jpopup_lista_contatti;
var jbody_tabella_contatti;
var jdatalist_contatti;
var jdatalist_aziende;
var jdatalist_stabilimenti;
var jsearch_nome_risorsa;
var jsearch_nome_azienda_risorsa;
var jsearch_nome_stabilimento_risorsa;
var jbottone_iscrivi_corso;
var jbottone_rimuovi_iscritto;
var jinput_ore_effettive;

var lista_partecipanti = [];
var lista_partecipanti_ordinata = [];
var filtro_partecipanti = {};
var filtro_risorse = {};

//Elementi personalizzati end

jQuery(document).ready(function() {

    inizializzazione();

    inizializzazioneMaterialize();

    inizializzazioneExtra();

    myclock = window.setInterval(myTimer, 1000);

    //timer_check_offline = window.setInterval(checkConnection, 3000);

});

function inizializzazione() {

    larghezza_schermo = window.innerWidth;
    altezza_schermo = window.innerHeight;

    jtitolo_pagina = $("#titolo_pagina");
    jclock = $("#clock");

    jalert_offline = $("#alert_offline");
    jcaricamento = $(".caricamento");
    jpopup_salvataggio_in_corso = $("#popup_salvataggio_in_corso");

    window.addEventListener('resize', function() {
        reSize();
    }, false);

    var titolo_pagina_temp = "";
    titolo_pagina_temp += "<a href='#!' onclick='chiudiScheda()' class='breadcrumb' style='font-weight: bold; font-size: 24px !important; color: #2b577c !important;'>Corso di Formazione</a>";
    titolo_pagina_temp += "<a href='#!' class='breadcrumb' style='color: #2b577c !important;'>Lista Partecipazioni</a>";

    jtitolo_pagina.empty();
    jtitolo_pagina.html(titolo_pagina_temp);

    $.datetimepicker.setLocale('it');

    $(".campo_data").datetimepicker({
        step: 5,
        lang: 'it',
        format: 'd/m/Y',
        formatDate: 'd/m/Y',
        timepicker: false
    });

}

function inizializzazioneMaterialize() {

    Materialize.updateTextFields();

    $('select').material_select();

    $('.collapsible').collapsible({
        accordion: false // A setting that changes the collapsible behavior to expandable instead of the default accordion style
    });

    $('ul.tabs').tabs();

    $('.tooltipped').tooltip({ delay: 50 });

}

function reSize() {

    larghezza_schermo = window.innerWidth;
    altezza_schermo = window.innerHeight;

}

function reloadScrollBars() {
    document.documentElement.style.overflow = 'auto'; // firefox, chrome
    document.body.scroll = "yes"; // ie only
}

function unloadScrollBars() {
    document.documentElement.style.overflow = 'hidden'; // firefox, chrome
    document.body.scroll = "no"; // ie only
}

function in_array(needle, haystack, argStrict) {
    var key = '',
        strict = !!argStrict;
    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return true;
            }
        }
    }
    return false;
}

function myTimer() {

    now = new Date();

    var giorno = now.getDate();
    giorno = String("0" + giorno).slice(-2);

    var mese = now.getMonth() + 1;
    mese = String("0" + mese).slice(-2);

    var anno = now.getFullYear();
    anno = String("0" + anno).slice(-4);

    var ore = now.getHours();
    ore = String("0" + ore).slice(-2);

    var minuti = now.getMinutes();
    minuti = String("0" + minuti).slice(-2);

    var secondi = now.getSeconds();
    secondi = String("0" + secondi).slice(-2);

    var data = giorno + "/" + mese + "/" + anno;
    var ora = ore + ":" + minuti + ":" + secondi;

    jclock.html(data + " " + ora);

}

function checkConnection() {

    if (navigator.onLine) {

        isOnline = true;
        jalert_offline.closeModal();

    } else {

        isOnline = false;
        jalert_offline.openModal();

    }
}

function normalizzaData(data) {

    var data_normalizzata = "";
    var anno = "";
    var mese = "";
    var giorno = "";

    data = data.trim();

    var new_date = data.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "-");

    new_date_split = new_date.split("-");

    if (new_date_split.length == 3) {

        if (new_date_split[2].length == 4) {

            anno = new_date_split[2];
            mese = new_date_split[1];
            giorno = new_date_split[0];

        } else if (new_date_split[0].length == 4) {

            anno = new_date_split[0];
            mese = new_date_split[1];
            giorno = new_date_split[2];

        }

        if (anno != "") {

            anno = String("0" + anno).slice(-4);

            mese = String("0" + mese).slice(-2);

            giorno = String("0" + giorno).slice(-2);

            data_normalizzata = anno + "-" + mese + "-" + giorno;

        }

    }

    return data_normalizzata;

}

function chiudiScheda() {

    dhtmlx.confirm({
        type: "confirm",
        text: "Eseguendo questa operazione la schermata corrente verrà chiusa; continuare?",
        callback: function(result) {

            if (result == true) {

                self.close();

            }

        }
    });

}

function inizializzazioneExtra() {

    jbody_tabella_partecipanti = $("#body_tabella_partecipanti");
    jbottone_chiudi_scheda = $("#bottone_chiudi_scheda");
    jsearch_nome_partecipante = $("#search_nome_partecipante");
    jsearch_nome_azienda = $("#search_nome_azienda");
    jsearch_nome_stabilimento = $("#search_nome_stabilimento");
    jsearch_ore = $("#search_ore");
    jbottone_add = $("#bottone_add");
    jpopup_lista_contatti = $("#popup_lista_contatti");
    jbody_tabella_contatti = $("#body_tabella_contatti");
    jdatalist_contatti = $("#datalist_contatti");
    jdatalist_aziende = $("#datalist_aziende");
    jdatalist_stabilimenti = $("#datalist_stabilimenti");
    jsearch_nome_risorsa = $("#search_nome_risorsa");
    jsearch_nome_azienda_risorsa = $("#search_nome_azienda_risorsa");
    jsearch_nome_stabilimento_risorsa = $("#search_nome_stabilimento_risorsa");

    jbottone_chiudi_scheda.click(function() {

        chiudiScheda();

    });

    popolaDatalistContatti();

    popolaDatalistAziende();

    popolaDatalistStabilimenti();

    caricaDatiCorsoDiFormazione();

    filtro_partecipanti = {
        crmid: crmid,
        nome_partecipante: "",
        nome_azienda: "",
        nome_stabilimento: "",
        ore_effettive: ""
    };

    caricaListaPartecipanti(filtro_partecipanti);

    filtro_risorse = {
        crmid: crmid,
        nome_risorsa: "",
        nome_azienda: "",
        nome_stabilimento: ""
    };

    caricaListaRisorse(filtro_risorse);

    jsearch_nome_partecipante.keyup(function(ev) {

        var nome_partecipante_temp = jsearch_nome_partecipante.val();

        var code = ev.which;
        if (code == 13 || nome_partecipante_temp == "") {

            filtro_partecipanti.crmid = crmid;
            filtro_partecipanti.nome_partecipante = nome_partecipante_temp;
            caricaListaPartecipanti(filtro_partecipanti);

        }

    });

    jsearch_nome_azienda.keyup(function(ev) {

        var nome_azienda_temp = jsearch_nome_azienda.val();

        var code = ev.which;
        if (code == 13 || nome_azienda_temp == "") {

            filtro_partecipanti.crmid = crmid;
            filtro_partecipanti.nome_azienda = nome_azienda_temp;
            caricaListaPartecipanti(filtro_partecipanti);

        }

    });

    jsearch_nome_stabilimento.keyup(function(ev) {

        var nome_stabilimento_temp = jsearch_nome_stabilimento.val();

        var code = ev.which;
        if (code == 13 || nome_stabilimento_temp == "") {

            filtro_partecipanti.crmid = crmid;
            filtro_partecipanti.nome_stabilimento = nome_stabilimento_temp;
            caricaListaPartecipanti(filtro_partecipanti);

        }

    });

    jsearch_ore.keyup(function(ev) {

        var ore_temp = jsearch_ore.val();

        var code = ev.which;
        if (code == 13 || ore_temp == "") {

            filtro_partecipanti.crmid = crmid;
            filtro_partecipanti.ore_effettive = ore_temp;
            caricaListaPartecipanti(filtro_partecipanti);

        }

    });

    jsearch_ore.change(function() {

        var ore_temp = jsearch_ore.val();

        filtro_partecipanti.crmid = crmid;
        filtro_partecipanti.ore_effettive = ore_temp;
        caricaListaPartecipanti(filtro_partecipanti);

    });

    jbottone_add.click(function() {

        apriPopupListaContatti();

    });

    jsearch_nome_risorsa.keyup(function(ev) {

        var nome_risorsa_temp = jsearch_nome_risorsa.val();

        var code = ev.which;
        if (code == 13 || nome_risorsa_temp == "") {

            filtro_risorse.crmid = crmid;
            filtro_risorse.nome_risorsa = nome_risorsa_temp;

            caricaListaRisorse(filtro_risorse);

        }

    });

    jsearch_nome_azienda_risorsa.keyup(function(ev) {

        var nome_azienda_temp = jsearch_nome_azienda_risorsa.val();

        var code = ev.which;
        if (code == 13 || nome_azienda_temp == "") {

            filtro_risorse.crmid = crmid;
            filtro_risorse.nome_azienda = nome_azienda_temp;

            caricaListaRisorse(filtro_risorse);

        }

    });

    jsearch_nome_stabilimento_risorsa.keyup(function(ev) {

        var nome_stabilimento_temp = jsearch_nome_stabilimento_risorsa.val();

        var code = ev.which;
        if (code == 13 || nome_stabilimento_temp == "") {

            filtro_risorse.crmid = crmid;
            filtro_risorse.nome_stabilimento = nome_stabilimento_temp;

            caricaListaRisorse(filtro_risorse);

        }

    });

}

function caricaDatiCorsoDiFormazione() {

    var dati = {
        crmid: crmid,
    };

    jQuery.ajax({
        url: 'DatiFormazione.php',
        dataType: 'json',
        async: true,
        data: dati,
        success: function(data) {

            //console.table(data);

            var lista_partecipanti_temp = "";

            if (data.length > 0) {

                var titolo_pagina_temp = "";
                titolo_pagina_temp += "<a href='#!' onclick='chiudiScheda()' class='breadcrumb' style='font-weight: bold; font-size: 24px !important; color: #2b577c !important;'>" + data[0].nome_corso + "</a>";
                titolo_pagina_temp += "<a href='#!' class='breadcrumb' style='color: #2b577c !important;'>Lista Partecipazioni</a>";

                jtitolo_pagina.empty();
                jtitolo_pagina.html(titolo_pagina_temp);

            }

        },
        fail: function() {
            console.error("Errore nel caricamento dei dati della formazione");
        }
    });

}

function caricaListaPartecipanti(filtro) {

    lista_partecipanti = [];
    lista_partecipanti_ordinata = [];

    jQuery.ajax({
        url: 'ListaPartecipanti.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

            jcaricamento.show();

        },
        success: function(data) {

            //console.table(data);

            var lista_partecipanti_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    lista_partecipanti_temp += "<tr id=" + data[i].partecipformazid + " class='tr_lista_partecipanti'>";
                    lista_partecipanti_temp += "<td style='text-align: center;'><a id='rimuovi_" + data[i].partecipformazid + "' class='bottone_rimuovi_iscritto btn-floating btn-medium waves-effect waves-light red' title='Disiscrivi'><i class='material-icons'>clear</i></a></td>";
                    lista_partecipanti_temp += "<td>" + data[i].nome_risorsa + "</td>";
                    lista_partecipanti_temp += "<td>" + data[i].nome_azienda + "</td>";
                    lista_partecipanti_temp += "<td>" + data[i].nome_stabilimento + "</td>";
                    lista_partecipanti_temp += "<td style='padding-right: 10px;'><input style='text-align: right;' class='input_ore_effettive' id='ore_eff_" + data[i].partecipformazid + "' type='number' value=" + data[i].tot_ore_effet + " /></td>";
                    lista_partecipanti_temp += "</tr>";

                    lista_partecipanti[data[i].partecipformazid] = {
                        indice: i + 1,
                        partecipformazid: data[i].partecipformazid,
                        nome_risorsa: data[i].nome_risorsa,
                        nome_azienda: data[i].nome_azienda,
                        nome_stabilimento: data[i].nome_stabilimento
                    };

                    lista_partecipanti_ordinata[i + 1] = {
                        partecipformazid: data[i].partecipformazid
                    }

                }

            } else {
                lista_partecipanti_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessun partecipante trovato!</em></td></tr>";
            }

            jbody_tabella_partecipanti.empty();
            jbody_tabella_partecipanti.append(lista_partecipanti_temp);

            jcaricamento.hide();

            jbottone_rimuovi_iscritto = $(".bottone_rimuovi_iscritto");
            jinput_ore_effettive = $(".input_ore_effettive");

            jbottone_rimuovi_iscritto.click(function() {

                var partecipante_id_temp = $(this).attr("id");
                partecipante_id_temp = partecipante_id_temp.substring(8, partecipante_id_temp.length);

                dhtmlx.confirm({
                    type: "confirm",
                    text: "Eseguendo questa operazione il partecipante verrà rimosso; continuare?",
                    callback: function(result) {

                        if (result == true) {

                            rimuoviPartecipante(partecipante_id_temp);

                        }

                    }
                });

            });

            jinput_ore_effettive.change(function() {

                var partecipante_id_temp = $(this).attr("id");
                partecipante_id_temp = partecipante_id_temp.substring(8, partecipante_id_temp.length);

                var ore_effettive_temp = $("#ore_eff_" + partecipante_id_temp).val();

                aggiornaOrePartecipante(partecipante_id_temp, ore_effettive_temp);

            });

        },
        fail: function() {
            console.error("Errore nel caricamento della lista partecipanti");
            jcaricamento.hide();
        }
    });

}

function apriPopupListaContatti() {

    jpopup_lista_contatti.openModal();

}

function caricaListaRisorse(filtro) {
    
    jQuery.ajax({
        url: 'ListaRisorse.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

            jcaricamento.show();

        },
        success: function(data) {

            //console.table(data);

            var lista_contatti_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    lista_contatti_temp += "<tr id=" + data[i].risorsa + " class='tr_lista_partecipanti'>";
                    lista_contatti_temp += "<td style='text-align: center;'><a id='iscrivi_" + data[i].risorsa + "' class='bottone_iscrivi_corso btn-floating btn-medium waves-effect waves-light amber' title='Iscrivi'><i class='material-icons'>person_add</i></a></td>";
                    lista_contatti_temp += "<td>" + data[i].nome_risorsa + "</td>";
                    lista_contatti_temp += "<td>" + data[i].nome_azienda + "</td>";
                    lista_contatti_temp += "<td>" + data[i].nome_stabilimento + "</td>";
                    lista_contatti_temp += "</tr>";

                }

            } else {
                lista_contatti_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessuna risorsa trovata!</em></td></tr>";
            }

            jbody_tabella_contatti.empty();
            jbody_tabella_contatti.append(lista_contatti_temp);

            jcaricamento.hide();

            jbottone_iscrivi_corso = $(".bottone_iscrivi_corso");
            jbottone_iscrivi_corso.click(function() {

                var risorsa_id_temp = $(this).attr("id");
                risorsa_id_temp = risorsa_id_temp.substring(8, risorsa_id_temp.length);

                iscriviRisorsa(risorsa_id_temp);

            });

        },
        fail: function() {
            console.error("Errore nel caricamento della lista risorse");
            jcaricamento.hide();
        }
    });

}

function popolaDatalistContatti() {

    jQuery.ajax({
        url: 'DataListContatti.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            var datalist_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    datalist_temp += '<option value="' + data[i].nome_risorsa + '">'; /* kpro@bid300420180900 */

                }

            }

            jdatalist_contatti.empty();
            jdatalist_contatti.append(datalist_temp);

        },
        fail: function() {
            console.error("Errore caricamento del datalist");
        }
    });

}

function popolaDatalistAziende() {

    jQuery.ajax({
        url: 'DataListAziende.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            var datalist_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    datalist_temp += '<option value="' + data[i].nome_azienda + '">'; /* kpro@bid300420180900 */

                }

            }

            jdatalist_aziende.empty();
            jdatalist_aziende.append(datalist_temp);

        },
        fail: function() {
            console.error("Errore caricamento del datalist");
        }
    });

}

function popolaDatalistStabilimenti() {

    jQuery.ajax({
        url: 'DataListStabilimenti.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            var datalist_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    datalist_temp += '<option value="' + data[i].nome_stabilimento + '">'; /* kpro@bid300420180900 */

                }

            }

            jdatalist_stabilimenti.empty();
            jdatalist_stabilimenti.append(datalist_temp);

        },
        fail: function() {
            console.error("Errore caricamento del datalist");
        }
    });

}

function iscriviRisorsa(risorsa) {

    var dati = {
        crmid: crmid,
        risorsa: risorsa,
        mode: "add"
    };

    jQuery.ajax({
        url: 'AggiornaPartecipazioniCorso.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

            jcaricamento.show();

        },
        success: function(data) {

            //console.table(data);

            caricaListaPartecipanti(filtro_partecipanti);

            caricaListaRisorse(filtro_risorse);

            jcaricamento.hide();

        },
        fail: function() {
            console.error("Errore nell'aggiornamento della partecipazione");
            jcaricamento.hide();
        }
    });

}

function rimuoviPartecipante(partecipante) {

    var dati = {
        crmid: crmid,
        partecipante: partecipante,
        mode: "remove"
    };

    jQuery.ajax({
        url: 'AggiornaPartecipazioniCorso.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

            jcaricamento.show();

        },
        success: function(data) {

            //console.table(data);

            caricaListaRisorse(filtro_risorse);

            caricaListaPartecipanti(filtro_partecipanti);

            jcaricamento.hide();

        },
        fail: function() {
            console.error("Errore nell'aggiornamento della partecipazione");
            jcaricamento.hide();
        }
    });

}

function aggiornaOrePartecipante(partecipante, ore_effettive) {

    var dati = {
        crmid: crmid,
        partecipante: partecipante,
        ore_effettive: ore_effettive,
        mode: "edit_ore_effettive"
    };

    jQuery.ajax({
        url: 'AggiornaPartecipazioniCorso.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

            jcaricamento.show();

        },
        success: function(data) {

            //console.table(data);

            jcaricamento.hide();

        },
        fail: function() {
            console.error("Errore nell'aggiornamento della partecipazione");
            jcaricamento.hide();
        }
    });

}
