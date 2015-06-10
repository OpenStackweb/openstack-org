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

/**
 * Class SapphireSpeakerRegistrationRequestRepository
 */
final class SapphireSpeakerRegistrationRequestRepository
    extends SapphireRepository
    implements ISpeakerRegistrationRequestRepository  {

    public function __construct(){
        parent::__construct(new SpeakerRegistrationRequest);
    }

    /**
     * @param string $token
     * @return ISpeakerRegistrationRequest
     */
    public function getByConfirmationToken($token)
    {
        $query = new QueryObject(new $this->entity_class);
        $query->addAndCondition(QueryCriteria::equal('ConfirmationHash', SpeakerRegistrationRequest::HashConfirmationToken($token)));
        return $this->getBy($query);
    }

    /**
     * @param string $token
     * @return bool
     */
    public function existsConfirmationToken($token)
    {
        $token = SpeakerRegistrationRequest::HashConfirmationToken($token);
        return intval(DB::query("SELECT COUNT(ID) FROM  SpeakerRegistrationRequest WHERE ConfirmationHash = '{$token}' ;")->value()) > 0;
    }

    /**
     * @param string $email
     * @return ISpeakerRegistrationRequest
     */
    public function getByEmail($email)
    {
        $query = new QueryObject(new $this->entity_class);
        $query->addAndCondition(QueryCriteria::equal('Email', $email));
        return $this->getBy($query);
    }
}