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
interface ISummitRegistrationPromoCodeRepository extends IEntityRepository
{
    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param string $term
     * @param string $sort_by
     * @param string $sort_dir
     * @return array
     */
    public function searchByTermAndSummitPaginated($summit_id, $type, $page= 1, $page_size = 10, $term = '', $sort_by = 'code', $sort_dir = 'asc');

    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param string $term
     * @param string $sort_by
     * @param string $sort_dir
     * @return array
     */
    public function searchSponsorByTermAndSummitPaginated($summit_id, $page= 1, $page_size = 10, $term = '', $sort_by = 'sponsor', $sort_dir = 'asc');

    /**
     * @param int $summit_id
     * @param string $code
     * @return ISummitRegistrationPromoCode
     */
    public function getByCode($summit_id, $code);

    /**
     * @param int $summit_id
     * @param int $org_id
     * @return ISummitRegistrationPromoCode[]
     */
    public function getByOrg($summit_id, $org_id);

    /**
     * @param int $summit_id
     * @return ArrayList
     */
    public function getGroupedByOrg($summit_id);

    /**
     * @param int $summit_id
     * @param int $owner_id
     * @return ISummitRegistrationPromoCode
     */
    public function getByOwner($summit_id, $owner_id);

    /**
     * @param int $summit_id
     * @param int $speaker_id
     * @return ISummitRegistrationPromoCode
     */
    public function getBySpeaker($summit_id, $speaker_id);

    /**
     * @param int $summit_id
     * @param string $email
     * @return ISummitRegistrationPromoCode
     */
    public function getByEmail($summit_id, $email);

}