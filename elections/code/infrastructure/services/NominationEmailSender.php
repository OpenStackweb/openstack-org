<?php
/**
 * Copyright 2018 OpenStack Foundation
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

final class NominationEmailSender implements IMessageSenderService
{

    /**
     * @param mixed $subject
     * @throws InvalidArgumentException
     * @return void
     */
    public function send($subject)
    {
        $candidate        = $subject['Candidate'];
        $current_election = $subject['CurrentElection'];

        $to = $candidate->Member()->Email;

        $subject = "You have been nominated in the " . $current_election->Name;
        $email = EmailFactory::getInstance()->buildEmail(CANDIDATE_NOMINATION_FROM_EMAIL, $to, $subject);
        $email->setTemplate('NominationEmail');
        // Gather Data to send to template
        $data["Candidate"] = $candidate;
        $data["Election"] = $current_election;
        $email->populateTemplate($data);
        $email->send();
    }
}