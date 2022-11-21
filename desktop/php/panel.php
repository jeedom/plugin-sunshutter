<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJs('jeedomUtils.backgroundIMG', 'plugins/sunshutter/core/img/panel.jpg');
?>
<div>
	<br />
	<form class="form-horizontal">
		<fieldset>
			<div class="form-group">
				<label class="col-sm-2 control-label">{{Position Moyenne}}</label>
				<div class="col-sm-1">
					<span class="label label-success posMoy"></span>
				</div>
				<label class="col-sm-2 control-label">{{Suspendu Manuel}}</label>
				<div class="col-sm-1">
					<span class="label label-danger manualSuspend"></span>
				</div>
				<label class="col-sm-2 control-label">{{Suspendu Auto}}</label>
				<div class="col-sm-1">
					<span class="label label-warning autoSuspend"></span>
				</div>
			</div>
		</fieldset>
	</form>
</div>
<div class="div_displayEquipement" style="width: 100%;">
	<?php
	foreach (sunshutter::byType('sunshutter') as $sunshutter) {
		echo $sunshutter->toHtml('view');
	}
	?>
</div>
<?php include_file('desktop', 'panel', 'js', 'sunshutter'); ?>