<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<table class="table table-condensed tablesorter" id="table_sunshutter">
	<thead>
		<tr>
            <th data-priority="1">{{Nom}}</th>
			<th data-priority="1">{{Azimuth}}</th>
			<th data-priority="1">{{Elevation}}</th>
			<th data-priority="1">{{Dernier}}</th>
			<th data-priority="1">{{Suspension}}</th>
            <th data-priority="1">{{Gestion}}</th>
            <th data-priority="1">{{Manuel}}</th>
		</tr>
	</thead>
	<tbody>
<table>
<?php include_file('desktop', 'panel', 'js', 'sunshutter');?>
