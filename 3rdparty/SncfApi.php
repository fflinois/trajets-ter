<?php

class SncfApi {
  /**
   * Fonction qui permet de récuperer les trajets entre deux gares
   * 
   * @param String $apiKey clé de l'api SNCF
   * @param String $depart code de la gare de départ
   * @param String $arrivee code de la gare d'arrivée
   * 
   * @return array tableau de trajets
   */
	public function getTrajets($apiKey, $depart, $arrivee, $nbrTrajet) {
		log::add('tter','debug','calling sncf api with :'.$apiKey.' / '.$depart.' / '.$arrivee);
		date_default_timezone_set("Europe/Paris");
		$currentDate = date("Ymd\TH:i");

			// construction de la requete vers l'API SNCF
			$baseQuery = 'https://'.$apiKey.'@api.sncf.com/v1/coverage/sncf/journeys?';
			$finalQuery = $baseQuery.'from='.$depart.'&to='.$arrivee.'&datetime='.$currentDate.'&datetime_represents=departure&min_nb_journeys='.$nbrTrajet;
			log::add('tter','debug',$finalQuery);

			// Execution de la requete
			$response = file_get_contents($finalQuery);
			log::add('tter','debug','API response :'.$response);

			// Decodage de la response en JSON
			$responseJSON = json_decode($response, true);
			log::add('tter','debug','API response json :'.$responseJSON);

			$trajets = [];
			$indexTrajet = 0;

			// Pour chaque 'journeys' du JSON représentant un trajet
			foreach($responseJSON['journeys'] as $trajet) {

			// récuperation des informations principales du trajet
			$dateTimeDepart = $trajet['departure_date_time'];
			$heureDepart = substr($dateTimeDepart,9,4);
			$dateTimeArrivee = $trajet['arrival_date_time'];
			$heureArrivee = substr($dateTimeArrivee,9,4);
			$numeroTrain = $trajet['sections'][1]['display_informations']['headsign'];
			$gareDepart = $trajet['sections'][1]['from']['stop_point']['name'];
			$gareArrivee = $trajet['sections'][1]['to']['stop_point']['name'];

			log::add('tter','debug','Found train '.$numeroTrain.' :'.$dateTimeDepart.' / '.$dateTimeArrivee.' - '.$gareDepart.' > '.$gareArrivee);

			// si le train est indisponible 
			if ($trajet['status'] == 'NO_SERVICE'){
				$retard = 'supprimé';
			}else{
				// sinon recherche des retards éventuels
				$retard = 'à l\'heure';
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

							if($trajet['sections'][1]['from']['id'] == $impactStop['id']){
								log::add('tter','debug', '######## TEST COMPARAISON ID DEPARTURE #######');
								$delayedDepartureTime = self::convertAmenededTimeToTimeString($impactStop['amended_departure_time']);
							}

							if($trajet['sections'][1]['to']['id'] == $impactStop['id']){
								log::add('tter','debug', '######## TEST COMPARAISON ID ARRIVAL #######');
								$delayedArrivalTime = self::convertAmenededTimeToTimeString($impactStop['amended_arrival_time']);
							}

							if ( substr($impactStop['base_departure_time'],0,4) == $heureDepart ) {							

								$delayedDepartureTimeForComputeDelay = $impactStop['amended_departure_time'];
								$causeOfDelayed = $impactStop['cause'];
								log::add('tter','debug', 'amended departure time : '.$delayedDepartureTime);
								// compute delay
								$retard = 
									( substr($delayedDepartureTimeForComputeDelay,0,2) * 60 + substr($delayedDepartureTimeForComputeDelay,2,2) ) 
									- ( substr($heureDepart,0,2) * 60 + substr($heureDepart,2,2) );
								
								if ($retard == 0) {
									$retard = 'à l\'heure';
								} else {
									$retard = 'retard '.$retard.' min.';
								}
								log::add('tter','debug', 'retard : '.$retard);
							}
						}					
					}
				}
			}		

					// store data for current train
			$trajets[$indexTrajet] = array(
				'numeroTrain' => $numeroTrain,
				'gareDepart' => $gareDepart,
				'gareArrivee' => $gareArrivee,
				'heureDepart' => self::convertDateToTimeString($trajet['departure_date_time']),
				'heureArrivee' => self::convertDateToTimeString($trajet['arrival_date_time']),
				'dureeTrajet' => self::convertDurationToTimeString($trajet['duration']),
				'retard' => $retard,
				'causeOfDelayed' => $causeOfDelayed,
				'delayedDepartureTime' => $delayedDepartureTime,
				'delayedArrivalTime' => $delayedArrivalTime,
			);

		log::add('tter','debug','trajet '.$indexTrajet.' : '.$trajets[$indexTrajet]);
      	log::add('tter','debug','gareDepart'.$indexTrajet.' : '.$trajets[$indexTrajet]['gareDepart']);
      	log::add('tter','debug','gareArrivee'.$indexTrajet.' : '.$trajets[$indexTrajet]['gareArrivee']);
	  	log::add('tter','debug','heureDepart'.$indexTrajet.' : '.$trajets[$indexTrajet]['heureDepart']);
	  	log::add('tter','debug','heureArrivee'.$indexTrajet.' : '.$trajets[$indexTrajet]['heureArrivee']);
      	log::add('tter','debug','retard'.$indexTrajet.' : '.$trajets[$indexTrajet]['retard']);
      	log::add('tter','debug','dureeTrajet'.$indexTrajet.' : '.$trajets[$indexTrajet]['dureeTrajet']);
		$indexTrajet++;
    }
    return $trajets;
  }

  public function convertDateToTimeString($dateToConvert){
	$dateToHhMm = substr($dateToConvert,9,4);
	return substr($dateToHhMm,0,2)."h".substr($dateToHhMm,2,2);	
  }

  public function convertDurationToTimeString($durationToConvert){
	$durationToConvertToInt = (int)$durationToConvert;
	$durationInMin = $durationToConvertToInt / 60;
	$durationToTimeString = '';
	if($durationInMin > 59){
		$hoursDuration = $durationInMin / 60;
		$minsDuration = $durationToConvertToInt / 60 - $hoursDuration * 60 ;
		$durationToTimeString = $hoursDuration.'h'.$minsDuration;
	}else{
		$durationToTimeString = $durationInMin.' min';
	}
	return $durationToTimeString;	
  }

  public function convertAmenededTimeToTimeString($amendedTimeToConvert){
	return substr($amendedTimeToConvert,0,2)."h".substr($amendedTimeToConvert,2,2);;	
  }

}


 ?>
