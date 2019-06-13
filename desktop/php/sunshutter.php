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
				<br/>
				<span>{{Santé}}</span>
			</div>
		</div>
		<legend><i class="fas fa-table"></i> {{Mes volets}}</legend>
		<input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
		<div class="eqLogicThumbnailContainer">
			<?php
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '</div>';
			}
			?>
		</div>
	</div>
	
	<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
			<li role="presentation"><a href="#configtab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-wrench"></i> {{Configuration}}</a></li>
			<li role="presentation"><a href="#conditiontab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-vial"></i> {{Condition}}</a></li>
			<li role="presentation"><a href="#positiontab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-drafting-compass"></i> {{Positionnement}}</a></li>
			<li role="presentation"><a href="#scheduletab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-calendar"></i> {{Planning}}</a></li>
			<li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
		</ul>
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<br/>
				<form class="form-horizontal">
					<fieldset>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Nom de l'équipement volet}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement template}}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" >{{Objet parent}}</label>
							<div class="col-sm-3">
								<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
									<option value="">{{Aucun}}</option>
									<?php
									foreach (object::all() as $object) {
										echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Catégorie}}</label>
							<div class="col-sm-9">
								<?php
								foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
									echo '<label class="checkbox-inline">';
									echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
									echo '</label>';
								}
								?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-9">
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i class="fa fa-plus-circle"></i> {{Commandes}}</a><br/><br/>
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>{{Nom}}</th><th>{{Type}}</th><th>{{Options}}</th><th>{{Action}}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div role="tabpanel" class="tab-pane" id="configtab">
			<br/>
				<fieldset>
				<form class="form-horizontal">
				<legend><i class="fas fa-cog"></i> {{Général}}</legend>
				<div class="form-group">
							<label class="col-sm-3 control-label">{{Vérification}}</label>
							<div class="col-sm-2">
								<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cron::executeAction">
									<option value="*/5 * * * *">{{Toutes les 5 minutes}}</option>
									<option value="*/10 * * * *">{{Toutes les 10 minutes}}</option>
									<option value="*/15 * * * *">{{Toutes les 15 minutes}}</option>
									<option value="*/30 * * * *">{{Toutes les 30 minutes}}</option>
									<option value="*/45 * * * *">{{Toutes les 45 minutes}}</option>
									<option value="custom">{{Cron Personnalisé}}</option>
								</select>
							</div>
							<label class="col-sm-1 control-label customcron" style="display : none;">{{Cron Personnalisé}}</label>
							<div class="col-sm-2 customcron" style="display : none;">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cron::custom"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Reprendre la main}}</label>
							<div class="col-sm-2">
								<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::nobackhand">
									<option value="0">{{Oui}}</option>
									<option value="1">{{Non}}</option>
									<option value="2">{{Oui avec délai}}</option>
								</select>
							</div>
							<label class="col-sm-1 control-label customDelay" style="display : none;">{{Au delà de (min)}}</label>
							<div class="col-sm-2 customDelay" style="display : none;">
								<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::customDelay"/>
							</div>
						</div>
						<legend><i class="fas fa-globe"></i> {{Coordonnées}}</legend>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Latitude}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="lat"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Longitude}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="long"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Altitude}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="alt"/>
							</div>
						</div>
						<legend><i class="icon jeedom-volet-ferme"></i> {{Volet}}</legend>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Etat volet}}</label>
							<div class="col-sm-3">
								<div class="input-group">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::state"/>
									<span class="input-group-btn">
										<a class="btn btn-default listCmdInfo roundedRight"><i class="fas fa-list-alt"></i></a>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Position volet}}</label>
							<div class="col-sm-3">
								<div class="input-group">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::position"/>
									<span class="input-group-btn">
										<a class="btn btn-default listCmdAction roundedRight"><i class="fas fa-list-alt"></i></a>
									</span>
								</div>
							</div>
						</div>
					</fieldset>
				</form>
				<br/>
				<div class="alert alert-info">{{Dans cet onglet, vous allez définir la configuration générale de la gestion :<br>
				- Vérification : tous les combiens le plugin vérifiera la position du soleil pour éventuellement changer la position du volet (un cron personnalisé est possible)<br>
				- Reprendre la main : si la position du volet se retrouve dans une position différente de celle voulue (changement manuel ou par exception), le système doit il reprendre la main automatiquement, 
				ne pas la reprendre, ou après un délai (délai après detection de l'écart et mise en suspens).<br>
				- Coordonnées : la latitude, la longitude et l'altitude de votre volet (suncalc.org est très bien pour cela)
				-Volet : ici vous allez choisi la commande état et la commande positionnement de votre volet}}</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="conditiontab">
				<br/>
				<fieldset>
					<form class="form-horizontal">
						<legend><i class="fas fa-cog"></i> {{Général}}</legend>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Condition pour action}}</label>
							<div class="col-sm-9">
								<div class="input-group">
									<textarea class="eqLogicAttr form-control" data-concat="1" data-l1key="configuration" data-l2key="condition::allowmove" style="height:75px"></textarea>
									<span class="input-group-btn">
										<a class="btn btn-default listCmdInfo roundedRight" ><i class="fas fa-list-alt"></i></a>
									</span>
								</div>
							</div>
						</div>
						<legend><i class="icon jeedom-volet-ferme"></i> {{Conditions}}
						<a class="btn btn-default btn-xs pull-right" style="margin-right:15px;" id="bt_addConditions"><i class="fas fa-plus"></i> {{Ajouter}}</a>
						</legend>
						<table class="table table-condensed" id="table_sunShutterConditions">
							<thead>
							<tr>
								<th>{{Position}}</th>
								<th>{{Action Systématique}}</th>
								<th>{{Suspendre}}</th>
								<th>{{Condition}}</th>
							</tr>
							</thead>
						<tbody>
						
						</tbody>
						</table>
					</fieldset>
				</form>
				<br/>
				<div class="alert alert-info">{{Dans cet onglet vous allez définir les conditions pour le moteur de gestion : <br>
				- Condition pour action : Condition nécessaire pour que le moteur fonctionne, si elle est remplie le moteur ne fera rien à part les conditions systématiques. Par défaut le champ est vide ce qui veut dire que le moteur est toujours actif.
				(exemples : fenêtre fermée, température > 25° etc...<br>
				- Conditions : cette partie permet de rajouter des règles d'exception <br>
				       . position : la position désirée<br>
					   . action systématique et immédiate : si coché cette action sera systématique si la condition est remplie et immédiatement exécutée. Sinon elle est executée que si la condition globale est vérifiée et au moment de la vérification<br>
					   . suspendre : valable que pour les conditions systematique (si cochée elle suspendra la gestion systematiquement et dans le cas d'une reprise sur délai repoussera le délai à chaque fois qu'elle est triggée)<br>
					   . condition : la condition en tant que tel (exemple : si j'ouvre la fenêtre, si j'active la climatisation etc...<br>
				Exemple de systématique : j'ouvre la fenêtre je veux que immédiatement le volet s'ouvre peut importe la condition sur action<br>
				Exemple de non systématique : si la luminosité est inférieur à 50lux alors je veux que le volet lors de la prochaine vérification s'ouvre à 80% indépendamment de la position du soleil que si le moteur à le droit de tourner (condition pour action)}}</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="positiontab">
				<br/>
				<fieldset>
					<form class="form-horizontal">
						<legend><i class="fas fa-cog"></i> {{Général}}</legend>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{% ouverture}}</label>
							<div class="col-sm-1">
								<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::openPosition"/>
							</div>
							<label class="col-sm-1 control-label">{{% fermeture}}</label>
							<div class="col-sm-1">
								<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::closePosition"/>
							</div>
							<label class="col-sm-1 control-label">{{Action par défaut}}</label>
							<div class="col-sm-2">
								<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::defaultAction">
									<option value="none">{{Ne rien faire}}</option>
									<option value="open">{{Ouvrir}}</option>
									<option value="close">{{Fermer}}</option>
									<option value="custom">{{Position Personnalisée}}</option>
								</select>
							</div>
							<label class="col-sm-1 control-label customPosition" style="display : none;">{{Position personnalisée}}</label>
							<div class="col-sm-1 customPosition" style="display : none;">
								<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::customPosition"/>
							</div>
						</div>
					</fieldset>
				</form>
				<legend><i class="icon jeedom-volet-ferme"></i> {{Positionnement}}
					<a class="btn btn-default btn-xs pull-right" style="margin-right:15px;" id="bt_addPosition"><i class="fas fa-plus"></i> {{Ajouter}}</a>
				</legend>
				<table class="table table-condensed" id="table_sunShutterPosition">
					<thead>
						<tr>
							<th>{{Azimuth}}</th>
							<th>{{Elevation}}</th>
							<th>{{Position}}</th>
							<th>{{Condition}}</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
				<br/>
				<div class="alert alert-info">{{Dans cet onglet vous allez définir le coeur même du moteur de gestion en définissant les règles de base de postionnement en fonction de la position du soleil : <br>
				-%ouverture : % maximum d'ouverture du volet (généralement 99 ou 100) <br>
				-% fermeture : % minimum de fermeture du volet (généralement 0) <br>
				- action par défaut : que faire lorsqu'aucune règle du tableau positionnement ne correspond (ouvrir/fermer/position personnalisée) <br>
				- Tableau de positionnement : ici vous allez définir les règles de positionnement (si aucune exception n'est en cours) <br>
				. Azimuth : borne d'azimuth du soleil (suncalc.org permet de faire bouger le soleil et voir son azimuth par rapport à votre volet) <br>
				. Elevation : borne d'élévation du soleil (suncalc.org permet de faire bouger le soleil et voir son élévation par rapport à votre volet) <br>
				. Position : valeur de position à appliquer au volet si les conditions d'azimuth et d'élévation sont réunies <br>
				. Condition : permet de définir une condition supplémentaire (cela permet par exemple pour une même position du soleil d'avoir un comportement différent si température au dessus ou en dessous d'une valeur, ou toute autres conditions) <br>
				NB : si pour une position du soleil donnée il ya plusieurs positionnement possible (toutes les conditions réunies) le système prendra la première<br>
				}}</div>
			</div>
			<div class="tab-pane" id="scheduletab">
				<form class="form-horizontal">
					<fieldset>
						<br/>
						<div id="div_schedule"></div>
					</fieldset>
				</form>
				<br/>
				<div class="alert alert-info">{{Dans cet onglet vous pouvez voir s'il y a un planning dans le plugin agenda agissant sur votre gestion de volet.<br> 
				Exemple : planifier une suspension et une reprise manuelle pendant les heures de sieste d'un enfant etc....}}</div>
			</div>
		</div>
	</div>
</div>

<?php include_file('desktop', 'sunshutter', 'js', 'sunshutter');?>
<?php include_file('core', 'plugin.template', 'js');?>
