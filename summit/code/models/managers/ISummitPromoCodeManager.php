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
interface ISummitPromoCodeManager
{
    /**
     * @param ISummit $summit
     * @param array $promocode_data
     * @return ISummitRegistrationPromoCode
     */
    public function createPromoCode(ISummit $summit, array $promocode_data);

    /**
     * @param ISummit $summit
     * @param array $promocode_data
     * @return ISummitRegistrationPromoCode
     */
    public function updatePromoCode(ISummit $summit, array $promocode_data);

    /**
     * @param ISummit $summit
     * @param array $data
     * @return ISummitRegistrationPromoCode
     */
    public function setMultiPromoCodes(ISummit $summit, array $data);

    /**
     * @param ISummit $summit
     * @param int $code_id
     * @return ISummitRegistrationPromoCode
     */
    public function sendEmailPromoCode(ISummit $summit, $code_id);
}