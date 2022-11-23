<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJs('jeedomUtils.backgroundIMG', 'plugins/sunshutter/core/img/panel.jpg');
?>
<div>
	<br />
	<legend>
		{{Position Moyenne}} <span class="label label-success posMoy"></span>
		{{Suspendu Manuel}} <span class="label label-danger manualSuspend"></span>
		{{Suspendu Auto}} <span class="label label-warning autoSuspend"></span>
	</legend>
</div>
<div class="div_displayEquipement" style="width: 100%;">
	<?php
	foreach (sunshutter::byType('sunshutter') as $sunshutter) {
		echo $sunshutter->toHtml('view');
	}
	?>
</div>
<?php include_file('desktop', 'panel', 'js', 'sunshutter'); ?>