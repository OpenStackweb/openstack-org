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
class ExportSpeakerEmailsTypes extends BuildTask
{
    /**
     * @var string $title Shown in the overview on the {@link TaskRunner}
     * HTML or CLI interface. Should be short and concise, no HTML allowed.
     */
    protected $title = "Export Speaker Emails Types";

    /**
     * @var string $description Describe the implications the task has,
     * and the changes it makes. Accepts HTML formatting.
     */
    protected $description = 'Export to CVS files on asset folder the speaker emails to send';


    /**
     * Implement this method in the task subclass to
     * execute via the TaskRunner
     */
    public function run($request)
    {
        set_time_limit(0);


        $type = intval($request->getVar('type'));
        if(empty($type)) $type = 1;

        $speakers = PresentationSpeaker::get()->filter('SummitID', Summit::get_active()->ID);

        $only_accepted  = array();
        $accepted_rejected = array();
        $only_rejected  = array();
        $only_alternate = array();
        $accepted_alternate = array();
        $alternate_rejected = array();

        foreach($speakers as $speaker)
        {
            $rejected  = $speaker->UnacceptedPresentations();
            $alternate = $speaker->AlternatePresentations();
            $accepted  = $speaker->AcceptedPresentations();

            if($accepted->count() > 0 && $rejected->count() === 0 && $alternate->count() === 0)
            {
                // only accepted
                if(!$speaker->Member()) continue;
                $email = $speaker->Member()->Email;
                if(empty($email)) continue;
                foreach($accepted as $row)
                    $only_accepted[] = array
                    (
                        'PresentationID'    => $row->ID,
                        'PresentationTitle' => $row->Title,
                        'SpeakerEmail'      => $email,
                        'SpeakerID'         => $speaker->Member()->ID,
                        'Status'            => 'ACCEPTED',
                    );
                continue;
            }

            if($accepted->count() === 0 && $rejected->count() === 0 && $alternate->count() > 0)
            {
                // only alternated
                if(!$speaker->Member()) continue;
                $email = $speaker->Member()->Email;
                if(empty($email)) continue;
                foreach($alternate as $row)
                    $only_alternate[] = array
                    (
                        'PresentationID'    => $row->ID,
                        'PresentationTitle' => $row->Title,
                        'SpeakerEmail'      => $speaker->Member()->Email,
                        'SpeakerID'         => $speaker->Member()->ID,
                        'Status'            => 'ALTERNATE',
                    );
                continue;
            }

            if($accepted->count() === 0 && $rejected->count() > 0 && $alternate->count() === 0)
            {
                // only rejected
                if(!$speaker->Member()) continue;
                $email = $speaker->Member()->Email;
                if(empty($email)) continue;
                foreach($rejected as $row)
                    $only_rejected[] = array
                    (
                        'PresentationID'    => $row->ID,
                        'PresentationTitle' => $row->Title,
                        'SpeakerEmail'      => $speaker->Member()->Email,
                        'SpeakerID'         => $speaker->Member()->ID,
                        'Status'            => 'REJECTED',
                    );
                continue;
            }


            if($accepted->count() > 0 && $rejected->count() === 0 && $alternate->count() > 0)
            {
                if(!$speaker->Member()) continue;
                $email = $speaker->Member()->Email;
                if(empty($email)) continue;
                // only accepted
                foreach($accepted as $row)
                    $accepted_alternate[] = array
                    (
                        'PresentationID'    => $row->ID,
                        'PresentationTitle' => $row->Title,
                        'SpeakerEmail'      => $speaker->Member()->Email,
                        'SpeakerID'         => $speaker->Member()->ID,
                        'Status'            => 'ACCEPTED',
                    );

                // only alternated
                foreach($alternate as $row)
                    $accepted_alternate[] = array
                    (
                        'PresentationID'    => $row->ID,
                        'PresentationTitle' => $row->Title,
                        'SpeakerEmail'      => $speaker->Member()->Email,
                        'SpeakerID'         => $speaker->Member()->ID,
                        'Status'            => 'ALTERNATE',
                    );
                continue;
            }

            if($accepted->count() > 0 && $rejected->count() > 0 && $alternate->count() === 0)
            {
                if(!$speaker->Member()) continue;
                $email = $speaker->Member()->Email;
                if(empty($email)) continue;
                // accepted and rejected
                foreach($accepted as $row)
                    $accepted_rejected[] = array
                    (
                        'PresentationID'    => $row->ID,
                        'PresentationTitle' => $row->Title,
                        'SpeakerEmail'      => $speaker->Member()->Email,
                        'SpeakerID'         => $speaker->Member()->ID,
                        'Status'            => 'ACCEPTED',
                    );

                foreach($rejected as $row)
                    $accepted_rejected[] = array
                    (
                        'PresentationID'    => $row->ID,
                        'PresentationTitle' => $row->Title,
                        'SpeakerEmail'      => $speaker->Member()->Email,
                        'SpeakerID'         => $speaker->Member()->ID,
                        'Status'            => 'REJECTED',
                    );
                continue;
            }

            if($accepted->count() === 0 && $rejected->count() > 0 && $alternate->count() > 0)
            {

                if(!$speaker->Member()) continue;
                $email = $speaker->Member()->Email;
                if(empty($email)) continue;
                foreach($alternate as $row)
                    $alternate_rejected[] = array
                    (
                        'PresentationID'    => $row->ID,
                        'PresentationTitle' => $row->Title,
                        'SpeakerEmail'      => $speaker->Member()->Email,
                        'SpeakerID'         => $speaker->Member()->ID,
                        'Status'            => 'ALTERNATE',
                    );

                foreach($rejected as $row)
                    $alternate_rejected[] = array
                    (
                        'PresentationID'    => $row->ID,
                        'PresentationTitle' => $row->Title,
                        'SpeakerEmail'      => $speaker->Member()->Email,
                        'SpeakerID'         => $speaker->Member()->ID,
                        'Status'            => 'REJECTED',
                    );
                continue;
            }

        }
        switch($type)
        {
            case 1:
                $filename = 'only_accepted_speakers.cvs';
                $result = $only_accepted;
                break;
            case 2:
                $filename = 'only_alternate_speakers.cvs';
                $result = $only_alternate;
                break;
            case 3:
                $filename = 'only_rejected_speakers.cvs';
                $result = $only_rejected;
                break;
            case 4:
                $filename = 'accepted_alternate_speakers.cvs';
                $result = $accepted_alternate;
                break;
            case 5:
                $filename = 'accepted_rejected_speakers.cvs';
                $result = $accepted_rejected;
                break;
            case 6:
                $filename = 'alternate_rejected_speakers.cvs';
                $result = $alternate_rejected;
                break;
        }
        CSVExporter::getInstance()->export($filename, $result);
    }
}