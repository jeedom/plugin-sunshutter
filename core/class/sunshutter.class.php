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
    foreach (eqLogic::byType('sunshutter', true) as $sunshutter) {
      $cron = $sunshutter->getConfiguration('cron::executeAction');
      if ($cron != '') {
        try {
          $c = new Cron\CronExpression(checkAndFixCron($cron), new Cron\FieldFactory);
          if ($c->isDue()) {
            $sunshutter->executeAction();
          }
        } catch (Exception $exc) {
          log::add('virtual', 'error', __('Expression cron non valide pour ', __FILE__) . $sunshutter->getHumanName() . ' : ' . $cron);
        }
      }
    }
  }
  
  public static function reExecuteAction($_options){
    $sunshutter = eqLogic::byId($_options['sunshutter_id']);
    if (!is_object($sunshutter)) {
      return;
    }
    $sunshutter->executeAction();
  }
  
  public static function getPanel($_type){
    log::add('sunshutter','debug','panle ' . $_type);
    $return = array();
    foreach (eqLogic::byType('sunshutter', true) as $sunshutter) {
      $name = $sunshutter->getHumanName(true);
      $cmdHandling = $sunshutter->getCmd(null, 'stateHandling');
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
      'cmdstatehtml' => $cmdstatehtml,
      'elevation' => $cmdElevation->execCmd(),
      'azimuth' => $cmdAzimuth->execCmd(),
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
  
  if($this->getConfiguration('condition::immediatforceopen') != '' || $this->getConfiguration('condition::immediatforceclose') != ''){
    $listener = listener::byClassAndFunction('sunshutter', 'reExecuteAction', array('sunshutter_id' => intval($this->getId())));
    if (!is_object($listener)) {
      $listener = new listener();
    }
    $listener->setClass('sunshutter');
    $listener->setFunction('reExecuteAction');
    $listener->setOption(array('sunshutter_id' => intval($this->getId())));
    $listener->emptyEvent();
    preg_match_all("/#([0-9]*)#/", $this->getConfiguration('condition::immediatforceopen'), $matches);
    foreach ($matches[1] as $cmd_id) {
      $listener->addEvent($cmd_id);
    }
    preg_match_all("/#([0-9]*)#/", $this->getConfiguration('condition::immediatforceclose'), $matches);
    foreach ($matches[1] as $cmd_id) {
      $listener->addEvent($cmd_id);
    }
    $listener->save();
  }else{
    $listener = listener::byClassAndFunction('sunshutter', 'reExecuteAction', array('sunshutter_id' => intval($this->getId())));
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
    
    log::add('sunshutter','debug','blabla ' . $handlingCmd->execCmd());
    $handlingCmd->event(true);
  }
}

public function calculPosition(){
  $sun_elevation = $this->getCmd(null, 'sun_elevation')->execCmd();
  $sun_azimuth = $this->getCmd(null, 'sun_azimuth')->execCmd();
  $positions = $this->getConfiguration('positions');
  foreach ($positions as $position) {
    if($sun_elevation > $position['sun::elevation::from'] && $sun_elevation <= $position['sun::elevation::to']){
      if($sun_azimuth > $position['sun::azimuth::from'] && $sun_azimuth <= $position['sun::azimuth::to']){
        return $position['shutter::position'];
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
    log::add('sunshutter','debug',$this->getHumanName().' - Do nothing, handling desactivated');
    return;
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
  if(!$_force && $this->getConfiguration('shutter::nobackhand',0) == 1){
    $lastPositionOrder = $this->getCache('lastPositionOrder',null);
    if($currentPosition !== null  && $lastPositionOrder !== null){
      $amplitude = abs($this->getConfiguration('shutter::closePosition',0)-$this->getConfiguration('shutter::openPosition',100));
      $delta = abs($currentPosition-$lastPositionOrder);
      $ecart = ($delta/$amplitude)*100;
      log::add('sunshutter','debug',$this->getHumanName().' - Ecart depuis le dernier ordre : ' . $ecart);
      if ($ecart>3){
        $this->checkAndUpdateCmd('stateHandling', false);
        log::add('sunshutter','debug',$this->getHumanName().' - Do nothing, position != last order by far 3% and I don\'t have control');
        return;
      }
    }
  }
  $position = null;
  $position = $this->calculPosition();
  if($this->getConfiguration('condition::forceopen') != '' && jeedom::evaluateExpression($this->getConfiguration('condition::forceopen'))){
    log::add('sunshutter','debug',$this->getHumanName().' - Force open');
    $position = $this->getConfiguration('shutter::openPosition',0);
  }
  if($this->getConfiguration('condition::immediatforceopen') != '' && jeedom::evaluateExpression($this->getConfiguration('condition::immediatforceopen'))){
    log::add('sunshutter','debug',$this->getHumanName().' - Force open immediate');
    $position = $this->getConfiguration('shutter::openPosition',0);
  }
  if($this->getConfiguration('condition::forceclose') != '' && jeedom::evaluateExpression($this->getConfiguration('condition::forceclose'))){
    log::add('sunshutter','debug',$this->getHumanName().' - Force close');
    $position = $this->getConfiguration('shutter::closePosition',0);
  }
  if($this->getConfiguration('condition::immediatforceclose') != '' && jeedom::evaluateExpression($this->getConfiguration('condition::immediatforceclose'))){
    log::add('sunshutter','debug',$this->getHumanName().' - Force close immediate');
    $position = $this->getConfiguration('shutter::closePosition',0);
  }
  log::add('sunshutter','debug',$this->getHumanName().' - Calcul position '.$position);
  if(($position !== null && $currentPosition !== null)){
    $amplitude = abs($this->getConfiguration('shutter::closePosition',0)-$this->getConfiguration('shutter::openPosition',100));
    $delta = abs($position-$currentPosition);
    $ecart = ($delta/$amplitude)*100;
    log::add('sunshutter','debug',$this->getHumanName().' - Ecart avec la cible : ' . $ecart);
    if ($ecart<3){
      log::add('sunshutter','debug',$this->getHumanName().' - Do nothing, position != new position by less than 3%');
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
    }
    if($this->getLogicalId() == 'resumeHandling'){
      $sunshutter->checkAndUpdateCmd('stateHandling', true);
      $sunshutter->executeAction(true);
    }
  }
  
  /*     * **********************Getteur Setteur*************************** */
}
