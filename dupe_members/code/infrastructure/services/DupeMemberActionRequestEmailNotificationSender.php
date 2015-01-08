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
 * Class DupeMemberActionRequestEmailNotificationSender
 */
final class DupeMemberActionRequestEmailNotificationSender
    implements IDupeMemberActionRequestNotificationSender{

    /**
     * @var IDupeMemberActionAccountRequestRepository
     */
    private $delete_request_repository;

    /**
     * @var IDupeMemberActionAccountRequestRepository
     */
    private $merge_request_repository;

    /**
     * @param IDupeMemberActionAccountRequestRepository $delete_request_repository
     * @param IDupeMemberActionAccountRequestRepository $merge_request_repository
     */
    public function __construct(IDupeMemberActionAccountRequestRepository $delete_request_repository,
                                IDupeMemberActionAccountRequestRepository $merge_request_repository) {

        $this->delete_request_repository = $delete_request_repository;
        $this->merge_request_repository  = $merge_request_repository;
    }

    public function sendMergeNotification(IDupeMemberMergeRequest $request)
    {
        $dupe     = $request->getDupeAccount();
        $email_to = $dupe->getEmail();

        $email = EmailFactory::getInstance()->buildEmail(DUPE_EMAIL_FROM, $email_to  , "You Have Requested to Merge Openstack Duplicated Account");

        $template_data = array(

        );

        $email->setTemplate('DupeMembers_MergeAccountEmail');

        do{
            $token = $request->generateConfirmationHash();
        } while ($this->merge_request_repository->existsConfirmationToken($token));

        $template_data['ConfirmationLink'] = sprintf('%s/dupes-members/%s/merge', Director::protocolAndHost(), $token);

        $email->populateTemplate($template_data);

        $email->send();
    }

    public function sendDeleteNotification(IDupeMemberDeleteRequest $request)
    {
        $dupe     = $request->getDupeAccount();
        $email_to = $dupe->getEmail();

        $email = EmailFactory::getInstance()->buildEmail(DUPE_EMAIL_FROM, $email_to  , "You Have Requested to Delete Openstack Duplicated Account");

        $template_data = array(
            'FirstName'   => $dupe->getFirstName(),
            'LastName'    => $dupe->getLastName(),
            'DupeAccount' => $email_to,
        );

        $email->setTemplate('DupeMembers_DeleteAccountEmail');

        do{
            $token = $request->generateConfirmationHash();
        } while ($this->delete_request_repository->existsConfirmationToken($token));

        $template_data['ConfirmationLink'] = sprintf('%s/dupes-members/%s/delete', Director::protocolAndHost(), $token);

        $email->populateTemplate($template_data);

        $email->send();
    }
}