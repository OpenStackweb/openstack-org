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
 * Class SummitSponsorPage
 */
class SummitSponsorPage extends SummitPage {
    
    private static $db = array (
        'SponsorAlert'                => 'HTMLText',
        'SponsorSteps'                => 'HTMLText',
		'SponsorContract'             => 'Text',
		'SponsorProspectus'           => 'Text',
        'SponsorProspectus'           => 'Text',
        'CallForSponsorShipStartDate' => 'SS_Datetime',
        'CallForSponsorShipEndDate'   => 'SS_Datetime',
    );    
    
	private static $has_many = array (
		'SummitPackages' => 'SummitPackage',
        'SummitAddOns'   => 'SummitAddOn',
	);


    private static $many_many = array(
        'Companies' => 'Company'
    );

    //sponsor type
    private static $many_many_extraFields = array(
        'Companies' => array(
            'SponsorshipType' => "Enum('Headline, Premier, Event, Startup, InKind, Spotlight, Media', 'Startup')",
            'SubmitPageUrl'=>'Text',
        ),
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        if($this->ID) {
            //set current page id
            $_REQUEST["PageId"] = $this->ID;

            // Optional Sponsor Alert
            $sponsorAlertField = new TextField('SponsorAlert','Sponsor Alert');
            $fields->addFieldToTab('Root.Main', $sponsorAlertField);

            
            // Sponsor Steps Editor
            $sponsorStepsField = new HTMLEditorField('SponsorSteps','Steps To Become A Sponsor');
            $fields->addFieldToTab('Root.Main', $sponsorStepsField, 'Content');
            //call for sponsorship dates

            $start_date =  new DatetimeField('CallForSponsorShipStartDate','Call For SponsorShip - Start Date');
            $end_date   =  new DatetimeField('CallForSponsorShipEndDate','Call For SponsorShip - End Date');
            $start_date->getDateField()->setConfig('showcalendar', true);
            $start_date->setConfig('dateformat', 'dd/MM/yyyy');
            $end_date->getDateField()->setConfig('showcalendar', true);
            $end_date->setConfig('dateformat', 'dd/MM/yyyy');
            $fields->addFieldToTab('Root.Main',$start_date);
            $fields->addFieldToTab('Root.Main', $end_date);
            
            // Summit Packages
            $config = GridFieldConfig_RelationEditor::create();
            $config->addComponent(new GridFieldSortableRows('Order'));
            $gridField = new GridField('SummitPackages', 'Sponsor Packages', $this->SummitPackages(), $config);
            $fields->addFieldToTab('Root.Packages',$gridField);
            
            // Summit Add Ons

            $config = GridFieldConfig_RelationEditor::create();
            $config->addComponent(new GridFieldSortableRows('Order'));
            
            // Remove pagination so that you can sort all add-ons collectively
            $config->removeComponentsByType('GridFieldPaginator');
            $config->removeComponentsByType('GridFieldPageCount');
            
            $gridField = new GridField('SummitAddOn', 'Sponsor Add Ons', $this->SummitAddOns(), $config);
            $fields->addFieldToTab('Root.AddOns',$gridField);
            
            $prospectusField = new TextField('SponsorProspectus');
            $fields->addFieldToTab('Root.ProspectusAndContract',$prospectusField);

            $contractField = new TextField('SponsorContract');
            $fields->addFieldToTab('Root.ProspectusAndContract',$contractField);

            // sponsors

            $companies = new GridField('Companies','Sponsors', $this->Companies(), GridFieldConfig_RelationEditor::create(10));

            $companies->getConfig()->removeComponentsByType('GridFieldEditButton');
            $companies->getConfig()->removeComponentsByType('GridFieldAddNewButton');

            $companies->getConfig()->getComponentByType('GridFieldDataColumns')->setDisplayFields(
                array( 'Name'            => 'Name',
                    "DDLSponsorshipType" => "Sponsorship Type",
                    "InputSubmitPageUrl" => "Sponsor Link")
            );

            $fields->addFieldToTab('Root.SponsorCompanies',$companies);
                        
        }
        return $fields;    

    }    
    
    public function getSortedPackages() {
        return $this->SummitPackages()->sort('Order');
    }    

    public function getSortedAddOns() {
        return $this->SummitAddOns()->sort('Order');
    }

    function onAfterWrite() {
        parent::onAfterWrite();
        //update all relationships with sponsors
        foreach($this->Companies() as $company){
            if(isset($_REQUEST["SponsorshipType_{$company->ID}"])){
                $type = $_REQUEST["SponsorshipType_{$company->ID}"];
                $sql = "UPDATE SummitSponsorPage_Companies SET SponsorshipType ='{$type}' WHERE CompanyID={$company->ID} AND SummitSponsorPageID={$this->ID};";
                DB::query($sql);
            }
            if(isset($_REQUEST["SubmitPageUrl_{$company->ID}"])){
                $page_url = $_REQUEST["SubmitPageUrl_{$company->ID}"];
                $sql = "UPDATE SummitSponsorPage_Companies SET SubmitPageUrl ='{$page_url}' WHERE CompanyID={$company->ID} AND SummitSponsorPageID={$this->ID};";
                DB::query($sql);
            }

        }
    }

}

/**
 * Class SummitSponsorPage_Controller
 */
class SummitSponsorPage_Controller extends SummitPage_Controller {
    /**
     * @var ISummitPackagePurchaseOrderManager
     */
    private $package_purchase_order_manager;

    /**
     * @return ISummitPackagePurchaseOrderManager
     */
    public function getPackagePurchaseOrderManager(){
        return $this->package_purchase_order_manager;
    }

    public function setPackagePurchaseOrderManager(ISummitPackagePurchaseOrderManager $package_purchase_order_manager){
        $this->package_purchase_order_manager = $package_purchase_order_manager;
    }

    /**
     * @var SecurityToken
     */
    private $packagePurchaseOrderSecurityToken;

	private static $allowed_actions = array (
		'prospectus',
        'contract',
        'emitPackagePurchaseOrder',
        'searchOrg',
	);    

    public function init() {
		parent::init();
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/additional-methods.js");
        Requirements::javascript('themes/openstack/javascript/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.js');
        Requirements::javascript('themes/openstack/javascript/pure.min.js');
		Requirements::javascript("summit/javascript/Chart.min.js");
        Requirements::javascript("summit/javascript/sponsor.js");
        Requirements::javascript('summit/javascript/sponsor.sponsorships.js');

        $this->packagePurchaseOrderSecurityToken = new SecurityToken('packagePurchaseOrderSecurityToken');
	}
    
    public function prospectus() {
        $ProspectusURL = $this->SponsorProspectus;
        return Controller::redirect($ProspectusURL);
    }

    public function contract() {
        $contractURL = $this->SponsorContract;
        return Controller::redirect($contractURL);
    }

    private function Sponsors($type){
        $page_id = $this->ID;
        $page    = SummitSponsorPage::get()->byID($page_id);
        $res     = $page->getManyManyComponents("Companies","SponsorshipType='{$type}'","ID");
        return $res;
    }

    public function StartupSponsors(){
        return $this->Sponsors("Startup");
    }

    public function HeadlineSponsors(){
        return $this->Sponsors("Headline");
    }

    public function PremierSponsors(){
        return $this->Sponsors("Premier");
    }

    public function EventSponsors(){
        return $this->Sponsors("Event");
    }

    public function InKindSponsors(){
        return $this->Sponsors("InKind");
    }

    public function SpotlightSponsors(){
        return $this->Sponsors("Spotlight");
    }

    public function MediaSponsors(){
        return $this->Sponsors("Media");
    }

    public function ShowSponsorShipPackages(){
        $now        = new \DateTime('now', new DateTimeZone('UTC'));
        $start_date = new \DateTime($this->CallForSponsorShipStartDate, new DateTimeZone('UTC'));
        $end_date   = new \DateTime($this->CallForSponsorShipEndDate, new DateTimeZone('UTC'));
        return $now >= $start_date && $now <= $end_date;
    }

    public function getPackagePurchaseOrderSecurityID(){
        return new HiddenField($this->packagePurchaseOrderSecurityToken->getName() , $this->packagePurchaseOrderSecurityToken->getName(), $this->packagePurchaseOrderSecurityToken->getValue());
    }

    /**
     * @param $request
     * @return SS_HTTPResponse|string
     */
    public function emitPackagePurchaseOrder($request){

        if (!Director::is_ajax()){
            return $this->forbiddenError();
        }

        if(!$this->packagePurchaseOrderSecurityToken->checkRequest($request)) {
            return $this->forbiddenError();
        }

        $body = $this->request->getBody();
        $json = json_decode($body,true);

        $this->packagePurchaseOrderSecurityToken->reset();

        try {
            $this->getPackagePurchaseOrderManager()->registerPurchaseOrder($json, new NewPurchaseOrderEmailMessageSender);
        }
        catch(EntityValidationException $ex1){
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(Exception $ex){
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
        return 'OK';
    }

    /**
     * @param $request
     * @return SS_HTTPResponse
     */
    public function searchOrg($request){

        if (!Director::is_ajax()){
           return $this->forbiddenError();
        }

        $term  = $request->getVar('term');
        $term  = Convert::raw2sql($term);

        $organizations = Org::get()->filter('Name:PartialMatch', $term)->limit(10);

        if($organizations) {

            $suggestions   = array();

            foreach ($organizations as $org) {
                array_push($suggestions, array('id' => $org->ID, 'label' => $org->Name, 'value' => $org->Name));
            }

            $response = new SS_HTTPResponse();
            $response->setStatusCode(200);
            $response->addHeader('Content-Type', 'application/json');
            $response->setBody(json_encode($suggestions));
            return $response;
        }
    }
	
}