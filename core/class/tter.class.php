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
require_once dirname(__FILE__).'/../../../tter/3rdparty/SncfApi.php';


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

	  /*
	public static function cron15($_eqlogic_id = null, $from=__FUNCTION__) {
		
	}
	*/

	public static function cron5($_eqlogic_id = null, $from=__FUNCTION__) {
		foreach (self::byType('tter') as $tter) {//parcours tous les équipements du plugin tter
			if ($tter->getIsEnable() == 1) {//vérifie que l'équipement est actif
				$refresh = $tter->getCmd(null, 'refresh');//retourne la commande "refresh si elle exxiste
				if (!is_object($refresh)) {//Si la commande n'existe pas
					continue; //continue la boucle
				}
				$refresh->execCmd(); // la commande existe on la lance
			}
		}
	}


    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        $tter = $this->getEqLogic();
		$tter->refreshData($tter);		
    }

    public function preSave() {
        
    }

    public function postSave() {

		log::add('tter','debug','Enable = '.$this->getIsEnable());
		if($this->getIsEnable() <> 1){
			$this->setIsEnable(1);
			log::add('tter','debug','set de Enable à '.$this->getIsEnable());
		}

		log::add('tter','debug','Début ajout des commandes');		
		$nbrTrajet = $this->getConfiguration('nbrTrajet');

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

		for ($indexTrajet = 0; $indexTrajet <= $nbrTrajet - 1; $indexTrajet++){

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
		$refresh->execCmd();
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
		log::add('tter','debug','apiKey : '.$apiKey);		
				
		$depart = $tter->getConfiguration('gareDepart');
		log::add('tter','debug','gare de depart : '.$depart);
		
		$arrivee = $tter->getConfiguration('gareArrivee');
		log::add('tter','debug','gare d arrivee : '.$arrivee);
		
		$nbrTrajet = $this->getConfiguration('nbrTrajet');
		log::add('tter','debug','nombre de trajet(s) : '.$nbrTrajet);
		
		// appel de l'API SNCF
		log::add('tter','debug','Appel API SNCF');
		$trajets = SncfApi::getTrajets($apiKey,$depart,$arrivee,$nbrTrajet);
	
		// set des infos récuperer
		$currentDate = strtotime(date("Ymd\TH:i"));
		// mise à jour des infos gare départ et arrivée
		// départ
		$tter->checkAndUpdateCmd('depart', $trajets[0]['gareDepart']);
		log::add('tter','debug','set: depart to : '.$trajets[0]['gareDepart']);
		// arrivée
		$tter->checkAndUpdateCmd('arrivee', $trajets[0]['gareArrivee']);
		log::add('tter','debug','set: arrivee to : '.$trajets[0]['gareArrivee']);
		
		for ($indexTrajet = 0; $indexTrajet <= $nbrTrajet - 1; $indexTrajet++){	
			// MàJ du champ heure de départ
			$tter->checkAndUpdateCmd('heureDepart'.$indexTrajet, $trajets[$indexTrajet]['heureDepart']);
			log::add('tter','debug','set: heureDepart to : '.$trajets[$indexTrajet]['heureDepart']);
						
			// MàJ du champ heure d'arrivée
			$tter->checkAndUpdateCmd('heureArrivee'.$indexTrajet, $trajets[$indexTrajet]['heureArrivee']);
			log::add('tter','debug','set: heureArrivee to : '.$trajets[$indexTrajet]['heureArrivee']);

			// MàJ du champ retard
			$tter->checkAndUpdateCmd('retard'.$indexTrajet, $trajets[$indexTrajet]['retard']);
			log::add('tter','debug','set: retard to : '.$trajets[$indexTrajet]['retard']);

			// MàJ du champ dureeTrajet
			$tter->checkAndUpdateCmd('dureeTrajet'.$indexTrajet, $trajets[$indexTrajet]['dureeTrajet']);
			log::add('tter','debug','set: dureeTrajet to : '.$trajets[$indexTrajet]['dureeTrajet']);
		}
	}



    
    // Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
    public function toHtml($_version = 'dashboard') {
		$replace = $this->preToHtml($_version); //récupère les informations de notre équipement
		  	if (!is_array($replace)) {
				return $replace;
		  	}
		$this->emptyCacheWidget(); //vide le cache. Pratique pour le développement
		
		// on récupere les infos données par l'API SNCF
		$depart = $this->getCmd(null, 'depart');
		$arrivee = $this->getCmd(null, 'arrivee');
		$replace['#depart#'] = $depart->execCmd();
		$replace['#arrivee#'] = $arrivee->execCmd();

		$nbrTrajet = $this->getConfiguration('nbrTrajet');

		for ($indexTrajet = 0; $indexTrajet <= $nbrTrajet - 1; $indexTrajet++){

			$heureDepart = $this->getCmd(null, 'heureDepart'.$indexTrajet);
			$heureArrivee = $this->getCmd(null, 'heureArrivee'.$indexTrajet);

			$dureeTrajet = $this->getCmd(null, 'dureeTrajet'.$indexTrajet);
			$retard = $this->getCmd(null, 'retard'.$indexTrajet);
			
			$classForDepartureTime = 'heure ';
			$classForArrivalTime = 'heure ';
			$classForDelayedDepartureTime = 'heure ';
			$classForDelayedArrivalTime = 'heure ';
			$classForRetard = '';
			$isDelayed = false;

			if($retard->execCmd() == 'à l\'heure'){
				log::add('tter','debug','train onTime');
				$classForDepartureTime .= 'whiteText';
				$classForArrivalTime .= 'whiteText';
				$classForRetard = 'onTime';
			}elseif($retard->execCmd() == 'supprimé'){
				log::add('tter','debug','train deleted');
				$classForDepartureTime .= 'whiteText deletedText';
				$classForArrivalTime .= 'whiteText deletedText';
				$classForRetard = 'deleted';
			}else{
				log::add('tter','debug','train delayed');
				$isDelayed = true;
				$classForDepartureTime .= 'whiteText deletedText';
				$classForArrivalTime .= 'whiteText deletedText';
				$classForDelayedDepartureTime .= 'redText';
				$classForDelayedArrivalTime .= 'redText';
				$classForRetard = 'delayed';
			}

			if($isDelayed){
				$replace['#heureDepart'.$indexTrajet.'#'] =
					'<center>'
					.'<span class="'.$classForDepartureTime.'">'.$heureDepart->execCmd().' </span>'
					.'<span class="'.$classForDelayedDepartureTime.'">'.$heureDepart->execCmd().'</span>'
					.'</center>';
				$replace['#heureArrivee'.$indexTrajet.'#'] =
					'<center>'
					.'<span class="'.$classForArrivalTime.'">'.$heureArrivee->execCmd().' </span>'
					.'<span class="'.$classForDelayedArrivalTime.'">'.$heureArrivee->execCmd().'</span>'
					.'</center>';	
			}else {
				$replace['#heureDepart'.$indexTrajet.'#'] =
					'<center class="'.$classForDepartureTime.'">'.$heureDepart->execCmd().'</center>';
				$replace['#heureArrivee'.$indexTrajet.'#'] =
					'<center class="'.$classForArrivalTime.'">'.$heureArrivee->execCmd().'</center>';
			}				
			
			$replace['#retard'.$indexTrajet.'#'] = '<center class="sticker '.$classForRetard.'"><div>'.$retard->execCmd().'</div></center>';
			
		}		
		$version = jeedom::versionAlias($_version);
		return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'tter', 'tter')));//  retourne notre template qui se nomme eqlogic pour le widget	  
    }
    

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
	public static $_widgetPossibility = array('custom' => false);

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


