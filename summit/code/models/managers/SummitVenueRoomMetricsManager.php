<?php
/**
 * Copyright 2016 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class SummitVenueRoomMetricsManager
 */
final class SummitVenueRoomMetricsManager implements ISummitVenueRoomMetricsManager
{
    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var ISummitRepository
     */
    private $summit_repository;

    /**
     * @var ITCPCloudRestApi
     */
    private $rest_api;

    public function __construct
    (
        ISummitRepository $summit_repository,
        ITCPCloudRestApi $rest_api,
        ITransactionManager $tx_manager)
    {
        $this->tx_manager        = $tx_manager;
        $this->rest_api          = $rest_api;
        $this->summit_repository = $summit_repository;
    }

    public function ingest($summit_id)
    {
        $this->tx_manager->transaction(function() use($summit_id){

            $summit = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) return;

            foreach($summit->getVenues() as $venue){

                if($venue->getTypeName()!= SummitVenue::TypeName ) continue;

                foreach($venue->getRooms() as $room){
                    foreach($room->getMetricTypes() as $metric){

                        $sample_data = $this->rest_api->getSamplesDataFromEndpointSinceTimestamp
                        (
                            $metric->getEndpointUrl(),
                            $metric->getLastSampleTimeStamp()
                        );

                        if(count($sample_data) == 0) continue;

                        foreach($sample_data as $entry)
                        {
                            if(count($entry) != 2) continue;
                            $metric->addSample($entry[0], $entry[1]);
                        }
                    }
                }
            }
        });
    }
}