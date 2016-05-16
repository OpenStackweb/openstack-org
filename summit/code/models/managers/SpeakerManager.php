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
 * Class SpeakerManager
 */
final class SpeakerManager implements ISpeakerManager
{

    /**
     * @var ISummitRepository
     */
    private $summit_repository;

    /**
     * @var ISpeakerRepository
     */
    private $speaker_repository;

    /**
     * @var IMemberRepository
     */
    private $member_repository;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * SpeakerManager constructor.
     * @param ISummitRepository $summit_repository
     * @param ISpeakerRepository $speaker_repository
     * @param IMemberRepository $member_repository
     * @param ITransactionManager $tx_manager
     */
    public function __construct
    (
        ISummitRepository $summit_repository,
        ISpeakerRepository $speaker_repository,
        IMemberRepository $member_repository,
        ITransactionManager $tx_manager
    )
    {
        $this->summit_repository       = $summit_repository;
        $this->speaker_repository      = $speaker_repository;
        $this->member_repository       = $member_repository;
        $this->tx_manager              = $tx_manager;
    }
    /**
     * @param Member $member
     * @return void
     */
    public function ensureSpeakerProfile(Member $member)
    {
        $speaker = $member->getSpeakerProfile();

        if (!$speaker) {
            $speaker = PresentationSpeaker::create
            (
                array
                (
                    'MemberID'  => Member::currentUserID(),
                    'FirstName' => Member::currentUser()->FirstName,
                    'LastName'  => Member::currentUser()->Surname
                )
            );
            $speaker->write();
        }
    }

    /**
     * @param $term
     * @param bool $obscure_email
     * @return array
     */
    public function getSpeakerByTerm($term, $obscure_email = true)
    {
        $data = $this->speaker_repository->searchByTerm($term);
        $res  = array();

        foreach($data as $row)
        {
            $entry               = array();
            $speaker_id          = intval($row['speaker_id']);
            $member_id           = intval($row['member_id']);
            $entry['name']       = sprintf("%s %s", $row['firstname'], $row['surname']);

            if(empty($entry['name'])) continue;

            $entry['title']      = '';
            $entry['company']    = '';
            $entry['speaker_id'] = $speaker_id;
            $entry['member_id']  = $member_id;

            if($member_id > 0)
            {
                $member       = $this->member_repository->getById($member_id);
                $entry['pic'] = $member->ProfilePhotoUrl();
                $company      = $member->getCurrentCompany();

                if(!empty($company))
                    $entry['company'] = $company;
            }

            if($speaker_id > 0)
            {
                $speaker      = $this->speaker_repository->getById($speaker_id);
                $entry['pic'] = $speaker->ProfilePhoto();

                if(!empty($speaker->Title))
                    $entry['title'] = $speaker->Title;
            }

            if($obscure_email)
                $entry['email'] = preg_replace('/(?<=.).(?=.*.@)/u','*',$row['email']);

            array_push($res, $entry);
        }

        return $res;
    }
}