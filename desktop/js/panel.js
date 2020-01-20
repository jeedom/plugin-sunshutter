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
function getSunshutterState(){
	$.ajax({
		type: "POST",
		url: "plugins/sunshutter/core/ajax/sunshutter.ajax.php",
		data: {
			action: "getPanel",
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
			$(".posMoy").value(data.result.global['moyPos']+'%');
			$(".manualSuspend").value(data.result.global['manual']);
			$(".autoSuspend").value(data.result.global['auto']);
			var table = '';
			for (sunshutter in data.result.shutters) {
				var shutter = data.result.shutters[sunshutter];
				handling ='Aucune';
				if (shutter['HandlingLabel'] == 'Auto'){
					handling = '<i class="fas fa-magic"></i> '+shutter['suspendTime'];
				} else if (shutter['HandlingLabel'] == 'Manuel'){
					handling = '<i class="fas fa-user"></i> '+shutter['suspendTime'];
				}
				table += '<tr><td><a href="' + shutter['link'] + '">' +  shutter['name']+'</a></td>';
				table += '<td><center><span class="label label-primary">'+ shutter['azimuth'] + '° / ' + shutter['elevation'] + '°</span></center></td>';
				table += '<td><center><span class="label label-primary">'+ shutter['mode'] + '</span></center></td>';
				table += '<td><center><span class="label label-primary">'+ shutter['position'] + '%</span></center></td>';
				table += '<td><center><span class="label label-primary">'+ handling + '</span></center></td>';
				table += '<td><center>' + '<a class="bt_sunshutterAction btn btn-default" data-cmd="'+shutter['resumeId']+'"><i class="fas fa-play"></i></a>';
				table += ' <a class="bt_sunshutterAction btn btn-default" data-cmd="'+shutter['pauseId']+'"><i class="fas fa-pause"></i></a>';
				table += ' <a class="bt_positionshutterAction btn btn-default" data-value="'+shutter['openvalue']+'" data-cmd="'+shutter['positionId']+'"><i class="fas fa-arrow-up"></i></a>';
				table += ' <a class="bt_positionshutterAction btn btn-default" data-value="'+shutter['closevalue']+'" data-cmd="'+shutter['positionId']+'"><i class="fas fa-arrow-down"></i></a>';
				table += ' <a class="bt_sunshutterAction btn btn-default" data-cmd="'+shutter['executeId']+'"><i class="fas fa-crosshairs"></i></a>';
				if (shutter['refreshId'] != 0){
					table += ' <a class="bt_sunshutterAction btn btn-default" data-cmd="'+shutter['refreshId']+'"><i class="fas fa-sync"></i></a>';
				}
				if(shutter['cmdmode'] != ''){
					table += '<br/>'+shutter['cmdmode']
				}
				table += '</center></td>';
				table += '<td><center>' + shutter['cmdhtml'] + '</center></td>';
				table += '</tr>';
			}
			$("#table_sunshutter tbody").empty().append(table);
		}
	});
}

$('#table_sunshutter tbody').on('click','.bt_sunshutterAction',function(){
	jeedom.cmd.execute({id: $(this).data('cmd')});
	getSunshutterState();
})

$('#table_sunshutter tbody').on('click','.bt_positionshutterAction',function(){
	jeedom.cmd.execute({id: $(this).data('cmd'), value: {slider: $(this).data('value')}});
	getSunshutterState();
})


getSunshutterState();
setInterval(function() {
	getSunshutterState();
}, 5000);
