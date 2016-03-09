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
 * Class PresentationSpeakerSummitAssistanceConfirmationRequest
 */
class PresentationSpeakerSummitAssistanceConfirmationRequest extends DataObject
{
    private static $db = array
    (
        'OnSitePhoneNumber'     => 'Text',
        'RegisteredForSummit'   => 'Boolean',
        'IsConfirmed'           => 'Boolean',
        'ConfirmationDate'      => 'SS_Datetime',
        'ConfirmationHash'      => 'Text',
        'CheckedIn'             => 'Boolean',
    );

    private static $has_one = array
    (
        'Speaker' => 'PresentationSpeaker',
        'Summit'  => 'Summit',
    );

    static $indexes = array(
        'Speaker_Summit' => array('type'=>'unique', 'value'=>'SpeakerID,SummitID ')
    );

    /**
     * @var string
     */
    private $token;

    /**
     * @param string $token
     * @return bool
     * @throws InvalidHashSpeakerRegistrationRequestException
     * @throws SpeakerRegistrationRequestAlreadyConfirmedException
     */
    public function confirm($token)
    {
        $original_hash = $this->getField('ConfirmationHash');
        if($this->IsConfirmed) throw new SpeakerRegistrationRequestAlreadyConfirmedException;
        if(self::HashConfirmationToken($token) === $original_hash){
            $this->IsConfirmed      = true;
            $this->ConfirmationDate = SS_Datetime::now()->Rfc2822();
            return true;
        }
        throw new InvalidHashSpeakerRegistrationRequestException;
    }

    /**
     * @return string
     */
    public function generateConfirmationToken() {
        $generator  = new RandomGenerator();
        $this->token = $generator->randomToken();
        $hash      = self::HashConfirmationToken($this->token);
        $this->setField('ConfirmationHash',$hash);
        return $this->token;
    }

    public static function HashConfirmationToken($token){
        return md5($token);
    }

    public function alreadyConfirmed()
    {
        return $this->IsConfirmed;
    }
}