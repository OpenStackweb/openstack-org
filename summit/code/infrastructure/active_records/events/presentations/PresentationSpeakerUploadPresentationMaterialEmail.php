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
class PresentationSpeakerUploadPresentationMaterialEmail extends DataObject
{
    static $db = [
        'SentDate'     => 'SS_Datetime',
        'IsRedeemed'   => 'Boolean',
        'RedeemedDate' => 'SS_Datetime',
        'Hash'         => 'Text',
    ];

    static $has_one = [
        'Summit'  => 'Summit',
        'Speaker' => 'PresentationSpeaker',
    ];

    private static $indexes = [
        'Summit_Speaker_IDX' => [
            'type'  => 'unique',
            'value' => '"SummitID","SpeakerID"'
        ]
    ];

    /**
     * @var string
     */
    private $token;

    /**
     * @param $token
     * @return bool
     * @throws InvalidHashPresentationSpeakerUploadPresentationMaterialEmailException
     * @throws PresentationSpeakerUploadPresentationMaterialEmailAlreadyRedeemedException
     */
    public function redeem($token)
    {
        $original_hash = $this->getField('Hash');
        if($this->IsRedeemed) throw new PresentationSpeakerUploadPresentationMaterialEmailAlreadyRedeemedException;
        if(self::HashConfirmationToken($token) === $original_hash){
            $this->IsRedeemed    = true;
            $this->RedeemedDate  = SS_Datetime::now()->Rfc2822();
            return true;
        }
        throw new InvalidHashPresentationSpeakerUploadPresentationMaterialEmailException;
    }

    /**
     * @return string
     */
    public function generateConfirmationToken() {
        $generator   = new RandomGenerator();
        $this->token = $generator->randomToken();
        $hash        = self::HashConfirmationToken($this->token);
        $this->setField('Hash',$hash);
        return $this->token;
    }

    public static function HashConfirmationToken($token){
        return md5($token);
    }

    public function alreadyRedeemed()
    {
        return $this->IsRedeemed;
    }
}