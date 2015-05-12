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
 * Class SummitsApi
 */
final class SummitsApi extends AbstractRestfulJsonApi {

    const ApiPrefix = 'api/v1/summits';

    /**
     * @var IEntityRepository
     */
    private $sponsorship_add_on_repository;

    /**
     * @var IEntityRepository
     */
    private $sponsorship_package_repository;

    /**
     * @var ISummitPackagePurchaseOrderManager
     */
    private $package_purchase_order_manager;

    /**
     * @param IEntityRepository $sponsorship_package_repository
     * @param IEntityRepository $sponsorship_add_on_repository
     * @param ISummitPackagePurchaseOrderManager $package_purchase_order_manager
     */
    public function __construct(IEntityRepository $sponsorship_package_repository,
                                IEntityRepository $sponsorship_add_on_repository,
                                ISummitPackagePurchaseOrderManager $package_purchase_order_manager){
        parent::__construct();
        $this->sponsorship_add_on_repository  = $sponsorship_add_on_repository;
        $this->sponsorship_package_repository = $sponsorship_package_repository;
        $this->package_purchase_order_manager = $package_purchase_order_manager;

        $this_var = $this;

        $this->addBeforeFilter('getAllSponsorshipAddOnsBySummit','check_own_request',function ($request) use($this_var){
            if(!$this_var->checkOwnAjaxRequest($request))
                return $this_var->permissionFailure();
        });

        $this->addBeforeFilter('getAllSponsorshipAddOnsBySummit','check_own_request2',function ($request) use($this_var){
            if(!$this_var->checkOwnAjaxRequest($request))
                return $this_var->permissionFailure();
        });

        $this->addBeforeFilter('approvePurchaseOrder','check_approve',function ($request) use($this_var){
            if(!$this_var->checkAdminPermissions($request))
                return $this_var->permissionFailure();
        });

        $this->addBeforeFilter('rejectPurchaseOrder','check_reject',function ($request) use($this_var){
            if(!$this_var->checkAdminPermissions($request))
                return $this_var->permissionFailure();
        });

    }

    public function checkOwnAjaxRequest($request){
        $referer = @$_SERVER['HTTP_REFERER'];
        if(empty($referer)) return false;
        //validate
        if (!Director::is_ajax()) return false;
        return Director::is_site_url($referer);
    }

    public function checkAdminPermissions($request){
        return Permission::check("SANGRIA_ACCESS");
    }

    protected function isApiCall(){
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
    }

    /**
     * @return bool
     */
    protected function authorize(){
        return true;
    }

    protected function authenticate() {
        return true;
    }

    static $url_handlers = array(
        'GET $SUMMIT_ID/add-ons'                                  => 'getAllSponsorshipAddOnsBySummit',
        'GET $SUMMIT_ID/packages'                                 => 'getAllSponsorshipPackagesBySummit',
        'PUT packages/purchase-orders/$PURCHASE_ORDER_ID/approve' => 'approvePurchaseOrder',
        'PUT packages/purchase-orders/$PURCHASE_ORDER_ID/reject'  => 'rejectPurchaseOrder',
    );

    static $allowed_actions = array(
        'getAllSponsorshipAddOnsBySummit',
        'getAllSponsorshipPackagesBySummit',
        'approvePurchaseOrder',
        'rejectPurchaseOrder',
    );

    public function getAllSponsorshipAddOnsBySummit(){
        try {
            $summit_id = (int)$this->request->param('SUMMIT_ID');
            $query = new QueryObject(new SummitAddOn);
            $query->addAddCondition(QueryCriteria::equal('SummitSponsorPageID', $summit_id));
            $query->addOrder(QueryOrder::asc("Order"));
            list($list, $count) = $this->sponsorship_add_on_repository->getAll($query, 0, 999999);
            $res = array();
            foreach ($list as $add_on) {
                array_push($res, SummitAddOnAssembler::toArray($add_on));
            }
            return $this->ok($res);
        }
        catch(Exception $ex) {
            SS_Log::log($ex, SS_Log::WARN);
            return $this->serverError();
        }
    }

    public function getAllSponsorshipPackagesBySummit(){
        try {
            $query = new QueryObject(new SummitPackage());
            $summit_id = (int)$this->request->param('SUMMIT_ID');
            $query->addAddCondition(QueryCriteria::equal('SummitSponsorPageID', $summit_id));
            $query->addOrder(QueryOrder::asc("Order"));
            list($list, $count) = $this->sponsorship_package_repository->getAll($query, 0, 999999);
            $res = array();
            foreach ($list as $package) {
                array_push($res, SummitPackageAssembler::toArray($package));
            }
            return $this->ok($res);
        }
        catch(Exception $ex) {
            SS_Log::log($ex, SS_Log::WARN);
            return $this->serverError();
        }
    }

    public function approvePurchaseOrder(){
        try{
            $order_id = (int)$this->request->param('PURCHASE_ORDER_ID');
            $this->package_purchase_order_manager->approvePurchaseOrder($order_id, new ApprovedPurchaseOrderEmailMessageSender);
            return $this->updated();
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::ERR);
            return $this->notFound($ex1->getMessage());
        }
        catch(EntityValidationException $ex2){
            SS_Log::log($ex2,SS_Log::NOTICE);
            return $this->validationError($ex2->getMessages());
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function rejectPurchaseOrder(){
        try{
            $order_id = (int)$this->request->param('PURCHASE_ORDER_ID');
            $this->package_purchase_order_manager->rejectPurchaseOrder($order_id, new RejectedPurchaseOrderEmailMessageSender);
            return $this->updated();
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::ERR);
            return $this->notFound($ex1->getMessage());
        }
        catch(EntityValidationException $ex2){
            SS_Log::log($ex2,SS_Log::NOTICE);
            return $this->validationError($ex2->getMessages());
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }
}