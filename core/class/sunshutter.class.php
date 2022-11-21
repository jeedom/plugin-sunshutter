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

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/../../vendor/autoload.php';

class sunshutter extends eqLogic {

  /*     * ***********************Methode static*************************** */

  public static function cron5() {
    foreach (eqLogic::byType(__CLASS__) as $sunshutter) {
      if (date('Gi') == 0) {
        $sunshutter->save();
      } else {
        $sunshutter->updateData();
      }
    }
  }

  public static function cron() {
    foreach (eqLogic::byType(__CLASS__, true) as $sunshutter) {
      if ($sunshutter->getIsEnable() == 0) {
        continue;
      }
      $forcedByDelay = 0;
      $stateHandlingCmd = $sunshutter->getCmd(null, 'stateHandling');
      if ($sunshutter->getConfiguration('shutter::nobackhand', 0) == 2) {
        if ($stateHandlingCmd->execCmd() == false) {
          if (!$sunshutter->getCache('manualSuspend')) {
            $delay = $sunshutter->getConfiguration('shutter::customDelay', 0);
            $since = $sunshutter->getCache('beginSuspend');
            $deltadelay = abs($since - time()) / 60;
            log::add(__CLASS__, 'debug', $sunshutter->getHumanName() . ' ' . __('Gestion automatique suspendue, vérification du délai avant reprise', __FILE__) . ' (' . $delay . ' ' . __('minutes', __FILE__) . ') : ' . round($deltadelay) . ' ' . __('minutes', __FILE__));
            if ($deltadelay >= $delay) {
              log::add(__CLASS__, 'debug', $sunshutter->getHumanName() . ' ' . __('Délai de reprise atteint : réactivation de la gestion automatique', __FILE__));
              $sunshutter->checkAndUpdateCmd('stateHandling', true);
              $sunshutter->checkAndUpdateCmd('stateHandlingLabel', 'Aucun');
              $sunshutter->setCache('beginSuspend', 0);
              $sunshutter->executeAction(true);
              $forcedByDelay = 1;
            }
          }
        } else {
          $lastPositionOrder = $sunshutter->getCache('lastPositionOrder', null);
          $currentPosition = $sunshutter->getCurrentPosition();
          if ($currentPosition !== null && $lastPositionOrder !== null) {
            $amplitude = abs($sunshutter->getConfiguration('shutter::closePosition', 0) - $sunshutter->getConfiguration('shutter::openPosition', 100));
            $delta = abs($currentPosition - $lastPositionOrder);
            $ecart = round(($delta / $amplitude) * 100, 2);
            // log::add(__CLASS__, 'debug', $sunshutter->getHumanName() . ' ' . __('Ecart avec la dernière position connue',__FILE__) . ' : ' . $ecart . ' %');
            if ($ecart > 4 && ($sunshutter->getConfiguration('shutter::moveDuration', 0) == 0 || (strtotime('now') - $sunshutter->getCache('lastPositionOrderTime', 0)) > $sunshutter->getConfiguration('shutter::moveDuration'))) {
              $sunshutter->checkAndUpdateCmd('stateHandling', false);
              $sunshutter->checkAndUpdateCmd('stateHandlingLabel', 'Auto');
              $sunshutter->setCache('beginSuspend', time());
              $sunshutter->setCache('manualSuspend', false);
              log::add(__CLASS__, 'debug', $sunshutter->getHumanName() . ' ' . __('Ecart avec la dernière position connue supérieur à 4 % : suspension de la gestion automatique', __FILE__));
            }
          }
        }
      }
      $cron = $sunshutter->getConfiguration('cron::executeAction');
      if ($cron == 'custom') {
        $cron = $sunshutter->getConfiguration('cron::custom');
      }
      if ($cron != '') {
        try {
          $c = new Cron\CronExpression(checkAndFixCron($cron), new Cron\FieldFactory);
          if ($c->isDue() && $forcedByDelay == 0) {
            $sunshutter->executeAction();
          }
        } catch (Exception $exc) {
          log::add(__CLASS__, 'error', __('Expression cron non valide pour', __FILE__) . ' ' . $sunshutter->getHumanName() . ' : ' . $cron);
        }
      }
    }
  }

  public static function immediateAction($_options) {
    $sunshutter = eqLogic::byId($_options['sunshutter_id']);
    if (!is_object($sunshutter)) {
      return;
    }
    if ($sunshutter->getIsEnable() == 0) {
      return;
    }
    if ($sunshutter->getCache('manualSuspend')) {
      return;
    }
    log::add(__CLASS__, 'debug', $sunshutter->getHumanName() . ' ' . __('Déclenchement de l\'action immédiate', __FILE__) . ' : ' . print_r($_options, true));
    if ($sunshutter->getConfiguration('condition::systematic', 0) == 1) {
      log::add(__CLASS__, 'debug', $sunshutter->getHumanName() . ' ' . __('Les actions immédiates sont prioritaires', __FILE__));
      $sunshutter->systematicAction($_options['event_id']);
    } else {
      $sunshutter->executeAction();
    }
  }

  public static function getSummary() {
    $numberShutters = 0;
    $sumposition = 0;
    $numbersupendedAuto = 0;
    $numbersupendedManual = 0;
    foreach (eqLogic::byType(__CLASS__, true) as $sunshutter) {
      $numberShutters += 1;
      $cmdHandling = $sunshutter->getCmd(null, 'stateHandling');
      $cmdHandlingLabel = $sunshutter->getCmd(null, 'stateHandlingLabel');
      $cmd = cmd::byId(str_replace('#', '', $sunshutter->getConfiguration('shutter::state')));
      if (is_object($cmd)) {
        $sumposition += $cmd->execCmd();
      }
      $handling =  $cmdHandling->execCmd();
      if ($handling == false) {
        $handlingLabel = $cmdHandlingLabel->execCmd();
        if ($handlingLabel == 'Auto') {
          $numbersupendedAuto += 1;
        } else {
          $numbersupendedManual += 1;
        }
      }
    }
    return array('moyPos' => ($numberShutters == 0) ? 'N/A' : round($sumposition / $numberShutters), 'auto' => $numbersupendedAuto, 'manual' => $numbersupendedManual);
  }

  /*     * *********************Méthodes d'instance************************* */

  public function toHtml($_version = 'dashboard') {
    $replace = $this->preToHtml($_version);
    if (!is_array($replace)) {
      return $replace;
    }
    $version = jeedom::versionAlias($_version);
    if ($version == 'mobile') {
      $replace['#class#'] = $replace['#class#'] . ' col2';
    }
    if ($_version == 'mview' || $_version == 'view') {
      $replace['#class#'] = $replace['#class#'] . ' displayObjectName';
    }

    $tableOption = $this->getDisplay('layout::' . $version . '::table::parameters', array());
    $tableOption['center'] = 1;
    $replace['#eqLogic_class#'] = 'eqLogic_layout_table';
    if ($_version == 'mview') {
      $tableOption['style::td::1::1'] = 'width:65%';
    }
    $table = self::generateHtmlTable(1, 2, $tableOption);
    $firstCmdMode = true;
    foreach ($this->getCmd(null, null, true) as $cmd) {
      if (isset($replace['#refresh_id#']) && $cmd->getId() == $replace['#refresh_id#']) {
        continue;
      }
      if ($cmd->getType() == 'action' && $cmd->getLogicalId() == 'mode' && $firstCmdMode == true) {
        $table['tag']['#cmd::1::1#'] .= '<br/>';
        $firstCmdMode = false;
      }
      $table['tag']['#cmd::1::1#'] .= $cmd->toHtml($version, '');
    }
    if ($_version == 'mview' || $_version == 'view') {
      $cmd = cmd::byId(str_replace('#', '', $this->getConfiguration('shutter::state')));
      if (is_object($cmd)) {
        $eqLogic_shutter = $cmd->getEqlogic();
        foreach ($eqLogic_shutter->getCmd(null, null, true) as $cmd) {
          if ($cmd->getLogicalId() == 'refresh') {
            continue;
          }
          $table['tag']['#cmd::1::2#'] .= $cmd->toHtml($version, '');
        }
      }
    }
    $replace['#cmd#'] = template_replace($table['tag'], $table['html']);
    return $this->postToHtml($version, template_replace($replace, getTemplate('core', $version, 'eqLogic')));
  }

  public function postSave() {
    $cmd = $this->getCmd(null, 'sun_elevation');
    if (!is_object($cmd)) {
      $cmd = new sunshutterCmd();
      $cmd->setLogicalId('sun_elevation');
      $cmd->setName(__('Elévation soleil', __FILE__));
      $cmd->setIsVisible(0);
    }
    $cmd->setType('info');
    $cmd->setSubType('numeric');
    $cmd->setUnite('°');
    $cmd->setEqLogic_id($this->getId());
    $cmd->save();

    $cmd = $this->getCmd(null, 'sun_azimuth');
    if (!is_object($cmd)) {
      $cmd = new sunshutterCmd();
      $cmd->setLogicalId('sun_azimuth');
      $cmd->setName(__('Azimuth soleil', __FILE__));
      $cmd->setIsVisible(0);
    }
    $cmd->setType('info');
    $cmd->setSubType('numeric');
    $cmd->setUnite('°');
    $cmd->setEqLogic_id($this->getId());
    $cmd->save();

    $cmd = $this->getCmd(null, 'stateHandling');
    if (!is_object($cmd)) {
      $cmd = new sunshutterCmd();
      $cmd->setLogicalId('stateHandling');
      $cmd->setName(__('Etat gestion', __FILE__));
    }
    $cmd->setType('info');
    $cmd->setSubType('binary');
    $cmd->setEqLogic_id($this->getId());
    $cmd->save();

    $cmd = $this->getCmd(null, 'stateHandlingLabel');
    if (!is_object($cmd)) {
      $cmd = new sunshutterCmd();
      $cmd->setLogicalId('stateHandlingLabel');
      $cmd->setName(__('Suspension (label)', __FILE__));
      $cmd->setIsVisible(0);
    }
    $cmd->setType('info');
    $cmd->setSubType('string');
    $cmd->setEqLogic_id($this->getId());
    $cmd->save();

    $cmd = $this->getCmd(null, 'mode');
    if (!is_object($cmd)) {
      $cmd = new sunshutterCmd();
      $cmd->setLogicalId('mode');
      $cmd->setName(__('Mode', __FILE__));
    }
    $cmd->setType('info');
    $cmd->setSubType('string');
    $cmd->setEqLogic_id($this->getId());
    $cmd->save();

    $cmd = $this->getCmd(null, 'label');
    if (!is_object($cmd)) {
      $cmd = new sunshutterCmd();
      $cmd->setLogicalId('label');
      $cmd->setName(__('Label', __FILE__));
    }
    $cmd->setType('info');
    $cmd->setSubType('string');
    $cmd->setEqLogic_id($this->getId());
    $cmd->save();

    $cmd = $this->getCmd(null, 'lastposition');
    if (!is_object($cmd)) {
      $cmd = new sunshutterCmd();
      $cmd->setLogicalId('lastposition');
      $cmd->setName(__('Dernière position', __FILE__));
      $cmd->setIsVisible(0);
    }
    $cmd->setType('info');
    $cmd->setSubType('numeric');
    $cmd->setUnite('%');
    $cmd->setEqLogic_id($this->getId());
    $cmd->save();

    $cmd = $this->getCmd(null, 'executeAction');
    if (!is_object($cmd)) {
      $cmd = new sunshutterCmd();
      $cmd->setLogicalId('executeAction');
      $cmd->setName(__('Forcer action', __FILE__));
      $cmd->setIsVisible(0);
    }
    $cmd->setType('action');
    $cmd->setSubType('other');
    $cmd->setEqLogic_id($this->getId());
    $cmd->save();

    $cmd = $this->getCmd(null, 'suspendHandling');
    if (!is_object($cmd)) {
      $cmd = new sunshutterCmd();
      $cmd->setLogicalId('suspendHandling');
      $cmd->setName(__('Suspendre', __FILE__));
    }
    $cmd->setType('action');
    $cmd->setSubType('other');
    $cmd->setEqLogic_id($this->getId());
    $cmd->save();

    $cmd = $this->getCmd(null, 'resumeHandling');
    if (!is_object($cmd)) {
      $cmd = new sunshutterCmd();
      $cmd->setLogicalId('resumeHandling');
      $cmd->setName(__('Reprendre', __FILE__));
    }
    $cmd->setType('action');
    $cmd->setSubType('other');
    $cmd->setEqLogic_id($this->getId());
    $cmd->save();

    $cmd = $this->getCmd(null, 'refresh');
    if (!is_object($cmd)) {
      $cmd = new sunshutterCmd();
      $cmd->setLogicalId('refresh');
      $cmd->setName(__('Rafraichir', __FILE__));
    }
    $cmd->setType('action');
    $cmd->setSubType('other');
    $cmd->setEqLogic_id($this->getId());
    $cmd->save();

    $conditions = $this->getConfiguration('conditions', '');
    if (is_array($conditions)) {
      $listener = listener::byClassAndFunction(__CLASS__, 'immediateAction', array('sunshutter_id' => intval($this->getId())));
      if (!is_object($listener)) {
        $listener = new listener();
      }
      $listener->setClass(__CLASS__);
      $listener->setFunction('immediateAction');
      $listener->setOption(array('sunshutter_id' => intval($this->getId())));
      $listener->emptyEvent();
      $nblistener = 0;
      foreach ($conditions as $condition) {
        if ($condition['conditions::immediate']) {
          preg_match_all("/#([0-9]*)#/", $condition['conditions::condition'], $matches);
          foreach ($matches[1] as $cmd_id) {
            $nblistener += 1;
            $listener->addEvent($cmd_id);
          }
        }
      }
      if ($nblistener > 0) {
        $listener->save();
      } else {
        $listener->remove();
      }
    } else {
      $listener = listener::byClassAndFunction(__CLASS__, 'immediateAction', array('sunshutter_id' => intval($this->getId())));
      if (is_object($listener)) {
        $listener->remove();
      }
    }
    $this->updateData();
  }

  public function updateData() {
    $SD = new SolarData\SolarData();
    if ($this->getConfiguration('useJeedomLocalisation') == 1) {
      $SD->setObserverPosition(config::byKey('info::latitude'), config::byKey('info::longitude'), config::byKey('info::altitude'));
    } else {
      $SD->setObserverPosition($this->getConfiguration('lat'), $this->getConfiguration('long'), $this->getConfiguration('alt'));
    }
    $SD->setObserverDate(date('Y'), date('n'), date('j'));
    $SD->setObserverTime(date('G'), date('i'), date('s'));
    $SD->setDeltaTime(67);
    $SD->setObserverTimezone(date('Z') / 3600);
    $SunPosition = $SD->calculate();
    $this->checkAndUpdateCmd('sun_elevation', round($SunPosition->e0°, 2));
    $this->checkAndUpdateCmd('sun_azimuth', round($SunPosition->Φ°, 2));
    $handlingCmd = $this->getCmd(null, 'stateHandling');
    if ($handlingCmd->execCmd() === '') {
      $handlingCmd->event(true);
      $this->checkAndUpdateCmd('stateHandlingLabel', 'Aucun');
    }
  }

  public function getCurrentPosition() {
    if ($this->getConfiguration('shutter::refreshPosition') != '') {
      $cmd = cmd::byId(str_replace('#', '', $this->getConfiguration('shutter::refreshPosition')));
      if (is_object($cmd)) {
        $cmd->execCmd();
      }
    }
    $currentPosition = null;
    $cmd = cmd::byId(str_replace('#', '', $this->getConfiguration('shutter::state')));
    if (is_object($cmd)) {
      $currentPosition = $cmd->execCmd();
    }
    return $currentPosition;
  }

  public function systematicAction($_cmdId) {
    $mode = '';
    if (is_object($modeCmd = $this->getCmd(null, 'mode'))) {
      $mode = strtolower($modeCmd->execCmd());
    }
    $conditions = $this->getConfiguration('conditions', '');
    if (is_array($conditions)) {
      foreach ($conditions as $condition) {
        if ($condition['conditions::immediate'] && strpos($condition['conditions::condition'], '#' . $_cmdId . '#') !== false) {
          if (isset($condition['conditions::mode']) && trim($condition['conditions::mode']) != '') {
            if (!in_array($mode, array_map('trim', explode(',', strtolower($condition['conditions::mode']))))) {
              log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Le mode ne correspond pas', __FILE__) . ' : ' . $modeCmd->execCmd() . ' != ' . $condition['conditions::mode']);
              continue;
            }
          }
          if ($condition['conditions::condition'] != '' && jeedom::evaluateExpression($condition['conditions::condition'])) {
            if (trim($condition['conditions::position']) != '') {
              if (trim($this->getConfiguration('condition::allowmove')) != '' && jeedom::evaluateExpression($this->getConfiguration('condition::allowmove')) == false) {
                if (isset($condition['conditions::forced']) && $condition['conditions::forced']) {
                  log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Condition générale non remplie - Mais action forcée', __FILE__) . ' : ' . $this->getConfiguration('condition::allowmove'));
                } else {
                  log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Condition générale non remplie - Aucune action', __FILE__) . ' : ' . $this->getConfiguration('condition::allowmove'));
                  continue;
                }
              }
              log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Condition avec action immédiate', __FILE__) . ' : ' . $condition['conditions::condition'] . ' (' . $condition['conditions::position'] . ' %)');
              $cmd = cmd::byId(str_replace('#', '', $this->getConfiguration('shutter::position')));
              if (is_object($cmd)) {
                $position = $condition['conditions::position'];
                if ($condition['conditions::suspend'] == 1) {
                  log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Condition avec action immédiate + suspension de la gestion automatique', __FILE__));
                  $this->setCache('beginSuspend', time());
                  $this->checkAndUpdateCmd('stateHandling', false);
                  $cmdStateLabel = $this->getCmd(null, 'stateHandlingLabel');
                  $stateLabel = $cmdStateLabel->execCmd();
                  if ($stateLabel != 'Manuel') {
                    $this->checkAndUpdateCmd('stateHandlingLabel', 'Auto');
                  }
                }
                $currentPosition = null;
                $currentPosition = $this->getCurrentPosition();
                $amplitude = abs($this->getConfiguration('shutter::closePosition', 0) - $this->getConfiguration('shutter::openPosition', 100));
                $delta = abs($position - $currentPosition);
                $ecart = round(($delta / $amplitude) * 100, 2);
                log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Ecart avec la position cible', __FILE__) . ' : ' . $ecart . ' %');
                if ($ecart <= 4) {
                  log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Ecart avec la position cible inférieur à 4 % : aucune action', __FILE__));
                } else {
                  log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Positionnement à', __FILE__) . ' ' . $position . ' %');
                  $cmd->execCmd(array('slider' => $position));
                  $this->setCache('lastPositionOrder', $position);
                  $this->setCache('lastPositionOrderTime', strtotime('now'));
                  $this->checkAndUpdateCmd('lastposition', $position);
                }
              }
              break;
            }
          }
        }
      }
    }
  }

  public function calculPosition() {
    $sun_elevation = $this->getCmd(null, 'sun_elevation')->execCmd();
    $sun_azimuth = $this->getCmd(null, 'sun_azimuth')->execCmd();
    $positions = $this->getConfiguration('positions', '');
    if (is_array($positions)) {
      foreach ($positions as $position) {
        if ($sun_elevation > $position['sun::elevation::from'] && $sun_elevation <= $position['sun::elevation::to']) {
          if ($sun_azimuth > $position['sun::azimuth::from'] && $sun_azimuth <= $position['sun::azimuth::to']) {
            if ($position['position::allowmove'] == '' || jeedom::evaluateExpression($position['position::allowmove']) == true) {
              log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Calcul de positionnement - Conditions remplies', __FILE__) . ' : ' . $position['position::allowmove'] . ' ' . __('Elévation', __FILE__) . ' = ' . $position['sun::elevation::from'] . '°-' . $position['sun::elevation::to'] . ' ' . __('Azimuth', __FILE__) . ' = ' . $position['sun::azimuth::from'] . '°-' . $position['sun::azimuth::to'] . '° ('  . $position['shutter::position'] . ' %)');
              //return $position['shutter::position'];
              return array('position' => $position['shutter::position'], 'label' => $position['position::label']);
            }
            log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Calcul de positionnement - Conditions non remplies', __FILE__) . ' : ' . $position['position::allowmove'] . ' ' . __('Elévation', __FILE__) . ' = '  . $position['sun::elevation::from'] . '°-' . $position['sun::elevation::to'] . ' ' . __('Azimuth', __FILE__) . ' = ' . $position['sun::azimuth::from'] . '°-' . $position['sun::azimuth::to'] . '° ('  . $position['shutter::position'] . ' %)');
          }
        }
      }
    }

    switch ($this->getConfiguration('shutter::defaultAction', 'none')) {
      case 'open':
        $default = $this->getConfiguration('shutter::openPosition', 100);
        log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Calcul de positionnement - Action par défaut : Ouvrir', __FILE__) . ' ' . __('à', __FILE__) . ' ' . $default . ' %');
        break;
      case 'close':
        $default = $this->getConfiguration('shutter::closePosition', 0);
        log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Calcul de positionnement - Action par défaut : Fermer', __FILE__) . ' ' . __('à', __FILE__) . ' ' . $default . ' %');
        break;
      case 'custom':
        $default = $this->getConfiguration('shutter::customPosition', 50);
        log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Calcul de positionnement - Action par défaut : Position personnalisée', __FILE__) . ' ' . __('à', __FILE__) . ' ' . $default . ' %');
        break;
      default:
        $default = $this->getCurrentPosition();
        log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Calcul de positionnement - Action par défaut : Ne rien faire', __FILE__));
        break;
    }

    //return $default;
    return array('position' => $default, 'label' => '');
  }

  public function executeAction($_force = false) {
    $stateHandlingCmd = $this->getCmd(null, 'stateHandling');
    if (!$_force && $stateHandlingCmd->execCmd() == false) {
      if ($this->getConfiguration('shutter::nobackhand', 0) == 2) {
        $delay = $this->getConfiguration('shutter::customDelay', 0);
        $since = $this->getCache('beginSuspend');
        $deltadelay = abs($since - time()) / 60;
        log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Gestion automatique suspendue, vérification du délai avant reprise', __FILE__) . ' (' . $delay . ' ' . __('minutes', __FILE__) . ') : ' . round($deltadelay) . ' ' . __('minutes', __FILE__));
        if ($this->getCache('manualSuspend')) {
          log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Gestion automatique suspendue manuellement : aucune action', __FILE__));
          return;
        }
        if ($deltadelay >= $delay) {
          log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Délai de reprise atteint : réactivation de la gestion automatique', __FILE__));
          $this->checkAndUpdateCmd('stateHandling', true);
          $this->checkAndUpdateCmd('stateHandlingLabel', 'Aucun');
          $_force = true;
        } else {
          log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Gestion automatique suspendue, réactivation dans', __FILE__) . ' ' . round($delay - $deltadelay) . ' ' . __('minutes', __FILE__));
          return;
        }
      } else {
        log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Gestion automatique suspendue : aucune action', __FILE__));
        return;
      }
    }
    log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Démarrage de la gestion automatique', __FILE__) . ' ' . $_force);
    $this->updateData();
    if (trim($this->getConfiguration('condition::allowmove')) != '' && jeedom::evaluateExpression($this->getConfiguration('condition::allowmove')) == false) {
      log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Condition générale non remplie - Aucune action', __FILE__) . ' : ' . $this->getConfiguration('condition::allowmove'));
      return;
    }
    $currentPosition = $this->getCurrentPosition();
    if (!$_force && $this->getConfiguration('shutter::nobackhand', 0) != 0) {
      $lastPositionOrder = $this->getCache('lastPositionOrder', null);
      if ($currentPosition !== null  && $lastPositionOrder !== null) {
        $amplitude = abs($this->getConfiguration('shutter::closePosition', 0) - $this->getConfiguration('shutter::openPosition', 100));
        $delta = abs($currentPosition - $lastPositionOrder);
        $ecart = round(($delta / $amplitude) * 100, 2);
        log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Ecart avec la dernière position connue', __FILE__) . ' : ' . $ecart . ' %');
        if ($ecart > 4 && ($this->getConfiguration('shutter::moveDuration', 0) == 0 || (strtotime('now') - $this->getCache('lastPositionOrderTime', 0)) > $this->getConfiguration('shutter::moveDuration'))) {
          $this->checkAndUpdateCmd('stateHandling', false);
          $this->checkAndUpdateCmd('stateHandlingLabel', 'Auto');
          $this->setCache('beginSuspend', time());
          $this->setCache('manualSuspend', false);
          log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Ecart avec la dernière position connue supérieur à 4 % : suspension de la gestion automatique', __FILE__));
          return;
        }
      }
    }
    $position = null;
    //$position = $this->calculPosition();
    $positionArray = $this->calculPosition();
    $position = $positionArray['position'];
    $label = $positionArray['label'];
    $conditions = $this->getConfiguration('conditions', '');
    $mode = '';
    if (is_object($modeCmd = $this->getCmd(null, 'mode'))) {
      $mode = strtolower($modeCmd->execCmd());
    }
    if (is_array($conditions) && count($conditions) > 0) {
      foreach ($conditions as $condition) {
        if ($condition['conditions::immediate'] && $this->getConfiguration('condition::systematic', 0) == 1) {
          continue;
        }
        if (isset($condition['conditions::mode']) && trim($condition['conditions::mode']) != '') {
          if (!in_array($mode, array_map('trim', explode(',', strtolower($condition['conditions::mode']))))) {
            log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Le mode ne correspond pas', __FILE__) . ' : ' . $modeCmd->execCmd() . ' != ' . $condition['conditions::mode']);
            continue;
          }
          if ($condition['conditions::condition'] == '') {
            log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Aucune condition définie - Mode valide', __FILE__) . ' : ' . $modeCmd->execCmd() . ' = ' . $condition['conditions::mode'] . ' (' . $condition['conditions::position'] . ' %)');
            $position = $condition['conditions::position'];
            $label = $condition['conditions::label'];
            break;
          }
        }
        if ($condition['conditions::condition'] != '' && jeedom::evaluateExpression($condition['conditions::condition'])) {
          if (isset($condition['conditions::suspend'])  && $condition['conditions::suspend'] == 1) {
            log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Condition avec suspension de la gestion automatique', __FILE__) . ' : ' . $condition['conditions::condition']);
            $this->checkAndUpdateCmd('stateHandling', false);
            $this->checkAndUpdateCmd('stateHandlingLabel', 'Auto');
            $this->setCache('beginSuspend', time());
            $this->setCache('manualSuspend', false);
          }
          if ($condition['conditions::position'] != '') {
            log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Condition remplie', __FILE__) . ' : ' . $condition['conditions::condition'] . ' (' . $condition['conditions::position'] . ' %)');
            $position = $condition['conditions::position'];
            $label = $condition['conditions::label'];
            break;
          } else {
            log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Condition remplie mais position vide - Aucune action', __FILE__) . ' : ' . $condition['conditions::condition']);
            $position = $currentPosition;
            $label = $condition['conditions::label'];
            break;
          }
        }
      }
    }
    log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Position actuelle', __FILE__) . ' : ' . $currentPosition . ' % → ' . __('Position cible', __FILE__) . ' : ' . $position . ' %');
    if (($position !== null && $currentPosition !== null)) {
      $amplitude = abs($this->getConfiguration('shutter::closePosition', 0) - $this->getConfiguration('shutter::openPosition', 100));
      $delta = abs($position - $currentPosition);
      $ecart = round(($delta / $amplitude) * 100, 2);
      log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Ecart avec la position cible', __FILE__) . ' : ' . $ecart . ' %');
      if ($ecart <= 4) {
        log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Ecart avec la position cible inférieur à 4 % : aucune action', __FILE__));
        $this->setCache('lastPositionOrder', $position);
        $this->checkAndUpdateCmd('lastposition', $position);
        $this->checkAndUpdateCmd('label', $label);
        return;
      }
    }
    if ($position !== null || $_force) {
      log::add(__CLASS__, 'debug', $this->getHumanName() . ' ' . __('Positionnement à', __FILE__) . ' ' . $position . ' %');
      $cmd = cmd::byId(str_replace('#', '', $this->getConfiguration('shutter::position')));
      if (is_object($cmd)) {
        $cmd->execCmd(array('slider' => $position));
      }
      $this->setCache('lastPositionOrder', $position);
      $this->setCache('lastPositionOrderTime', strtotime('now'));
      $this->checkAndUpdateCmd('lastposition', $position);
      $this->checkAndUpdateCmd('label', $label);
    }
  }
}

class sunshutterCmd extends cmd {

  public function execute($_options = array()) {
    $sunshutter = $this->getEqLogic();
    if ($this->getLogicalId() == 'refresh') {
      $sunshutter->updateData();
      $sunshutter->getCurrentPosition();
    }
    if ($this->getLogicalId() == 'executeAction') {
      $sunshutter->executeAction(true);
    }
    if ($this->getLogicalId() == 'suspendHandling') {
      log::add('sunshutter', 'debug', $sunshutter->getHumanName() . ' ' . __('Suspension manuelle de la gestion automatique', __FILE__));
      $sunshutter->checkAndUpdateCmd('stateHandling', false);
      $sunshutter->checkAndUpdateCmd('stateHandlingLabel', 'Manuel');
      $sunshutter->setCache('beginSuspend', time());
      $sunshutter->setCache('manualSuspend', true);
    }
    if ($this->getLogicalId() == 'resumeHandling') {
      log::add('sunshutter', 'debug', $sunshutter->getHumanName() . ' ' . __('Reprise manuelle de la gestion automatique', __FILE__));
      $sunshutter->checkAndUpdateCmd('stateHandling', true);
      $sunshutter->checkAndUpdateCmd('stateHandlingLabel', 'Aucun');
      $sunshutter->setCache('beginSuspend', 0);
      $sunshutter->setCache('manualSuspend', false);
      $sunshutter->executeAction(true);
    }
    if ($this->getLogicalId() == 'mode') {
      log::add('sunshutter', 'debug', $sunshutter->getHumanName() . ' ' . __('Passage en mode', __FILE__) . ' ' . $this->getName());
      $sunshutter->checkAndUpdateCmd('mode', $this->getName());
      if ($sunshutter->getConfiguration('condition::allowIgnoreSuspend', 0) == 1) {
        $sunshutter->checkAndUpdateCmd('stateHandling', true);
        $sunshutter->checkAndUpdateCmd('stateHandlingLabel', 'Aucun');
        $sunshutter->setCache('beginSuspend', 0);
        $sunshutter->setCache('manualSuspend', false);
        $sunshutter->executeAction(true);
      }
    }
  }
}
