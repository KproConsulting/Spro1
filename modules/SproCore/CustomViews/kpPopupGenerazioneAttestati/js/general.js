/* kpro@tom04072017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package gestioneAttestati
 * @version 1.0
 */

var jbody_tabella_template;
var jtemplate_check;
var jbottone_genera;
var jcaricamento;
var jsearch_nome_template;
var jform_invia_mail;

var larghezza_schermo;
var altezza_schermo;
var in_salvataggio = false;
var template = [];

window.addEventListener("load", function() {

    inizializza();

    inizializzazioneMaterialize();

});

function reSize() {

    larghezza_schermo = window.innerWidth;
    altezza_schermo = window.innerHeight;

}

function inizializzazioneMaterialize() {

    Materialize.updateTextFields();

}

function inizializza() {

    larghezza_schermo = window.innerWidth;
    altezza_schermo = window.innerHeight;

    jbody_tabella_template = jQuery("#body_tabella_template");
    jbottone_genera = jQuery("#bottone_genera");
    jcaricamento = jQuery("#caricamento");
    jsearch_nome_template = jQuery("#search_nome_template");
    jform_invia_mail = jQuery("#form_invia_mail");

    window.addEventListener('resize', function() {
        reSize();
    }, false);

    var filtro_template = {
        modulo: "KpPartecipFormaz",
        nome_template: ""
    };

    caricaListaTemplate(filtro_template);

    jsearch_nome_template.keyup(function() {

        filtro_template.modulo = "KpPartecipFormaz";
        filtro_template.nome_template = jsearch_nome_template.val();

        caricaListaTemplate(filtro_template);

    });

    jbottone_genera.click(function() {

        template = [];

        jQuery(".template_check:checked").each(function() {

            template.push(jQuery(this).prop("id")); /* kpro@bid090520181030 */

        });

        if (template.length == 0) {
            Materialize.toast('Selezionare almeno un template', 4000);
        } else {

            if (!in_salvataggio) {

                in_salvataggio = true;

                var invia_mail_temp = jform_invia_mail.prop('checked'); /* kpro@bid090520181030 */

                generaTemplatePdf(template, invia_mail_temp);

            }
        }

    });

}

function chiudiPopUp() {

    //closePopup();
    parent.location.reload();

}

function caricaListaTemplate(filtro) {

    jQuery.ajax({
        url: 'modules/SproCore/CustomViews/kpPopupGenerazioneAttestati/ListaTemplate.php',
        dataType: 'json',
        async: true,
        data: filtro,
        success: function(data) {

            //console.table(data);

            var lista_template_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    lista_template_temp += "<tr>";
                    lista_template_temp += "<td>";
                    if (i == 0) {
                        lista_template_temp += "<input type='checkbox' class='template_check' id='" + data[i].templateid + "' checked='checked'/>";
                    } else {
                        lista_template_temp += "<input type='checkbox' class='template_check' id='" + data[i].templateid + "'/>";
                    }
                    lista_template_temp += "<label for='" + data[i].templateid + "'>" + data[i].filename + "</label>";
                    lista_template_temp += "</td>";
                    lista_template_temp += "</tr>";

                }

            } else {
                lista_template_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessun template trovato!</em></td></tr>"
            }

            jbody_tabella_template.empty();
            jbody_tabella_template.append(lista_template_temp);

            jtemplate_check = jQuery(".template_check");

            jtemplate_check.change(function() {

                jtemplate_check.prop('checked', false);
                jQuery(this).prop('checked', true);

            });

        },
        fail: function() {
            console.error("Errore nel caricamento della lista template");
        }
    });

}

function generaTemplatePdf(templateid, invia_mail) {

    jQuery.ajax({
        url: 'modules/SproCore/CustomViews/kpPopupGenerazioneAttestati/GeneraTemplatePdf.php',
        dataType: 'json',
        data: 'record=' + crmid + '&invia_mail=' + invia_mail + '&templates=' + templateid,
        async: true,
        beforeSend: function() {

            jcaricamento.show();

        },
        success: function(data) {

            //console.log(data);

            chiudiPopUp();
            jcaricamento.hide();

        },
        fail: function() {

            console.log("Errore nel salvataggio")
            jcaricamento.hide();

        }
    });

}
