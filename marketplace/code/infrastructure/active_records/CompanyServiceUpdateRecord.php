<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class CompanyServiceUpdateRecord
 */
class CompanyServiceUpdateRecord extends DataObject implements ICompanyServiceUpdateRecord
{

    static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    static $db = array(
    );

    static $has_one = array(
        'CompanyService' => 'CompanyServiceDraft',
        'Editor'         => 'Member'
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function storeUpdate(ICompanyService $company_service) {
        // we only store changes to drafts, bc changes to live versions are just 'publish'
        if (is_a($company_service, 'CompanyServiceDraft')) {
            $this->setCompanyService($company_service);
            $this->setEditor(Member::currentUser());
            $this->write();
        }
    }

    public function setCompanyService(ICompanyService $company_service)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this,'CompanyService')->setTarget($company_service);
    }

    public function getCompanyService()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this,'CompanyService')->getTarget();
    }

    public function setEditor(IFoundationMember $member)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this,'Editor')->setTarget($member);
    }

    public function getEditor()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Editor')->getTarget();
    }
}