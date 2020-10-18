<?php

/**
 * Copyright 2017 Open Infrastructure Foundation
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
final class ExpiredPoweredOpenStackImplementationEmailMessageSender implements IMessageSenderService
{

    /**
     * @param mixed $subject
     * @throws Exception
     */
    public function send($subject)
    {
        if(!is_array($subject)) return;
        if(!isset($subject['ExpiredOpenStackPoweredImplementations'])
            || !isset($subject['AboutExpiredOpenStackPoweredImplementations']) )
            return;

        $email = PermamailTemplate::get()->filter('Identifier', EXPIRED_POWERED_OPENSTACK_IMPLEMENTATION_EMAIL)->first();
        if(is_null($email)) throw new Exception(sprintf('Email Template %s does not exists on DB!', EXPIRED_POWERED_OPENSTACK_IMPLEMENTATION_EMAIL));

        $email = EmailFactory::getInstance()->buildEmail('noreply@openstack.org', EXPIRED_POWERED_OPENSTACK_IMPLEMENTATION_EMAIL_TO);

        $email->setUserTemplate(EXPIRED_POWERED_OPENSTACK_IMPLEMENTATION_EMAIL)
              ->populateTemplate
              (
                $subject
              )->send();

    }
}