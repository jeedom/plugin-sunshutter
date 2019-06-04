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
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Longitude}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="long"/>
							</div>
							<label class="col-sm-1 control-label">{{Latitude}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="lat"/>
							</div>
							<label class="col-sm-1 control-label">{{Altitude}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="alt"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Orientation w}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="w"/>
							</div>
							<label class="col-sm-1 control-label">{{Orientation y}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="y"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Fermer entre}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="angle:close::from"/>
							</div>
							<label class="col-sm-1 control-label">{{et}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="angle:close::to"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Cron de vérification}}</label>
							<div class="col-sm-2">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cron::executeAction"/>
							</div>
							<label class="col-sm-2 control-label">{{Ne pas reprendre la main}}</label>
							<div class="col-sm-1">
								<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="shutter::nobackhand"/>
							</div>
						</div>
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
							<label class="col-sm-1 control-label">{{% ouverture}}</label>
							<div class="col-sm-1">
								<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::openPosition"/>
							</div>
							<label class="col-sm-1 control-label">{{% fermeture}}</label>
							<div class="col-sm-1">
								<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="shutter::closePosition"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Condition pour action}}</label>
							<div class="col-sm-9">
								<div class="input-group">
									<input type="text" class="eqLogicAttr form-control" data-concat="1" data-l1key="configuration" data-l2key="condition::allowmove"/>
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
					</fieldset>
				</form>
			</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i class="fa fa-plus-circle"></i> {{Commandes}}</a><br/><br/>
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>{{Nom}}</th><th>{{Type}}</th><th>{{Action}}</th>
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
