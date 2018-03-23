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

var jbody_tabella_rilevazione_richi;
var jform_probabilita;
var jform_magnitudo;
var jhelpProbabilitaModal;
var jhelpMagnitudoModal;
var jhelpNoteModal;
var jhelp_probabilita;
var jhelp_magnitudo;
var jbottone_nota;
var jnome_minaccia_nota;
var jpopup_nota_probabilita;
var jpopup_nota_magnitudo;
var jpopup_nota_rischio;
var jpopup_nota_frase_di_rischio;
var jpopup_nota;
var jbottone_salva_nota;

var minaccia_impianto = [];
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

    window.addEventListener('resize', function() {
        reSize();
    }, false);

    reSize();

    jbottone_modifica.click(function() {

        sbloccaForm();

    });

    jbottone_annulla.click(function() {

        getRilevazioneRischio(record);

    });

    jbottone_salva.click(function() {

        if (!readonly) {

            bloccaForm();

            leggiForm();

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
    jbottone_nota.prop("disabled", false);

    jbottone_salva.hide();
    jbottone_annulla.hide();
    jbottone_modifica.show();

}

function sbloccaForm() {

    jform = jQuery('[id*="form_"]');
    readonly = false;
    jform.prop("disabled", false);
    jbottone_nota.prop("disabled", true);

    jbottone_modifica.hide();
    jbottone_salva.show();
    jbottone_annulla.show();

}

function inizializzazioneExtra() {

    jbody_tabella_rilevazione_richi = jQuery("#body_tabella_rilevazione_richi");
    jhelpProbabilitaModal = jQuery("#helpProbabilitaModal");
    jhelpMagnitudoModal = jQuery("#helpMagnitudoModal");
    jhelp_probabilita = jQuery(".help_probabilita"); //kpro@tom230320181207
    jhelp_magnitudo = jQuery(".help_magnitudo"); //kpro@tom230320181207
    jhelpNoteModal = jQuery("#helpNoteModal");
    jnome_minaccia_nota = jQuery(".nome_minaccia_nota");
    jpopup_nota_probabilita = jQuery("#popup_nota_probabilita");
    jpopup_nota_magnitudo = jQuery("#popup_nota_magnitudo");
    jpopup_nota_rischio = jQuery("#popup_nota_rischio");
    jpopup_nota_frase_di_rischio = jQuery("#popup_nota_frase_di_rischio");
    jpopup_nota = jQuery("#popup_nota");
    jbottone_salva_nota = jQuery("#bottone_salva_nota");

    getRilevazioneRischio(record);

    jbottone_salva_nota.click(function() {

        if (!inSalvataggio && elemento_selezionato != 0) {

            inSalvataggio = true;

            setNotaRilevazioneRischio(elemento_selezionato);

        }

    });

}

function getRilevazioneRischio(id) {

    var dati = {
        id: id
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischiPrivacy/GetRilevazioneRischio.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

        },
        success: function(data) {

            //console.table(data);

            var lista_rilevazione_temp = "";
            var id_impianto = "";
            minaccia_impianto = [];

            for (var i = 0; i < data.length; i++) {

                id_impianto = data[i].impianto;

                lista_rilevazione_temp += "<tr>";
                lista_rilevazione_temp += "<td style='vertical-align: top; padding-top: 65px;'>";
                lista_rilevazione_temp += "<b>" + data[i].nome_impianto + "</b>";
                lista_rilevazione_temp += "</td>";

                lista_rilevazione_temp += "<td>";
                lista_rilevazione_temp += getTabellaMinacceImpianto(data[i], data[i].lista_minacce);
                lista_rilevazione_temp += "</td>";

                lista_rilevazione_temp += "</tr>";

            }

            jbody_tabella_rilevazione_richi.empty();
            jbody_tabella_rilevazione_richi.append(lista_rilevazione_temp);

            jform_probabilita = jQuery(".form_probabilita");
            jform_magnitudo = jQuery(".form_magnitudo");
            jhelp_probabilita = jQuery(".help_probabilita"); //kpro@tom230320181207
            jhelp_magnitudo = jQuery(".help_magnitudo"); //kpro@tom230320181207
            jbottone_nota = jQuery(".bottone_nota");

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
    lista_minacce_temp += "<th style='text-align: left;'>Minaccia</th>";
    //kpro@tom230320181207
    lista_minacce_temp += "<th style='width: 160px; text-align: center;'>Probabilità <a href='#' class='help_probabilita'><span class='glyphicon glyphicon-question-sign'></span></a></th>";
    lista_minacce_temp += "<th style='width: 140px; text-align: center;'>Magnitudo <a href='#' class='help_magnitudo'><span class='glyphicon glyphicon-question-sign'></span></a></th>";
    //kpro@tom230320181207 end
    lista_minacce_temp += "<th style='width: 100px; text-align: center;'>Rischio</th>";
    lista_minacce_temp += "<th style='width: 120px; text-align: center;'>Frase di Rischio</th>";
    lista_minacce_temp += "<th style='width: 200px; text-align: left;'>Tempi di Ripristino</th>";
    lista_minacce_temp += "<th style='width: 100px; text-align: center;'>Nota</th>";
    lista_minacce_temp += "<th style='width: 400px;'>Misure Riduzione Rischio Attive</th>";
    lista_minacce_temp += "</tr>";
    lista_minacce_temp += "</thead>";
    lista_minacce_temp += "<tbody>";

    for (var i = 0; i < lista_minacce.length; i++) {

        id_minaccia_impianto = dati_impianto.impianto + "_" + lista_minacce[i].id;

        lista_minacce_temp += "<tr>";

        lista_minacce_temp += "<td style='vertical-align: middle; vertical-align: top; padding-top: 20px;'>";
        lista_minacce_temp += lista_minacce[i].nome;
        lista_minacce_temp += "</td>";

        lista_minacce_temp += "<td style='text-align: center; padding-top: 20px;'>";
        lista_minacce_temp += "<div class='form-group'>";
        lista_minacce_temp += "<select style='vertical-align: top !important; text-align: center !important;' class='form_probabilita form-control' id='form_probabilita_" + id_minaccia_impianto + "'>";
        lista_minacce_temp += "<option value='-' selected='selected'></option>";

        if (lista_minacce[i].probabilita == 1) {
            lista_minacce_temp += "<option value='1' selected='selected' >1 - Improbabile</option>";
        } else {
            lista_minacce_temp += "<option value='1'>1 - Improbabile</option>";
        }

        if (lista_minacce[i].probabilita == 2) {
            lista_minacce_temp += "<option value='2' selected='selected' >2 - Raro</option>";
        } else {
            lista_minacce_temp += "<option value='2'>2 - Raro</option>";
        }

        if (lista_minacce[i].probabilita == 3) {
            lista_minacce_temp += "<option value='3' selected='selected' >3 - Possibile</option>";
        } else {
            lista_minacce_temp += "<option value='3'>3 - Possibile</option>";
        }

        if (lista_minacce[i].probabilita == 4) {
            lista_minacce_temp += "<option value='4' selected='selected' >4 - Probabile</option>";
        } else {
            lista_minacce_temp += "<option value='4'>4 - Probabile</option>";
        }

        if (lista_minacce[i].probabilita == 5) {
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

        if (lista_minacce[i].magnitudo == 1) {
            lista_minacce_temp += "<option value='1' selected='selected' >1 - Trascurabile</option>";
        } else {
            lista_minacce_temp += "<option value='1'>1 - Trascurabile</option>";
        }

        if (lista_minacce[i].magnitudo == 2) {
            lista_minacce_temp += "<option value='2' selected='selected' >2 - Contenuto</option>";
        } else {
            lista_minacce_temp += "<option value='2'>2 - Contenuto</option>";
        }

        if (lista_minacce[i].magnitudo == 3) {
            lista_minacce_temp += "<option value='3' selected='selected' >3 - Significativo</option>";
        } else {
            lista_minacce_temp += "<option value='3'>3 - Significativo</option>";
        }

        if (lista_minacce[i].magnitudo == 4) {
            lista_minacce_temp += "<option value='4' selected='selected' >4 - Rilevante</option>";
        } else {
            lista_minacce_temp += "<option value='4'>4 - Rilevante</option>";
        }

        if (lista_minacce[i].magnitudo == 5) {
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

            if (lista_minacce[i].rischio == y) {
                lista_minacce_temp += "<option value='" + y + "' selected='selected' >" + y + "</option>";
            } else {
                lista_minacce_temp += "<option value='" + y + "'>" + y + "</option>";
            }

        }

        lista_minacce_temp += "</select>";
        lista_minacce_temp += "</div>";
        lista_minacce_temp += "</td>";

        lista_minacce_temp += "<td style='vertical-align: top !important; text-align: center; padding-top: 30px;' id='readonly_frase_rischio_" + id_minaccia_impianto + "'>";
        lista_minacce_temp += getFraseDiRischio(lista_minacce[i].rischio);
        lista_minacce_temp += "</td>";

        lista_minacce_temp += "<td style='padding-top: 20px;'>";
        lista_minacce_temp += "<div class='form-group'>";
        lista_minacce_temp += "<select style='vertical-align: top !important; text-align: left;' class='form_ripristino form-control' id='form_ripristino_" + id_minaccia_impianto + "'>";

        for (var y = 0; y < dati_impianto.opzioni_tempi_ripristino.length; y++) {

            if (lista_minacce[i].tempi_ripristino == dati_impianto.opzioni_tempi_ripristino[y]) {
                lista_minacce_temp += "<option  value='" + dati_impianto.opzioni_tempi_ripristino[y] + "' selected='selected' >" + dati_impianto.opzioni_tempi_ripristino[y] + "</option>";
            } else {
                lista_minacce_temp += "<option  value='" + dati_impianto.opzioni_tempi_ripristino[y] + "'>" + dati_impianto.opzioni_tempi_ripristino[y] + "</option>";
            }

        }

        lista_minacce_temp += "</select>";
        lista_minacce_temp += "</div>";
        lista_minacce_temp += "</td>";

        lista_minacce_temp += "<td style='padding-top: 10px;'>";

        if (lista_minacce[i].rischio != "" && lista_minacce[i].rischio != "-") {

            if (lista_minacce[i].descrizione == "") {
                lista_minacce_temp += "<button type='button' class='btn btn-default btn-sm bottone_nota' id='bottone_nota_" + id_minaccia_impianto + "' >";
                lista_minacce_temp += "<span class='glyphicon glyphicon-pencil'></span> Nota";
                lista_minacce_temp += "</button>";
            } else {
                lista_minacce_temp += "<button type='button' class='btn btn-default btn-sm bottone_nota' style='background-color: green;' id='bottone_nota_" + id_minaccia_impianto + "' >";
                lista_minacce_temp += "<span class='glyphicon glyphicon-pencil'></span> Nota";
                lista_minacce_temp += "</button>";
            }

        }

        lista_minacce_temp += "</td>";

        lista_minacce_temp += "<td>";
        lista_minacce_temp += getTabellaMisureRiduzioneMinacciaImpianto(dati_impianto, lista_minacce[i], lista_minacce[i].lista_misure);
        lista_minacce_temp += "</td>";


        lista_minacce_temp += "</tr>";

        minaccia_impianto[id_minaccia_impianto] = {
            nome_minaccia: lista_minacce[i].nome,
            probabilita: lista_minacce[i].probabilita,
            magnitudo: lista_minacce[i].magnitudo,
            rischio: lista_minacce[i].rischio,
            frase_di_rischio: getFraseDiRischio(lista_minacce[i].rischio),
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
    lista_misure_temp += "<tbody>";

    for (var i = 0; i < lista_misure.length; i++) {

        id_misura_minaccia_impianto = dati_impianto.impianto + "_" + dati_minaccia.id + "_" + lista_misure[i].id;

        lista_misure_temp += "<tr>";

        lista_misure_temp += "<td style='vertical-align: middle;'>";
        lista_misure_temp += lista_misure[i].nome;
        lista_misure_temp += "</td>";

        lista_misure_temp += "<td style='width: 70px; vertical-align: middle;'>";
        lista_misure_temp += "<div class='form-group'>";
        lista_misure_temp += "<select style='text-align: center; vertical-align: middle;' class='form_misura_attiva form-control' id='form_misura_attiva_" + id_misura_minaccia_impianto + "'>";

        if (lista_misure[i].attiva == "No") {
            lista_misure_temp += "<option value='No' selected='selected'>No</option>";
        } else {
            lista_misure_temp += "<option value='No'>No</option>";
        }
        if (lista_misure[i].attiva == "Si") {
            lista_misure_temp += "<option value='Si' selected='selected'>Sì</option>";
        } else {
            lista_misure_temp += "<option value='Si'>Sì</option>";
        }

        lista_misure_temp += "</select>";
        lista_misure_temp += "</div>";
        lista_misure_temp += "</td>";

        lista_misure_temp += "</tr>";

    }
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

    jform_probabilita = jQuery(".form_probabilita");

    var array_result = [];

    jform_probabilita.each(function() {

        var elemento_temp = jQuery(this).prop("id");
        elemento_temp = elemento_temp.substring(17, elemento_temp.length);

        var elemento_temp_array = elemento_temp.split("_");

        var impianto = elemento_temp_array[0];
        var minaccia = elemento_temp_array[1];

        var probabilita_value_temp = jQuery(this).val();
        var mangnitudo_value_temp = jQuery("#form_magnitudo_" + elemento_temp).val();
        var rischio_value_temp = jQuery("#readonly_rischio_" + elemento_temp).val();
        var ripristino_value_temp = jQuery("#form_ripristino_" + elemento_temp).val();

        var array_misure = leggiFormMisureAttive(impianto, minaccia);

        array_result.push({
            impianto: impianto,
            minaccia: minaccia,
            probabilita: probabilita_value_temp,
            magnitudo: mangnitudo_value_temp,
            rischio: rischio_value_temp,
            ripristino: ripristino_value_temp,
            array_misure: array_misure
        });

    });

    //console.table(array_result);

    var array_json = JSON.stringify(array_result);

    setValoriNelDatabase(array_json);

}

function leggiFormMisureAttive(impianto, minaccia) {

    var jform_misura_attiva = jQuery('[id*="form_misura_attiva_' + impianto + '_' + minaccia + '"]');

    var array_result = [];

    jform_misura_attiva.each(function() {

        var elemento_temp = jQuery(this).prop("id");
        elemento_temp = elemento_temp.substring(19, elemento_temp.length);

        elemento_temp_array = elemento_temp.split("_");

        var impianto = elemento_temp_array[0];
        var minaccia = elemento_temp_array[1];
        var misura = elemento_temp_array[2];

        var attiva_value_temp = jQuery(this).val();

        array_result.push({
            impianto: impianto,
            minaccia: minaccia,
            misura: misura,
            attiva: attiva_value_temp
        });

    });

    return array_result;

}

function setValoriNelDatabase(dati_json) {

    var dati = {
        id: record,
        dati: dati_json
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischiPrivacy/SetRilevazioneRischio.php',
        dataType: 'json',
        async: true,
        method: 'POST',
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {

            getRilevazioneRischio(record);

        },
        fail: function() {

            console.error("Errore nel salvataggio");
            sbloccaForm();
            location.reload();

        }
    });

}

function getPopupNotaRilevazioneRischio(id) {

    elemento_selezionato = id;

    jnome_minaccia_nota.html("Nota relativa alla valutazione della minaccia: <b>" + minaccia_impianto[id].nome_minaccia + "</b>");

    var nome_probabilita = "";
    switch (minaccia_impianto[id].probabilita) {
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
    switch (minaccia_impianto[id].magnitudo) {
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

    jpopup_nota_rischio.html(minaccia_impianto[id].rischio);

    jpopup_nota_frase_di_rischio.html(minaccia_impianto[id].frase_di_rischio);

    jpopup_nota.val(HtmlEntities.decode(minaccia_impianto[id].descrizione));

    jhelpNoteModal.modal('show');

}

function setNotaRilevazioneRischio(id) {

    var elemento_temp_array = id.split("_");

    var impianto = elemento_temp_array[0];
    var minaccia = elemento_temp_array[1];

    var dati = {
        id: record,
        impianto: impianto,
        minaccia: minaccia,
        nota: HtmlEntities.kpencode(jpopup_nota.val())
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischiPrivacy/SetNotaRilevazioneRischio.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {

            jhelpNoteModal.modal('hide');
            getRilevazioneRischio(record);
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