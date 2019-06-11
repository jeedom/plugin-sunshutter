
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

$('#bt_addPosition').off('click').on('click',function(){
  addPosition({});
});

function addPosition(_position){
  if(!_position['sun::elevation::from']){
    _position['sun::elevation::from'] = 0;
  }
  if(!_position['sun::elevation::to']){
    _position['sun::elevation::to'] = 90;
  }
  var tr = '<tr>';
  tr += '<td>';
  tr += '<input class="form-control positionAttr" data-l1key="sun::azimuth::from" style="width:calc( 50% - 10px);display:inline-block;" /> {{à}} <input class="form-control positionAttr" data-l1key="sun::azimuth::to"  style="width:calc( 50% - 10px);display:inline-block;"/>';
  tr += '</td>';
  tr += '<td>';
  tr += '<input class="form-control positionAttr" data-l1key="sun::elevation::from" style="width:calc( 50% - 10px);display:inline-block;" /> {{à}} <input class="form-control positionAttr" data-l1key="sun::elevation::to" style="width:calc( 50% - 10px);display:inline-block;"/>';
  tr += '</td>';
  tr += '<td>';
  tr += '<input class="form-control positionAttr" data-l1key="shutter::position" style="width:calc( 100% - 20px);display:inline-block;" /> %';
  tr += '</td>';
  tr += '<td>';
  tr += '<i class="fas fa-minus-circle cursor bt_removePosition"></i>';
  tr += '</td>';
  tr += '</tr>';
  $('#table_sunShutterPosition').find('tbody').append(tr);
  $('#table_sunShutterPosition').find('tbody tr').last().setValues(_position, '.positionAttr');
}

$('#table_sunShutterPosition').off('click','.bt_removePosition').on('click','.bt_removePosition',function(){
  $(this).closest('tr').remove();
});

$(".eqLogic").on('click',".listCmdInfo",  function () {
  var el = $(this).closest('.form-group').find('.eqLogicAttr');
  jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function (result) {
    if (el.attr('data-concat') == 1) {
      el.atCaret('insert', result.human);
    } else {
      el.value(result.human);
    }
  });
});

$("body").on('click',".listCmdAction", function () {
  var el = $(this).closest('.form-group').find('.eqLogicAttr');
  jeedom.cmd.getSelectModal({cmd: {type: 'action'}}, function (result) {
    el.value(result.human);
  });
});

function saveEqLogic(_eqLogic) {
  if (!isset(_eqLogic.configuration)) {
    _eqLogic.configuration = {};
  }
  _eqLogic.configuration.positions = $('#table_sunShutterPosition').find('tbody tr').getValues('.positionAttr');
  return _eqLogic;
}

function printEqLogic(_eqLogic) {
  $('#table_sunShutterPosition').find('tbody').empty();
  if (isset(_eqLogic.configuration)) {
    if (isset(_eqLogic.configuration.positions)) {
      for (var i in _eqLogic.configuration.positions) {
        addPosition(_eqLogic.configuration.positions[i]);
      }
    }
  }
  printScheduling(_eqLogic);
}

function printScheduling(_eqLogic){
  $.ajax({
    type: 'POST',
    url: 'plugins/sunshutter/core/ajax/sunshutter.ajax.php',
    data: {
      action: 'getLinkCalendar',
      id: _eqLogic.id,
    },
    dataType: 'json',
    error: function (request, status, error) {
      handleAjaxError(request, status, error);
    },
    success: function (data) {
      if (data.state != 'ok') {
        $('#div_alert').showAlert({message: data.result, level: 'danger'});
        return;
      }
      $('#div_schedule').empty();
      if(data.result.length == 0){
        $('#div_schedule').append("<center><span style='color:#767676;font-size:1.2em;font-weight: bold;'>{{Vous n'avez encore aucune programmation. Veuillez cliquer <a href='index.php?v=d&m=calendar&p=calendar'>ici</a> pour programmer votre thermostat à l'aide du plugin agenda}}</span></center>");
      }else{
        var html = '<legend>{{Liste des programmations du plugin Agenda liées au Volet}}</legend>';
        for (var i in data.result) {
          var color = init(data.result[i].cmd_param.color, '#2980b9');
          if(data.result[i].cmd_param.transparent == 1){
            color = 'transparent';
          }
          html += '<span class="label label-info cursor" style="font-size:1.2em;background-color : ' + color + ';color : ' + init(data.result[i].cmd_param.text_color, 'black') + '">';
          html += '<a href="index.php?v=d&m=calendar&p=calendar&id='+data.result[i].eqLogic_id+'&event_id='+data.result[i].id+'" style="color : ' + init(data.result[i].cmd_param.text_color, 'black') + '">'
          
          if (data.result[i].cmd_param.eventName != '') {
            html += data.result[i].cmd_param.icon + ' ' + data.result[i].cmd_param.eventName;
          } else {
            html += data.result[i].cmd_param.icon + ' ' + data.result[i].cmd_param.name;
          }
          html += '</a></span><br\><br\>';
        }
        $('#div_schedule').empty().append(html);
      }
    }
  });
  
}


$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
/*
* Fonction pour l'ajout de commande, appellé automatiquement par plugin.template
*/
function addCmdToTable(_cmd) {
  if (!isset(_cmd)) {
    var _cmd = {configuration: {}};
  }
  if (!isset(_cmd.configuration)) {
    _cmd.configuration = {};
  }
  var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
  tr += '<td>';
  tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
  tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
  tr += '</td>';
  tr += '<td>';
  tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
  tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
  tr += '</td>';
  tr += '<td>';
  if (is_numeric(_cmd.id)) {
    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
  }
  tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
  tr += '</td>';
  tr += '</tr>';
  $('#table_cmd tbody').append(tr);
  $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
  if (isset(_cmd.type)) {
    $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
  }
  jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}
