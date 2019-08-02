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
		$currentDateMinusOneHour = self::getcurrentDateMinusOneHour();		
		$currentDate = date("Hi");

			// construction de la requete vers l'API SNCF
			$baseQuery = 'https://'.$apiKey.'@api.sncf.com/v1/coverage/sncf/journeys?';
			$finalQuery = $baseQuery.'from='.$depart.'&to='.$arrivee.'&datetime='.$currentDateMinusOneHour.'&datetime_represents=departure&min_nb_journeys='.$nbrTrajet*2;
			log::add('tter','debug',$finalQuery);

			// Execution de la requete
			$response = file_get_contents($finalQuery);
			//log::add('tter','debug','API response :'.$response);

			// Decodage de la response en JSON
			$responseJSON = json_decode($response, true);
			//log::add('tter','debug','API response json :'.$responseJSON);

			$trajets = [];
			$indexTrajet = 0;

			// Pour chaque 'journeys' du JSON représentant un trajet
			foreach($responseJSON['journeys'] as $trajet) {

			// récuperation des informations principales du trajet			
			$departureTimeForComputeDelay = substr($trajet['departure_date_time'],9,4);			
			$arrivalTimeForComputeDelay = substr($trajet['arrival_date_time'],9,4);
			$departureTime = self::convertDateToTimeString($trajet['departure_date_time']);
			$arrivalTime = self::convertDateToTimeString($trajet['arrival_date_time']);
			$numeroTrain = $trajet['sections'][1]['display_informations']['headsign'];
			$gareDepart = $trajet['sections'][1]['from']['stop_point']['name'];
			$gareArrivee = $trajet['sections'][1]['to']['stop_point']['name'];

			//log::add('tter','debug','Found train '.$numeroTrain.' :'.$dateTimeDepart.' / '.$dateTimeArrivee.' - '.$gareDepart.' > '.$gareArrivee);
			
			$departureTimeBeforeCurrentTime = self::departureTimeBeforeCurrentTime($trajet['departure_date_time']);	
			$isValidJourney = FALSE;
			$isDisruption = FALSE;
			// si le train est indisponible 
			if ($trajet['status'] == 'NO_SERVICE'){
				$retard = 'supprimé';
				if($departureTimeBeforeCurrentTime == FALSE){
					$isValidJourney = TRUE;
				}
			}else{
				// sinon recherche des retards éventuels
				$retard = 'à l\'heure';
				$updatedTime = $departureTimeForComputeDelay;
				// récupération de l'ID de disruption du trajet (id du retard)
				$numdisrup = $trajet['sections'][1]['display_informations']['links'][0]['id'];

				// Si un ID de disruption est présent on va rechercher celui ci dans l'objet disruptions
				if($numdisrup != null || $numdisrup != '' ||$numdisrup != ' '){
					$disruptions = $responseJSON['disruptions'];

					foreach($disruptions as $disruption) {
						// si c'est la disruption de notre trajet 
						if ( $disruption['disruption_id'] == $numdisrup ) {
							
							// on parcours chaque arrêt impacté de la disruption pour trouver nos arrêt concerné
							foreach($disruption['impacted_objects'][0]['impacted_stops'] as $impactStop) {
								
								// si l'id de l'arret observé correspond à l'id de la gare de départ
								if($trajet['sections'][1]['from']['id'] == $impactStop['stop_point']['id']){

									$delayedDepartureTime = self::convertAmenededTimeToTimeString($impactStop['amended_departure_time']);
									log::add('tter','debug', 'amended departure time : '.$delayedDepartureTime);
									
									if(substr($impactStop['amended_departure_time'],0,4) >= $currentDate){
										$isValidJourney = TRUE;
										$delayedDepartureTimeForComputeDelay = $impactStop['amended_departure_time'];
										$causeOfDelayed = $impactStop['cause'];
										// calcul du retard
										$retard = 
											( substr($delayedDepartureTimeForComputeDelay,0,2) * 60 + substr($delayedDepartureTimeForComputeDelay,2,2) ) 
											- ( substr($departureTimeForComputeDelay,0,2) * 60 + substr($departureTimeForComputeDelay,2,2) );
									
										if ($retard == 0) {
											$retard = 'à l\'heure';
										} else {
											$retard = 'retard '.$retard.' min.';
										}
									}
									$isDisruption = TRUE;
								}

								// si l'id de l'arret observé correspond à l'id de la gare d'arrivée
								if($trajet['sections'][1]['to']['id'] == $impactStop['stop_point']['id']){
									$delayedArrivalTime = self::convertAmenededTimeToTimeString($impactStop['amended_arrival_time']);
								}
							}					
						}
					}
				}
				if($isDisruption == FALSE && $isValidJourney == FALSE && $departureTimeBeforeCurrentTime == FALSE){
					$isValidJourney = TRUE;
				}
			}		
			log::add('tter','debug', 'valid journey: '.$isValidJourney.' no diruption : '.$isNoDisruption.' before : '.$departureTimeBeforeCurrentTime);

			if($isValidJourney == TRUE){
				// store data for current train
				$trajets[$indexTrajet] = array(
					'numeroTrain' => $numeroTrain,
					'gareDepart' => $gareDepart,
					'gareArrivee' => $gareArrivee,
					'heureDepart' => $departureTime,
					'heureArrivee' => $arrivalTime,
					'dureeTrajet' => self::convertDurationToTimeString($trajet['duration']),
					'retard' => $retard,
					'causeOfDelayed' => $causeOfDelayed,
					'delayedDepartureTime' => $delayedDepartureTime,
					'delayedArrivalTime' => $delayedArrivalTime,
				);
				$indexTrajet++;
			}	
		
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

  public function getcurrentDateMinusOneHour(){
	$date = new DateTime();
	$currentTimestamp = $date->getTimestamp();
	$currentTimestampMinusOneHour = $currentTimestamp - 3600;
	return date("Ymd\TH:i",$currentTimestampMinusOneHour);
  }

  public function departureTimeBeforeCurrentTime($departureTime){
	$date = new DateTime();
	$currentTimestamp = $date->getTimestamp();
	$date->setDate(substr($departureTime,0,4),substr($departureTime,4,2),substr($departureTime,6,2));
	$date->setTime(substr($departureTime,9,2),substr($departureTime,11,2),substr($departureTime,13,2));
	$departureTimestamp = $date->getTimestamp();
	return $departureTimestamp < $currentTimestamp;
  }

}


 ?>
