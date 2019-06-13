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

function initSunshutterSunshutter() {
	 getSunshutterState()
}

function getSunshutterState(){
	$.ajax({
        type: "POST",
        url: "plugins/sunshutter/core/ajax/sunshutter.ajax.php",
        data: {
            action: "getPanel",
            type: "mobile",
        },
        dataType: 'json',
		global : false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
		},
        success: function (data) { // si l'appel a bien fonctionné
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
			handling ='';
			if (shutter['HandlingLabel'] == 'Auto'){
				handling = '<i class="fas fa-magic"></i>';
			} else if (shutter['HandlingLabel'] == 'Manuel'){
				handling = '<i class="fas fa-user"></i>';
			}
			table += '<tr><td>' +  shutter['name'] +' <br/> '+ shutter['position'] +'% <br/>' + handling +'</td>';
			if (shutter['handling'] == '0'){
				table += '<td>' + '<a class="bt_sunshutterAction ui-btn ui-mini ui-btn-inline ui-btn-raised clr-primary" data-cmd="'+shutter['resumeId']+'"><i class="fas fa-play"></i></a>';
			} else {
				table += '<td>' + '<a class="bt_sunshutterAction ui-btn ui-mini ui-btn-inline ui-btn-raised clr-primary" data-cmd="'+shutter['pauseId']+'"><i class="fas fa-pause"></i></a>';
			}
			table += ' <a class="bt_sunshutterAction ui-btn ui-mini ui-btn-inline ui-btn-raised clr-primary" data-cmd="'+shutter['executeId']+'"><i class="fas fa-crosshairs"></i></a>';
			table += ' <a class="bt_positionshutterAction ui-btn ui-mini ui-btn-inline ui-btn-raised clr-primary" data-value="'+shutter['openvalue']+'" data-cmd="'+shutter['positionId']+'"><i class="fas fa-arrow-up"></i></a>';
			table += ' <a class="bt_positionshutterAction ui-btn ui-mini ui-btn-inline ui-btn-raised clr-primary" data-value="'+shutter['closevalue']+'" data-cmd="'+shutter['positionId']+'"><i class="fas fa-arrow-down"></i></a></td>';
			table += '<td>' + shutter['cmdhtml'] + '</td>';
			table += '</tr>';
		}
		$("#table_sunshutter tbody").empty().append(table);
		$("#table_sunshutter tbody").trigger('create');
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

setInterval(function() {

getSunshutterState();

}, 5000); 
