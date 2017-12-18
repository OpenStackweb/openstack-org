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

    private static $db = [
        'SponsorIntro'                        => 'HTMLText',
        'SponsorAlert'                        => 'HTMLText',
        'SponsorContract'                     => 'Text',
        'SponsorProspectus'                   => 'Text',
        'CallForSponsorShipStartDate'         => 'SS_Datetime',
        'CallForSponsorShipEndDate'           => 'SS_Datetime',
        'AudienceIntro'                       => 'HTMLText',
        'ShowAudience'                        => 'Boolean',
        'AudienceMetricsTitle'                => 'Text',
        'AudienceTotalSummitAttendees'        => 'Text',
        'AudienceCompaniesRepresented'        => 'Text',
        'AudienceCountriesRepresented'        => 'Text',
        'HowToSponsorContent'                 => 'HTMLText',
        'VenueMapContent'                     => 'HTMLText',
        'SponsorshipPackagesTitle'            => 'HTMLText',
        'ConditionalSponsorshipPackagesTitle' => 'HTMLText',
        'SponsorshipAddOnsTitle'              => 'HTMLText'
    ];

    private static $has_many = [
        'AttendeesByRegion' => 'SummitPieDataItemRegion',
        'AttendeesByRoles'  => 'SummitPieDataItemRole'
    ];

    private static $has_one = [
        'CrowdImage'   => 'BetterImage',
        'ExhibitImage' => 'BetterImage'
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('GoogleConversionTracking');
        $fields->removeByName('FacebookConversionTracking');
        $fields->removeByName('TwitterConversionTracking');
        $fields->removeByName('SummitPageImages');
        $fields->removeFieldFromTab('Root.Main','Content');

        $fields_array = array();
        HtmlEditorConfig::set_active('simple');

        // Main
        $fields_array[] = new TextField('SponsorAlert', 'Sponsor Alert');
        $fields_array[] = new HtmlEditorField('SponsorIntro', 'Sponsor Intro Text');
        $fields_array[] = new HtmlEditorField('HowToSponsorContent', 'How To Sponsor (Bottom)');
        $fields_array[] = new HtmlEditorField('VenueMapContent', 'Venue Map Content');
        $fields->addFieldsToTab('Root.Main', $fields_array);

        // Packages & Addons
        $start_date = new DatetimeField('CallForSponsorShipStartDate', 'Call For SponsorShip - Start Date');
        $end_date   = new DatetimeField('CallForSponsorShipEndDate', 'Call For SponsorShip - End Date');
        $start_date->getDateField()->setConfig('showcalendar', true);
        $start_date->setConfig('dateformat', 'dd/MM/yyyy');
        $end_date->getDateField()->setConfig('showcalendar', true);
        $end_date->setConfig('dateformat', 'dd/MM/yyyy');
        $packages_title = new HTMLEditorField('SponsorshipPackagesTitle', 'Sponsorship Packages Title');
        $packages_title->setRows(8);
        $conditional_title = new HTMLEditorField('ConditionalSponsorshipPackagesTitle', 'Conditional Sponsorship Packages Title');
        $conditional_title->setRows(8);
        $addons_title = new HTMLEditorField('SponsorshipAddOnsTitle', 'Sponsorship AddOns Title');
        $addons_title->setRows(8);

        $fields->addFieldsToTab('Root.Packages&Addons', [$start_date,$end_date,$packages_title,$conditional_title,$addons_title]);


        if ($this->ID) {
            //set current page id
            $_REQUEST["PageId"] = $this->ID;

            // Images and Files
            $upload_0 = new UploadField('CrowdImage','Crowd Image');
            $upload_0->setFolderName('summits/sponsorship/background');
            $upload_0->setAllowedMaxFileNumber(1);
            $upload_0->setAllowedFileCategories('image');
            $upload_1 = new UploadField('ExhibitImage','Exhibit Image');
            $upload_1->setFolderName('summits/sponsorship/background');
            $upload_1->setAllowedMaxFileNumber(1);
            $upload_1->setAllowedFileCategories('image');
            $prospectusField = new TextField('SponsorProspectus');
            $contractField = new TextField('SponsorContract');
            $fields->addFieldsToTab("Root.Images&Files", [$upload_0,$upload_1,$prospectusField,$contractField]);

            // sponsors
            $companies = new GridField('Sponsors', 'Sponsors', $this->Summit()->Sponsors(), GridFieldConfig_RelationEditor::create(PHP_INT_MAX));
            $companies->getConfig()->removeComponentsByType('GridFieldEditButton');
            $companies->getConfig()->removeComponentsByType('GridFieldAddNewButton');
            $companies->getConfig()->removeComponentsByType('GridFieldAddExistingAutocompleter');
            $completer = new SponsorsGridFieldAddExistingAutocompleter();
            $completer->setSearchList(Company::get());
            $completer->setPlaceholderText('Search by Company Name');
            $completer->setSummitID($this->Summit()->ID);
            $companies->getConfig()->addComponent($completer);
            $companies->getConfig()->getComponentByType('GridFieldDataColumns')->setDisplayFields(
                [
                    'Company.Name'       => 'Name',
                    'DDLSponsorshipType' => 'Sponsorship Type',
                    'InputSubmitPageUrl' => 'Sponsor Link'
                ]
            );

            $config = GridFieldConfig_RecordEditor::create();
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $sponsorship_types = new GridField('SponsorshipTypes', 'Sponsoship Types', SponsorshipType::get(), $config);

            $fields->addFieldsToTab('Root.SponsorCompanies', [$companies,$sponsorship_types]);


            //audience
            $config = GridFieldConfig_RecordEditor::create();
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $attendees_region = new GridField('AttendeesByRegion', 'Attendees By Region', $this->AttendeesByRegion(), $config);
            $config = GridFieldConfig_RecordEditor::create();
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $attendees_roles = new GridField('AttendeesByRoles', 'Attendees By Roles', $this->AttendeesByRoles(), $config);

        }

        //audience
        $fields->addFieldsToTab('Root.Audience', new CheckboxField('ShowAudience', 'Show Audience'));
        $fields->addFieldsToTab('Root.Audience', $audience_intro = new HtmlEditorField('AudienceIntro', 'Intro'));
        $audience_intro->setRows(8);
        $fields->addFieldsToTab('Root.Audience', new TextField('AudienceMetricsTitle', 'Metrics Title'));
        $fields->addFieldsToTab('Root.Audience', new TextField('AudienceTotalSummitAttendees', 'Total Summit Attendees'));
        $fields->addFieldsToTab('Root.Audience', new TextField('AudienceCompaniesRepresented', 'Companies Represented'));
        $fields->addFieldsToTab('Root.Audience', new TextField('AudienceCountriesRepresented', 'Countries Represented'));
        if ($this->ID) {
            $fields->addFieldsToTab('Root.Audience', [$attendees_region,$attendees_roles]);
        }

        return $fields;

    }

    public function getSortedPackages()
    {
        return $this->Summit()->SummitPackages()->sort('Order');
    }

    public function getSortedAddOns()
    {
        return $this->Summit()->SummitAddOns()->sort('Order');
    }

    /**
     * @return bool
     */
    public function HasDiscountPackages(){
        foreach($this->Summit()->SummitPackages()->sort('Order') as $package){
            if($package->DiscountPackages()->count() > 0) return true;
        }
        return false;
    }

    function onAfterWrite()
    {
        parent::onAfterWrite();
        //update all relationships with sponsors
        foreach ($this->Summit()->Sponsors() as $sponsor) {
            if (isset($_REQUEST["SponsorshipType_{$sponsor->ID}"])) {
                $type = $_REQUEST["SponsorshipType_{$sponsor->ID}"];
                $sql = "UPDATE Sponsor SET SponsorshipTypeID ='{$type}' WHERE CompanyID={$sponsor->CompanyID} AND SummitID={$this->Summit()->ID};";
                DB::query($sql);
            }
            if (isset($_REQUEST["SubmitPageUrl_{$sponsor->ID}"])) {
                $page_url = $_REQUEST["SubmitPageUrl_{$sponsor->ID}"];
                $sql = "UPDATE Sponsor SET SubmitPageUrl ='{$page_url}' WHERE CompanyID={$sponsor->CompanyID} AND SummitID={$this->Summit()->ID};";
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

    public function getSponsorshipPackagesTitle(){
        $res = $this->getField('SponsorshipPackagesTitle');
        if (empty($res))
            $res = 'Sponsorships Packages Available <span>(prices in USD)</span>';
        return $res;
    }

    public function getConditionalSponsorshipPackagesTitle(){
        $res = $this->getField('ConditionalSponsorshipPackagesTitle');
        if (empty($res))
            $res = "*NEW* Special ‘Bundle & Save’ Discount Sponsorships Packages Available for the November 2017 Summit in Sydney, Australia (prices in USD)";
        return $res;
    }

    public function getSponsorshipAddOnsTitle(){
        $res = $this->getField('SponsorshipAddOnsTitle');
        if (empty($res))
            $res = "Sponsorship Add-Ons Available <span>(prices in USD)</span>";
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
        foreach ($this->getSponsorshipTypes() as $type) {
            if ($this->getSponsorsByType($type)) return true;
        }

        return false;
    }

    public function getSponsorsByType($type)
    {
        $page_id = $this->ID;
        $page = SummitSponsorPage::get()->byID($page_id);
        $res = $page->Summit()->Sponsors()
                ->leftJoin('SponsorshipType', 'SponsorshipType.ID = Sponsor.SponsorshipTypeID')
                ->where("SponsorshipType.Name='{$type}'");

        return $res;
    }

    public function getSponsorshipTypes()
    {
        return SponsorshipType::get()->sort('Order');
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
        JQueryValidateDependencies::renderRequirements(true, false);
        JQueryUIDependencies::renderRequirements();
        Requirements::javascript('themes/openstack/javascript/jquery.cleanform.js');

        Requirements::javascript('node_modules/pure/libs/pure.min.js');
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
        Requirements::css('themes/openstack/javascript/secondary-nav.jquery/secondary-nav.jquery.css');
        Requirements::javascript('themes/openstack/javascript/secondary-nav.jquery/secondary-nav.jquery.js');
        Requirements::javascript("summit/javascript/summit-sponsor-page.js");
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