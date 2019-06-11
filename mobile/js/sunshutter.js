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
            action: "getMobilePanel",
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
		var table = '';
		for (sunshutter in data.result) {
			table += '<tr><td>' +  data.result[sunshutter]['name'] +' <br/> Dernier : '+ data.result[sunshutter]['position'] +'%</td>';
			if (data.result[sunshutter]['handling'] == '0'){
				table += '<td>' + '<a class="bt_sunshutterAction ui-btn ui-mini ui-btn-inline ui-btn-raised clr-primary" data-cmd="'+data.result[sunshutter]['resumeId']+'"><i class="fas fa-play"></i></a>';
			} else {
				table += '<td>' + '<a class="bt_sunshutterAction ui-btn ui-mini ui-btn-inline ui-btn-raised clr-primary" data-cmd="'+data.result[sunshutter]['pauseId']+'"><i class="fas fa-pause"></i></a>';
			}
			table += '<br/><br/><a class="bt_sunshutterAction ui-btn ui-mini ui-btn-inline ui-btn-raised clr-primary" data-cmd="'+data.result[sunshutter]['executeId']+'"><i class="fas fa-magic"></i></a>' +'</td>';
			table += '<td>' + data.result[sunshutter]['cmdhtml'];
			table += '<br/><center><a class="bt_positionshutterAction ui-btn ui-mini ui-btn-inline ui-btn-raised clr-primary" data-value="'+data.result[sunshutter]['openvalue']+'" data-cmd="'+data.result[sunshutter]['positionId']+'"><i class="fas fa-arrow-up"></i></a>';
			table += '<a class="bt_positionshutterAction ui-btn ui-mini ui-btn-inline ui-btn-raised clr-primary" data-value="'+data.result[sunshutter]['closevalue']+'" data-cmd="'+data.result[sunshutter]['positionId']+'"><i class="fas fa-arrow-down"></i></a></center></td>';
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
