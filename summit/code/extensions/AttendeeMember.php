<?php

/**
 * Copyright 2015 OpenStack Foundation
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
final class AttendeeMember extends DataExtension implements IAttendeeMember
{
    private static $has_many = array
    (
        'SummitAttendance' => 'SummitAttendee'
    );

    /**
     * @param int|null $summit_id
     * @return bool
     */
    public function isAttendee($summit_id = null)
    {
        $attendee = $this->getSummitAttendee($summit_id);

        return ($attendee ? true : false);
    }

    /**
     * @param int|null $summit_id
     * @return ISummitAttendee
     */
    public function getSummitAttendee($summit_id = null)
    {
        $attendee = $this->owner->SummitAttendance();
        if (!is_null($summit_id)) {
            $attendee = $attendee->filter(array
            (
                'SummitID' => $summit_id
            ));
        }
        return $attendee->first();
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->owner->getField('ID');
    }

    /**
     * @return ISummitAttendee|null
     */
    public function getCurrentSummitAttendee()
    {
        $current_summit = Summit::CurrentSummit();
        if ($current_summit) {
            return $this->getSummitAttendee($current_summit->ID);
        }

        return $this->getUpcomingSummitAttendee();
    }

    /**
     * @return ISummitAttendee|null
     */
    public function getUpcomingSummitAttendee()
    {
        $upcoming_summit = Summit::ActiveSummit();
        if ($upcoming_summit) {
            return $this->getSummitAttendee($upcoming_summit->ID);
        }

        return null;
    }

    public function onBeforeDelete()
    {
        // Remove attendees
        foreach ($this->owner->SummitAttendance() as $attendee) {
            $attendee->delete();
        }
    }
}