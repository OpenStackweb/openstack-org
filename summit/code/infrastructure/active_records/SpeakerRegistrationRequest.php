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
 * Class SpeakerRegistrationRequest
 */
class SpeakerRegistrationRequest
    extends DataObject
    implements ISpeakerRegistrationRequest {

    const ConfirmationTokenParamName = 'spk_reg_t';

    private static $db = array(
        'IsConfirmed'      => 'Boolean',
        'Email'            => 'Varchar(254)',
        'ConfirmationDate' => 'SS_Datetime',
        'ConfirmationHash' => 'Text',
    );

    private static $has_one = array(
        'Proposer' => 'Member',
        'Speaker'  => 'PresentationSpeaker',
    );

    /**
     * @var string
     */
    private $token;

    private static $has_many_many = array (

    );

    private static $belongs_to = array(

    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

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
    public function proposedSpeakerEmail()
    {
        return $this->getField('Email');
    }

    /**
     * @return string
     */
    public function generateConfirmationToken() {
        $generator  = new RandomGenerator();
        $this->token = $generator->randomToken();
        $hash      = self::HashConfirmationToken($this->token);
        $this->setField('ConfirmationHash',$hash);
        Session::set(self::SpeakerRegistrationRequest.'_'. $this->Speaker()->ID, $this->token);
        return $this->token;
    }

    public static function HashConfirmationToken($token){
        return md5($token);
    }

    /**
     * @return string
     */
    public function getToken()
    {
        if(empty($this->token)){
            $this->token = Session::get(self::SpeakerRegistrationRequest.'_'. $this->Speaker()->ID);
        }
        return $this->token;
    }

    /**
     * @return string
     */
    public function proposedSpeakerFirstName()
    {
        return $this->Speaker()->ID > 0 ? $this->Speaker()->FirstName: '';
    }

    /**
     * @return string
     */
    public function proposedSpeakerLastName()
    {
        return $this->Speaker()->ID > 0 ? $this->Speaker()->LastName: '';
    }

    /**
     * @return IPresentationSpeaker
     */
    public function associatedSpeaker()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Speaker')->getTarget();
    }

    /**
     * @return bool
     */
    public function alreadyConfirmed()
    {
        return $this->IsConfirmed;
    }
}