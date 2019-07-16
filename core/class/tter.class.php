<?php

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

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class tter extends eqLogic {
    /*     * *************************Attributs****************************** */



    /*     * ***********************Methode static*************************** */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {

      }
     */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {

      }
     */



    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        
    }

    public function preSave() {
        
    }

    public function postSave() {
		log::add('tter','debug','Début ajout des commandes');
		
		// Création des commandes de type action		
		$refresh = $this->getCmd(null, 'refresh');
		if (!is_object($refresh)) {
			$refresh = new tterCmd();
			$refresh->setLogicalId('refresh');
			$refresh->setOrder(1);
			$refresh->setName(__('Mise à jour', __FILE__));
			log::add('tter','debug','Ajout commande : refresh');
		}
		$refresh->setType('action');
		$refresh->setSubType('other');
		$refresh->setEqLogic_id($this->getId());
		$refresh->save();

		// Création des différentes commandes de type 'info'
		// Création de la commande depart
        $depart = $this->getCmd(null, 'depart');
		if (!is_object($depart)) {
			$depart = new tterCmd();
			$depart->setLogicalId('depart');
			$depart->setIsVisible(1);
      		$depart->setOrder(2);
			$depart->setName(__('Gare de départ', __FILE__));
			log::add('tter','debug','Ajout commande : depart');
		}
		$depart->setType('info');
        $depart->setSubType('string');
		$depart->setEventOnly(1);
		$depart->setEqLogic_id($this->getId());
		$depart->save();

		// Création de la commande arrivee
		$arrivee = $this->getCmd(null, 'arrivee');
		if (!is_object($arrivee)) {
			$arrivee = new tterCmd();
			$arrivee->setLogicalId('arrivee');
			$arrivee->setIsVisible(1);
      		$arrivee->setOrder(3);
			$arrivee->setName(__('Gare d\'arrivée', __FILE__));
			log::add('tter','debug','Ajout commande : arrivee');
		}
		$arrivee->setType('info');
		$arrivee->setSubType('string');
		$arrivee->setEventOnly(1);
		$arrivee->setEqLogic_id($this->getId());
		$arrivee->save();

		// Création des commandes en tableau d'objets
		$arrayTrajets = [];

		for ($indexTrajet = 0; $indexTrajet <= 3; $indexTrajet++){

			$arrayTrajets[$indexTrajet] = array(				
			  	'heureDepart' => $this->getCmd(null, 'heureDepart'.$indexTrajet),
			  	'heureArrivee' => $this->getCmd(null, 'heureArrivee'.$indexTrajet),
			  	'dureeTrajet' => $this->getCmd(null, 'dureeTrajet'.$indexTrajet),
			  	'retard' => $this->getCmd(null, 'retard'.$indexTrajet)
			);
			// Création de la commande heureDepart
			if (!is_object($arrayTrajets[$indexTrajet]['heureDepart'])) {
				$arrayTrajets[$indexTrajet]['heureDepart'] = new tterCmd();
				$arrayTrajets[$indexTrajet]['heureDepart']->setLogicalId('heureDepart'.$indexTrajet);
				$arrayTrajets[$indexTrajet]['heureDepart']->setIsVisible(1);
				$arrayTrajets[$indexTrajet]['heureDepart']->setOrder(4+$indexTrajet*4);
				$arrayTrajets[$indexTrajet]['heureDepart']->setName(__('Heure départ train '.$indexTrajet, __FILE__));
				log::add('tter','debug','Ajout commande : heureDepart'.$indexTrajet);
			}
			$arrayTrajets[$indexTrajet]['heureDepart']->setType('info');
			$arrayTrajets[$indexTrajet]['heureDepart']->setSubType('string');
			$arrayTrajets[$indexTrajet]['heureDepart']->setEventOnly(1);
			$arrayTrajets[$indexTrajet]['heureDepart']->setEqLogic_id($this->getId());
			$arrayTrajets[$indexTrajet]['heureDepart']->save();

			// Création de la commande heureArrivee
			if (!is_object($arrayTrajets[$indexTrajet]['heureArrivee'])) {
				$arrayTrajets[$indexTrajet]['heureArrivee'] = new tterCmd();
				$arrayTrajets[$indexTrajet]['heureArrivee']->setLogicalId('heureArrivee'.$indexTrajet);
				$arrayTrajets[$indexTrajet]['heureArrivee']->setIsVisible(1);
				$arrayTrajets[$indexTrajet]['heureArrivee']->setOrder(5+$indexTrajet*4);
				$arrayTrajets[$indexTrajet]['heureArrivee']->setName(__('Heure arrivée train '.$indexTrajet, __FILE__));
				log::add('tter','debug','Ajout commande : heureArrivee'.$indexTrajet);
			}
			$arrayTrajets[$indexTrajet]['heureArrivee']->setType('info');
			$arrayTrajets[$indexTrajet]['heureArrivee']->setSubType('string');
			$arrayTrajets[$indexTrajet]['heureArrivee']->setEventOnly(1);
			$arrayTrajets[$indexTrajet]['heureArrivee']->setEqLogic_id($this->getId());
			$arrayTrajets[$indexTrajet]['heureArrivee']->save();

			// Création de la commande dureeTrajet
			if (!is_object($arrayTrajets[$indexTrajet]['dureeTrajet'])) {
				$arrayTrajets[$indexTrajet]['dureeTrajet'] = new tterCmd();
				$arrayTrajets[$indexTrajet]['dureeTrajet']->setLogicalId('dureeTrajet'.$indexTrajet);
				$arrayTrajets[$indexTrajet]['dureeTrajet']->setIsVisible(1);
				$arrayTrajets[$indexTrajet]['dureeTrajet']->setOrder(6+$indexTrajet*4);
				$arrayTrajets[$indexTrajet]['dureeTrajet']->setName(__('Temps de trajet train '.$indexTrajet, __FILE__));
				log::add('tter','debug','Ajout commande : dureeTrajet'.$indexTrajet);
			}
			$arrayTrajets[$indexTrajet]['dureeTrajet']->setType('info');
			$arrayTrajets[$indexTrajet]['dureeTrajet']->setSubType('string');
			$arrayTrajets[$indexTrajet]['dureeTrajet']->setEventOnly(1);
			$arrayTrajets[$indexTrajet]['dureeTrajet']->setEqLogic_id($this->getId());
			$arrayTrajets[$indexTrajet]['dureeTrajet']->save();
		
			// Création de la commande retard
			if (!is_object($arrayTrajets[$indexTrajet]['retard'])) {
				$arrayTrajets[$indexTrajet]['retard'] = new tterCmd();
				$arrayTrajets[$indexTrajet]['retard']->setLogicalId('retard'.$indexTrajet);
				$arrayTrajets[$indexTrajet]['retard']->setIsVisible(1);
				$arrayTrajets[$indexTrajet]['retard']->setOrder(7+$indexTrajet*4);
				$arrayTrajets[$indexTrajet]['retard']->setName(__('Retard train '.$indexTrajet, __FILE__));
				log::add('tter','debug','Ajout commande : retard'.$indexTrajet);
			}
			$arrayTrajets[$indexTrajet]['retard']->setType('info');
			$arrayTrajets[$indexTrajet]['retard']->setSubType('string');
			$arrayTrajets[$indexTrajet]['retard']->setEventOnly(1);
			$arrayTrajets[$indexTrajet]['retard']->setEqLogic_id($this->getId());
			$arrayTrajets[$indexTrajet]['retard']->save();
		}	
		
		log::add('tter','debug','Fin ajout des commandes');

    }

    public function preUpdate() {
        
    }

    public function postUpdate() {
        $this->postSave();
    }

    public function preRemove() {
        
    }

    public function postRemove() {
        
	}
	
	/**
	 * méthode appelée pour remplir les champs avec un appel à l'API
	 * 
	 */
	public function refreshData($tter) {

		log::add('tter','debug','Debut fonction refreshData');
		$apiKey = config::byKey('apiKey', 'tter', 0);
		log::add('tter','debug',$apiKey);		
		// appel de l'API SNCF
		//$api = new SncfApi();
		//log::add('tter','debug',$api);
		$depart = $tter->getConfiguration('gareDepart');
		log::add('tter','debug',$gareDepart);
		$arrivee = $tter->getConfiguration('gareArrivee');
		log::add('tter','debug',$gareArrivee);

		//$api = $this->getEqLogic();
		//log::add('tter','debug',$api);
		//$trajets = $api->getTrajets($apiKey, $gareDepart, $gareArrivee);
		//log::add('tter','debug','Trajets '.serialize($trajets));

		// ##############################################################

					log::add('tter','debug','calling sncf api with :'.$apiKey.' / '.$depart.' / '.$arrivee);
				date_default_timezone_set("Europe/Paris");
			$currentDate = date("Ymd\TH:i");

			// construction de la requete vers l'API SNCF
			$baseQuery = 'https://'.$apiKey.'@api.sncf.com/v1/coverage/sncf/journeys?';
			$finalQuery = $baseQuery.'from='.$depart.'&to='.$arrivee.'&datetime='.$currentDate.'&datetime_represents=departure&min_nb_journeys=4';
			log::add('tter','debug',$finalQuery);

			// Execution de la requete
			$response = file_get_contents($finalQuery);
				log::add('tter','debug','API response :'.$response);

			// Decodage de la response en JSON
			$responseJSON = json_decode($response, true);

			$trajets = [];
			$indexTrajet = 0;

			// Pour chaque 'journeys' du JSON représentant un trajet
			foreach($responseJSON['journeys'] as $trajet) {

			// récuperation des informations principales du trajet
			$dateTimeDepart = $trajet['departure_date_time'];
			$heureDepart = substr($dateTimeDepart,9,4);
			$dateTimeArrivee = $trajet['arrival_date_time'];
			$heureArrivee = substr($dateTimeArrivee,9,4);
			$dureeTrajet = gmdate("Hi", strtotime($dateTimeArrivee)-strtotime($dateTimeDepart));
			$numeroTrain = $trajet['sections'][1]['display_informations']['headsign'];
			$gareDepart = $trajet['sections'][1]['from']['stop_point']['name'];
			$gareArrivee = $trajet['sections'][1]['to']['stop_point']['name'];

					log::add('tter','debug','Found train '.$numeroTrain.' :'.$dateTimeDepart.' / '.$dateTimeArrivee.' - '.$gareDepart.' > '.$gareArrivee);

			// si le train est indisponible 
			if ($trajet['status'] == 'NO_SERVICE'){
				$retard = 'PAS DE SERVICE';
			}else{
				// sinon recherche des retards éventuels
				$retard = 'aucun';
				$updatedTime = $heureDepart;
				$numdisrup = $trajet['sections'][1]['display_informations']['links'][0]['id'];
				log::add('tter','debug','Disruption ID '.$numdisrup);

				$disruptions = $responseJSON['disruptions'];
				foreach($disruptions as $disruption) {
					if ( $disruption['disruption_id']== $numdisrup ) {
					log::add('tter','debug','Disruption ID '.$numdisrup. ' has been found!');
					log::add('tter','debug','Search for impacted departure '.$heureDepart);
					// go through each impacted stops
					foreach($disruption['impacted_objects'][0]['impacted_stops'] as $impactStop) {
						log::add('tter','debug','testing departure '.substr($impactStop['base_departure_time'],0,4));

						if ( substr($impactStop['base_departure_time'],0,4) == $heureDepart ) {
							$updatedTime = $impactStop['amended_departure_time'];
							// compute delay
							$retard = ( substr($updatedTime,0,2) * 60 + substr($updatedTime,2,2) ) - ( substr($heureDepart,0,2) * 60 + substr($heureDepart,2,2) ).' minutes';
							if ($retard == 0) {
								$retard = 'aucun';
							} elseif ($retard > 1) {
								$retard .= ' minutes';
							} else {
								$retard .= ' minute';
							}
							break;
						}
					}
					break;
					}
				}
			}		

					// store data for current train
				$trajets[$indexTrajet] = array(
						'numeroTrain' => $numeroTrain,
					'gareDepart' => $gareDepart,
					'gareArrivee' => $gareArrivee,
					'dateTimeDepart' => $dateTimeDepart,
					'heureDepart' => $heureDepart,
					'dateTimeArrivee' => $dateTimeArrivee,
					'heureArrivee' => $heureArrivee,
					'dureeTrajet' => $dureeTrajet,
					'retard' => $retard,
					'updatedheureDepart' => $updatedTime
					);
				$indexTrajet++;
			}


		// ##############################################################




	
		// find the right train...
		$currentDate = strtotime(date("Ymd\TH:i"));
		for ($indexTrajet = 0; $indexTrajet <= 3; $indexTrajet++){
			$heureDepart = date('Hi',strtotime($trajets[$indexTrajet]['departureDate']));
			$heureArrivee = date('Hi',strtotime($journeys[$indexTrajet]['arrivalDate']));
			// update widget info
			foreach ($tter->getCmd('info') as $cmd) {
				switch ($cmd->getLogicalId()) {
				  
					case 'retard'+$indexTrajet:
						$value = $trajets[$indexTrajet]['retard'];
						break;
					case 'dureeTrajet'+$indexTrajet:
						$value = substr($trajets[$indexTrajet]['duration'],0,2)."h".substr($trajets[$indexTrajet]['duration'],2,2);
						break;
				    case 'heureArrivee'+$indexTrajet:
						$value = substr($heureArrivee,0,2)."h".substr($heureArrivee,2,2);
						break;
				  	case 'heureDepart':
						$value = substr($heureDepart,0,2)."h".substr($heureDepart,2,2);
						break;
					case 'arrivee':
          				$value = $trajets[$indexTrajet]['gareTo'];
        				break;
        			case 'depart':
         				$value = $trajets[$indexTrajet]['gareFrom'];
       					break;
				}
			$cmd->setCollectDate('');
			$cmd->event($value);
			log::add('tter','debug','set:'.$cmd->getLogicalId().' to '. $value);
			}
		}
		log::add('ter','debug','selected journey n° '.$i);	
	}

	  /**
   * Fonction qui permet de récuperer les trajets entre deux gares
   * 
   * @param String $apiKey clé de l'api SNCF
   * @param String $depart code de la gare de départ
   * @param String $arrivee code de la gare d'arrivée
   * 
   * @return array tableau de trajets
   */
  public function getTrajets($apiKey, $depart, $arrivee) {
	log::add('tter','debug','calling sncf api with :'.$apiKey.' / '.$depart.' / '.$arrivee);
	date_default_timezone_set("Europe/Paris");
$currentDate = date("Ymd\TH:i");

// construction de la requete vers l'API SNCF
$baseQuery = 'https://'.$apiKey.'@api.sncf.com/v1/coverage/sncf/journeys?';
$finalQuery = $baseQuery.'from='.$depart.'&to='.$arrivee.'&datetime='.$currentDate.'&datetime_represents=departure&min_nb_journeys=4';
log::add('tter','debug',$query);

// Execution de la requete
$response = file_get_contents($query);
	log::add('tter','debug','API response :'.$response);

// Decodage de la response en JSON
$responseJSON = json_decode($response, true);

$trajets = [];
$indexTrajet = 0;

// Pour chaque 'journeys' du JSON représentant un trajet
foreach($responseJSON['journeys'] as $trajet) {

  // récuperation des informations principales du trajet
  $dateTimeDepart = $trajet['departure_date_time'];
  $heureDepart = substr($dateTimeDepart,9,4);
  $dateTimeArrivee = $trajet['arrival_date_time'];
  $heureArrivee = substr($dateTimeArrivee,9,4);
  $dureeTrajet = gmdate("Hi", strtotime($dateTimeArrivee)-strtotime($dateTimeDepart));
  $numeroTrain = $trajet['sections'][1]['display_informations']['headsign'];
  $gareDepart = $trajet['sections'][1]['from']['stop_point']['name'];
  $gareArrivee = $trajet['sections'][1]['to']['stop_point']['name'];

		log::add('tter','debug','Found train '.$numeroTrain.' :'.$dateTimeDepart.' / '.$dateTimeArrivee.' - '.$gareDepart.' > '.$gareArrivee);

  // si le train est indisponible 
  if ($trajet['status'] == 'NO_SERVICE'){
	  $retard = 'PAS DE SERVICE';
  }else{
	// sinon recherche des retards éventuels
	$retard = 'aucun';
	$updatedTime = $heureDepart;
	$numdisrup = $trajet['sections'][1]['display_informations']['links'][0]['id'];
	log::add('tter','debug','Disruption ID '.$numdisrup);

	$disruptions = $responseJSON['disruptions'];
	foreach($disruptions as $disruption) {
		if ( $disruption['disruption_id']== $numdisrup ) {
		  log::add('tter','debug','Disruption ID '.$numdisrup. ' has been found!');
		  log::add('tter','debug','Search for impacted departure '.$heureDepart);
		  // go through each impacted stops
		  foreach($disruption['impacted_objects'][0]['impacted_stops'] as $impactStop) {
			log::add('tter','debug','testing departure '.substr($impactStop['base_departure_time'],0,4));

			  if ( substr($impactStop['base_departure_time'],0,4) == $heureDepart ) {
				  $updatedTime = $impactStop['amended_departure_time'];
				  // compute delay
				  $retard = ( substr($updatedTime,0,2) * 60 + substr($updatedTime,2,2) ) - ( substr($heureDepart,0,2) * 60 + substr($heureDepart,2,2) ).' minutes';
				  if ($retard == 0) {
					$retard = 'aucun';
				  } elseif ($retard > 1) {
					$retard .= ' minutes';
				  } else {
					$retard .= ' minute';
				  }
				  break;
			  }
		  }
		  break;
		}
	}
  }		

		// store data for current train
	$trajets[$indexTrajet] = array(
			'numeroTrain' => $numeroTrain,
		  'gareDepart' => $gareDepart,
		  'gareArrivee' => $gareArrivee,
		  'dateTimeDepart' => $dateTimeDepart,
		  'heureDepart' => $heureDepart,
		  'dateTimeArrivee' => $dateTimeArrivee,
		  'heureArrivee' => $heureArrivee,
		  'dureeTrajet' => $dureeTrajet,
		  'retard' => $retard,
		  'updatedheureDepart' => $updatedTime
		);
	$indexTrajet++;
  }
	return $trajets;
}

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class tterCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
		log::add('tter','debug','Lancement fonction execute');
		$tter = $this->getEqLogic();
		if ($this->getLogicalId() == 'refresh') {
			log::add('tter','debug','appel fonction refresh');
			$tter->refreshData($tter);
		}
    }

    /*     * **********************Getteur Setteur*************************** */
}


