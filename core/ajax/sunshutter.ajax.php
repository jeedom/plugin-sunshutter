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

try {
	require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
	include_file('core', 'authentification', 'php');

	if (!isConnect()) {
		throw new Exception(__('401 - Accès non autorisé', __FILE__));
	}

	ajax::init();

	if (init('action') == 'getLinkCalendar') {
		if (!isConnect('admin')) {
			throw new Exception(__('401 - Accès non autorisé', __FILE__));
		}
		$sunshutter = sunshutter::byId(init('id'));
		if (!is_object($sunshutter)) {
			throw new Exception(__('Volet non trouvé', __FILE__) . ' : ' . init('id'));
		}
		try {
			$plugin = plugin::byId('calendar');
			if (!is_object($plugin) || $plugin->isActive() != 1) {
				ajax::success(array());
			}
		} catch (Exception $e) {
			ajax::success(array());
		}
		if (!class_exists('calendar_event')) {
			ajax::success(array());
		}
		$return = array();
		foreach ($sunshutter->getCmd(null) as $sunshutter_cmd) {
			foreach (calendar_event::searchByCmd($sunshutter_cmd->getId()) as $event) {
				$return[$event->getId()] = $event;
			}
		}
		ajax::success(utils::o2a($return));
	}

	if (init('action') == 'getsunshutter') {
		$return = array();
		$return['eqLogics'] = array();
		foreach (sunshutter::byType('sunshutter') as $sunshutter) {
			$return['eqLogics'][] = $sunshutter->toHtml(init('version'));
		}
		ajax::success($return);
	}

	if (init('action') == 'getSummary') {
		ajax::success(sunshutter::getSummary());
	}

	throw new Exception(__('Aucune methode correspondante à', __FILE__) . ' : ' . init('action'));
} catch (Exception $e) {
	ajax::error(displayExeption($e), $e->getCode());
}
