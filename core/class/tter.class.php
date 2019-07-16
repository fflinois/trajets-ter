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
	
		// Création des commandes de type action
		$maj = $this->getCmd(null, 'maj');
		if (!is_object($maj)) {
            $maj = new tterCmd();
			$maj->setLogicalId('maj');
			//$maj.setIsVisible(1);
            $maj->setOrder(19);
            $maj->setName(__('Refresh', __FILE__));
		}
		$maj->setType('action');
		$maj->setSubType('other');
		$maj->setEqLogic_id($this->getId());
		$maj->save();

		/*$refreshTter = $this->getCmd(null, 'refreshtter');
		log::add('tter','debug','getcmd refresh '.$refreshTter);
		if (!is_object($refreshTter)) {
			$refreshTter = new tterCmd();
			log::add('tter','debug','création objet cmd refresh '.$refreshTter);
			$refreshTter->setLogicalId('refreshtter');
			log::add('tter','debug','création logical id refresh '.$refreshTter);
			$refreshTter.setIsVisible(1);
			log::add('tter','debug','création isvisible '.$refreshTter);
			$depart->setOrder(1);
			log::add('tter','debug','création order '.$refreshTter);
			$refreshTter->setName(__('Mise à jour', __FILE__));
			log::add('tter','debug','création name '.$refreshTter);

			log::add('tter','debug','Ajout commande : refreshtter');
		}
		$refreshTter->setType('action');
		$refreshTter->setSubType('other');
		$refreshTter->setEqLogic_id($this->getId());
		$refreshTter->save();*/


		log::add('tter','debug','Fin ajout des commandes');

    }

    public function preUpdate() {
        
    }

    public function postUpdate() {
        
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
		$apiKey = config::byKey('apiKey', 'ter', 0);		
		// appel de l'API SNCF
		$api = new SncfApi();
		$gareDepart = $ter->getConfiguration('gareDepart');
		log::add('tter','debug',$gareDepart);
		$gareArrivee = $ter->getConfiguration('gareArrivee');
		log::add('tter','debug',$gareArrivee);
		$trajets = $api->retrieveJourneys($apiKey, $gareDepart, $gareArrivee);
		log::add('tter','debug','Trajets '.serialize($trajets));
	
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
		if ($this->getLogicalId() == 'maj') {
			log::add('tter','debug','appel fonction refresh');
			$tter->refreshData($tter);
		}
    }

    /*     * **********************Getteur Setteur*************************** */
}


