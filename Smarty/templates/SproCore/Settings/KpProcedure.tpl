{**************************************************************************************
/* kpro@tom31072017 */

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
					<td width=50 rowspan=2 valign=top><img src="{'module_maker.png'|@vtiger_imageurl:$THEME}" alt="Procedure" width="48" height="48" border=0 title="Configurazione Gestione Procedure"></td>
					<td class=heading2 valign=bottom><b>Impostazioni Spro > Configurazione Gestione Procedure</b></td>
				</tr>
				<tr>
					<td valign=top class="small">Permette di gestire le varie impostazioni legate alle procedure</td>
				</tr>
				</table>
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
					<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
					<tr>
		         	    <td class="small" valign=top>
		         	    	
							<table width="100%"  border="0" cellspacing="0" cellpadding="5">

								<tr>
									<td width="20%" nowrap class="small cellLabel"><strong>Gestione revisione procedure</strong></td>
									<td width="80%" class="small cellText">
										<div class='checkbox'>
											<label>
												{if $revisioneProcessi eq 'enabled'}
													<input type="checkbox" checked id="check_abilita_revisione_processi" name="abilita_revisione_processi"></input>
												{else}
													<input type="checkbox" id="check_abilita_revisione_processi" name="abilita_revisione_processi"></input>
												{/if}
											</label>
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

<script src="Smarty/templates/SproCore/Settings/KpProcedure/js/general.js"></script>

{/literal}