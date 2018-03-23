{**************************************************************************************
/* kpro@bid29112017 */

/**
 * @author Bidese Jacopo
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
					<td width=50 rowspan=2 valign=top><img src="{'taxConfiguration.gif'|@vtiger_imageurl:$THEME}" alt="Configurazione Tassazione" width="48" height="48" border=0 title="Configurazione Tassazione"></td>
					<td class=heading2 valign=bottom><b>Impostazioni Spro > Configurazione Tassazione</b></td>
				</tr>
				<tr>
					<td valign=top class="small">Permette di configurare in maniera avanzata le tasse</td>
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

				<div id="pagina_principale">

					{$tabella_configurazioni_tasse}

				</div>

				</td>
				</tr>
				</table>
			</td>
			</tr>
			</table>
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

<script src="Smarty/templates/SproCore/Settings/KpTasse/js/general.js"></script>

{/literal}