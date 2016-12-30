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
 * Class SpeakerBureauManager
 */
final class SpeakerBureauManager
{
    /**
     * @var IEntityRepository
     */
    private $speaker_repository;
    /**
     * @var ITransactionManager
     */
    private $tx_manager;
    /**
     * @var IEntityRepository
     */
    private $email_repository;
    /**
     * @var ISpeakerContactEmailFactory
     */
    private $contact_email_factory;


    /**
     * @param IEntityRepository $speaker_repository
     * @param IEntityRepository $email_repository
     * @param ISpeakerContactEmailFactory $contact_email_factory
     * @param ITransactionManager $tx_manager
     */
    public function __construct(
        IEntityRepository $speaker_repository,
        IEntityRepository $email_repository,
        ISpeakerContactEmailFactory $contact_email_factory,
        ITransactionManager $tx_manager
    ) {

        $this->speaker_repository = $speaker_repository;
        $this->email_repository = $email_repository;
        $this->contact_email_factory = $contact_email_factory;
        $this->tx_manager = $tx_manager;
    }

    /**
     * @param int $speakerID
     * @param array $form_data
     */
    public function sendEmail($speaker_id, $form_data)
    {
        $email_repository = $this->email_repository;
        $speaker_repository = $this->speaker_repository;
        $contact_email_factory = $this->contact_email_factory;

        $this->tx_manager->transaction(function () use (
            $speaker_repository,
            $email_repository,
            $contact_email_factory,
            $speaker_id,
            $form_data
        ) {

            $speaker = $speaker_repository->getById($speaker_id);
            $contact_email_data = $contact_email_factory->buildSpeakerContactEmail($form_data, $speaker);
            $speaker_email = $speaker->getEmail();

            if(!$speaker_email) throw new EntityValidationException('email not valid');

            $email = EmailFactory::getInstance()->buildEmail($contact_email_data->OrgEmail,
                $speaker_email,
                "Openstack Web Contact: " . $contact_email_data->OrgName);


            $email->setTemplate('SpeakerContactEmail');
            $email->populateTemplate(array(
                'EmailData' => $contact_email_data
            ));

            $contact_email_data->EmailSent = $email->send();

            $email_repository->add($contact_email_data);
        });
    }


} 