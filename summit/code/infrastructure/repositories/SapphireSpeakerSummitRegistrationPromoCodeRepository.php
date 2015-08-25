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
class SapphireSpeakerSummitRegistrationPromoCodeRepository
    extends SapphireRepository
    implements ISpeakerSummitRegistrationPromoCodeRepository
{

    private $promo_code_speaker_session_pool = array();
    private $promo_code_alternate_speaker_session_pool = array();

    public function __construct()
    {
        parent::__construct(new SpeakerSummitRegistrationPromoCode);
    }

    /**
     * @param string $promo_code_type
     * @param int $batch_size
     * @return ISpeakerSummitRegistrationPromoCode
     */
    public function getNextAvailableByType($promo_code_type, $batch_size = 10)
    {
        switch($promo_code_type)
        {
            case ISpeakerSummitRegistrationPromoCode::TypeAccepted:
            {
                if(count($this->promo_code_speaker_session_pool) === 0 )
                {
                    $query = new QueryObject(new SpeakerSummitRegistrationPromoCode);
                    $query->addAndCondition(QueryCriteria::equal('Type', $promo_code_type));
                    $query->addAndCondition(QueryCriteria::equal('OwnerID',0));
                    $query->addAndCondition(QueryCriteria::equal('SpeakerID',0));
                    $query->addAndCondition(QueryCriteria::equal('SummitID',Summit::get_active()->ID));
                    $query->addOrder(QueryOrder::asc('ID'));
                    list($this->promo_code_speaker_session_pool, $count) = $this->getAll($query,0, $batch_size);
                }
                return array_shift($this->promo_code_speaker_session_pool);
            }
            break;
            case ISpeakerSummitRegistrationPromoCode::TypeAlternate:
            {
                if(count($this->promo_code_alternate_speaker_session_pool) === 0 )
                {
                    $query = new QueryObject(new SpeakerSummitRegistrationPromoCode);
                    $query->addAndCondition(QueryCriteria::equal('Type', $promo_code_type));
                    $query->addAndCondition(QueryCriteria::equal('OwnerID',0));
                    $query->addAndCondition(QueryCriteria::equal('SpeakerID',0));
                    $query->addAndCondition(QueryCriteria::equal('SummitID',Summit::get_active()->ID));
                    $query->addOrder(QueryOrder::asc('ID'));
                    list($this->promo_code_alternate_speaker_session_pool, $count) = $this->getAll($query,0, $batch_size);
                }
                return array_shift($this->promo_code_alternate_speaker_session_pool);
            }
            break;
        }
        return null;
    }
}