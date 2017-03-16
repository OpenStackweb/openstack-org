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
final class SummitPromoCodeManager implements ISummitPromoCodeManager
{

    /**
     * @var ISummitRegistrationPromoCodeRepository
     */
    private $promocode_repository;
    /**
     * @var ITransactionManager
     */
    private $tx_service;

    /**
     * SummitPromoCodeManager constructor.
     * @param ISummitRegistrationPromoCodeRepository $promocode_repository
     * @param ITransactionManager $tx_service
     */
    public function __construct
    (
        ISummitRegistrationPromoCodeRepository $promocode_repository,
        ITransactionManager $tx_service
    )
    {
        $this->promocode_repository = $promocode_repository;
        $this->tx_service           = $tx_service;
    }


    /**
     * @param ISummit $summit
     * @param array $promocode_data
     * @return ISummitRegistrationPromoCode
     */
    public function createPromoCode(ISummit $summit, array $promocode_data)
    {
        $promocode_factory                    = new SummitRegistrationPromoCodeFactory();

        return $this->tx_service->transaction(function () use
        (
            $summit, $promocode_data, $promocode_factory
        ) {

            $codes = explode(',',$promocode_data['code']);
            foreach ($codes as $code) {
                // check if code already exists
                $code_obj = $this->promocode_repository->getByCode($summit->getIdentifier(),$code);
                if ($code_obj) {
                    throw new EntityValidationException("Code ".$code." already exists.");
                }

                $promocode_data['code'] = $code;
                $promocode = $promocode_factory->buildPromoCode($promocode_data,$summit->getIdentifier());

                $promocode->write();
            }

            return $promocode;
        });
    }

    /**
     * @param ISummit $summit
     * @param array $promocode_data
     * @return ISummitRegistrationPromoCode
     */
    public function updatePromoCode(ISummit $summit, array $promocode_data)
    {
        $promocode_factory      = new SummitRegistrationPromoCodeFactory();

        return $this->tx_service->transaction(function () use ($summit, $promocode_data, $promocode_factory) {
            $code_id    = trim($promocode_data['code_id']);
            $promocode  = $this->promocode_repository->getById($code_id);
            if(is_null($promocode)) throw new NotFoundEntityException('PromoCode');

            $promocode = $promocode_factory->populatePromoCode($summit->getIdentifier(),$promocode_data,$promocode);
            $promocode->write();

            return $promocode;

        });
    }

    /**
     * @param ISummit $summit
     * @param array $data
     * @return ISummitRegistrationPromoCode
     */
    public function setMultiPromoCodes(ISummit $summit, array $data)
    {
        $promocode_factory                    = new SummitRegistrationPromoCodeFactory();

        return $this->tx_service->transaction(function() use($summit, $data , $promocode_factory) {
            $codes = array();

            // first we get the matching codes
            if (isset($data['use_codes']) && $data['use_codes']) {
                $codes = $this->promocode_repository->getFreeByTypeAndSummit
                (
                    $summit->getIdentifier(),
                    $data['code_type'],
                    $data['code_prefix'],
                    $data['company_id'],
                    $data['code_qty']
                )->toArray();
            }

            // complete number of codes requested with new ones
            $diff = $data['code_qty'] - count($codes);
            if ($diff > 0) {
                for ($i=1;$i <= $diff; $i++) {
                    $prefix = (!empty($data['code_prefix'])) ? trim($data['code_prefix']) : substr($data['code_type'],0,3);
                    $code_string = $prefix.'_'.random_string(6);

                    if ($this->promocode_repository->getByCode($summit->getIdentifier(),$code_string)) {
                        $i--; //redo
                    } else {
                        $data['code'] = $code_string;
                        $promocode = $promocode_factory->buildPromoCode($data,$summit->getIdentifier());
                        $promocode->write();
                        $codes[] = $promocode;
                    }
                }
            }

            // Now assign members to these codes
            if ($data['code_type'] == 'ALTERNATE' || $data['code_type'] == 'ACCEPTED') {
                $owners = (isset($data['speakers'])) ? explode(',',$data['speakers']) : array();
            } else {
                $owners = (isset($data['members'])) ? explode(',',$data['members']) : array();
            }

            if(count($owners) > 0) {
                foreach ($codes as $code) {
                    $owner_id = array_pop($owners);
                    if ($owner_id) {
                        if ($code->ClassName == 'SpeakerSummitRegistrationPromoCode') {
                            $code->SpeakerID = $owner_id;
                        } else {
                            $code->OwnerID = $owner_id;
                        }

                        $code->write();
                    }
                }
            }

            return $codes;
        });
    }

    /**
     * @param ISummit $summit
     * @param int $code_id
     * @return ISummitRegistrationPromoCode
     */
    public function sendEmailPromoCode(ISummit $summit, $code_id)
    {

        return $this->tx_service->transaction(function () use ($summit, $code_id) {
            $promocode  = $this->promocode_repository->getById($code_id);
            if(is_null($promocode)) throw new NotFoundEntityException('PromoCode');
            $email = '';
            $name = '';

            if($promocode->ClassName == 'SpeakerSummitRegistrationPromoCode' && $promocode->Speaker()->exists() ){
                $name = $promocode->Speaker()->getName();
                if ($promocode->Speaker()->Member()->exists()) {
                    $email = $promocode->Speaker()->Member()->getEmail();
                } elseif ($promocode->Speaker()->RegistrationRequest()->exists()) {
                    $email = $promocode->Speaker()->RegistrationRequest()->Email;
                }
            }

            if($promocode->is_a('MemberSummitRegistrationPromoCode')) {
                if ($promocode->Owner()->exists()) {
                    $email = $promocode->Owner()->getEmail();
                    $name = $promocode->Owner()->getName();
                } elseif (!empty($promocode->Email) && !empty($promocode->FirstName)) {
                    $email = $promocode->Email;
                    $name = $promocode->FirstName.' '.$promocode->LastName;
                }
            }

            if(empty($email))
                throw new EntityValidationException('cannot find email address for the promocode owner!');

            if(empty($name))
                throw new EntityValidationException('cannot find name for the promocode owner!');

            if (!$promocode->EmailSent) {

                $promocode->setEmailSent(1);
                $promocode->write();

                $params = array
                (
                    'Name'      => $name,
                    'Email'     => $email,
                    'Summit'    => $summit,
                    'PromoCode' => $promocode
                );

                $sender = new MemberPromoCodeEmailSender();
                $sender->send($params);
            }

            return $promocode;

        });
    }

}