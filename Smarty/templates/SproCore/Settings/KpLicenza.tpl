{**************************************************************************************
/* kpro@tom31072017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */
 ***************************************************************************************}

{literal}

<script src="Smarty/templates/SproCore/Settings/KpLicenza/js/general.js"></script>

{/literal}

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
					<td width=50 rowspan=2 valign=top><img src="{'ico-profile.gif'|@vtiger_imageurl:$THEME}" alt="Licenza" width="48" height="48" border=0 title="Licenza"></td>
					<td class=heading2 valign=bottom><b>Impostazioni Spro > Licenza</b></td>
				</tr>
				<tr>
					<td valign=top class="small">Permette di gestire le varie impostazioni legate alla licenza</td>
				</tr>
				</table>
				<br>

				<div id="pagina_principale">

					<table border=0 cellspacing=0 cellpadding=5 width=100%>
						<tr>
							<td width=50 style="text-align: left;">
								<button id="bottone_pagina_aggiorna_licenza" type="button" class="btn btn-outline-success waves-effect"><b>Aggiorna Licenza</b></button>
							</td>
							<td width=50 style="text-align: right;">
								<button id="bottone_genera_richiesta" type="button" class="btn btn-outline-success waves-effect"><b>Genera Richiesta</b></button>
							</td>
						</tr>
					</table>

					<ul class="nav nav-tabs nav-justified" style="margin: 0px !important; padding: 0px !important;" role="tablist">
						<li class="nav-item active" id="li_moduli">
							<a data-toggle="tab" onClick="tabSelezionato('moduli')" aria-controls="moduli" href="#moduli" style="color: #2b577c !important;">
								Moduli
							</a>
						</li>
						<li class="nav-item" id="li_programmi">
							<a data-toggle="tab" onClick="tabSelezionato('programmi')" aria-controls="programmi" href="#programmi" style="color: #2b577c !important;">
								Programmi
							</a>
						</li>
					</ul>

					<div class="tab-content">

						<div id="moduli" class="tab-pane fade in active" style="color: #2b577c !important;" role="tabpanel">
							{$tabella_licenze_moduli}
						</div>
						
						<div id="programmi" class="tab-pane fade" style="color: #2b577c !important;" role="tabpanel">
							{$tabella_licenze_programmi}
						</div>

					</div>	

					<p><span style="font-size: 10px">
						IMPORTANTE! Se php5-mcrypt non è installato esegui da console i seguenti comandi: <br />
						<em>apt install php5-mcrypt <br />
						php5enmod mcrypt <br />
						service apache2 restart</em> <br />
					</span></p>

				</div>

				<div id="pagina_genera_richiesta" style="display: none;">

					<table border=0 cellspacing=0 cellpadding=5 width=100%>
						<tr>
							<td width=50 style="text-align: left;">
								<button type="button" class="bottone_torna_pagina_principale btn btn-outline-success waves-effect"><b>Torna</b></button>
							</td>
							<td width=50 style="text-align: right;">
							</td>
						</tr>
					</table>

					<div class="form-group">
						<label for="readonly_chiave_richiesta">Chiave di Richiesta</label>
						<textarea class="form-control" id="readonly_chiave_richiesta" rows="30" readonly></textarea>
					</div>

					<hr />

					<div class="form-group">
						<label for="form_chiave_attivazione">Chiave di Attivazione</label>
						<textarea class="form-control" id="form_chiave_attivazione" rows="30"></textarea>
					</div>

					<p><span style="font-size: 10px">
						IMPORTANTE! Se php5-mcrypt non è installato esegui da console i seguenti comandi: <br />
						<em>apt install php5-mcrypt <br />
						php5enmod mcrypt <br />
						service apache2 restart</em> <br />
					</span></p>

					<table border=0 cellspacing=0 cellpadding=5 width=100%>
						<tr>
							<td style="text-align: center;">
								<button id="bottone_attiva_licenza" type="button" class="btn btn-outline-success waves-effect"><b>Attiva</b></button>
							</td>
						</tr>
					</table>

				</div>

				<div id="pagina_aggiorna_licenza" style="display: none;">

					<table border=0 cellspacing=0 cellpadding=5 width=100%>
						<tr>
							<td width=50 style="text-align: left;">
								<button type="button" class="bottone_torna_pagina_principale btn btn-outline-success waves-effect"><b>Torna</b></button>
							</td>
							<td width=50 style="text-align: right;">
							</td>
						</tr>
					</table>

					<div class="form-group">
						<label for="form_chiave_aggiorna_licenza">Chiave di Attivazione</label>
						<textarea class="form-control" id="form_chiave_aggiorna_licenza" rows="30"></textarea>
					</div>

					<p><span style="font-size: 10px">
						IMPORTANTE! Se php5-mcrypt non è installato esegui da console i seguenti comandi: <br />
						<em>apt install php5-mcrypt <br />
						php5enmod mcrypt <br />
						service apache2 restart</em> <br />
					</span></p>

					<table border=0 cellspacing=0 cellpadding=5 width=100%>
						<tr>
							<td style="text-align: center;">
								<button id="bottone_aggiorna_licenza" type="button" class="btn btn-outline-success waves-effect"><b>Aggiorna</b></button>
							</td>
						</tr>
					</table>

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