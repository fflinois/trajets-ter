<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}

$plugin = plugin::byId('tter');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());

include_file('desktop', 'tter', 'css', 'tter');

?>

<div class="row row-overflow">
  <!-- Container bootstrap du menu latéral -->
	<div class="col-lg-2 col-md-3 col-sm-4">
    <!-- Container du menu latéral -->
		<div class="bs-sidebar">
			<ul id="ul_eqLogic" class="nav nav-list bs-sidenav"><a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un trajet TER}}</a>
				<li class="filter" style="margin-bottom: 5px;">
					<input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%" />
				</li>
				<?php
				foreach ($eqLogics as $eqLogic) {
					echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
				}
				?>
			</ul>
		</div>
	</div>

      <!-- Container des listes de commandes / éléments -->
	  <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay">
      <legend><i class="fa fa-cog"></i> {{Gestion}}</legend>
      <div class="eqLogicThumbnailContainer">
        <!-- Bouton d ajout d un objet -->
        <div class="cursor eqLogicAction" data-action="add"
             style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
          
            <i class="fa fa-plus-circle" style="font-size : 6em;color:#94ca02;"></i>
          
          <span
                  style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02">{{Ajouter}}</span>
        </div>
        <!-- Bouton d accès à la configuration -->
        <div class="cursor eqLogicAction" data-action="gotoPluginConf"
             style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
          
            <i class="fa fa-wrench" style="font-size : 6em;color:#767676;"></i>
          
          <span
                  style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676">{{Configuration}}</span>
        </div>
      </div>
      <!-- Début de la liste des objets -->
      <legend><i class="fa fa-table"></i> {{Mes objects}}</legend>
      <!-- Container de la liste -->
      <div class="eqLogicThumbnailContainer">
        <!-- Boucle sur les objects -->
        <?php
        foreach ($eqLogics as $eqLogic) : ?>
          <div class="eqLogicDisplayCard cursor" data-eqLogic_id="<?php echo $eqLogic->getId(); ?>"
               style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
            
              <i class="fa fa-cube" style="font-size : 6em;color:#767676;"></i>
            
            <span
                    style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><?php echo $eqLogic->getHumanName(true, true); ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>


  <!-- Container du panneau de contrôle -->
	<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
			
		<!-- Bouton sauvegarder -->
		<a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i>
		{{Sauvegarder}}</a>
		<!-- Bouton Supprimer -->
		<a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i>
		{{Supprimer}}</a>
		<!-- Bouton configuration avancée -->
		<a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i>
		{{Configuration avancée}}</a>
		<!-- Liste des onglets -->
		<ul class="nav nav-tabs" role="tablist">
		<!-- Bouton de retour -->
		<li role="presentation"><a class="eqLogicAction cursor" aria-controls="home" role="tab"
			data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
		<!-- Onglet "Equipement" -->
		<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab"
			data-toggle="tab"><i
			class="fa fa-tachometer"></i> {{Equipement}}</a></li>
		<!-- Onglet "Commandes" -->
		<li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i
			class="fa fa-list-alt"></i> {{Commandes}}</a></li>
		</ul>

		<!-- Container du contenu des onglets -->
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<!-- Panneau de modification de l'objet -->
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<!-- Car le CSS, c'est pour les faibles -->
				<br/>
				<!-- Ligne de contenu -->
				<div class="row">
					<!-- Division en colonne -->
					<div class="col-sm-7">
						<!-- Début du formulaire -->
						<form class="form-horizontal">
							<!-- Bloc de champs -->
							<fieldset>
								<!-- Container global d'un champ du formulaire -->
								<div class="form-group">
									<!-- Label du champ -->
									<label class="col-sm-6 control-label">{{Nom de l'équipement}}</label>
									<!-- Container du champ -->
									<div class="col-sm-6">
										<!-- Champ contenant l'identifiant caché. Pourquoi pas un hidden ? -->
										<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;"/>
										<!-- Champ contenant l'information -->
										<input type="text" class="eqLogicAttr form-control" data-l1key="name"
										placeholder="{{Nom de l'équipement}}"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-6 control-label">{{Etat}}</label>
									<div class="col-sm-6">
										<!-- Case à cocher activant l'équipement -->
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
										<!-- Case à cocher pour rendre l'élément visible -->
										<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-6 control-label" >{{Objet parent}}</label>
									<div class="col-sm-6">
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
									<label class="col-sm-6 control-label">{{Gare de départ}}</label>
									<div class="col-sm-6">
										<input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="gareDepart" placeholder="stop_area:OCE:SA:87611004" />
										<p class="help-block" style="font-size : 0.8em;">
											<i class="fa fa-info-circle"></i> Récupérez le champ ID dans le résultat de la requête https://api.sncf.com/v1/coverage/sncf/places?q=#nom_gare_sncf# <br/>
											avec #nom_gare_sncf# = gare SNCF de départ. Exemple : TOULOUSE-MATABIAU
										</p>
									</div>
								</div>							
								<div class="form-group">
									<label class="col-sm-6 control-label">{{Gare d'arrivée}}</label>
									<div class="col-sm-6">
										<input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="gareArrivee" placeholder="stop_area:OCE:SA:87611004" />
										<p class="help-block" style="font-size : 0.8em;">
											<i class="fa fa-info-circle"></i> Récupérez le champ ID dans le résultat de la requête https://api.sncf.com/v1/coverage/sncf/places?q=#nom_gare_sncf# <br/>
											avec #nom_gare_sncf# = gare SNCF de d'arrivée. Exemple : VALENCE-AGEN
										</p>
									</div>
								</div>							
								<div class="form-group">
								<label class="col-sm-6 control-label" >{{Nombre de trajet(s) à afficher}}</label>
									<div class="col-sm-6">
										<select id="typeEq" class="form-control eqLogicAttr" data-l1key="configuration" data-l2key="nbrTrajet">
											<?php										
												for ($nbrTrajet = 1; $nbrTrajet <= 10; $nbrTrajet++){
													echo '<option value="' . $nbrTrajet . '">' . $nbrTrajet . '</option>';
												}
											?>
										</select>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
			<!-- Panneau des commandes de l'objet -->
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<!-- Bouton d ajout d'une commande -->
				<a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"> <i
					class="fa fa-plus-circle"></i> {{Commandes}}</a>
				<br/><br/>
				<!-- Tableau des commandes -->
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
						<th>{{Nom}}</th>
                        <th>{{Type}}</th>
                        <th>{{Configuration}}</th>
                        <th>{{Action}}</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php include_file('desktop', 'tter', 'js', 'tter');?>
<?php include_file('core', 'plugin.template', 'js');?>
