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
							<label class="col-sm-3 control-label">{{Ne pas reprendre la main}}</label>
							<div class="col-sm-1">
								<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="shutter::nobackhand"/>
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
				<div class="alert alert-info">{{Dans cet onglet BLABALABLABALBALBA}}</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="conditiontab">
				<br/>
				<fieldset>
					<form class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Condition pour action}}</label>
							<div class="col-sm-9">
								<div class="input-group">
									<textarea class="eqLogicAttr form-control" data-concat="1" data-l1key="configuration" data-l2key="condition::allowmove" style="height:200px"></textarea>
									<span class="input-group-btn">
										<a class="btn btn-default listCmdInfo roundedRight" ><i class="fas fa-list-alt"></i></a>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Forcer l'ouverture si}}</label>
							<div class="col-sm-9">
								<div class="input-group">
									<input type="text" class="eqLogicAttr form-control" data-concat="1" data-l1key="configuration" data-l2key="condition::forceopen"/>
									<span class="input-group-btn">
										<a class="btn btn-default listCmdInfo roundedRight" ><i class="fas fa-list-alt"></i></a>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Forcer l'ouverture immediatement si}}</label>
							<div class="col-sm-9">
								<div class="input-group">
									<input type="text" class="eqLogicAttr form-control" data-concat="1" data-l1key="configuration" data-l2key="condition::immediatforceopen"/>
									<span class="input-group-btn">
										<a class="btn btn-default listCmdInfo roundedRight" ><i class="fas fa-list-alt"></i></a>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Forcer la fermeture si}}</label>
							<div class="col-sm-9">
								<div class="input-group">
									<input type="text" class="eqLogicAttr form-control" data-concat="1" data-l1key="configuration" data-l2key="condition::forceclose"/>
									<span class="input-group-btn">
										<a class="btn btn-default listCmdInfo roundedRight" ><i class="fas fa-list-alt"></i></a>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Forcer l'ouverture immediatement si}}</label>
							<div class="col-sm-9">
								<div class="input-group">
									<input type="text" class="eqLogicAttr form-control" data-concat="1" data-l1key="configuration" data-l2key="condition::immediatforceclose"/>
									<span class="input-group-btn">
										<a class="btn btn-default listCmdInfo roundedRight" ><i class="fas fa-list-alt"></i></a>
									</span>
								</div>
							</div>
						</div>
					</fieldset>
				</form>
				<br/>
				<div class="alert alert-info">{{Dans cet onglet BLABALABLABALBALBA}}</div>
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
							<div class="col-sm-1">
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
				<div class="alert alert-info">{{Dans cet onglet BLABALABLABALBALBA}}</div>
			</div>
			<div class="tab-pane" id="scheduletab">
				<form class="form-horizontal">
					<fieldset>
						<br/>
						<div id="div_schedule"></div>
					</fieldset>
				</form>
				<br/>
				<div class="alert alert-info">{{Dans cet onglet BLABALABLABALBALBA}}</div>
			</div>
		</div>
	</div>
</div>

<?php include_file('desktop', 'sunshutter', 'js', 'sunshutter');?>
<?php include_file('core', 'plugin.template', 'js');?>
