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
 * Class SummitPackagePurchaseOrder
 */
class SummitPackagePurchaseOrder
    extends DataObject
    implements ISummitPackagePurchaseOrder {

    public function __construct($record = null, $isSingleton = false, $model = null) {
        parent::__construct($record, $isSingleton, $model);
        if($this->getIdentifier() === 0) {
            $this->Created = MySQLDatabase56::nowRfc2822();
        }
    }

    private static $db = array (
        'FirstName'    => 'Varchar',
        'Surname'      => 'Varchar',
        'Email'        => 'Varchar(254)',
        'Organization' => 'Varchar',
        'Approved'     => 'Boolean',
        'ApprovedDate' => 'SS_Datetime',
        'Rejected'     => 'Boolean',
        'RejectedDate' => 'SS_Datetime',
    );

    private static $defaults = array(
        'Approved' => FALSE,
        'Rejected' => FALSE
    );

    private static $has_one = array (

        'RegisteredOrganization' => 'Org',
        'ApprovedBy'             => 'Member',
        'RejectedBy'             => 'Member',
        'Package'                => 'SummitPackage',
    );

    private static $summary_fields = array(
    );


    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @param IMessageSenderService $sender_service
     * @return void
     */
    public function approve(IMessageSenderService $sender_service = null)
    {
        if(!is_null($sender_service)) $sender_service->send($this);
        $this->Approved     = true;
        $this->ApprovedDate = MySQLDatabase56::nowRfc2822();
        $this->ApprovedByID = Member::currentUserID();
    }

    /**
     * @param IMessageSenderService $sender_service
     * @return void
     */
    public function reject(IMessageSenderService $sender_service = null)
    {
        if(!is_null($sender_service)) $sender_service->send($this);
        $this->Rejected     = true;
        $this->RejectedDate = MySQLDatabase56::nowRfc2822();
        $this->RejectedByID = Member::currentUserID();
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return (bool)$this->Approved;
    }

    /**
     * @return bool
     */
    public function isRejected()
    {
       return (bool)$this->Rejected;
    }

    /**
     * @return ISummitPackage
     */
    public function package()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Package')->getTarget();
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getField('Email');
    }

    public function getFullName()
    {
        return $this->getField('FirstName').' '.$this->getField('Surname');
    }

    /**
     * @return string
     */
    public function getOrganization()
    {
        return $this->getField('Organization');
    }

    /**
     * @return string
     */
    public function getCreatedDate()
    {
        $res =  $this->getField('Created');
        return $res;
    }
}