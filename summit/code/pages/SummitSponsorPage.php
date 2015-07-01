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
class SummitSponsorPage extends SummitPage
{

    private static $db = array(
        'SponsorIntro' => 'HTMLText',
        'SponsorAlert' => 'HTMLText',
        'SponsorSteps' => 'HTMLText',
        'SponsorContract' => 'Text',
        'SponsorProspectus' => 'Text',
        'SponsorProspectus' => 'Text',
        'CallForSponsorShipStartDate' => 'SS_Datetime',
        'CallForSponsorShipEndDate' => 'SS_Datetime',
        'AudienceIntro' => 'HTMLText',
        'ShowAudience' => 'Boolean',
        'AudienceMetricsTitle' => 'Text',
        'AudienceTotalSummitAttendees' => 'Text',
        'AudienceCompaniesRepresented' => 'Text',
        'AudienceCountriesRepresented' => 'Text',
        'HowToSponsorContent' => 'HTMLText',
        'VenueMapContent' => 'HTMLText',
    );

    private static $has_many = array(
        'SummitPackages'    => 'SummitPackage',
        'SummitAddOns'      => 'SummitAddOn',
        'AttendeesByRegion' => 'SummitPieDataItemRegion',
        'AttendeesByRoles'  => 'SummitPieDataItemRole',
    );

    private static $has_one = array(
        'CrowdImage'   => 'BetterImage',
        'ExhibitImage' => 'BetterImage'
    );

    private static $many_many = array(
        'Companies' => 'Company'
    );

    //sponsor type
    private static $many_many_extraFields = array(
        'Companies' => array(
            'SponsorshipType' => "Enum('Headline, Premier, Event, Startup, InKind, Spotlight, Media', 'Startup')",
            'SubmitPageUrl'   => 'Text',
            'SummitID'        => 'Int'
        ),
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Optional Sponsor Alert
        $sponsorAlertField = new TextField('SponsorAlert', 'Sponsor Alert');

        $fields->addFieldToTab('Root.Main', $sponsorAlertField);

        $fields->addFieldsToTab('Root.Main', new HtmlEditorField('SponsorIntro', 'Sponsor Intro Text'));

        $fields->addFieldsToTab('Root.Main', new HtmlEditorField('HowToSponsorContent', 'How To Sponsor (Bottom)'));

        $fields->addFieldsToTab('Root.Main', new HtmlEditorField('VenueMapContent', 'Venue Map Content'));

        // Sponsor Steps Editor
        $sponsorStepsField = new HTMLEditorField('SponsorSteps', 'Steps To Become A Sponsor');
        $fields->addFieldToTab('Root.Main', $sponsorStepsField, 'Content');
        //call for sponsorship dates

        $start_date = new DatetimeField('CallForSponsorShipStartDate', 'Call For SponsorShip - Start Date');
        $end_date = new DatetimeField('CallForSponsorShipEndDate', 'Call For SponsorShip - End Date');
        $start_date->getDateField()->setConfig('showcalendar', true);
        $start_date->setConfig('dateformat', 'dd/MM/yyyy');
        $end_date->getDateField()->setConfig('showcalendar', true);
        $end_date->setConfig('dateformat', 'dd/MM/yyyy');
        $fields->addFieldToTab('Root.Main', $start_date);
        $fields->addFieldToTab('Root.Main', $end_date);

        if ($this->ID) {
            //set current page id
            $_REQUEST["PageId"] = $this->ID;

            // Summit Packages
            $config = GridFieldConfig_RelationEditor::create();
            $config->addComponent(new GridFieldSortableRows('Order'));
            $gridField = new GridField('SummitPackages', 'Sponsor Packages', $this->SummitPackages(), $config);
            $fields->addFieldToTab('Root.Packages', $gridField);

            // Summit Add Ons

            $config = GridFieldConfig_RelationEditor::create();
            $config->addComponent(new GridFieldSortableRows('Order'));

            // Remove pagination so that you can sort all add-ons collectively
            $config->removeComponentsByType('GridFieldPaginator');
            $config->removeComponentsByType('GridFieldPageCount');

            $gridField = new GridField('SummitAddOn', 'Sponsor Add Ons', $this->SummitAddOns(), $config);
            $fields->addFieldToTab('Root.AddOns', $gridField);

            $prospectusField = new TextField('SponsorProspectus');
            $fields->addFieldToTab('Root.ProspectusAndContract', $prospectusField);

            $contractField = new TextField('SponsorContract');
            $fields->addFieldToTab('Root.ProspectusAndContract', $contractField);

            // sponsors

            $companies = new GridField('Companies', 'Sponsors', $this->Companies(), GridFieldConfig_RelationEditor::create(10));

            $companies->getConfig()->removeComponentsByType('GridFieldEditButton');
            $companies->getConfig()->removeComponentsByType('GridFieldAddNewButton');

            $companies->getConfig()->getComponentByType('GridFieldDataColumns')->setDisplayFields(
                array('Name' => 'Name',
                    "DDLSponsorshipType" => "Sponsorship Type",
                    "InputSubmitPageUrl" => "Sponsor Link")
            );

            $fields->addFieldToTab('Root.SponsorCompanies', $companies);

            $fields->addFieldsToTab("Root.Main", $upload_0 = new UploadField('CrowdImage','Crowd Image'));
            $fields->addFieldsToTab("Root.Main", $upload_1 = new UploadField('ExhibitImage','Exhibit Image'));

            $upload_0->setFolderName('summits/sponsorship/backgroun');
            $upload_0->setAllowedMaxFileNumber(1);
            $upload_0->setAllowedFileCategories('image');

            $upload_1->setFolderName('summits/sponsorship/background');
            $upload_1->setAllowedMaxFileNumber(1);
            $upload_1->setAllowedFileCategories('image');

            //audience
            $config = GridFieldConfig_RecordEditor::create();
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $gridField = new GridField('AttendeesByRegion', 'Attendees By Region', $this->AttendeesByRegion(), $config);
            $fields->add( $gridField);

            $config = GridFieldConfig_RecordEditor::create();
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $gridField = new GridField('AttendeesByRoles', 'Attendees By Roles', $this->AttendeesByRoles(), $config);
            $fields->add( $gridField);


        }

        $fields->addFieldsToTab('Root.Audience', new CheckboxField('ShowAudience', 'Show Audience'));
        $fields->addFieldsToTab('Root.Audience', new HtmlEditorField('AudienceIntro', 'Intro'));
        $fields->addFieldsToTab('Root.Audience', new TextField('AudienceMetricsTitle', 'Metrics Title'));
        $fields->addFieldsToTab('Root.Audience', new TextField('AudienceTotalSummitAttendees', 'Total Summit Attendees'));
        $fields->addFieldsToTab('Root.Audience', new TextField('AudienceCompaniesRepresented', 'Companies Represented'));
        $fields->addFieldsToTab('Root.Audience', new TextField('AudienceCountriesRepresented', 'Countries Represented'));
        return $fields;

    }

    public function getSortedPackages()
    {
        return $this->SummitPackages()->sort('Order');
    }

    public function getSortedAddOns()
    {
        return $this->SummitAddOns()->sort('Order');
    }

    function onAfterWrite()
    {
        parent::onAfterWrite();
        $summit = Summit::get_active();
        //update all relationships with sponsors
        foreach ($this->Companies() as $company) {
            if (isset($_REQUEST["SponsorshipType_{$company->ID}"])) {
                $type = $_REQUEST["SponsorshipType_{$company->ID}"];
                $sql = "UPDATE SummitSponsorPage_Companies SET SponsorshipType ='{$type}', SummitID = '{$summit->ID}' WHERE CompanyID={$company->ID} AND SummitSponsorPageID={$this->ID};";
                DB::query($sql);
            }
            if (isset($_REQUEST["SubmitPageUrl_{$company->ID}"])) {
                $page_url = $_REQUEST["SubmitPageUrl_{$company->ID}"];
                $sql = "UPDATE SummitSponsorPage_Companies SET SubmitPageUrl ='{$page_url}', SummitID = '{$summit->ID}' WHERE CompanyID={$company->ID} AND SummitSponsorPageID={$this->ID};";
                DB::query($sql);
            }

        }
    }

    public function getSponsorIntro()
    {

        $res = $this->getField('SponsorIntro');
        if (empty($res))
            $res = '<h1>Thank You To The OpenStack Summit Sponsors</h1><p> The generous support of our sponsors makes it possible for our community to gather, learn and build the future of cloud computing. A warm thank you to all of our sponsors for the May 2015 OpenStack Summit. </p>';
        return $res;
    }

    public function getAudienceIntro()
    {

        $res = $this->getField('AudienceIntro');
        if (empty($res))
            $res = 'The Summit has experienced tremendous growth since its inception, and we\'re proud of the diverse
                    audience reached at each one. Here\'s a quick look at the audience who attended the Vancouver Summit in
                    May 2015.';
        return $res;
    }

    public function getAudienceMetricsTitle()
    {

        $res = $this->getField('AudienceMetricsTitle');
        if (empty($res))
            $res = 'November 2015 Vancouver OpenStack Summit Metrics:';
        return $res;
    }

    public function getAudienceTotalSummitAttendees()
    {

        $res = $this->getField('AudienceTotalSummitAttendees');
        if (empty($res))
            $res = '6,000+';
        return $res;
    }

    public function getAudienceCompaniesRepresented()
    {

        $res = $this->getField('AudienceCompaniesRepresented');
        if (empty($res))
            $res = '967';
        return $res;
    }

    public function getAudienceCountriesRepresented()
    {
        $res = $this->getField('AudienceCountriesRepresented');
        if (empty($res))
            $res = '55';
        return $res;
    }

    /**
     * @return bool
     */
    public function HasSponsors(){
        return $this->StartupSponsors()->Count() > 0 || $this->HeadlineSponsors()->Count() > 0
        || $this->PremierSponsors()->Count() > 0 || $this->EventSponsors()->Count() > 0
        || $this->InKindSponsors()->Count() > 0 || $this->SpotlightSponsors()->Count() > 0
        || $this->MediaSponsors()->Count() > 0;
    }

    private function Sponsors($type)
    {
        $page_id = $this->ID;
        $page = SummitSponsorPage::get()->byID($page_id);
        $res = $page->getManyManyComponents("Companies", "SponsorshipType='{$type}'", "ID");
        return $res;
    }

    public function StartupSponsors()
    {
        return $this->Sponsors("Startup");
    }

    public function HeadlineSponsors()
    {
        return $this->Sponsors("Headline");
    }

    public function PremierSponsors()
    {
        return $this->Sponsors("Premier");
    }

    public function EventSponsors()
    {
        return $this->Sponsors("Event");
    }

    public function InKindSponsors()
    {
        return $this->Sponsors("InKind");
    }

    public function SpotlightSponsors()
    {
        return $this->Sponsors("Spotlight");
    }

    public function MediaSponsors()
    {
        return $this->Sponsors("Media");
    }

    public function CrowdImageUrl(){
        if($this->CrowdImage()->exists()){
            return $this->CrowdImage()->getURL();
        }
        return '/summit/images/sponsors-bkgd.jpg';
    }

    public function ExhibitImageUrl(){
        if($this->ExhibitImage()->exists()){
            return $this->ExhibitImage()->getURL();
        }
        return '/summit/images/sponsor-bkgd-exhibit.jpg';
    }

    public function getOrderedAttendeesByRegion(){
        return $this->AttendeesByRegion()->sort('Order');
    }

    public function getOrderedAttendeesByRoles(){
        return $this->AttendeesByRoles()->sort('Order');
    }
}

/**
 * Class SummitSponsorPage_Controller
 */
class SummitSponsorPage_Controller extends SummitPage_Controller
{
    /**
     * @var ISummitPackagePurchaseOrderManager
     */
    private $package_purchase_order_manager;

    /**
     * @return ISummitPackagePurchaseOrderManager
     */
    public function getPackagePurchaseOrderManager()
    {
        return $this->package_purchase_order_manager;
    }

    public function setPackagePurchaseOrderManager(ISummitPackagePurchaseOrderManager $package_purchase_order_manager)
    {
        $this->package_purchase_order_manager = $package_purchase_order_manager;
    }

    /**
     * @var SecurityToken
     */
    private $packagePurchaseOrderSecurityToken;

    private static $allowed_actions = array(
        'prospectus',
        'contract',
        'emitPackagePurchaseOrder',
        'searchOrg',
    );

    public function init()
    {
        parent::init();
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/additional-methods.js");
        Requirements::javascript('themes/openstack/javascript/jquery.cleanform.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.js');
        Requirements::javascript('themes/openstack/javascript/pure.min.js');
        Requirements::javascript("summit/javascript/Chart.min.js");

        if($this->getOrderedAttendeesByRegion()->count() > 0){
            $script = '// Audience Chart - Attendees by Region
             var pieDataRegion = [];';
            foreach($this->getOrderedAttendeesByRegion() as $data){
                $script .= sprintf('pieDataRegion.push({
                value: %s,
                color: "#%s",
                label: "%s"
            });',$data->Value, $data->Color, $data->Label);
            }
            Requirements::customScript($script);
        }
        else{
            Requirements::customScript('// Audience Chart - Attendees by Region
            var pieDataRegion = [{
                value: 47,
                color: "#cf3427",
                label: "Europe"
            }, {
                value: 35,
                color: "#29abe2",
                label: "North America"
            }, {
                value: 15,
                color: "#2A4E68",
                label: "APAC"
            }, {
                value: 2,
                color: "#5BB6A7",
                label: "Middle East"
            }, {
                value: 1,
                color: "#faaf3c",
                label: "Latin America"
            }];');
        }

        if($this->getOrderedAttendeesByRoles()->count() > 0){
            $script = '// Audience Chart - Attendees by Region
             var pieDataRole = [];';
            foreach($this->getOrderedAttendeesByRoles() as $data){
                $script .= sprintf('pieDataRole.push({
                value: %s,
                color: "#%s",
                label: "%s"
            });',$data->Value, $data->Color, $data->Label);
            }
            Requirements::customScript($script);
        }
        else{
            Requirements::customScript('// Audience Chart - Attendees by Roles
            var pieDataRole = [{
                value: 33,
                color: "#cf3427",
                label: "Prdt Strgy, Mgt, Archt"
            }, {
                value: 26,
                color: "#29abe2",
                label: "Developer"
            }, {
                value: 16,
                color: "#2A4E68",
                label: "User, Sys Admin"
            }, {
                value: 10,
                color: "#5BB6A7",
                label: "Bus Dev, Mrkt"
            }, {
                value: 9,
                color: "#faaf3c",
                label: "CEO, CIO, IT Mgr"
            }, {
                value: 6,
                color: "#000000",
                label: "Other"
            }];');
        }

        Requirements::javascript("summit/javascript/sponsor.js");
        Requirements::javascript('summit/javascript/sponsor.sponsorships.js');

        $this->packagePurchaseOrderSecurityToken = new SecurityToken('packagePurchaseOrderSecurityToken');
    }

    public function prospectus()
    {
        $ProspectusURL = $this->SponsorProspectus;
        return Controller::redirect($ProspectusURL);
    }

    public function contract()
    {
        $contractURL = $this->SponsorContract;
        return Controller::redirect($contractURL);
    }


    public function ShowSponsorShipPackages()
    {
        if(is_null($this->CallForSponsorShipStartDate) || is_null($this->CallForSponsorShipEndDate)) return false;
        $now = new \DateTime('now', new DateTimeZone('UTC'));
        $start_date = new \DateTime($this->CallForSponsorShipStartDate, new DateTimeZone('UTC'));
        $end_date = new \DateTime($this->CallForSponsorShipEndDate, new DateTimeZone('UTC'));
        return $now >= $start_date && $now <= $end_date;
    }

    public function getPackagePurchaseOrderSecurityID()
    {
        return new HiddenField($this->packagePurchaseOrderSecurityToken->getName(), $this->packagePurchaseOrderSecurityToken->getName(), $this->packagePurchaseOrderSecurityToken->getValue());
    }

    /**
     * @param $request
     * @return SS_HTTPResponse|string
     */
    public function emitPackagePurchaseOrder($request)
    {

        if (!Director::is_ajax()) {
            return $this->forbiddenError();
        }

        if (!$this->packagePurchaseOrderSecurityToken->checkRequest($request)) {
            return $this->forbiddenError();
        }

        $body = $this->request->getBody();
        $json = json_decode($body, true);

        $this->packagePurchaseOrderSecurityToken->reset();

        try {
            $this->getPackagePurchaseOrderManager()->registerPurchaseOrder($json, new NewPurchaseOrderEmailMessageSender);
        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
        return $this->ok(array('token' => $this->packagePurchaseOrderSecurityToken->getValue()));
    }

    /**
     * @param $request
     * @return SS_HTTPResponse
     */
    public function searchOrg($request)
    {

        if (!Director::is_ajax()) {
            return $this->forbiddenError();
        }

        $term = $request->getVar('term');
        $term = Convert::raw2sql($term);

        $organizations = Org::get()->filter('Name:PartialMatch', $term)->limit(10);

        if ($organizations) {

            $suggestions = array();

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