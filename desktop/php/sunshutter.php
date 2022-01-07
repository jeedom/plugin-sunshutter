<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('sunshutter');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" data-action="add">
				<i class="fas fa-plus-circle"></i>
				<br>
				<span>{{Ajouter}}</span>
			</div>
			<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
				<i class="fas fa-wrench"></i>
				<br>
				<span>{{Configuration}}</span>
			</div>
			<div class="cursor logoSecondary" id="bt_healthsunshutter">
				<i class="fas fa-medkit"></i>
				<br>
				<span>{{Santé}}</span>
			</div>
		</div>
		<legend><i class="icon jeedom-volet-ferme"></i> {{Mes volets}}</legend>
		<?php
		if (count($eqLogics) == 0) {
			echo '<br><div class="text-center" style="font-size:1.2em;font-weight:bold;">{{Aucun volet n\'est paramétré, cliquer sur "Ajouter" pour commencer}}</div>';
		}
		else {
			echo '<div class="input-group" style="margin:5px;">';
			echo '<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic">';
			echo '<div class="input-group-btn">';
			echo '<a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i></a>';
			echo '<a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>';
			echo '</div>';
			echo '</div>';
			echo '<div class="eqLogicThumbnailContainer">';
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $plugin->getPathImgIcon() . '">';
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '<span class="hiddenAsCard displayTableRight hidden">';
				$cron = ($eqLogic->getConfiguration('cron::executeAction','') == 'custom') ? $eqLogic->getConfiguration('cron::custom',''):$eqLogic->getConfiguration('cron::executeAction','');
				echo '<span class="label label-info hidden-xs">' . $cron . '</span>';
				echo ($eqLogic->getIsVisible() == 1) ? ' <i class="fas fa-eye" title="{{Visible}}"></i>' : ' <i class="fas fa-eye-slash" title="{{Non visible}}"></i>';
				echo '</span>';
				echo '</div>';
			}
			echo '</div>';
		}
		?>
	</div>

	<div class="col-xs-12 eqLogic" style="display:none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
				</a><a class="btn btn-sm btn-default eqLogicAction" data-action="copy"><i class="fas fa-copy"></i><span class="hidden-xs">  {{Dupliquer}}</span>
				</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
				</a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
				</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
			<li role="presentation"><a href="#conditiontab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-tasks"></i><span class="hidden-xs">  {{Exceptions}}</span></a></li>
			<li role="presentation"><a href="#positiontab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-globe"></i><span class="hidden-xs">  {{Positionnement}}</span></a></li>
			<?php try {
				if (is_object(plugin::byId('calendar'))) { ?>
				<li role="presentation"><a href="#scheduletab" aria-controls="profile" role="tab" data-toggle="tab"><i class="far fa-clock"></i><span class="hidden-xs"> {{Programmation}}</span></a></li>
			<?php }}catch(Exception $e){} ?>
			<li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Commandes}}</a></li>
		</ul>

		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<form class="form-horizontal">
					<fieldset>
						<div class="col-lg-6">
							<legend><i class="fas fa-wrench"></i> {{Paramètres généraux}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Nom du volet}}</label>
								<div class="col-sm-6">
									<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display:none;" >
									<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom du volet}}">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label" >{{Objet parent}}</label>
								<div class="col-sm-6">
									<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
										<option value="">{{Aucun}}</option>
										<?php
										$options = '';
										foreach ((jeeObject::buildTree(null, false)) as $object) {
											$options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
										}
										echo $options;
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Catégorie}}</label>
								<div class="col-sm-6">
									<?php
									foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
										echo '<label class="checkbox-inline">';
										echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" >' . $value['name'];
										echo '</label>';
									}
									?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Options}}</label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked>{{Activer}}</label>
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked>{{Visible}}</label>
								</div>
							</div>

							<legend><i class="fas fa-magic"></i> {{Gestion automatique}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Condition pour vérification}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Condition à remplir pour que la gestion automatique s'active (vide par défaut = toujours active)}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<div class="input-group">
										<textarea class="eqLogicAttr form-control roundedLeft autogrow" data-concat="1" data-l1key="configuration" data-l2key="condition::allowmove"></textarea>
										<span class="input-group-addon roundedRight">
											<a class="btn btn-default listCmdInfo"><i class="fas fa-list-alt"></i></a>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Fréquence de vérification}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Indiquer la fréquence de contrôle des exceptions et des conditions de positionnement}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cron::executeAction">
										<option value="*/5 * * * *">{{Toutes les 5 minutes}}</option>
										<option value="*/10 * * * *">{{Toutes les 10 minutes}}</option>
										<option value="*/15 * * * *">{{Toutes les 15 minutes}}</option>
										<option value="*/30 * * * *">{{Toutes les 30 minutes}}</option>
										<option value="*/45 * * * *">{{Toutes les 45 minutes}}</option>
										<option value="custom">{{Cron personnalisé}}</option>
									</select>
								</div>
							</div>
							<div class="form-group customcron" style="display:none;">
								<label class="col-sm-4 control-label">{{Cron personnalisé}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Indiquer la fréquence de vérification voulue par une notation cron}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cron::custom">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Reprendre la main}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Reprendre la gestion automatique après un déplacement manuel du volet}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::nobackhand">
										<option value="0">{{Oui}}</option>
										<option value="1">{{Non}}</option>
										<option value="2">{{Oui, après délai}}</option>
									</select>
								</div>
							</div>
							<div class="form-group customDelay" style="display:none;">
								<label class="col-sm-4 control-label">{{Délai de reprise}} <sub>({{min.}})</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Indiquer le délai en minutes après lequel la gestion automatique peut reprendre}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::customDelay">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Reprendre sur changement de mode}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Cocher la case pour reprendre la gestion automatique en cas de changement de mode}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="condition::allowIgnoreSuspend">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Exceptions immédiates prioritaires}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Cocher la case pour que les exceptions immédiates s'exécutent peu importe les autres conditions, même en cas de suspension}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="condition::systematic">
								</div>
							</div>
						</div>

						<div class="col-lg-6">
							<legend><i class="fas fa-map-marked-alt"></i> {{Coordonnées GPS}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Utiliser la configuration générale}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Cocher la case pour utiliser les coordonnées renseignées dans la configuration générale de Jeedom}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<input type="checkbox" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="useJeedomLocalisation">
								</div>
							</div>
							<br>
							<div class="form-group customLocalisation">
								<label class="col-sm-4 control-label">{{Latitude}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Indiquer la latitude du bâtiment ou du volet}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="lat">
								</div>
							</div>
							<div class="form-group customLocalisation">
								<label class="col-sm-4 control-label">{{Longitude}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Indiquer la longitude du bâtiment ou du volet}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="long">
								</div>
							</div>
							<div class="form-group customLocalisation">
								<label class="col-sm-4 control-label">{{Altitude}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Indiquer l'altitude du bâtiment ou du volet}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="alt">
								</div>
							</div>

							<legend><i class="icon jeedomapp-volet-ouvert"></i> {{Contrôle du volet}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Pourcentages de fermeture/ouverture}} <sub>(%)</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Indiquer le pourcentage minimal de fermeture (généralement 0) et maximal d'ouverture (généralement 99 ou 100)}}"></i></sup>
								</label>
								<div class="col-sm-6 input-group">
									<span class="input-group-addon roundedLeft" title="{{Pourcentage minimal de fermeture (0 par défaut)}}">{{Min.}}</span>
									<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::closePosition" placeholder="0">
									<span class="input-group-addon" title="{{Pourcentage maximal d'ouverture (100 par défaut)}}">{{Max.}}</span>
									<input type="number" class="eqLogicAttr form-control roundedRight" data-l1key="configuration" data-l2key="shutter::openPosition" placeholder="99 ou 100">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Durée d'un déplacement}} <sub>(s.)</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Indiquer le temps maximum en secondes pour effectuer un mouvement complet d'ouverture ou de fermeture}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::moveDuration">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Commande d'état}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Renseigner la commande info/numérique indiquant la position actuelle du volet}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<div class="input-group">
										<input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="shutter::state">
										<span class="input-group-btn">
											<a class="btn btn-default listCmdInfo roundedRight"><i class="fas fa-list-alt"></i></a>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Commande de positionnement}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Renseigner la commande action/curseur permettant de positionner le volet}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<div class="input-group">
										<input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="shutter::position">
										<span class="input-group-btn">
											<a class="btn btn-default listCmdAction roundedRight"><i class="fas fa-list-alt"></i></a>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Commande d'actualisation}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Renseigner la commande action permettant de rafraîchir la position du volet (facultatif)}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<div class="input-group">
										<input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="shutter::refreshPosition">
										<span class="input-group-btn">
											<a class="btn btn-default listCmdAction roundedRight"><i class="fas fa-list-alt"></i></a>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Action par défaut}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Renseigner l'action qui sera effectuée par défaut si aucune exception ni position n'est valide}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::defaultAction">
										<option value="none">{{Ne rien faire}}</option>
										<option value="open">{{Ouvrir}}</option>
										<option value="close">{{Fermer}}</option>
										<option value="custom">{{Position personnalisée}}</option>
									</select>
								</div>
							</div>
							<div class="form-group customPosition" style="display:none;">
								<label class="col-sm-4 control-label">{{Position personnalisée}} <sub>(%)</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Indiquer la position voulue en pourcentage}}"></i></sup>
								</label>
								<div class="col-sm-6">
									<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::customPosition">
								</div>
							</div>
						</div>
					</fieldset>
				</form>
				<hr>
			</div>

			<div role="tabpanel" class="tab-pane" id="conditiontab">
				<br>
				<form class="form-horizontal">
					<fieldset>
						<div class="alert alert-info col-xs-10 col-xs-offset-1">
							{{Cet onglet permet de définir des règles spécifiques à appliquer en exception aux conditions relatives à la position du soleil.}}
							<br>
							{{Les règles d'exception sont vérifiées les unes après les autres, il est possible d'en modifier l'ordre par glisser-déposer.}}
							<br>
							{{La gestion automatique s'arrête à la première règle validée et place le volet à la position indiquée.}}
							<br><br>
							<a class="btn btn-default col-xs-6 col-xs-offset-3" id="bt_addConditions"><i class="fas fa-plus"></i> {{Ajouter une règle}}</a>
						</div>
						<table class="table table-bordered table-condensed" id="table_sunShutterConditions">
							<thead>
								<tr>
									<th style="min-width:250px;width:750px">{{Condition}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Condition à remplir pour exécuter la règle (facultatif)}}"></i></sup>
									</th>
									<th style="min-width:100px;width:200px">{{Mode}}
										<sup><i class="fas fa-question-circle tooltips" title="{{La règle ne sera valide que si le volet est dans le mode spécifié, plusieurs modes peuvent être renseignés séparés par des virgules (facultatif)}}"></i></sup>
									</th>
									<th>{{Exception immédiate}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Cocher la case pour que la règle s'exécute immédiatement dès que la condition est valide}}"></i></sup>
									</th>
									<th>{{Suspendre}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Cocher la case pour suspendre la gestion automatique tant que la règle est valide}}"></i></sup>
									</th>
									<th style="width:100px;">{{Position}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Indiquer la position voulue en pourcentage (vide = aucune action)}}"></i></sup>
									</th>
									<th style="min-width:80px;width:150px">{{Label}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Label associé à la validation de la règle d'exception (facultatif)}}"></i></sup>
									</th>
									<th style="min-width:50px;width:380px">{{Commentaire}}</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</fieldset>
				</form>
			</div>

			<div role="tabpanel" class="tab-pane" id="positiontab">
				<br>
				<form class="form-horizontal">
					<fieldset>
						<div class="alert alert-info col-xs-10 col-xs-offset-1">
							{{Cet onglet permet de définir le cœur même du moteur de gestion automatique et ainsi régler la position du volet en fonction de celle du soleil. Des conditions complémentaires peuvent être ajoutées pour affiner ces critères.}}
							<br>
							{{Les conditions sont vérifiées les unes après les autres, il est possible d'en modifier l'ordre par glisser-déposer.}}
							<br>
							{{Si aucune règle ne vient en exception, la gestion automatique s'arrête à la première condition validée et place le volet à la position indiquée. Si aucune condition n'est valide alors la gestion automatique réalise l'action par défaut.}}
							<br><br>
							<a class="btn btn-default col-xs-6 col-xs-offset-3" id="bt_addPosition"><i class="fas fa-plus"></i> {{Ajouter une condition}}</a>
						</div>
						<table class="table table-bordered table-condensed" id="table_sunShutterPosition">
							<thead>
								<tr>
									<th style="min-width:250px;width:750px">{{Condition}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Condition complémentaire à remplir (facultatif)}}"></i></sup>
									</th>
									<th style="min-width:180px;">{{Azimuth}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Bornes d'azimuth du soleil en degrés}}"></i></sup>
									</th>
									<th style="min-width:180px;">{{Elévation}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Bornes d'élévation du soleil en degrés}}"></i></sup>
									</th>
									<th style="width:100px;">{{Position}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Indiquer la position voulue en pourcentage}}"></i></sup>
									</th>
									<th style="min-width:80px;width:150px">{{Label}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Label associé à la validation de la condition (facultatif)}}"></i></sup>
									</th>
									<th style="min-width:50px;width:380px">{{Commentaire}}</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</fieldset>
				</form>
			</div>

			<div class="tab-pane" id="scheduletab">
				<br>
				<form class="form-horizontal">
					<fieldset>
						<div class="alert alert-info col-xs-10 col-xs-offset-1">
							{{Cet onglet recense les programmations du plugin Agenda agissant sur la gestion automatique de ce volet.}}
							<br>
							{{Exemple : planifier une suspension et une reprise manuelle pendant les heures de sieste d'un enfant.}}
						</div>
						<div class="col-xs-12" id="div_schedule"></div>
					</fieldset>
				</form>
				<hr>
			</div>

			<div role="tabpanel" class="tab-pane" id="commandtab">
				<a class="btn btn-sm btn-primary pull-right cmdAction" data-action="add" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Ajouter un mode}}</a>
				<br><br>
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th style="min-width:50px;width:100px;">{{ID}}</th>
							<th style="min-width:150px;width:250px;">{{Nom}}</th>
							<th></th>
							<th style="min-width:150px;">{{Options}}</th>
							<th style="min-width:100px;width:200px;">{{Action}}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>

		</div>
	</div>
</div>

<?php include_file('desktop', 'sunshutter', 'js', 'sunshutter');?>
<?php include_file('core', 'plugin.template', 'js');?>
