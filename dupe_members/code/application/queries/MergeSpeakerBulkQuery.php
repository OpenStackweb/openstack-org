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

        return array
        (
            "UPDATE PresentationSpeaker SET MemberID = {$primary_id} WHERE MemberID=  {$dupe_id};",
            "UPDATE SummitSelectedPresentationList SET MemberID = {$primary_id} WHERE MemberID=  {$dupe_id};",
            "UPDATE SummitSelectedPresentation SET MemberID = {$primary_id} WHERE MemberID =  {$dupe_id};",
            "UPDATE Presentation_Speakers SET PresentationSpeakerID = {$primary_id} WHERE PresentationSpeakerID = {$dupe_id};",
            "UPDATE PresentationVote SET MemberID = {$primary_id} WHERE MemberID = {$dupe_id};",
            "UPDATE SummitTrackChair SET MemberID = {$primary_id} WHERE MemberID = {$dupe_id};",
            "UPDATE VideoPresentation SET MemberID = {$primary_id} WHERE MemberID = {$dupe_id};"
        );

    }
}