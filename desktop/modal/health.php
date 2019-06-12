<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

if (!isConnect('admin')) {
	throw new Exception('401 Unauthorized');
}
$plugin = plugin::byId('sunshutter');
$eqLogics = sunshutter::byType($plugin->getId());
?>

<table class="table table-condensed tablesorter" id="table_healthsunshutter">
	<thead>
		<tr>
			<th>{{Volet}}</th>
			<th>{{Cron}}</th>
			<th>{{Reprise}}</th>
			<th>{{%ouverture}}</th>
			<th>{{%fermeture}}</th>
			<th>{{Action défaut}}</th>
			<th>{{Condition pour action}}</th>
			<th>{{Ouverture Forcée}}</th>
			<th>{{Fermeture Forcée}}</th>
			<th>{{Ouverture Immédiate}}</th>
			<th>{{Fermeture Immédiate}}</th>
		</tr>
	</thead>
	<tbody>
	 <?php
foreach ($eqLogics as $eqLogic) {
	echo '<tr><td><a href="' . $eqLogic->getLinkToConfiguration() . '" style="text-decoration: none;">' . $eqLogic->getHumanName(true) . '</a></td>';
	$cron = $eqLogic->getConfiguration('cron::executeAction','');
	if ($cron == 'custom') {
		$cron = $eqLogic->getConfiguration('cron::custom','');
	}
	echo '<td><span class="label label-info" style="font-size : 1em;cursor:default;">' . $cron . '</span></td>';
	$backhand = '<span class="label label-danger" style="font-size : 1em;cursor:default;">{{Non}}</span>';
	if ($eqLogic->getConfiguration('shutter::nobackhand',0) == 1) {
		$backhand = '<span class="label label-success" style="font-size : 1em;cursor:default;">{{Oui}}</span>';
	}
	if ($eqLogic->getConfiguration('shutter::nobackhand',0) == 2) {
		$backhand = '<span class="label label-warning" style="font-size : 1em;cursor:default;">'. $eqLogic->getConfiguration('shutter::customDelay','0') . 'min</span>';
	}
	echo '<td>' . $backhand . '</td>';
	echo '<td><span class="label label-info" style="font-size : 1em;cursor:default;">' . $eqLogic->getConfiguration('shutter::openPosition','') . '</span></td>';
	echo '<td><span class="label label-info" style="font-size : 1em;cursor:default;">' . $eqLogic->getConfiguration('shutter::closePosition','') . '</span></td>';
	$defaultAction = $eqLogic->getConfiguration('shutter::defaultAction','open');
	$action = '<span class="label label-primary" style="font-size : 1em;cursor:default;">{{Ouvrir}}</span>';
	if ($defaultAction == 'none') {
		$action = '<span class="label label-primary" style="font-size : 1em;cursor:default;">{{Rien}}</span>';
	}if ($defaultAction == 'close') {
		$action = '<span class="label label-primary" style="font-size : 1em;cursor:default;">{{Fermer}}</span>';
	}if ($defaultAction == 'custom') {
		$action = '<span class="label label-primary" style="font-size : 1em;cursor:default;">'. $eqLogic->getConfiguration('shutter::customPosition','0') .'%</span>';
	}
	echo '<td>' . $action . '</td>';
	$condition = '<span class="label label-success" style="font-size : 1em;cursor:default;">{{Oui}}</span>';
	if ($eqLogic->getConfiguration('condition::allowmove','') == '') {
		$condition = '<span class="label label-danger" style="font-size : 1em;cursor:default;">{{Non}}</span>';
	}
	echo '<td>' . $condition . '</td>';
	$open = '<span class="label label-success" style="font-size : 1em;cursor:default;">{{Oui}}</span>';
	if ($eqLogic->getConfiguration('condition::forceopen','') == '') {
		$open = '<span class="label label-danger" style="font-size : 1em;cursor:default;">{{Non}}</span>';
	}
	echo '<td>' . $open . '</td>';
	$close = '<span class="label label-success" style="font-size : 1em;cursor:default;">{{Oui}}</span>';
	if ($eqLogic->getConfiguration('condition::forceclose','') == '') {
		$close = '<span class="label label-danger" style="font-size : 1em;cursor:default;">{{Non}}</span>';
	}
	echo '<td>' . $close . '</td>';
	$immediatopen = '<span class="label label-success" style="font-size : 1em;cursor:default;">{{Oui}}</span>';
	if ($eqLogic->getConfiguration('condition::immediatforceopen','') == '') {
		$immediatopen = '<span class="label label-danger" style="font-size : 1em;cursor:default;">{{Non}}</span>';
	}
	echo '<td>' . $immediatopen . '</td>';
	$immediatclose = '<span class="label label-success" style="font-size : 1em;cursor:default;">{{Oui}}</span>';
	if ($eqLogic->getConfiguration('condition::immediatforceclose','') == '') {
		$immediatclose = '<span class="label label-danger" style="font-size : 1em;cursor:default;">{{Non}}</span>';
	}
	echo '<td>' . $immediatclose . '</td>';
	echo '</tr>';
}
?>
	</tbody>
</table>
