<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class MergeSpeakerBulkQuery
 */
final class MergeSpeakerBulkQuery
    extends AbstractMergeBulkQuery {

    /**
     * @return string
     */
    public function toSQL()
    {
        $primary_id = $this->primary_id;
        $dupe_id    = $this->dupe_id;

        return array(
            "UPDATE Speaker SET AdminID = {$primary_id} WHERE AdminID=  {$dupe_id};",
            "UPDATE Speaker SET OldMemberID = {$primary_id} WHERE OldMemberID=  {$dupe_id};",
            "UPDATE Speaker SET MemberID = {$primary_id} WHERE MemberID =  {$dupe_id};",
            "UPDATE SpeakerVote SET VoterID = {$primary_id} WHERE VoterID = {$dupe_id};",
            "UPDATE Voter SET MemberID = {$primary_id} WHERE MemberID = {$dupe_id};",
            "UPDATE Talk SET OwnerID = {$primary_id} WHERE OwnerID = {$dupe_id};");

    }
}