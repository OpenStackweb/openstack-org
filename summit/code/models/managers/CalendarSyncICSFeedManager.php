<?php
/**
 * Copyright 2019 OpenStack Foundation
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
 * Class CalendarSyncICSFeedManager
 */
final class CalendarSyncICSFeedManager implements ICalendarSyncICSFeedManager
{

    /**
     * @param string $token
     * @throws NotFoundEntityException
     * @throws ValidationException
     * @return string
     */
    public function buildFeed(string $token): string
    {
        $share_info = PersonalCalendarShareInfo::get()->filter("Hash", $token)->first();

        if(is_null($share_info))
            throw new NotFoundEntityException(PersonalCalendarShareInfo::class);

        if($share_info->Revoked)
            throw new ValidationException();

        $member = $share_info->Owner();
        $summit = $share_info->Summit();
        $timeZone = $summit->getTimeZone();
        $vCalendar = \CalDAVClient\Facade\Utils\ICalTimeZoneBuilder::build($timeZone, $summit->Title, true);
        foreach($member->getScheduleBySummit($summit->ID) as $summitEvent){
            $local_start_time = new DateTime($summitEvent->getLocalStartDate(), $timeZone);
            $local_end_time   = new DateTime($summitEvent->getLocalEndDate(), $timeZone);
            $vEvent            = new \Eluceo\iCal\Component\Event($summitEvent->ID);

            $vEvent
                ->setCreated(new DateTime())
                ->setDtStart($local_start_time)
                ->setDtEnd($local_end_time)
                ->setNoTime(false)
                ->setSummary($summitEvent->Title)
                ->setDescription(strip_tags($summitEvent->Abstract))
                ->setDescriptionHTML($summitEvent->Abstract);

            if($timeZone->getName() == 'UTC'){
                $vEvent->setUseUtc(true)
                    ->setUseTimezone(false);
            }
            else{
                $vEvent->setUseUtc(false)
                    ->setUseTimezone(true);
            }

            if(!empty($summitEvent->getLocationTitle())){
                $geo = sprintf("%s;%s", $summitEvent->getLocationLat(), $summitEvent->getLocationLng());
                $vEvent->setLocation($summitEvent->getLocationTitle(), $summitEvent->getLocationTitle(), $geo);
            }

            $vCalendar->addComponent($vEvent);
        }

        return $vCalendar->render();

    }
}