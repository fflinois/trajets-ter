<?php

class SncfApi {

	public function retrieveJourneys($apiKey, $stopAreaFromId, $stopAreaToId) {
		log::add('ter','debug','calling sncf api with :'.$apiKey.' / '.$stopAreaFromId.' / '.$stopAreaToId);
		date_default_timezone_set("Europe/Paris");
    $currentDate = date("Ymd\TH:i");
	 // $currentDate = "20170509T18:40";
    // retrieve raw data from SNCF Api
    $query = 'https://'.$apiKey.'@api.sncf.com/v1/coverage/sncf/journeys?from='.$stopAreaFromId.'&to='.$stopAreaToId.'&datetime='.$currentDate.'&datetime_represents=departure&min_nb_journeys=4';
    log::add('ter','debug',$query);
    $response = file_get_contents($query);
		log::add('ter','debug','API response :'.$response);
		$json = json_decode($response, true);

    // go through each journey
    $journeys = [];
    $iTrain = 0;
    foreach($json['journeys'] as $trains) {
      $departureDate = $trains['departure_date_time'];
      $departureTime = substr($departureDate,9,4);
      $arrivalDate = $trains['arrival_date_time'];
      $arrivalTime = substr($arrivalDate,9,4);
      $duration = gmdate("Hi", strtotime($arrivalDate)-strtotime($departureDate));
      $trainNumber = $trains['sections'][1]['display_informations']['headsign'];
      $gareFrom = $trains['sections'][1]['from']['stop_point']['name'];
      $gareTo = $trains['sections'][1]['to']['stop_point']['name'];

			log::add('ter','debug','Found train '.$trainNumber.' :'.$departureDate.' / '.$arrivalDate.' - '.$gareFrom.' > '.$gareTo);

      // is train available ?
      if ($trains['status'] == 'NO_SERVICE'){
      	continue;
      }

			// retrieve disruptions if any...
        $retard = 'aucun';
        $updatedTime = $departureTime;
        $numdisrup = $trains['sections'][1]['display_informations']['links'][0]['id'];
        log::add('ter','debug','Disruption ID '.$numdisrup);

        $disruptions = $json['disruptions'];
        foreach($disruptions as $disruption) {
            if ( $disruption['disruption_id']== $numdisrup ) {
              log::add('ter','debug','Disruption ID '.$numdisrup. ' has been found!');
              log::add('ter','debug','Search for impacted departure '.$departureTime);
              // go through each impacted stops
              foreach($disruption['impacted_objects'][0]['impacted_stops'] as $impactStop) {
                log::add('ter','debug','testing departure '.substr($impactStop['base_departure_time'],0,4));

                  if ( substr($impactStop['base_departure_time'],0,4) == $departureTime ) {
                      $updatedTime = $impactStop['amended_departure_time'];
                      // compute delay
                      $retard = ( substr($updatedTime,0,2) * 60 + substr($updatedTime,2,2) ) - ( substr($departureTime,0,2) * 60 + substr($departureTime,2,2) ).' minutes';
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

			// store data for current train
    	$journeys[$iTrain] = array(
				'trainNumber' => $trainNumber,
			  'gareFrom' => $gareFrom,
			  'gareTo' => $gareTo,
			  'departureDate' => $departureDate,
			  'departureTime' => $departureTime,
			  'arrivalDate' => $arrivalDate,
			  'arrivalTime' => $arrivalTime,
			  'duration' => $duration,
			  'retard' => $retard,
			  'updatedDepartureTime' => $updatedTime
			);
    	$iTrain++;
  	}
		return $journeys;
	}
}

 ?>
