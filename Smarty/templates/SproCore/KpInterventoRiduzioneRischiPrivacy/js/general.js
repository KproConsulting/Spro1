/* kpro@tom18072017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

var altezza_schermo;
var larghezza_schermo;
var readonly = false;
var inSalvataggio = false;

var record;

var jform;
var jbottone_modifica;
var jbottone_salva;
var jbottone_annulla;

var jbody_tabella_intervento_riduzione_rischi;
var jform_probabilita;
var jform_magnitudo;
var jform_misura_applicata;
var jbottone_disattiva_misura;
var jdisattivaMisuraModal;
var jpopup_probabilita;
var jpopup_magnitudo;
var jpopup_rischio;
var jpopup_frase_di_rischio;
var jbottone_salva_disattivazione_misura;
var jmisura_da_disattivare;
var jhelpProbabilitaModal;
var jhelpMagnitudoModal;
var jhelp_probabilita;
var jhelp_magnitudo;
var jnome_minaccia_nota;
var jpopup_nota_probabilita;
var jpopup_nota_magnitudo;
var jpopup_nota_rischio;
var jpopup_nota_frase_di_rischio;
var jpopup_nota;
var jbottone_salva_nota;
var jhelpNoteModal;

var valori_minaccia_impianto = [];
var valori_misure_minaccie = [];
var elemento_selezionato = 0;

jQuery(document).ready(function() {

    record = getObj('record').value;

    inizializzazione();

    inizializzazioneExtra();

});

function inizializzazione() {

    jbottone_modifica = jQuery("#bottone_modifica");
    jbottone_salva = jQuery("#bottone_salva");
    jbottone_annulla = jQuery("#bottone_annulla");
    jdisattivaMisuraModal = jQuery('#disattivaMisuraModal');

    jpopup_probabilita = jQuery('#popup_probabilita');
    jpopup_magnitudo = jQuery('#popup_magnitudo');
    jpopup_rischio = jQuery('#popup_rischio');
    jpopup_frase_di_rischio = jQuery('#popup_frase_di_rischio');
    jbottone_salva_disattivazione_misura = jQuery('#bottone_salva_disattivazione_misura');
    jmisura_da_disattivare = jQuery('.misura_da_disattivare');

    window.addEventListener('resize', function() {
        reSize();
    }, false);

    reSize();

    jQuery('[data-toggle="tooltip"]').tooltip();

    jbottone_modifica.click(function() {

        sbloccaForm();

    });

    jbottone_annulla.click(function() {

        getInterventoRiduzioneRischio(record)

    });

    jbottone_salva.click(function() {

        if (!readonly && !inSalvataggio) {

            inSalvataggio = true;

            bloccaForm();

            leggiForm();

        }

    });

    jpopup_probabilita.change(function() {

        var probabilita = jQuery(this).val();

        var magnitudo = jpopup_magnitudo.val();
        if (magnitudo != "" && magnitudo != "-" && probabilita != "" && probabilita != "-") {

            magnitudo = parseInt(magnitudo);
            probabilita = parseInt(probabilita);

            var rischio = magnitudo * probabilita;

            jpopup_rischio.val(rischio);

            jpopup_frase_di_rischio.html(getFraseDiRischio(rischio));

        }

    });

    jpopup_magnitudo.change(function() {

        var magnitudo = jQuery(this).val();

        var probabilita = jpopup_probabilita.val();
        if (magnitudo != "" && magnitudo != "-" && probabilita != "" && probabilita != "-") {

            magnitudo = parseInt(magnitudo);
            probabilita = parseInt(probabilita);

            var rischio = magnitudo * probabilita;

            jpopup_rischio.val(rischio);

            jpopup_frase_di_rischio.html(getFraseDiRischio(rischio));

        }

    });

    jbottone_salva_disattivazione_misura.click(function() {

        if (!inSalvataggio && elemento_selezionato != "" && elemento_selezionato != 0) {

            inSalvataggio = true;

            salvaDisattivazioneMisura(elemento_selezionato);

        }

    });

}

function reSize() {

    larghezza_schermo = window.innerWidth;
    altezza_schermo = window.innerHeight;

}

function bloccaForm() {

    jform = jQuery('[id*="form_"]');
    readonly = true;
    jform.prop("disabled", true);
    jbottone_disattiva_misura.prop("disabled", false);
    jbottone_nota.prop("disabled", false);

    jbottone_salva.hide();
    jbottone_annulla.hide();
    jbottone_modifica.show();

}

function sbloccaForm() {

    jform = jQuery('[id*="form_"]');
    readonly = false;
    jform.prop("disabled", false);
    jbottone_disattiva_misura.prop("disabled", true);
    jbottone_nota.prop("disabled", true);

    jbottone_modifica.hide();
    jbottone_salva.show();
    jbottone_annulla.show();

}

function inizializzazioneExtra() {

    jbody_tabella_intervento_riduzione_rischi = jQuery("#body_tabella_intervento_riduzione_rischi");
    jhelpProbabilitaModal = jQuery("#helpProbabilitaModal");
    jhelpMagnitudoModal = jQuery("#helpMagnitudoModal");
    jhelp_probabilita = jQuery(".help_probabilita"); //kpro@tom230320181207
    jhelp_magnitudo = jQuery(".help_magnitudo"); //kpro@tom230320181207
    jnome_minaccia_nota = jQuery(".nome_minaccia_nota");
    jpopup_nota_probabilita = jQuery("#popup_nota_probabilita");
    jpopup_nota_magnitudo = jQuery("#popup_nota_magnitudo");
    jpopup_nota_rischio = jQuery("#popup_nota_rischio");
    jpopup_nota_frase_di_rischio = jQuery("#popup_nota_frase_di_rischio");
    jpopup_nota = jQuery("#popup_nota");
    jbottone_salva_nota = jQuery("#bottone_salva_nota");
    jhelpNoteModal = jQuery("#helpNoteModal");

    getInterventoRiduzioneRischio(record);

    jbottone_salva_nota.click(function() {

        if (!inSalvataggio && elemento_selezionato != 0) {

            inSalvataggio = true;

            setNotaInterventoRiduzioneRischio(elemento_selezionato);

        }

    });

}

function getInterventoRiduzioneRischio(id) {

    var dati = {
        id: id
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpInterventoRiduzioneRischiPrivacy/GetInterventoRiduzioneRischio.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

        },
        success: function(data) {

            //console.table(data);

            var lista_interventi_temp = "";
            var id_impianto = "";
            valori_minaccia_impianto = [];
            valori_misure_minaccie = [];

            for (var i = 0; i < data.length; i++) {

                id_impianto = data[i].impianto;

                lista_interventi_temp += "<tr>";
                lista_interventi_temp += "<td style='vertical-align: top; padding-top: 75px;'>";
                lista_interventi_temp += "<b>" + data[i].nome_impianto + "</b>";
                lista_interventi_temp += "</td>";

                lista_interventi_temp += "<td>";
                lista_interventi_temp += getTabellaMinacceImpianto(data[i], data[i].lista_minacce);
                lista_interventi_temp += "</td>";

                lista_interventi_temp += "</tr>";

            }

            jbody_tabella_intervento_riduzione_rischi.empty();
            jbody_tabella_intervento_riduzione_rischi.append(lista_interventi_temp);

            jform_probabilita = jQuery(".form_probabilita");
            jform_magnitudo = jQuery(".form_magnitudo");
            jform_misura_applicata = jQuery(".form_misura_applicata");
            jbottone_disattiva_misura = jQuery(".bottone_disattiva_misura");
            jhelp_probabilita = jQuery(".help_probabilita"); //kpro@tom230320181207
            jhelp_magnitudo = jQuery(".help_magnitudo"); //kpro@tom230320181207
            jbottone_nota = jQuery(".bottone_nota");

            jQuery('[data-toggle="tooltip"]').tooltip();

            bloccaForm();
            inSalvataggio = false;
            elemento_selezionato = 0;

            jform_probabilita.change(function() {

                var elemento_temp = jQuery(this).prop("id");
                elemento_temp = elemento_temp.substring(17, elemento_temp.length);

                var probabilita = jQuery(this).val();

                var magnitudo = jQuery("#form_magnitudo_" + elemento_temp).val();
                if (magnitudo != "" && magnitudo != "-" && probabilita != "" && probabilita != "-") {

                    magnitudo = parseInt(magnitudo);
                    probabilita = parseInt(probabilita);

                    var rischio = magnitudo * probabilita;

                    jQuery("#readonly_rischio_" + elemento_temp).val(rischio);

                    jQuery("#readonly_frase_rischio_" + elemento_temp).html(getFraseDiRischio(rischio));

                }

            });

            jform_magnitudo.change(function() {

                var elemento_temp = jQuery(this).prop("id");
                elemento_temp = elemento_temp.substring(15, elemento_temp.length);

                var magnitudo = jQuery(this).val();

                var probabilita = jQuery("#form_probabilita_" + elemento_temp).val();
                if (magnitudo != "" && magnitudo != "-" && probabilita != "" && probabilita != "-") {

                    magnitudo = parseInt(magnitudo);
                    probabilita = parseInt(probabilita);

                    var rischio = magnitudo * probabilita;

                    jQuery("#readonly_rischio_" + elemento_temp).val(rischio);

                    jQuery("#readonly_frase_rischio_" + elemento_temp).html(getFraseDiRischio(rischio));

                }

            });

            jform_misura_applicata.change(function() {

                var elemento_temp = jQuery(this).prop("id");
                elemento_temp = elemento_temp.substring(22, elemento_temp.length);

                var misura_applicata = jQuery(this).val();

                var elemento_temp_array = elemento_temp.split("_");

                var impianto = elemento_temp_array[0];
                var minaccia = elemento_temp_array[1];
                var misura = elemento_temp_array[2];

                if (jQuery("#readonly_rischio_" + impianto + "_" + minaccia).val() == "-") {

                    jQuery("#form_probabilita_" + impianto + "_" + minaccia).val(valori_minaccia_impianto[impianto + "_" + minaccia].probabilita_pre);

                    jQuery("#form_magnitudo_" + impianto + "_" + minaccia).val(valori_minaccia_impianto[impianto + "_" + minaccia].magnitudo_pre);

                    var magnitudo = parseInt(valori_minaccia_impianto[impianto + "_" + minaccia].magnitudo_pre);
                    var probabilita = parseInt(valori_minaccia_impianto[impianto + "_" + minaccia].probabilita_pre);

                    var rischio = magnitudo * probabilita;

                    jQuery("#readonly_rischio_" + impianto + "_" + minaccia).val(rischio);

                    jQuery("#readonly_frase_rischio_" + impianto + "_" + minaccia).html(getFraseDiRischio(rischio));

                }

            });

            jbottone_disattiva_misura.click(function() {

                if (readonly && !inSalvataggio) {

                    var elemento_temp = jQuery(this).prop("id");
                    elemento_temp = elemento_temp.substring(23, elemento_temp.length);

                    elemento_selezionato = elemento_temp;

                    //console.table(valori_misure_minaccie);
                    //console.log(elemento_selezionato);

                    jmisura_da_disattivare.prop("id", elemento_selezionato);
                    jmisura_da_disattivare.html("Misura da disattivare: <b>" + valori_misure_minaccie[elemento_selezionato].nome_misura + "</b>");

                    var elemento_temp_array = elemento_temp.split("_");

                    var impianto = elemento_temp_array[0];
                    var minaccia = elemento_temp_array[1];
                    var misura = elemento_temp_array[2];

                    jpopup_probabilita.val(valori_minaccia_impianto[impianto + "_" + minaccia].probabilita_pre);
                    jpopup_magnitudo.val(valori_minaccia_impianto[impianto + "_" + minaccia].magnitudo_pre);

                    var magnitudo = parseInt(valori_minaccia_impianto[impianto + "_" + minaccia].magnitudo_pre);
                    var probabilita = parseInt(valori_minaccia_impianto[impianto + "_" + minaccia].probabilita_pre);

                    var rischio = magnitudo * probabilita;

                    jpopup_rischio.val(rischio);

                    jpopup_frase_di_rischio.html(getFraseDiRischio(rischio));

                    jdisattivaMisuraModal.modal('show');

                } else {

                    alert("Salvare l'intervento prima di procedere con la disattivazione delle misure!");

                }

            });

            jhelp_probabilita.click(function() {

                jhelpProbabilitaModal.modal('show');

            });

            jhelp_magnitudo.click(function() {

                jhelpMagnitudoModal.modal('show');

            });

            jbottone_nota.click(function() {

                var elemento_temp = jQuery(this).prop("id");
                elemento_temp = elemento_temp.substring(13, elemento_temp.length);

                getPopupNotaRilevazioneRischio(elemento_temp);

            });


        },
        fail: function() {

        }
    });

}

function getTabellaMinacceImpianto(dati_impianto, lista_minacce) {

    var lista_minacce_temp = "";
    var id_minaccia_impianto = "";

    lista_minacce_temp = "<table style='width: 100%; margin: 0px;' class='table table-striped'>";
    lista_minacce_temp += "<thead>";
    lista_minacce_temp += "<tr>";
    lista_minacce_temp += "<th style='text-align: left;'></th>";
    lista_minacce_temp += "<th colspan='2' style='text-align: center;'>Pre Intervento</th>";
    lista_minacce_temp += "<th style='text-align: center;'></th>";
    lista_minacce_temp += "<th colspan='4' style='text-align: center;'>Post Intervento</th>";
    lista_minacce_temp += "</tr>";
    lista_minacce_temp += "<tr>";
    lista_minacce_temp += "<th style='text-align: left;'>Minaccia</th>";
    //lista_minacce_temp += "<th style='width: 160px; text-align: center;'>Probabilità</th>";
    //lista_minacce_temp += "<th style='width: 140px; text-align: center;'>Magnitudo</th>";
    lista_minacce_temp += "<th style='width: 100px; text-align: center;'>Rischio</th>";
    lista_minacce_temp += "<th style='width: 120px; text-align: center;'>Frase di Rischio</th>";
    lista_minacce_temp += "<th style='width: 500px;'>Dati Intervento</th>";
    //kpro@tom230320181207
    lista_minacce_temp += "<th style='width: 160px; text-align: center;'>Probabilità <a href='#' class='help_probabilita'><span class='glyphicon glyphicon-question-sign'></span></a></th>";
    lista_minacce_temp += "<th style='width: 140px; text-align: center;'>Magnitudo <a href='#' class='help_magnitudo'><span class='glyphicon glyphicon-question-sign'></span></a></th>";
    //kpro@tom230320181207 end
    lista_minacce_temp += "<th style='width: 100px; text-align: center;'>Rischio</th>";
    lista_minacce_temp += "<th style='width: 120px; text-align: center;'>Frase di Rischio</th>";
    lista_minacce_temp += "</tr>";
    lista_minacce_temp += "</thead>";
    lista_minacce_temp += "<tbody>";

    for (var i = 0; i < lista_minacce.length; i++) {

        id_minaccia_impianto = dati_impianto.impianto + "_" + lista_minacce[i].id;

        lista_minacce_temp += "<tr>";

        lista_minacce_temp += "<td style='vertical-align: middle; vertical-align: top; padding-top: 20px;'>";
        lista_minacce_temp += lista_minacce[i].nome;
        lista_minacce_temp += "</td>";

        /*lista_minacce_temp += "<td style='text-align: center; padding-top: 20px;'>";
        lista_minacce_temp += "<div class='form-group'>";
        lista_minacce_temp += "<select style='vertical-align: top !important; text-align: center !important;' class='readonly_probabilita form-control' id='readonly_probabilita_" + id_minaccia_impianto + "' disabled >";
        lista_minacce_temp += "<option value='-' selected='selected'></option>";

        if (lista_minacce[i].probabilita_pre == 1) {
            lista_minacce_temp += "<option value='1' selected='selected' >1 - Improbabile</option>";
        } else {
            lista_minacce_temp += "<option value='1'>1 - Improbabile</option>";
        }

        if (lista_minacce[i].probabilita_pre == 2) {
            lista_minacce_temp += "<option value='2' selected='selected' >2 - Raro</option>";
        } else {
            lista_minacce_temp += "<option value='2'>2 - Raro</option>";
        }

        if (lista_minacce[i].probabilita_pre == 3) {
            lista_minacce_temp += "<option value='3' selected='selected' >3 - Possibile</option>";
        } else {
            lista_minacce_temp += "<option value='3'>3 - Possibile</option>";
        }

        if (lista_minacce[i].probabilita_pre == 4) {
            lista_minacce_temp += "<option value='4' selected='selected' >4 - Probabile</option>";
        } else {
            lista_minacce_temp += "<option value='4'>4 - Probabile</option>";
        }

        if (lista_minacce[i].probabilita_pre == 5) {
            lista_minacce_temp += "<option value='5' selected='selected' >5 - Molto Probabile</option>";
        } else {
            lista_minacce_temp += "<option value='5'>5 - Molto Probabile</option>";
        }

        lista_minacce_temp += "</select>";
        lista_minacce_temp += "</div>";
        lista_minacce_temp += "</td>";

        lista_minacce_temp += "<td style='text-align: center; padding-top: 20px;'>";
        lista_minacce_temp += "<div class='form-group'>";
        lista_minacce_temp += "<select style='vertical-align: top !important; text-align: center !important;' class='readonly_magnitudo form-control' id='readonly_magnitudo_" + id_minaccia_impianto + "' disabled >";
        lista_minacce_temp += "<option value='-' selected='selected'></option>";

        if (lista_minacce[i].magnitudo_pre == 1) {
            lista_minacce_temp += "<option value='1' selected='selected' >1 - Trascurabile</option>";
        } else {
            lista_minacce_temp += "<option value='1'>1 - Trascurabile</option>";
        }

        if (lista_minacce[i].magnitudo_pre == 2) {
            lista_minacce_temp += "<option value='2' selected='selected' >2 - Contenuto</option>";
        } else {
            lista_minacce_temp += "<option value='2'>2 - Contenuto</option>";
        }

        if (lista_minacce[i].magnitudo_pre == 3) {
            lista_minacce_temp += "<option value='3' selected='selected' >3 - Significativo</option>";
        } else {
            lista_minacce_temp += "<option value='3'>3 - Significativo</option>";
        }

        if (lista_minacce[i].magnitudo_pre == 4) {
            lista_minacce_temp += "<option value='4' selected='selected' >4 - Rilevante</option>";
        } else {
            lista_minacce_temp += "<option value='4'>4 - Rilevante</option>";
        }

        if (lista_minacce[i].magnitudo_pre == 5) {
            lista_minacce_temp += "<option value='5' selected='selected' >5 - Catastrofico</option>";
        } else {
            lista_minacce_temp += "<option value='5'>5 - Catastrofico</option>";
        }

        lista_minacce_temp += "</select>";
        lista_minacce_temp += "</div>";
        lista_minacce_temp += "</td>";*/

        lista_minacce_temp += "<td style='vertical-align: top !important; text-align: center; padding-top: 20px;'>";
        lista_minacce_temp += "<div class='form-group'>";
        lista_minacce_temp += "<select style='text-align: center !important;' class='readonly_rischio_pre form-control' id='readonly_rischio_pre_" + id_minaccia_impianto + "' disabled >";
        lista_minacce_temp += "<option value='-' selected='selected'></option>";

        for (var y = 1; y <= 25; y++) {

            if (lista_minacce[i].rischio_pre == y) {
                lista_minacce_temp += "<option value='" + y + "' selected='selected' >" + y + "</option>";
            } else {
                lista_minacce_temp += "<option value='" + y + "'>" + y + "</option>";
            }

        }

        lista_minacce_temp += "</select>";
        lista_minacce_temp += "</div>";
        lista_minacce_temp += "</td>";

        lista_minacce_temp += "<td style='vertical-align: top !important; text-align: center; padding-top: 30px;' id='readonly_frase_rischio_pre_" + id_minaccia_impianto + "'>";

        if (lista_minacce[i].rischio_pre != "" && lista_minacce[i].rischio_pre != "-") {
            lista_minacce_temp += getFraseDiRischio(lista_minacce[i].rischio_pre);
        } else {
            lista_minacce_temp += "<span class='glyphicon glyphicon-alert' style='color: red;' data-toggle='tooltip' data-placement='top' title='Rischio non rilevato!'></span>";
        }

        lista_minacce_temp += "</td>";

        lista_minacce_temp += "<td>";
        lista_minacce_temp += getTabellaMisureRiduzioneMinacciaImpianto(dati_impianto, lista_minacce[i], lista_minacce[i].lista_misure);
        lista_minacce_temp += "</td>";

        lista_minacce_temp += "<td style='text-align: center; padding-top: 20px;'>";
        lista_minacce_temp += "<div class='form-group'>";
        lista_minacce_temp += "<select style='vertical-align: top !important; text-align: center !important;' class='form_probabilita form-control' id='form_probabilita_" + id_minaccia_impianto + "'>";
        lista_minacce_temp += "<option value='-' selected='selected'></option>";

        if (lista_minacce[i].probabilita_post == 1) {
            lista_minacce_temp += "<option value='1' selected='selected' >1 - Improbabile</option>";
        } else {
            lista_minacce_temp += "<option value='1'>1 - Improbabile</option>";
        }

        if (lista_minacce[i].probabilita_post == 2) {
            lista_minacce_temp += "<option value='2' selected='selected' >2 - Raro</option>";
        } else {
            lista_minacce_temp += "<option value='2'>2 - Raro</option>";
        }

        if (lista_minacce[i].probabilita_post == 3) {
            lista_minacce_temp += "<option value='3' selected='selected' >3 - Possibile</option>";
        } else {
            lista_minacce_temp += "<option value='3'>3 - Possibile</option>";
        }

        if (lista_minacce[i].probabilita_post == 4) {
            lista_minacce_temp += "<option value='4' selected='selected' >4 - Probabile</option>";
        } else {
            lista_minacce_temp += "<option value='4'>4 - Probabile</option>";
        }

        if (lista_minacce[i].probabilita_post == 5) {
            lista_minacce_temp += "<option value='5' selected='selected' >5 - Molto Probabile</option>";
        } else {
            lista_minacce_temp += "<option value='5'>5 - Molto Probabile</option>";
        }

        lista_minacce_temp += "</select>";
        lista_minacce_temp += "</div>";
        lista_minacce_temp += "</td>";

        lista_minacce_temp += "<td style='text-align: center; padding-top: 20px;'>";
        lista_minacce_temp += "<div class='form-group'>";
        lista_minacce_temp += "<select style='vertical-align: top !important; text-align: center !important;' class='form_magnitudo form-control' id='form_magnitudo_" + id_minaccia_impianto + "'>";
        lista_minacce_temp += "<option value='-' selected='selected'></option>";

        if (lista_minacce[i].magnitudo_post == 1) {
            lista_minacce_temp += "<option value='1' selected='selected' >1 - Trascurabile</option>";
        } else {
            lista_minacce_temp += "<option value='1'>1 - Trascurabile</option>";
        }

        if (lista_minacce[i].magnitudo_post == 2) {
            lista_minacce_temp += "<option value='2' selected='selected' >2 - Contenuto</option>";
        } else {
            lista_minacce_temp += "<option value='2'>2 - Contenuto</option>";
        }

        if (lista_minacce[i].magnitudo_post == 3) {
            lista_minacce_temp += "<option value='3' selected='selected' >3 - Significativo</option>";
        } else {
            lista_minacce_temp += "<option value='3'>3 - Significativo</option>";
        }

        if (lista_minacce[i].magnitudo_post == 4) {
            lista_minacce_temp += "<option value='4' selected='selected' >4 - Rilevante</option>";
        } else {
            lista_minacce_temp += "<option value='4'>4 - Rilevante</option>";
        }

        if (lista_minacce[i].magnitudo_post == 5) {
            lista_minacce_temp += "<option value='5' selected='selected' >5 - Catastrofico</option>";
        } else {
            lista_minacce_temp += "<option value='5'>5 - Catastrofico</option>";
        }

        lista_minacce_temp += "</select>";
        lista_minacce_temp += "</div>";
        lista_minacce_temp += "</td>";

        lista_minacce_temp += "<td style='vertical-align: top !important; text-align: center; padding-top: 20px;'>";
        lista_minacce_temp += "<div class='form-group'>";
        lista_minacce_temp += "<select style='text-align: center !important;' class='form_rischio form-control' id='readonly_rischio_" + id_minaccia_impianto + "' disabled >";
        lista_minacce_temp += "<option value='-' selected='selected'></option>";

        for (var y = 1; y <= 25; y++) {

            if (lista_minacce[i].rischio_post == y) {
                lista_minacce_temp += "<option value='" + y + "' selected='selected' >" + y + "</option>";
            } else {
                lista_minacce_temp += "<option value='" + y + "'>" + y + "</option>";
            }

        }

        lista_minacce_temp += "</select>";
        lista_minacce_temp += "</div>";
        lista_minacce_temp += "</td>";

        lista_minacce_temp += "<td style='vertical-align: top !important; text-align: center; padding-top: 30px;' id='readonly_frase_rischio_" + id_minaccia_impianto + "'>";
        lista_minacce_temp += getFraseDiRischio(lista_minacce[i].rischio_post);
        lista_minacce_temp += "</td>";

        lista_minacce_temp += "</tr>";

        valori_minaccia_impianto[id_minaccia_impianto] = {
            nome_minaccia: lista_minacce[i].nome,
            probabilita_pre: lista_minacce[i].probabilita_pre,
            magnitudo_pre: lista_minacce[i].magnitudo_pre,
            rischio_pre: lista_minacce[i].rischio_pre,
            probabilita_post: lista_minacce[i].probabilita_post,
            magnitudo_post: lista_minacce[i].magnitudo_post,
            rischio_post: lista_minacce[i].rischio_post,
            frase_di_rischio_post: getFraseDiRischio(lista_minacce[i].rischio_post),
            descrizione: lista_minacce[i].descrizione
        };

    }

    lista_minacce_temp += "</tbody>";
    lista_minacce_temp += "</table>";

    return lista_minacce_temp;

}

function getTabellaMisureRiduzioneMinacciaImpianto(dati_impianto, dati_minaccia, lista_misure) {

    var lista_misure_temp = "";
    var id_misura_minaccia_impianto = "";

    lista_misure_temp = "<table style='width: 100%; margin: 0px;' class='table table-striped'>";
    lista_misure_temp += "<thead>";
    lista_misure_temp += "<tr>";
    lista_misure_temp += "<th style='width: 30px; text-align: center;'>Attiva</th>";
    lista_misure_temp += "<th style='text-align: left;'>Nome Misura</th>";
    lista_misure_temp += "<th style='width: 70px; text-align: left;'>Attuata</th>";
    lista_misure_temp += "<th style='width: 100px; text-align: center;'>Nota</th>";
    lista_misure_temp += "</tr>";
    lista_misure_temp += "</thead>";
    lista_misure_temp += "<tbody>";

    for (var i = 0; i < lista_misure.length; i++) {

        id_misura_minaccia_impianto = dati_impianto.impianto + "_" + dati_minaccia.id + "_" + lista_misure[i].id;

        lista_misure_temp += "<tr>";

        lista_misure_temp += "<td id='td_disattivazione_misura_" + id_misura_minaccia_impianto + "' style='width: 30px; vertical-align: middle; text-align: center;'>";
        if (lista_misure[i].attiva == "Si") {
            lista_misure_temp += "<button type='button' class='btn btn-default btn-sm bottone_disattiva_misura' id='readonly_misura_attiva_" + id_misura_minaccia_impianto + "'>";
            lista_misure_temp += "<span id='misura_attiva_" + id_misura_minaccia_impianto + "'>SI </span><span class='glyphicon glyphicon-pencil'></span>";
            lista_misure_temp += "</button>";
        } else {
            lista_misure_temp += "<span id='misura_attiva_" + id_misura_minaccia_impianto + "'>NO</span>";
        }
        lista_misure_temp += "</td>";

        lista_misure_temp += "<td style='vertical-align: middle;'>";
        lista_misure_temp += lista_misure[i].nome;
        lista_misure_temp += "</td>";

        lista_misure_temp += "<td style='width: 70px; vertical-align: middle; padding-top: 15px;'>";
        lista_misure_temp += "<div class='form-group'>";
        lista_misure_temp += "<select style='text-align: center; vertical-align: middle;' class='form_misura_applicata form-control' id='form_misura_applicata_" + id_misura_minaccia_impianto + "'>";

        if (lista_misure[i].attuata == "No") {
            lista_misure_temp += "<option value='No' selected='selected'>No</option>";
        } else {
            lista_misure_temp += "<option value='No'>No</option>";
        }
        if (lista_misure[i].attuata == "Si") {
            lista_misure_temp += "<option value='Si' selected='selected'>Sì</option>";
        } else {
            lista_misure_temp += "<option value='Si'>Sì</option>";
        }

        lista_misure_temp += "<td>";

        if (lista_misure[i].attuata == "Si") {

            if (lista_misure[i].descrizione == "") {
                lista_misure_temp += "<button type='button' class='btn btn-default btn-sm bottone_nota' id='bottone_nota_" + id_misura_minaccia_impianto + "' >";
                lista_misure_temp += "<span class='glyphicon glyphicon-pencil'></span> Nota";
                lista_misure_temp += "</button>";
            } else {
                lista_misure_temp += "<button type='button' class='btn btn-default btn-sm bottone_nota' style='background-color: green;' id='bottone_nota_" + id_misura_minaccia_impianto + "' >";
                lista_misure_temp += "<span class='glyphicon glyphicon-pencil'></span> Nota";
                lista_misure_temp += "</button>";
            }

        }

        lista_misure_temp += "</td>";

        lista_misure_temp += "</select>";
        lista_misure_temp += "</div>";
        lista_misure_temp += "</td>";

        lista_misure_temp += "</tr>";

        valori_misure_minaccie[id_misura_minaccia_impianto] = {
            nome_misura: lista_misure[i].nome,
            attuata: lista_misure[i].attuata,
            attiva: lista_misure[i].attiva,
            descrizione: lista_misure[i].descrizione
        };

    }

    //console.table(valori_misure_minaccie);

    lista_misure_temp += "</tbody>";
    lista_misure_temp += "</table>";

    return lista_misure_temp;

}

function getFraseDiRischio(valore_rischio) {

    var frase_di_rischio = "";

    if (valore_rischio > 0 && valore_rischio <= 5) {
        frase_di_rischio = "<span style='background-color: white;'><b>Irrilevante</b></span>";
    } else if (valore_rischio > 5 && valore_rischio <= 10) {
        frase_di_rischio = "<span style='background-color: green;'><b>Minore</b></span>";
    } else if (valore_rischio > 10 && valore_rischio <= 15) {
        frase_di_rischio = "<span style='background-color: yellow;'><b>Moderato</b></span>";
    } else if (valore_rischio > 15 && valore_rischio <= 20) {
        frase_di_rischio = "<span style='background-color: orange;'><b>Significativo</b></span>";
    } else if (valore_rischio > 20 && valore_rischio <= 25) {
        frase_di_rischio = "<span style='background-color: red;'><b>Estremo</b></span>";
    }

    return frase_di_rischio;

}

function leggiForm() {

    jform_misura_applicata = jQuery(".form_misura_applicata");

    var array_result = [];

    jform_misura_applicata.each(function() {

        var attuata_value_temp = jQuery(this).val();

        var elemento_temp = jQuery(this).prop("id");
        elemento_temp = elemento_temp.substring(22, elemento_temp.length);

        var elemento_temp_array = elemento_temp.split("_");

        var impianto = elemento_temp_array[0];
        var minaccia = elemento_temp_array[1];
        var misura = elemento_temp_array[2];

        var misura_attiva_attiva_value_temp = jQuery("#misura_attiva_" + elemento_temp).html();
        misura_attiva_attiva_value_temp = misura_attiva_attiva_value_temp.trim();

        var probabilita_value_temp = jQuery("#form_probabilita_" + impianto + "_" + minaccia).val();

        var mangnitudo_value_temp = jQuery("#form_magnitudo_" + impianto + "_" + minaccia).val();

        var rischio_value_temp = jQuery("#readonly_rischio_" + impianto + "_" + minaccia).val();

        array_result.push({
            impianto: impianto,
            minaccia: minaccia,
            misura: misura,
            probabilita: probabilita_value_temp,
            magnitudo: mangnitudo_value_temp,
            rischio: rischio_value_temp,
            attuata: attuata_value_temp,
            misura_attiva: misura_attiva_attiva_value_temp
        });

    });

    //console.table(array_result);

    var array_json = JSON.stringify(array_result);

    setValoriNelDatabase(array_json);

}

function setValoriNelDatabase(dati_json) {

    var dati = {
        id: record,
        dati: dati_json
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpInterventoRiduzioneRischiPrivacy/SetInterventoRiduzioneRischio.php',
        dataType: 'json',
        async: true,
        method: 'POST',
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {

            getInterventoRiduzioneRischio(record);

        },
        fail: function() {

            console.error("Errore nel salvataggio");
            sbloccaForm();
            location.reload();
            inSalvataggio = false;

        }
    });

}

function salvaDisattivazioneMisura(id_sel) {

    var elemento_temp_array = id_sel.split("_");

    var impianto = elemento_temp_array[0];
    var minaccia = elemento_temp_array[1];
    var misura = elemento_temp_array[2];

    var dati = {
        id: record,
        impianto: impianto,
        minaccia: minaccia,
        misura: misura,
        probabilita: jpopup_probabilita.val(),
        magnitudo: jpopup_magnitudo.val(),
        rischio: jpopup_rischio.val()
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpInterventoRiduzioneRischiPrivacy/SetDisattivazioneMisuraPrivacy.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {

            jdisattivaMisuraModal.modal('hide');
            getInterventoRiduzioneRischio(record);

        },
        fail: function() {

            console.error("Errore nel salvataggio");
            location.reload();
            inSalvataggio = false;

        }
    });

}

function getPopupNotaRilevazioneRischio(id) {

    elemento_selezionato = id;

    var elemento_temp_array = elemento_selezionato.split("_");

    var impianto = elemento_temp_array[0];
    var minaccia = elemento_temp_array[1];
    var misura = elemento_temp_array[2];

    jnome_minaccia_nota.html("Nota relativa alla misura: <b>" + valori_misure_minaccie[elemento_selezionato].nome_misura + "</b>");

    var nome_probabilita = "";
    switch (valori_minaccia_impianto[impianto + "_" + minaccia].probabilita_post) {
        case '1':
            nome_probabilita = "1 - Improbabile";
            break;
        case '2':
            nome_probabilita = "2 - Raro";
            break;
        case '3':
            nome_probabilita = "3 - Possibile";
            break;
        case '4':
            nome_probabilita = "4 - Probabile";
            break;
        case '5':
            nome_probabilita = "5 - Molto Probabile";
            break;
        default:
            nome_probabilita = "-";
    }
    jpopup_nota_probabilita.html(nome_probabilita);

    var nome_magnitudo = "";
    switch (valori_minaccia_impianto[impianto + "_" + minaccia].magnitudo_post) {
        case '1':
            nome_magnitudo = "1 - Trascurabile";
            break;
        case '2':
            nome_magnitudo = "2 - Contenuto";
            break;
        case '3':
            nome_magnitudo = "3 - Significativo";
            break;
        case '4':
            nome_magnitudo = "4 - Rilevante";
            break;
        case '5':
            nome_magnitudo = "5 - Catastrofico";
            break;
        default:
            nome_magnitudo = "-";
    }
    jpopup_nota_magnitudo.html(nome_magnitudo);

    jpopup_nota_rischio.html(valori_minaccia_impianto[impianto + "_" + minaccia].rischio_post);

    jpopup_nota_frase_di_rischio.html(valori_minaccia_impianto[impianto + "_" + minaccia].frase_di_rischio_post);

    jpopup_nota.val(HtmlEntities.decode(valori_misure_minaccie[elemento_selezionato].descrizione));

    jhelpNoteModal.modal('show');

}

function setNotaInterventoRiduzioneRischio(id) {

    var elemento_temp_array = id.split("_");

    var impianto = elemento_temp_array[0];
    var minaccia = elemento_temp_array[1];
    var misura = elemento_temp_array[2];

    var dati = {
        id: record,
        impianto: impianto,
        minaccia: minaccia,
        misura: misura,
        nota: HtmlEntities.kpencode(jpopup_nota.val())
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpInterventoRiduzioneRischiPrivacy/SetNotaInterventoRiduzioneRischio.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {

            jhelpNoteModal.modal('hide');
            getInterventoRiduzioneRischio(record);
            elemento_selezionato = 0;
            jpopup_nota.val();
            jnome_minaccia_nota.html("");
            jpopup_nota_probabilita.html("");
            jpopup_nota_magnitudo.html("");
            jpopup_nota_rischio.html("");
            jpopup_nota_frase_di_rischio.html("");

        },
        fail: function() {

            console.error("Errore nel salvataggio");
            elemento_selezionato = 0;
            inSalvataggio = false;
            location.reload();

        }
    });


}