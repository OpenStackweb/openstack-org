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
final class COACertification
{
    /**
     * @var string
     */
    private $code;
    /**
     * @var string
     */
    private $number;
    /**
     * @var string
     */
    private $status;
    /**
     * @var DateTime
     */
    private $expiration_date;

    /**
     * COACertification constructor.
     * @param string $code
     * @param string $number
     * @param string $status
     * @param DateTime $expiration_date
     */
    public function __construct($code, $number, $status, DateTime $expiration_date = null)
    {
        $this->code            = $code;
        $this->number          = $number;
        $this->status          = $status;
        $this->expiration_date = $expiration_date;
    }

    /**
     * @return string
     */
    public function getCode(){
        return $this->code;
    }

    /**
     * @return string
     */
    public function getNumber(){
        return $this->number;
    }

    /**
     * @return string
     */
    public function getStatus(){
        return $this->status;
    }

    /**
     * @return DateTime
     */
    public function getExpirationDate(){
        return $this->expiration_date;
    }

}