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

class SangriaPageSummitSponsorshipPurchaseOrderExtension extends Extension {

    /**
     * @var IEntityRepository
     */
    private $packages_repository;

    /**
     * @return IEntityRepository
     */
    public function getPackagesRepository(){
        return $this->packages_repository;
    }

    public function setPackagesRepository(IEntityRepository $packages_repository){
        $this->packages_repository = $packages_repository;
    }

    public function onBeforeInit(){
        Config::inst()->update(get_class($this), 'allowed_actions', array('ViewPackagePurchaseOrderDetails'));
        Config::inst()->update(get_class($this->owner), 'allowed_actions', array('ViewPackagePurchaseOrderDetails'));
    }

    public function onAfterInit(){

    }

    public function getQuickActionsExtensions(&$html){
        $view = new SSViewer('SangriaPage_SummitPackagePurchaseOrderLinks');
        $html .= $view->process($this->owner);
    }

    public function ViewPackagePurchaseOrderDetails(){
        return $this->owner->getViewer('ViewPackagePurchaseOrderDetails')->process($this->owner);
    }

    public function getPendingApprovalPackagesPurchaseOrder(){
        $query = new QueryObject(new SummitPackagePurchaseOrder);
        $query->addAddCondition(QueryCriteria::equal('Approved',0));
        $query->addAddCondition(QueryCriteria::equal('Rejected',0));
        $query->addOrder(QueryOrder::asc('Created'));
        list($list, $count) = $this->packages_repository->getAll($query, 0 , 999999);
        return new ArrayList($list);
    }
}