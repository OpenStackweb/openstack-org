<?php

class SummitSponsorPage extends SummitPage {
    
    private static $db = array (
        'SponsorAlert' => 'HTMLText',
        'SponsorSteps' => 'HTMLText',
		'SponsorContract' => 'Text'        
    );    
    
	private static $has_many = array (
		'SummitPackages' => 'SummitPackage',
        'SummitAddOns'   => 'SummitAddOn'
	);    

    private static $has_one = array (
		'SponsorProspectus' => 'File'
	);

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        if($this->ID) {
            
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
            
            $prospectusField = new UploadField('SponsorProspectus');
            $fields->addFieldToTab('Root.ProspectusAndContract',$prospectusField);

            $contractField = new TextField('SponsorContract');
            $fields->addFieldToTab('Root.ProspectusAndContract',$contractField);            
                        
        }
        return $fields;    

    }    
    
    public function getSortedPackages() {
        return $this->SummitPackages()->sort('Order');
    }    

    public function getSortedAddOns() {
        return $this->SummitAddOns()->sort('Order');
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
        $ProspectusURL = $this->SponsorProspectus()->Link();
        return Controller::redirect($ProspectusURL);
    }

    public function contract() {
        $contractURL = $this->SponsorContract;
        return Controller::redirect($contractURL);
    }
    
    
	
}