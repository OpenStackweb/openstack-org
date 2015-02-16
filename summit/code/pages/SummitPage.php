<?php


class SummitPage extends Page {

    private static $has_one = array (
		'SummitImage' => 'SummitImage',
	);    

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        if($this->ID) {
            
            // Summit Images
            $summitImageField = singleton('SummitImage')->getCMSFields();
            $config = GridFieldConfig_RelationEditor::create();
            $config->getComponentByType('GridFieldDetailForm')->setFields($summitImageField);
            $gridField = new GridField('SummitImage', 'SummitImage', SummitImage::get(), $config);
            $fields->addFieldToTab('Root.SummitPageImages',$gridField);
            
            // Summit Image has_one selector
            
            $dropdown = DropdownField::create(
                'SummitImageID',
                'Please choose an image for this page',
                SummitImage::get()->map("ID", "Title", "Please Select")
            )
            ->setEmptyString('(None)');
            
            $fields->addFieldToTab('Root.Main',$dropdown);
            
        }
        return $fields;    
    }

}


class SummitPage_Controller extends Page_Controller {


	public function init() {
		parent::init();
	    Requirements::javascript("summit/javascript/jquery-1.11.0.js");
	    Requirements::javascript("summit/javascript/bootstrap.min.js");
	    Requirements::javascript("summit/bower_components/sweetalert/lib/sweet-alert.js");
	    Requirements::css("summit/bower_components/sweetalert/lib/sweet-alert.css");
		Requirements::javascript("summit/javascript/summit.js");
	}


	public function CurrentSummit() {
		$summit = Summit::get_active();

		return $summit->isInDB() ? $summit : false;
	}


	public function PreviousSummit() {
		return Summit::get()
			->sort('SummitEndDate','DESC')
			->filter('SummitEndDate:LessThan', date('Y-m-D'))
			->first();
	}


	public function CountdownDigits() {
		if(!$this->CurrentSummit()) return null;

		$date = $this->CurrentSummit()->obj('StartDate');
		
		if($date->InPast()) {
			return;
		}
        
        $exploded_date = explode(' ',$date->TimeDiffIn('days'),3);
        
		$days = str_pad($exploded_date[0], 3, '0', STR_PAD_LEFT);
		$list = ArrayList::create();

		foreach(str_split($days) as $digit) {
			$list->push(ArrayData::create(array(
				'Digit' => $digit
			)));
		}

		return $list;
	}
    
    function getTopSection() {
        return $this->Link();
    }


	public function IsWelcome() {
		return $this->request->getVar('welcome');
	}



}