<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<div>
	<br/>
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
<table class="table table-condensed tablesorter" id="table_sunshutter">
	<thead>
		<tr>
			<th data-priority="1">{{Nom}}</th>
			<th data-priority="1">{{Soleil}}</th>
			<th data-priority="1">{{Mode/Label}}</th>
			<th data-priority="1">{{Dernier}}</th>
			<th data-priority="1">{{Suspension}}</th>
			<th data-priority="1">{{Gestion}}</th>
			<th data-priority="1">{{Manuel}}</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<?php include_file('desktop', 'panel', 'js', 'sunshutter');?>
