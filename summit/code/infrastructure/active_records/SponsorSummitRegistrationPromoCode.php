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
class SponsorSummitRegistrationPromoCode extends MemberSummitRegistrationPromoCode implements ISponsorSummitRegistrationPromoCode
{
    private static $db = array
    (
    );

    private static $has_one = array
    (
        'Sponsor' => 'Company',
    );

    /**
     * @return string
     */
    public function getType()
    {
        return 'SPONSOR';
    }

    /**
     * @return ICompany
     */
    public function getSponsor()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Sponsor')->getTarget();
    }

    /**
     * @param ICompany $company
     * @return $this
     */
    public function assignSponsor(ICompany $company)
    {
        $this->SponsorID = $company->getIdentifier();
        AssociationFactory::getInstance()->getMany2OneAssociation($this,'Sponsor')->setTarget($company);
    }
}