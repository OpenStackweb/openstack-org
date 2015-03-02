<?php

class SummitSponsorPage extends SummitPage {
    
    private static $db = array (
        'SponsorAlert' => 'HTMLText',
        'SponsorSteps' => 'HTMLText',
		'SponsorContract' => 'Text',
		'SponsorProspectus' => 'Text'        
    );    
    
	private static $has_many = array (
		'SummitPackages' => 'SummitPackage',
        'SummitAddOns'   => 'SummitAddOn'
	);


    private static $many_many = array(
        'Companies' => 'Company'
    );

    //sponsor type
    private static $many_many_extraFields = array(
        'Companies' => array(
            'SponsorshipType' => "Enum('Headline, Premier, Event, Startup, InKind, Spotlight', 'Startup')",
            'SubmitPageUrl'=>'Text',
            'LogoSize' => "Enum('None, Small, Medium, Large, Big', 'None')",
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
            
            
            // Summit Packages
            $questionFields = singleton('SummitPackage')->getCMSFields();
            $config = GridFieldConfig_RelationEditor::create();
            $config->getComponentByType('GridFieldDetailForm')->setFields($questionFields);
            $config->addComponent(new GridFieldSortableRows('Order'));
            $gridField = new GridField('SummitPackages', 'Sponsor Packages', $this->SummitPackages(), $config);
            $fields->addFieldToTab('Root.Packages',$gridField);
            
            // Summit Add Ons
            $addOnsFields = singleton('SummitAddOn')->getCMSFields();
            $config = GridFieldConfig_RelationEditor::create();
            $config->getComponentByType('GridFieldDetailForm')->setFields($addOnsFields);
            $config->addComponent(new GridFieldSortableRows('Order'));
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
                    "DDLLogoSize"        => "Logo Size",
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

            if(isset($_REQUEST["LogoSize_{$company->ID}"])){
                $logo_size = $_REQUEST["LogoSize_{$company->ID}"];
                $sql = "UPDATE SummitSponsorPage_Companies SET LogoSize ='{$logo_size}' WHERE CompanyID={$company->ID} AND SummitSponsorPageID={$this->ID};";
                DB::query($sql);
            }
        }
    }

}


class SummitSponsorPage_Controller extends SummitPage_Controller {
    
	private static $allowed_actions = array (
		'prospectus',
        'contract'
	);    

    public function init() {
		parent::init();
		Requirements::javascript("summit/javascript/Chart.min.js");		         
        Requirements::javascript("summit/javascript/sponsor.js");
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
    
	
}