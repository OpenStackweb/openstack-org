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
final class AttendeeBulkQuery extends AbstractMergeBulkQuery
{

    /**
     * @return string[]
     */
    public function toSQL()
    {
        $primary_id = $this->primary_id;
        $dupe_id    = $this->dupe_id;
        $primary    = Member::get()->byID($primary_id);
        $dupe       = Member::get()->byID($dupe_id);
        $queries    = array();
        $summits    = Summit::get();
        foreach($summits as $summit)
        {
            if($dupe->isAttendee($summit->ID) && !$primary->isAttendee($summit->ID) )
            {
                // move
                array_push($queries, "UPDATE SummitAttendee SET MemberID = {$primary_id} WHERE MemberID = {$dupe_id} AND SummitID = {$summit->ID};");
            }
        }
        return $queries;
    }
}