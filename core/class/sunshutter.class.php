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
  
  /*     * *********************Méthodes d'instance************************* */
  
  public function postSave() {
    $cmd = $this->getCmd(null, 'sun_angle');
    if (!is_object($cmd)) {
      $cmd = new sunshutterCmd();
      $cmd->setLogicalId('sun_angle');
      $cmd->setName(__('Angle soleil', __FILE__));
    }
    $cmd->setType('info');
    $cmd->setSubType('numeric');
    $cmd->setEqLogic_id($this->getId());
    $cmd->save();
    
    
    $cmd = $this->getCmd(null, 'executeAction');
    if (!is_object($cmd)) {
      $cmd = new sunshutterCmd();
      $cmd->setLogicalId('executeAction');
      $cmd->setName(__('Executer action', __FILE__));
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
  }
  
  public function updateData(){
    $SD = new SolarData\SolarData();
    $SD->setObserverPosition($this->getConfiguration('lat'),$this->getConfiguration('long'),$this->getConfiguration('alt'));
    $SD->setObserverDate(date('Y'), date('n'), date('j'));
    $SD->setObserverTime(date('G'), date('i'),date('s'));
    $SD->setDeltaTime(67);
    $SD->setObserverTimezone(date('Z') / 3600);
    
    $this->checkAndUpdateCmd('sun_angle', $SD->getSurfaceIncidenceAngle($this->getConfiguration('w'),$this->getConfiguration('y'))) || $changed;
  }
  
  public function executeAction($_force = false){
    log::add('sunshutter','debug','Start executeAction');
    $this->updateData();
    if(!$_force && $this->getConfiguration('condition::allowmove') != '' && evaluate($this->getConfiguration('condition::allowmove')) == false){
      log::add('sunshutter','debug','Do nothing, false condition');
      return;
    }
    $currentPosition = null;
    $cmd = cmd::byId(str_replace('#','',$this->getConfiguration('shutter::state')));
    if(is_object($cmd)){
      $currentPosition = $cmd->execCmd();
    }
    if(!$_force && $this->getConfiguration('shutter::nobackhand',0) == 1){
      $lastPositionOrder = $this->getCache('lastPositionOrder',null);
      if($currentPosition !== null  && $lastPositionOrder !== null && $lastPositionOrder != $currentPosition){
        log::add('sunshutter','debug','Do nothing, position != last order and I don\'t have controle');
        return;
      }
    }
    $position = null;
    $sun_angle = $this->getCmd(null, 'sun_angle')->execCmd();
    log::add('sunshutter','debug','Sun angle '.$sun_angle);
    if($sun_angle > $this->getConfiguration('angle:close::from') && $sun_angle < $this->getConfiguration('angle:close::to')){
      $position = $this->getConfiguration('shutter::closePosition',100);
    }else{
      $position = $this->getConfiguration('shutter::openPosition',0);
    }
    if($this->getConfiguration('condition::forceopen') != '' && evaluate($this->getConfiguration('condition::forceopen'))){
      log::add('sunshutter','debug','Force open');
      $position = $this->getConfiguration('shutter::openPosition',0);
    }
    if($this->getConfiguration('condition::forceclose') != '' && evaluate($this->getConfiguration('condition::forceclose'))){
      log::add('sunshutter','debug','Force close');
      $position = $this->getConfiguration('shutter::closePosition',0);
    }
    log::add('sunshutter','debug','Calcul position '.$position);
    if($position !== null && ($currentPosition === null || $position != $currentPosition || $_force)){
      log::add('sunshutter','debug','Do action');
      $cmd = cmd::byId(str_replace('#','',$this->getConfiguration('shutter::position')));
      if(is_object($cmd)){
        $cmd->execCmd(array('slider' => $position));
      }
      $this->setCache('lastPositionOrder',$position);
    }
  }
  
  /*     * **********************Getteur Setteur*************************** */
}

class sunshutterCmd extends cmd {
  /*     * *************************Attributs****************************** */
  
  
  /*     * ***********************Methode static*************************** */
  
  
  /*     * *********************Methode d'instance************************* */
  
  
  public function execute($_options = array()) {
    if($this->getLogicalId() == 'refresh'){
      $this->getEqLogic()->updateData();
    }
    
    if($this->getLogicalId() == 'executeAction'){
      $this->getEqLogic()->executeAction(true);
    }
  }
  
  /*     * **********************Getteur Setteur*************************** */
}
