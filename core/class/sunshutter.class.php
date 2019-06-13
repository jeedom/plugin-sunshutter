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
  /*     * *************************Attributs****************************** */
  
  
  
  /*     * ***********************Methode static*************************** */
  
  
  public static function cron5() {
    foreach (eqLogic::byType('sunshutter') as $sunshutter) {
      $sunshutter->updateData();
    }
  }
  
  public static function cron() {
    $forcedByDelay = 0;
    foreach (eqLogic::byType('sunshutter', true) as $sunshutter) {
      $stateHandlingCmd = $sunshutter->getCmd(null,'stateHandling');
      if ($stateHandlingCmd->execCmd() == false) {
            if (!$sunshutter->getCache('manualSuspend')){
                if ($sunshutter->getConfiguration('shutter::nobackhand',0) == 2){
                    $delay = $sunshutter->getConfiguration('shutter::customDelay',0);
                    $since = $sunshutter->getCache('beginSuspend');
                    $deltadelay = abs($since - time())/60;
                    log::add('sunshutter','debug',$sunshutter->getHumanName().' - CRON CHECK DELAY : delay is ' . $delay . ' min - delta is ' . round($deltadelay,2)) . ' minutes';
                    if ($deltadelay>=$delay){
                        log::add('sunshutter','debug',$sunshutter->getHumanName().' - CRON CHECK DELAY Going back to normal delay is passed recalculating...');
                        $sunshutter->checkAndUpdateCmd('stateHandling', true);
                        $sunshutter->checkAndUpdateCmd('stateHandlingLabel', 'Aucun');
                        $sunshutter->setCache('beginSuspend',0);
                        $sunshutter->executeAction(true);
                        $forcedByDelay = 1;
                    }
                }
            }
      } else {
            if($sunshutter->getConfiguration('shutter::nobackhand',0) != 0){
                $lastPositionOrder = $sunshutter->getCache('lastPositionOrder',null);
                $currentPosition = null;
                $cmd = cmd::byId(str_replace('#','',$sunshutter->getConfiguration('shutter::state')));
                if(is_object($cmd)){
                    $currentPosition = $cmd->execCmd();
                }
                if($currentPosition !== null  && $lastPositionOrder !== null){
                    $amplitude = abs($sunshutter->getConfiguration('shutter::closePosition',0)-$sunshutter->getConfiguration('shutter::openPosition',100));
                    $delta = abs($currentPosition-$lastPositionOrder);
                    $ecart = ($delta/$amplitude)*100;
                    log::add('sunshutter','debug',$sunshutter->getHumanName().' - Ecart depuis le dernier ordre : ' . $ecart);
                    if ($ecart>3){
                        $sunshutter->checkAndUpdateCmd('stateHandling', false);
                        $sunshutter->checkAndUpdateCmd('stateHandlingLabel', 'Auto');
                        $sunshutter->setCache('beginSuspend',time());
                        $sunshutter->setCache('manualSuspend',false);
                        log::add('sunshutter','debug',$sunshutter->getHumanName().' - Position != last order by far 3% i suspend');
                    }
                }
            }
      }
      $cron = $sunshutter->getConfiguration('cron::executeAction');
      if ($cron == 'custom'){
        $cron = $sunshutter->getConfiguration('cron::custom');
      }
      if ($cron != '') {
        try {
          $c = new Cron\CronExpression(checkAndFixCron($cron), new Cron\FieldFactory);
          if ($c->isDue()) {
            if ($forcedByDelay == 0){
                $sunshutter->executeAction();
            }
          }
        } catch (Exception $exc) {
          log::add('sunshutter', 'error', __('Expression cron non valide pour ', __FILE__) . $sunshutter->getHumanName() . ' : ' . $cron);
        }
      }
    }
  }
  
  public static function immediateAction($_options){
    $sunshutter = eqLogic::byId($_options['sunshutter_id']);
    if (!is_object($sunshutter)) {
      return;
    }
    log::add('sunshutter', 'debug', $sunshutter->getHumanName().' - Immediate Trigger from ' . print_r($_options,true));
    $conditions = $sunshutter->getConfiguration('conditions','');
    if($conditions != '' ){
      foreach ($conditions as $condition) {
        if ($condition['conditions::immediate']) {
            if($condition['conditions::condition'] != '' && jeedom::evaluateExpression($condition['conditions::condition'])){
                if ($condition['conditions::position'] != '') {
                    log::add('sunshutter','debug',$sunshutter->getHumanName().' - Immediate Condition Met : ' . $condition['conditions::condition'] . ' (' . $condition['conditions::position'] . '%)');
                    $cmd = cmd::byId(str_replace('#','',$sunshutter->getConfiguration('shutter::position')));
                    if(is_object($cmd)){
                        $position = $condition['conditions::position'];
                        if ($condition['conditions::suspend'] == 1) {
                            log::add('sunshutter','debug',$sunshutter->getHumanName().' - Immediate Condition is a suspendable condition : suspend');
                            $sunshutter->setCache('beginSuspend',time());
                            $sunshutter->checkAndUpdateCmd('stateHandling', false);
                            $sunshutter->checkAndUpdateCmd('stateHandlingLabel', 'Auto');
                        }
                        $currentPosition = null;
                        $cmdState = cmd::byId(str_replace('#','',$sunshutter->getConfiguration('shutter::state')));
                        if(is_object($cmdState)){
                            $currentPosition = $cmdState->execCmd();
                        }
                        $amplitude = abs($sunshutter->getConfiguration('shutter::closePosition',0)-$sunshutter->getConfiguration('shutter::openPosition',100));
                        $delta = abs($position-$currentPosition);
                        $ecart = ($delta/$amplitude)*100;
                        log::add('sunshutter','debug',$sunshutter->getHumanName().' - Ecart avec la cible : ' . $ecart);
                        if ($ecart<3){
                          log::add('sunshutter','debug',$sunshutter->getHumanName().' - Do nothing, position != new position by less than 3%');
                        } else {
                            log::add('sunshutter','debug',$sunshutter->getHumanName().' - Do action ' . $position);
                            $cmd->execCmd(array('slider' => $position));
                        }
                    }
                    break;
                }
            }
        }
      }
    }
  }
  
  public static function getPanel($_type){
    $return = array();
    foreach (eqLogic::byType('sunshutter', true) as $sunshutter) {
      $name = $sunshutter->getHumanName(true);
      $cmdHandling = $sunshutter->getCmd(null, 'stateHandling');
      $cmdHandlingLabel = $sunshutter->getCmd(null, 'stateHandlingLabel');
      $cmdAzimuth = $sunshutter->getCmd(null, 'sun_azimuth');
      $cmdElevation = $sunshutter->getCmd(null, 'sun_elevation');
      $cmdpause = $sunshutter->getCmd(null, 'suspendHandling');
      $cmdresume = $sunshutter->getCmd(null, 'resumeHandling');
      $cmdExecute = $sunshutter->getCmd(null, 'executeAction');
      $openvalue = $sunshutter->getConfiguration('shutter::openPosition',0);
      $closevalue = $sunshutter->getConfiguration('shutter::closePosition',0);
      $currentPosition = null;
      $cmdstatehtml = '';
      $cmdhtml = '';
      $cmd = cmd::byId(str_replace('#','',$sunshutter->getConfiguration('shutter::state')));
      if (is_object($cmd)) {
        $currentPosition = $cmd->execCmd();
        $cmdstatehtml = $cmd->toHtml($_type);
      }
      $cmdPosition = str_replace('#','',$sunshutter->getConfiguration('shutter::position'));
      $cmd = cmd::byId($cmdPosition);
      if (is_object($cmd)) {
        $cmdhtml = $cmd->toHtml($_type);
      }
      $handling =  $cmdHandling->execCmd();
      $datas = array('name' => $name,
      'position' => $sunshutter->getCache('lastPositionOrder',null),
      'handling' => $handling,
      'pauseId' => $cmdpause->getId(),
      'resumeId' => $cmdresume->getId(),
      'executeId' => $cmdExecute->getId(),
      'state' => $currentPosition,
      'openvalue' => $openvalue,
      'closevalue' => $closevalue,
      'positionId' => $cmdPosition,
      'cmdhtml' => $cmdhtml,
      'HandlingLabel' => $cmdHandlingLabel->execCmd(),
      'cmdstatehtml' => $cmdstatehtml,
      'elevation' => $cmdElevation->execCmd(),
      'azimuth' => $cmdAzimuth->execCmd(),
      'link' => $sunshutter->getLinkToConfiguration(),
    );
    $return[]=$datas;
  }
  return $return;
}

/*     * *********************Méthodes d'instance************************* */

public function postSave() {
  $cmd = $this->getCmd(null, 'sun_elevation');
  if (!is_object($cmd)) {
    $cmd = new sunshutterCmd();
    $cmd->setLogicalId('sun_elevation');
    $cmd->setName(__('Elévation soleil', __FILE__));
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
    $cmd->setName(__('Suspension (Label)', __FILE__));
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
  
  $conditions = $this->getConfiguration('conditions','');
  if($conditions != '' ){
    $listener = listener::byClassAndFunction('sunshutter', 'immediateAction', array('sunshutter_id' => intval($this->getId())));
    if (!is_object($listener)) {
        $listener = new listener();
    }
    $listener->setClass('sunshutter');
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
    }
  } else {
    $listener = listener::byClassAndFunction('sunshutter', 'immediateAction', array('sunshutter_id' => intval($this->getId())));
    if (is_object($listener)) {
      $listener->remove();
    }
  }
  $this->updateData();
}

public function updateData(){
  $SD = new SolarData\SolarData();
  $SD->setObserverPosition($this->getConfiguration('lat'),$this->getConfiguration('long'),$this->getConfiguration('alt'));
  $SD->setObserverDate(date('Y'), date('n'), date('j'));
  $SD->setObserverTime(date('G'), date('i'),date('s'));
  $SD->setDeltaTime(67);
  $SD->setObserverTimezone(date('Z') / 3600);
  $SunPosition = $SD->calculate();
  $this->checkAndUpdateCmd('sun_elevation', round($SunPosition->e0°,2));
  $this->checkAndUpdateCmd('sun_azimuth', round($SunPosition->Φ°,2));
  $handlingCmd = $this->getCmd(null, 'stateHandling');
  if ($handlingCmd->execCmd() === '') {
    $handlingCmd->event(true);
    $this->checkAndUpdateCmd('stateHandlingLabel', 'Aucun');
  }
}

public function calculPosition(){
  $sun_elevation = $this->getCmd(null, 'sun_elevation')->execCmd();
  $sun_azimuth = $this->getCmd(null, 'sun_azimuth')->execCmd();
  $positions = $this->getConfiguration('positions');
  foreach ($positions as $position) {
    if($sun_elevation > $position['sun::elevation::from'] && $sun_elevation <= $position['sun::elevation::to']){
      if($sun_azimuth > $position['sun::azimuth::from'] && $sun_azimuth <= $position['sun::azimuth::to']){
        if($position['position::allowmove'] == '' || jeedom::evaluateExpression($position['position::allowmove']) == true){
            log::add('sunshutter','debug',$this->getHumanName().' - Valid condition : ' . $position['position::allowmove'] . ' Elevation : ' . $position['sun::elevation::from'] . '°-' . $position['sun::elevation::to'] . '° Azimuth : ' . $position['sun::azimuth::from'] . '°-' . $position['sun::azimuth::to'] . '° ('  . $position['shutter::position'] . '%)');
            return $position['shutter::position'];
        }
         log::add('sunshutter','debug',$this->getHumanName().' - Invalid condition : ' . $position['position::allowmove'] . ' Elevation : ' . $position['sun::elevation::from'] . '°-' . $position['sun::elevation::to'] . '° Azimuth : ' . $position['sun::azimuth::from'] . '°-' . $position['sun::azimuth::to'] . '° ('  . $position['shutter::position'] . '%)');
      }
    }
  }
  $default = $this->getConfiguration('shutter::openPosition',0);
  if ($this->getConfiguration('shutter::defaultAction','open') == 'close'){
    $default = $this->getConfiguration('shutter::closePosition',0);
  }
  if ($this->getConfiguration('shutter::defaultAction','open') == 'custom'){
    $default = $this->getConfiguration('shutter::customPosition',0);
  }
  if ($this->getConfiguration('shutter::defaultAction','close') == 'none'){
    $default = $this->getCache('lastPositionOrder',null);
  }
  return $default;
}

public function executeAction($_force = false){
  $stateHandlingCmd = $this->getCmd(null,'stateHandling');
  if (!$_force && $stateHandlingCmd->execCmd() == false) {
    if ($this->getConfiguration('shutter::nobackhand',0) == 2){
        $delay = $this->getConfiguration('shutter::customDelay',0);
        $since = $this->getCache('beginSuspend');
        $deltadelay = abs($since - time())/60;
        log::add('sunshutter','debug',$this->getHumanName().' - Handling desactivated : delay is ' . $delay . ' min - delta is ' . round($deltadelay,2)) . ' minutes';
        if ($this->getCache('manualSuspend')){
            log::add('sunshutter','debug',$this->getHumanName().' - Do nothing, Handling desactivated manually');
            return;
        }
        if ($deltadelay>=$delay){
            log::add('sunshutter','debug',$this->getHumanName().' - Going back to normal delay is passed ');
            $this->checkAndUpdateCmd('stateHandling', true);
            $this->checkAndUpdateCmd('stateHandlingLabel', 'Aucun');
            $_force = true;
        } else {
            log::add('sunshutter','debug',$this->getHumanName().' - Do nothing, handling desactivated');
            return;
        }
    } else {
        log::add('sunshutter','debug',$this->getHumanName().' - Do nothing, handling desactivated');
        return;
    }
  }
  log::add('sunshutter','debug',$this->getHumanName().' - Start executeAction');
  $this->updateData();
  if($this->getConfiguration('condition::allowmove') != '' && jeedom::evaluateExpression($this->getConfiguration('condition::allowmove')) == false){
    log::add('sunshutter','debug',$this->getHumanName().' - Do nothing, false condition');
    return;
  }
  $currentPosition = null;
  $cmd = cmd::byId(str_replace('#','',$this->getConfiguration('shutter::state')));
  if(is_object($cmd)){
    $currentPosition = $cmd->execCmd();
  }
  if(!$_force && $this->getConfiguration('shutter::nobackhand',0) != 0){
    $lastPositionOrder = $this->getCache('lastPositionOrder',null);
    if($currentPosition !== null  && $lastPositionOrder !== null){
      $amplitude = abs($this->getConfiguration('shutter::closePosition',0)-$this->getConfiguration('shutter::openPosition',100));
      $delta = abs($currentPosition-$lastPositionOrder);
      $ecart = ($delta/$amplitude)*100;
      log::add('sunshutter','debug',$this->getHumanName().' - Ecart depuis le dernier ordre : ' . $ecart);
      if ($ecart>3){
        $this->checkAndUpdateCmd('stateHandling', false);
        $this->checkAndUpdateCmd('stateHandlingLabel', 'Auto');
        $this->setCache('beginSuspend',time());
        $this->setCache('manualSuspend',false);
        log::add('sunshutter','debug',$this->getHumanName().' - Do nothing, position != last order by far 3% i suspend');
        return;
      }
    }
  }
  $position = null;
  $position = $this->calculPosition();
  $conditions = $this->getConfiguration('conditions','');
  if($conditions != '' ){
    foreach ($conditions as $condition) {
        if (!$condition['conditions::immediate']) {
            if($condition['conditions::condition'] != '' && jeedom::evaluateExpression($condition['conditions::condition'])){
                if ($condition['conditions::position'] != '') {
                    log::add('sunshutter','debug',$this->getHumanName().' - Condition Met : ' . $condition['conditions::condition'] . ' (' . $condition['conditions::position'] . '%)');
                    $position = $condition['conditions::position'];
                    break;
                }
            }
        }
    }
  }
  log::add('sunshutter','debug',$this->getHumanName().' - Calcul position '.$position);
  if(($position !== null && $currentPosition !== null)){
    $amplitude = abs($this->getConfiguration('shutter::closePosition',0)-$this->getConfiguration('shutter::openPosition',100));
    $delta = abs($position-$currentPosition);
    $ecart = ($delta/$amplitude)*100;
    log::add('sunshutter','debug',$this->getHumanName().' - Ecart avec la cible : ' . $ecart);
    if ($ecart<3){
      log::add('sunshutter','debug',$this->getHumanName().' - Do nothing, position != new position by less than 3%');
      $this->setCache('lastPositionOrder',$position);
      $this->checkAndUpdateCmd('lastposition', $position);
      return;
    }
  }
  if ($position !== null || $_force){
    log::add('sunshutter','debug',$this->getHumanName().' - Do action ' . $position);
    $cmd = cmd::byId(str_replace('#','',$this->getConfiguration('shutter::position')));
    if(is_object($cmd)){
      $cmd->execCmd(array('slider' => $position));
    }
    $this->setCache('lastPositionOrder',$position);
    $this->checkAndUpdateCmd('lastposition', $position);
  }
}

/*     * **********************Getteur Setteur*************************** */
}

class sunshutterCmd extends cmd {
  /*     * *************************Attributs****************************** */
  
  
  /*     * ***********************Methode static*************************** */
  
  
  /*     * *********************Methode d'instance************************* */
  
  
  public function execute($_options = array()) {
    $sunshutter = $this->getEqLogic();
    if($this->getLogicalId() == 'refresh'){
      $sunshutter->updateData();
    }
    
    if($this->getLogicalId() == 'executeAction'){
      $sunshutter->executeAction(true);
    }
    if($this->getLogicalId() == 'suspendHandling'){
      $sunshutter->checkAndUpdateCmd('stateHandling', false);
      $sunshutter->checkAndUpdateCmd('stateHandlingLabel', 'Manuel');
      $sunshutter->setCache('beginSuspend',time());
      $sunshutter->setCache('manualSuspend',true);
    }
    if($this->getLogicalId() == 'resumeHandling'){
      $sunshutter->checkAndUpdateCmd('stateHandling', true);
      $sunshutter->checkAndUpdateCmd('stateHandlingLabel', 'Aucun');
      $sunshutter->setCache('beginSuspend',0);
      $sunshutter->setCache('manualSuspend',false);
      $sunshutter->executeAction(true);
    }
  }
  
  /*     * **********************Getteur Setteur*************************** */
}
