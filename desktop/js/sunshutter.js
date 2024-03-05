
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

$('#bt_healthsunshutter').on('click', function() {
  $('#md_modal').dialog({title: "{{Santé Gestion Volet}}"})
  $('#md_modal').load('index.php?v=d&plugin=sunshutter&modal=health').dialog('open')
})

$('.eqLogicAttr[data-l1key=configuration][data-l2key=useJeedomLocalisation]').off('change').on('change', function() {
  if ($(this).value() == 1) {
    $('.customLocalisation').hide()
  }
  else {
    $('.customLocalisation').show()
  }
})

$('.eqLogicAttr[data-l1key=configuration][data-l2key="cron::executeAction"]').on('change', function() {
  if ($(this).value() == 'custom') {
    $('.customcron').show()
  }
  else {
    $('.customcron').hide()
  }
})

$('.eqLogicAttr[data-l1key=configuration][data-l2key="shutter::defaultAction"]').on('change', function() {
  if ($(this).value() == 'custom') {
    $('.customPosition').show()
  }
  else {
    $('.customPosition').hide()
  }
})

$('.eqLogicAttr[data-l1key=configuration][data-l2key="shutter::nobackhand"]').on('change', function() {
  if ($(this).value() == '2') {
    $('.customDelay').show()
  }
  else {
    $('.customDelay').hide()
  }
})

$('#bt_addPosition').off('click').on('click', function() {
  addPosition({})
})

$('#bt_addConditions').off('click').on('click', function() {
  addConditions({})
})

$('#table_sunShutterPosition').off('click','.bt_removePosition').on('click','.bt_removePosition', function() {
  $(this).closest('tr').remove()
})

$('#table_sunShutterConditions').off('click','.bt_removeCondition').on('click','.bt_removeCondition', function() {
  $(this).closest('tr').remove()
})

$(".eqLogic").on('click',".listCmdInfo", function() {
  var el = $(this).closest('.form-group').find('.eqLogicAttr')
  jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function(result) {
    if (el.attr('data-concat') == 1) {
      el.atCaret('insert', result.human)
    }
    else {
      el.value(result.human)
    }
  })
})

$(".eqLogic").on('click',".listCmdInfoPos",  function () {
  var el = $(this).closest('.input-group').find('.positionAttr')
  jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function(result) {
    if (el.attr('data-concat') == 1) {
      el.atCaret('insert', result.human)
    }
    else {
      el.value(result.human)
    }
  })
})

$(".eqLogic").on('click',".listCmdInfoConditions",  function () {
  var el = $(this).closest('.input-group').find('.conditionsAttr')
  jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function(result) {
    if (el.attr('data-concat') == 1) {
      el.atCaret('insert', result.human)
    }
    else {
      el.value(result.human)
    }
  })
})

$(".eqLogic").on('click',".listCmdAction", function () {
  var el = $(this).closest('.form-group').find('.eqLogicAttr')
  jeedom.cmd.getSelectModal({cmd: {type: 'action'}}, function(result) {
    el.value(result.human)
  })
})

function addPosition(_position) {
  if (!_position['sun::elevation::from']) {
    _position['sun::elevation::from'] = 0
  }
  if (!_position['sun::elevation::to']) {
    _position['sun::elevation::to'] = 90
  }
  var tr = '<tr class="position">'
  tr += '<td>'
  tr += '<div class="input-group">'
  tr += '<textarea class="positionAttr form-control roundedLeft" data-concat="1" data-l1key="position::allowmove" style="height:72px;"></textarea>'
  tr += '<span class="input-group-addon roundedRight"><a class="btn btn-default listCmdInfoPos"><i class="fas fa-list-alt"></i></a></span>'
  tr += '</div></td>'
  tr += '<td>'
  tr += '<div class="input-group">'
  tr += '<span class="input-group-addon roundedLeft">{{de}}</span>'
  tr += '<input class="form-control positionAttr" data-l1key="sun::azimuth::from">'
  tr += '<span class="input-group-addon">{{à}}</span>'
  tr+= '<input class="form-control positionAttr roundedRight" data-l1key="sun::azimuth::to">'
  tr += '</div></td>'
  tr += '<td>'
  tr += '<div class="input-group">'
  tr += '<span class="input-group-addon roundedLeft">{{de}}</span>'
  tr += '<input class="form-control positionAttr" data-l1key="sun::elevation::from">'
  tr += '<span class="input-group-addon">{{à}}</span>'
  tr += '<input class="form-control positionAttr roundedRight" data-l1key="sun::elevation::to">'
  tr += '</div></td>'
  tr += '<td>'
  tr += '<div class="input-group"><input type="number" class="form-control positionAttr roundedLeft" data-l1key="shutter::position"><span class="input-group-addon roundedRight">%</span></div>'
  tr += '</td>'
  tr += '<td>'
  tr += '<input class="form-control positionAttr" data-l1key="position::label">'
  tr += '</td>'
  tr += '<td>'
  tr += '<div class="input-group">'
  tr += '<textarea class="positionAttr form-control roundedLeft" data-concat="1" data-l1key="position::comment" style="height:72px;"></textarea>'
  tr += '<span class="input-group-addon roundedRight cursor bt_removePosition" title="{{Supprimer la condition}}"><a class="btn btn-default"><i class="fas fa-minus-circle"></i></a></span>'
  tr += '</div></td>'
  tr += '</tr>'
  $('#table_sunShutterPosition').find('tbody').append(tr)
  $('#table_sunShutterPosition').find('tbody tr').last().setValues(_position, '.positionAttr')
}

function addConditions(_condition) {
  var tr = '<tr class="conditions">'
  tr += '<td>'
  tr += '<div class="input-group">'
  tr += '<textarea class="conditionsAttr form-control roundedLeft" data-concat="1" data-l1key="conditions::condition" style="height:72px;"></textarea>'
  tr += '<span class="input-group-addon roundedRight"><a class="btn btn-default listCmdInfoConditions"><i class="fas fa-list-alt"></i></a></span>'
  tr += '</div></td>'
  tr += '<td>'
  tr += '<input type="text" class="form-control conditionsAttr" data-l1key="conditions::mode">'
  tr += '</td>'
  tr += '<td>'
  tr += '<input type="checkbox" class="conditionsAttr" data-l1key="conditions::immediate">'
  tr += '</td>'
  tr += '<td>'
  tr += '<input type="checkbox" class="conditionsAttr" data-l1key="conditions::forced">'
  tr += '</td>'
  tr += '<td>'
  tr += '<input type="checkbox" class="conditionsAttr" data-l1key="conditions::suspend">'
  tr += '</td>'
  tr += '<td>'
  tr += '<div class="input-group"><input type="number" class="form-control conditionsAttr roundedLeft" data-l1key="conditions::position"><span class="input-group-addon roundedRight">%</span></div>'
  tr += '</td>'
  tr += '<td>'
  tr += '<input type="text" class="form-control conditionsAttr" data-l1key="conditions::label">'
  tr += '</td>'
  tr += '<td>'
  tr += '<div class="input-group">'
  tr += '<textarea class="conditionsAttr form-control roundedLeft" data-concat="1" data-l1key="position::comment" style="height:72px;"></textarea>'
  tr += '<span class="input-group-addon roundedRight cursor bt_removeCondition" title="{{Supprimer la règle}}"><a class="btn btn-default"><i class="fas fa-minus-circle"></i></a></span>'
  tr += '</div></td>'
  tr += '</tr>'
  $('#table_sunShutterConditions').find('tbody').append(tr)
  $('#table_sunShutterConditions').find('tbody tr').last().setValues(_condition, '.conditionsAttr')
}

function saveEqLogic(_eqLogic) {
  if (!isset(_eqLogic.configuration)) {
    _eqLogic.configuration = {}
  }
  _eqLogic.configuration.positions = $('#table_sunShutterPosition').find('tbody tr').getValues('.positionAttr')
  _eqLogic.configuration.conditions = $('#table_sunShutterConditions').find('tbody tr').getValues('.conditionsAttr')
  return _eqLogic
}

function printEqLogic(_eqLogic) {
  $('#table_sunShutterPosition').find('tbody').empty()
  if (isset(_eqLogic.configuration)) {
    if (isset(_eqLogic.configuration.positions)) {
      for (var i in _eqLogic.configuration.positions) {
        addPosition(_eqLogic.configuration.positions[i])
      }
    }
  }
  $('#table_sunShutterConditions').find('tbody').empty()
  if (isset(_eqLogic.configuration)) {
    if (isset(_eqLogic.configuration.conditions)) {
      for (var i in _eqLogic.configuration.conditions) {
        addConditions(_eqLogic.configuration.conditions[i])
      }
    }
  }
  if ($('a[href="#scheduletab"]').length > 0) {
    printScheduling(_eqLogic)
  }
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
      handleAjaxError(request, status, error)
    },
    success: function (data) {
      if (data.state != 'ok') {
        $('#div_alert').showAlert({message: data.result, level: 'danger'})
        return
      }
      $('#div_schedule').empty()
      console.log(data)
      if (data.result.length == 0) {
        $('#div_schedule').append('<div class="text-center" style="font-size:1.2em;font-weight:bold;">{{Aucune programmation trouvée, accéder au}} <a href="index.php?v=d&m=calendar&p=calendar">{{plugin Agenda}}</a> {{pour programmer ce volet}}.</div>')
      }
      else {
        var html = '<legend><i class="fas fa-external-link-alt"></i> {{Programmations du plugin Agenda liées au volet}}</legend>'
        for (var i in data.result) {
          var color = init(data.result[i].cmd_param.color, '#2980b9')
          if (data.result[i].cmd_param.transparent == 1) {
            color = 'transparent'
          }
          html += '<span class="label label-info cursor" style="font-size:1.2em;background-color : ' + color + 'color : ' + init(data.result[i].cmd_param.text_color, 'black') + '">'
          html += '<a href="index.php?v=d&m=calendar&p=calendar&id='+data.result[i].eqLogic_id+'&event_id='+data.result[i].id+'" style="color : ' + init(data.result[i].cmd_param.text_color, 'black') + '">'
          if (data.result[i].cmd_param.eventName != '') {
            html += data.result[i].cmd_param.icon + ' ' + data.result[i].cmd_param.eventName
          }
          else {
            html += data.result[i].cmd_param.icon + ' ' + data.result[i].cmd_param.name
          }
          html += '</a></span>'
          html += ' ' + data.result[i].startDate.substr(11,5) + ' {{à}} ' + data.result[i].endDate.substr(11,5)+'<br\><br\>'
        }
        $('#div_schedule').empty().append(html)
      }
    }
  })
}

$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true})
$("#table_sunShutterPosition").sortable({axis: "y", cursor: "move", items: ".position", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true})
$("#table_sunShutterConditions").sortable({axis: "y", cursor: "move", items: ".conditions", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true})

function addCmdToTable(_cmd) {
  if (!isset(_cmd)) {
    var _cmd = {configuration: {}}
  }
  if (!isset(_cmd.configuration)) {
    _cmd.configuration = {}
  }
  if (!_cmd.logicalId) {
    _cmd.logicalId = 'mode'
    _cmd.type = 'action'
    _cmd.subType = 'other'
  }
  var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">'
  tr += '<td>'
  tr += '<span class="cmdAttr" data-l1key="id"></span>'
  tr += '</td>'
  tr += '<td>'
  tr += '<span class="cmdAttr" data-l1key="logicalId" style="display:none;"></span>'
  tr += '<div class="input-group">'
  tr += '<input class="cmdAttr form-control input-sm roundedLeft" data-l1key="name" placeholder="{{Nom}}">'
  tr += '<span class="input-group-btn"><a class="cmdAction btn btn-sm btn-default" data-l1key="chooseIcon" title="{{Choisir une icône}}"><i class="fas fa-icons"></i></a></span>'
  tr += '<span class="cmdAttr input-group-addon roundedRight" data-l1key="display" data-l2key="icon" style="font-size:19px;padding:0 5px 0 0!important;"></span>'
  tr += '</div>'
  tr += '</td>'
  tr += '<td></td>'
  tr += '<td>'
  tr += '<span class="type" type="' + init(_cmd.type) + '" style="display:none;">' + jeedom.cmd.availableType() + '</span>'
  tr += '<span class="subType" subType="' + init(_cmd.subType) + '" style="display:none;"></span>'
  tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked>{{Afficher}}</label></span> '
  tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked>{{Historiser}}</label></span> '
  tr += '</td>'
  tr += '<td>'
  tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>'
  tr += '</td>'
  tr += '<td>'
  if (is_numeric(_cmd.id)) {
    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> '
    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>'
  }
  tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>'
  tr += '</td>'
  tr += '</tr>'
  $('#table_cmd tbody').append(tr)
  $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr')
  if (isset(_cmd.type)) {
    $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type))
  }
  jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType))
}
