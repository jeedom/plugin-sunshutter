<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<div class="col-sm-12" id="div_displayObject">
	<div class="div_displayEquipement" style="width: 100%;">
		<?php
		echo '';
		foreach (sunshutter::byType('sunshutter') as $sunshutter) {
			echo $sunshutter->toHtml('dashboard');
		}
		?>
	</div>
</div>
<?php include_file('desktop', 'panel', 'js', 'sunshutter'); ?>