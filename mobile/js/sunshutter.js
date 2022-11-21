/* This file is part of Plugin openzwave for jeedom.
*
* Plugin openzwave for jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Plugin openzwave for jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Plugin openzwave for jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

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

function initSunshutterSunshutter() {
	if(typeof setBackgroundImage == 'function'){
	  setBackgroundImage('plugins/sunshutter/core/img/panel.jpg');
	}
	$.showLoading();
	$.ajax({
	  type: 'POST',
	  url: 'plugins/sunshutter/core/ajax/sunshutter.ajax.php',
	  data: {
		action: 'getsunshutter',
		version: 'mview'
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
		$('#div_displayEquipementsunshutter').empty();
		for (var i in data.result.eqLogics) {
		  $('#div_displayEquipementsunshutter').append(data.result.eqLogics[i]).trigger('create');
		}
		jeedomUtils.setTileSize('.eqLogic');
		$('.eqLogic-widget').addClass('displayObjectName');
		$('#div_displayEquipementsunshutter').packery({gutter : 0});
		$.hideLoading();
	  }
	});

	getSunshutterState();
	setInterval(getSunshutterState, 5000);
	
	$(window).on("resize", function (event) {
	  jeedomUtils.setTileSize('.eqLogic');
	  $('#div_displayEquipementsunshutter').packery({gutter : 0});
	});
  }

  function getSunshutterState(){
	$.ajax({
		type: "POST",
		url: "plugins/sunshutter/core/ajax/sunshutter.ajax.php",
		data: {
			action: "getSummary",
			type: "dashboard",
		},
		dataType: 'json',
		global : false,
		error: function (request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function (data) {
			if (data.state != 'ok') {
				$('#div_inclusionAlert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$(".posMoy").value(data.result['moyPos']+'%');
			$(".manualSuspend").value(data.result['manual']);
			$(".autoSuspend").value(data.result['auto']);
		}
	});
}
