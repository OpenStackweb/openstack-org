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
final class FullScheduleViewModelMapper implements IViewModelMapper
{

    /**
     * @param array $params
     * @return mixed
     */
    public function map(array $params)
    {
        $schedule  = $params[0];
        $sort      = $params[1];
        $events    = [];
        $index     = '';
        $sort_list = false;

        foreach ($schedule as $event) {

            switch ($sort) {
                case 'day':
                    $index = $event->getDayLabel();
                    break;
                case 'track':
                    if (!$event->isPresentation() || !$event->Category() || !$event->Category()->Title) {
                        continue 2;
                    }
                    $index     = $event->Category()->Title;
                    $sort_list = true;
                    break;
                case 'event_type':
                    $index = $event->Type->Type;
                    $sort_list = true;
                    break;
            }

            if (!isset($events[$index])) $events[$index] = array();

            $events[$index][] = [
                'id'          => $event->ID,
                'start_time'  => $event->getStartTime(),
                'end_time'    => $event->getEndTime(),
                'title'       => $event->Title,
                'room'        => $event->getLocationNameNice(),
                'total'       => $event->Attendees()->Count(),
                'capacity'    => $event->getLocationCapacity(),
                'rsvp'        => $event->getRSVPLink()
            ];

            if ($sort_list) ksort($events);
        }

        return $events;
    }
}