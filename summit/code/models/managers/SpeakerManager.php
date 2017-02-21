<?php
/**
 * Copyright 2017 OpenStack Foundation
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
     * @var ISpeakerRegistrationRequestManager
     */
    private $speaker_registration_request_manager;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * SpeakerManager constructor.
     * @param ISummitRepository $summit_repository
     * @param ISpeakerRepository $speaker_repository
     * @param IMemberRepository $member_repository
     * @param ISpeakerRegistrationRequestManager $speaker_registration_request_manager
     * @param ITransactionManager $tx_manager
     */
    public function __construct
    (
        ISummitRepository $summit_repository,
        ISpeakerRepository $speaker_repository,
        IMemberRepository $member_repository,
        ISpeakerRegistrationRequestManager $speaker_registration_request_manager,
        ITransactionManager $tx_manager
    )
    {
        $this->summit_repository                    = $summit_repository;
        $this->speaker_repository                   = $speaker_repository;
        $this->member_repository                    = $member_repository;
        $this->speaker_registration_request_manager = $speaker_registration_request_manager;
        $this->tx_manager                           = $tx_manager;
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
        $data = $this->speaker_repository->searchByTermActive($term);
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

    /**
     * @param ISummit $summit
     * @param array $speaker_data
     * @param IMessageSenderService $speaker_creation_email_sender
     * @return IPresentationSpeaker
     */
    public function createSpeaker(ISummit $summit, array $speaker_data, IMessageSenderService $speaker_creation_email_sender)
    {

        return $this->tx_manager->transaction(function () use
        (
            $summit,
            $speaker_data,
            $speaker_creation_email_sender
        ) {

            $speaker   = PresentationSpeaker::create();
            $member_id = 0;
            if(!isset($speaker_data['email']) && !isset($speaker_data['member_id']))
                throw
                new EntityValidationException
                ("you must provide an email or a member_id in order to create a speaker!");

            if(isset($speaker_data['member_id']) && intval($speaker_data['member_id']) > 0){
                $member_id   = intval($speaker_data['member_id']);
                $old_speaker = $this->speaker_repository->getByMemberID($member_id);
                if(!is_null($old_speaker))
                    throw new EntityValidationException
                    (
                        sprintf
                        (
                            "Member %s already has assigned an speaker!",
                            $member_id
                        )
                    );
            }

            $speaker->Title          = trim($speaker_data['title']);
            $speaker->FirstName      = trim($speaker_data['first_name']);
            $speaker->LastName       = trim($speaker_data['last_name']);
            $speaker->IRCHandle      = trim($speaker_data['twitter_name']);
            $speaker->TwitterName    = trim($speaker_data['irc_name']);
            $speaker->MemberID       = $member_id;
            $speaker->CreatedFromAPI = true;
            $this->speaker_repository->add($speaker);
            $speaker->write();

            if($member_id === 0 && isset($speaker_data['email'])){
                $email  = trim($speaker_data['email']);
                $member = $this->member_repository->findByEmail($email);
                if(is_null($member)){
                    // we need to create a registration request
                    $request = $this->speaker_registration_request_manager->register($speaker, $email);
                    $request->SpeakerID = $speaker->ID;
                    $request->write();
                    $speaker->RegistrationRequestID = $request->ID;
                    $speaker->write();
                }
                else
                {
                    $old_speaker = $this->speaker_repository->getByMemberID($member->getIdentifier());
                    if(!is_null($old_speaker))
                        throw new EntityValidationException
                        (
                            sprintf
                            (
                                "Member %s already has assigned an speaker!",
                                $member->getIdentifier()
                            )
                        );
                    $speaker->MemberID = $member->getIdentifier();
                    $speaker->write();
                }
            }

            // send email to speaker so he can register as a member
            $speaker_creation_email_sender->send(['Speaker' => $speaker]);

            $onsite_phone = trim($speaker_data['onsite_phone']);
            if(!empty($onsite_phone)) {
                $summit_assistance = $speaker->createAssistanceFor($summit->getIdentifier());
                $summit_assistance->OnSitePhoneNumber = $onsite_phone;
                $summit_assistance->write();
            }

            return $speaker;
        });
    }

    /**
     * @param ISummit $summit
     * @param array $speaker_data
     * @return IPresentationSpeaker
     */
    public function updateSpeaker(ISummit $summit, array $speaker_data)
    {

        return $this->tx_manager->transaction(function () use ($summit, $speaker_data) {
            $speaker_id = intval($speaker_data['speaker_id']);
            $speaker    = $this->speaker_repository->getById($speaker_id);
            if(is_null($speaker)) throw new NotFoundEntityException('PresentationSpeaker');
            $member_id            = intval($speaker_data['member_id']);
            if($member_id > 0)
            {
                $old_speaker = $this->speaker_repository->getByMemberID($member_id);
                if($old_speaker && $old_speaker->getIdentifier() !== $speaker_id)
                    throw new EntityValidationException
                    (
                        sprintf
                        (
                            "Member %s already has assigned an speaker!",
                            $member_id
                        )
                    );
            }

            $speaker->Title       = trim($speaker_data['title']);
            $speaker->FirstName   = trim($speaker_data['first_name']);
            $speaker->LastName    = trim($speaker_data['last_name']);
            $speaker->Bio         = trim($speaker_data['bio']);
            $speaker->IRCHandle   = trim($speaker_data['twitter_name']);
            $speaker->TwitterName = trim($speaker_data['irc_name']);
            $speaker->PhotoID     = ($speaker_data['picture_id'] != 0) ? intval($speaker_data['picture_id']) : $speaker->PhotoID;

            if($speaker->MemberID > 0  && $member_id == 0)
                throw new EntityValidationException
                (
                    sprintf('you cant leave Speaker %s without associated Member!', $speaker_id)
                );

            $speaker->MemberID    = $member_id;

            // set email
            if ($speaker->MemberID > 0) {
                $speaker->Member()->Email = trim($speaker_data['email']);
                $speaker->Member()->write();
            } else {
                $speaker->RegistrationRequest()->Email = trim($speaker_data['email']);
                $speaker->RegistrationRequest()->write();
            }

            $onsite_phone = trim($speaker_data['onsite_phone']);
            $reg_code     = trim($speaker_data['reg_code']);

            if(!empty($onsite_phone)) {
                $summit_assistance = $speaker->getAssistanceFor($summit->getIdentifier());
                if(is_null($summit_assistance)){
                    $summit_assistance = $speaker->createAssistanceFor($summit->getIdentifier());
                }
                $summit_assistance->OnSitePhoneNumber = $onsite_phone;
                $summit_assistance->write();
            }

            if(!empty($reg_code)){
                $speaker->registerSummitPromoCodeByValue($reg_code, $summit);
            }
            return $speaker;

        });
    }

    /**
     * @param ISummit $summit
     * @param $speaker_id
     * @param $tmp_file
     * @return BetterImage
     */
    public function uploadSpeakerPic(ISummit $summit, $speaker_id, $tmp_file)
    {

        return $this->tx_manager->transaction(function () use ($summit, $speaker_id, $tmp_file) {
            $speaker_id = intval($speaker_id);
            $speaker    = $this->speaker_repository->getById($speaker_id);
            if(is_null($speaker)) throw new NotFoundEntityException('PresentationSpeaker');

            $image = new BetterImage();
            $upload = new Upload();
            $validator = new Upload_Validator();
            $validator->setAllowedExtensions(array('png','jpg','jpeg','gif'));
            $validator->setAllowedMaxFileSize(800*1024); // 300Kb
            $upload->setValidator($validator);

            if (!$upload->loadIntoFile($tmp_file,$image,'profile-images')) {
                throw new EntityValidationException($upload->getErrors());
            }

            $image->write();

            return $image;

        });
    }

    /**
     * @param ISummit $summit
     * @param ISummit $speaker_id_1
     * @param ISummit $speaker_id_2
     * @param array $data
     * @return array
     */
    public function mergeSpeakers(ISummit $summit, $speaker_id_1, $speaker_id_2, array $data)
    {

        $changes = $this->tx_manager->transaction(function () use ($summit, $data, $speaker_id_1, $speaker_id_2) {

            $speaker_1 = $this->speaker_repository->getById($speaker_id_1);
            $speaker_2 = $this->speaker_repository->getById($speaker_id_2);
            if(is_null($speaker_1) || is_null($speaker_2)) throw new NotFoundEntityException('PresentationSpeaker');

            $changes = array();

            foreach ($data as $field => $speaker_id) {

                if ($speaker_1->ID != $speaker_id) {
                    if ($field == 'Email') {
                        if ($speaker_1->RegistrationRequest()->Exists()) {
                            $speaker_1->RegistrationRequest()->Email = $speaker_2->RegistrationRequest()->Email;
                        } else {
                            $speaker_1->RegistrationRequestID = $speaker_2->RegistrationRequestID;
                        }
                    } elseif (is_callable(array($speaker_1, $field)) && $speaker_1->hasMethod($field)){
                        $speaker_1->$field()->setByIDList($speaker_2->$field()->getIDList());
                    } else {
                        $speaker_1->$field = $speaker_2->$field;
                    }

                    $changes[] = $field;
                }
            }

            // DELETE SPEAKER 2
            $this->speaker_repository->delete($speaker_2);
            return $changes;
        });

        return $changes;

    }

}