<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJs('jeedomUtils.backgroundIMG', 'plugins/sunshutter/core/img/panel.jpg');
?>
<div>
	<br />
	<legend><center>
		{{Position Moyenne}} <i class="icon jeedom-volet-ouvert"></i> <span class="label label-success posMoy"></span>
		{{Suspendu Manuel}} <i class="fas fa-hand-paper"></i> <span class="label label-danger manualSuspend"></span>
		{{Suspendu Auto}} <i class="fas fa-robot"></i> <span class="label label-warning autoSuspend"></span>
	</center></legend>
</div>
<div class="div_displayEquipement" style="width: 100%;">
	<?php
	foreach (sunshutter::byType('sunshutter') as $sunshutter) {
		echo $sunshutter->toHtml('view');
	}
	?>
</div>
<?php include_file('desktop', 'panel', 'js', 'sunshutter'); ?>