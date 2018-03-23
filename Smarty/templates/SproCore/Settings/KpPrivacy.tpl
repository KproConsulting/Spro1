{**************************************************************************************
/* kpro@tom10112017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */
 ***************************************************************************************}

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody><tr>
<td valign="top"></td>
<td class="showPanelBg" style="padding: 5px;" valign="top" width="100%">
<form action="index.php" method="post" id="form" onsubmit="VtigerJS_DialogBox.block();">
<input type='hidden' name='module' value='Users'>
<input type='hidden' name='action' value='DefModuleView'>
<input type='hidden' name='return_action' value='ListView'>
<input type='hidden' name='return_module' value='Users'>
<input type='hidden' name='parenttab' value='Settings'>

	<div align=center>
		{include file='SetMenu.tpl'}
		{include file='Buttons_List.tpl'}
		<!-- DISPLAY -->
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
		<tr>
			<td width=50 rowspan=2 valign=top><img src="{'module_maker.png'|@vtiger_imageurl:$THEME}" alt="Procedure" width="48" height="48" border=0 title="Configurazione Gestione Privacy"></td>
			<td class=heading2 valign=bottom><b>Impostazioni Spro > Configurazione Gestione Privacy</b></td>
		</tr>
		<tr>
			<td valign=top class="small">Permette di gestire le varie impostazioni legate alla gestione della privacy</td>
		</tr>
		</table>
		<br>
		<table width=100%>

			<tr>

				<td style="text-align: right;">
					<button id="bottone_modifica" type="button" class="crmbutton small edit" title="Modifica">Modifica</button>
					<button id="bottone_salva" type="button" class="crmbutton small save" title="Salva" style="margin-right:5px; display: none;">Salva</button>
					<button id="bottone_annulla" type="button" class="crmbutton small cancel" title="Annulla" style="display: none;">Annulla</button>
				</td>

			</tr>

		</table>
		<h5><b>Legenda valori "Probabilità"</b></h5>
		<table width=100% class='table table-striped' >
			<tr>
				<td style="width: 150px;">
					1 - Improbabile
				</td>
				<td>
					<div class='form-group'>
						<textarea class="form-control" id="form_probabilita_1" cols="40" rows="3" >{$form_probabilita_1}</textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td style="width: 150px;">
					2 - Raro
				</td>
				<td>
					<div class='form-group'>
						<textarea class="form-control" id="form_probabilita_2" cols="40" rows="3">{$form_probabilita_2}</textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td style="width: 150px;">
					3 - Possibile
				</td>
				<td>
					<div class='form-group'>
						<textarea class="form-control" id="form_probabilita_3" cols="40" rows="3">{$form_probabilita_3}</textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td style="width: 150px;">
					4 - Probabile
				</td>
				<td>
					<div class='form-group'>
						<textarea class="form-control" id="form_probabilita_4" cols="40" rows="3">{$form_probabilita_4}</textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td style="width: 150px;">
					5- Molto Probabile
				</td>
				<td>
					<div class='form-group'>
						<textarea class="form-control" id="form_probabilita_5" cols="40" rows="3">{$form_probabilita_5}</textarea>
					</div>
				</td>
			</tr>
		</table>

		<hr />

		<h5><b>Legenda valori "Magnitudo"</b></h5>
		<table width=100% class='table table-striped' >
			<tr>
				<td style="width: 150px;">
					1 - Trascurabile
				</td>
				<td>
					<div class='form-group'>
						<textarea class="form-control" id="form_magnitudo_1" cols="40" rows="3">{$form_magnitudo_1}</textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td style="width: 150px;">
					2 - Contenuto
				</td>
				<td>
					<div class='form-group'>
						<textarea class="form-control" id="form_magnitudo_2" cols="40" rows="3">{$form_magnitudo_2}</textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td style="width: 150px;">
					3 - Significativo
				</td>
				<td>
					<div class='form-group'>
						<textarea class="form-control" id="form_magnitudo_3" cols="40" rows="3">{$form_magnitudo_3}</textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td style="width: 150px;">
					4 - Rilevante
				</td>
				<td>
					<div class='form-group'>
						<textarea class="form-control" id="form_magnitudo_4" cols="40" rows="3">{$form_magnitudo_4}</textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td style="width: 150px;">
					5- Catastrofico
				</td>
				<td>
					<div class='form-group'>
						<textarea class="form-control" id="form_magnitudo_5" cols="40" rows="3">{$form_magnitudo_5}</textarea>
					</div>
				</td>
			</tr>
		</table>
		
	</div>
</td>
<td valign="top"></td>
</tr>
</tbody>
</form>
</table>

{literal}

<script src="Smarty/templates/SproCore/Settings/KpPrivacy/js/general.js"></script>

{/literal}