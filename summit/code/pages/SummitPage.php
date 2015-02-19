<?php


class SummitPage extends Page {

    private static $has_one = array (
		'SummitImage' => 'SummitImage',
	);

	private static $db = array(
		//Google Analitycs Params
		'GAConversionId'=>'Text',
		'GAConversionLanguage'=>'Text',
		'GAConversionFormat'=>'Text',
		'GAConversionColor'=>'Text',
		'GAConversionLabel'=>'Text',
		'GAConversionValue'=>'Int',
		'GARemarketingOnly'=>'Boolean',
		//Facebook Conversion Params
		'FBPixelId'        => 'Text',
		'FBValue'          => 'Text',
		'FBCurrency'       => 'Text',
	);

	static $defaults = array(
		//Google
		"GAConversionId" => "994798451",
		"GAConversionLanguage" => "en",
		"GAConversionFormat" => "3",
		"GAConversionColor" => "ffffff",
		"GAConversionLabel" => "IuM5CK3OzQYQ89at2gM",
		"GAConversionValue" => 0,
		"GARemarketingOnly" => false,
		//FB
		'FBPixelId'        => '6013247449963',
		'FBValue'          => '0.00',
		'FBCurrency'       => 'USD',
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
		//Google Conversion Tracking params
		$fields->addFieldToTab("Root.GoogleConversionTracking",new TextField("GAConversionId","Conversion Id","994798451"));
		$fields->addFieldToTab("Root.GoogleConversionTracking",new TextField("GAConversionLanguage","Conversion Language","en"));
		$fields->addFieldToTab("Root.GoogleConversionTracking",new TextField("GAConversionFormat","Conversion Format","3"));
		$fields->addFieldToTab("Root.GoogleConversionTracking",new ColorField("GAConversionColor","Conversion Color","ffffff"));
		$fields->addFieldToTab("Root.GoogleConversionTracking",new TextField("GAConversionLabel","Conversion Label","IuM5CK3OzQYQ89at2gM"));
		$fields->addFieldToTab("Root.GoogleConversionTracking",new TextField("GAConversionValue","Conversion Value","0"));
		$fields->addFieldToTab("Root.GoogleConversionTracking",new CheckboxField("GARemarketingOnly","Remarketing Only"));
		//Facebook Conversion Params
		$fields->addFieldToTab("Root.FacebookConversionTracking",new TextField("FBPixelId","Pixel Id","6013247449963"));
		$fields->addFieldToTab("Root.FacebookConversionTracking",new TextField("FBValue","Value","0.00"));
		$fields->addFieldToTab("Root.FacebookConversionTracking",new TextField("FBCurrency","Currency","USD"));
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

	/*
    * Return google tracking script if ?order=complete query string param is present
    *  using settings of current conference page
    */
	function GATrackingCode(){
		$request = $this->request;
		$order = $request->requestVar("order");
		$tracking_code = '';
		if(isset($order) && $order=="complete"){
			//add GA tracking script
			$page = SummitOverviewPage::get()->byID($this->ID);
			if($page && !empty($page->GAConversionId)
				&& !empty($page->GAConversionLanguage)
				&& !empty($page->GAConversionFormat)
				&& !empty($page->GAConversionColor)
				&& !empty($page->GAConversionLabel)){
				$tracking_code = $this->renderWith("SummitPage_GA",array(
					"GA_Data"=> new ArrayData(array(
						"GAConversionId"       => $page->GAConversionId,
						"GAConversionLanguage" => $page->GAConversionLanguage,
						"GAConversionFormat"   => $page->GAConversionFormat,
						"GAConversionColor"    => $page->GAConversionColor,
						"GAConversionLabel"    => $page->GAConversionLabel,
						"GAConversionValue"    => $page->GAConversionValue,
						"GARemarketingOnly"    => $page->GARemarketingOnly?"true":"false",
					))
				));
			}
		}
		return $tracking_code;
	}

	function FBTrackingCode(){
		$request = $this->request;
		$order = $request->requestVar("order");
		$tracking_code = '';
		if(isset($order) && $order=="complete"){
			//add FB tracking script
			$page = ConferencePage::get()->byID($this->ID);
			if($page && !empty($page->FBPixelId)
				&& !empty($page->FBValue)
				&& !empty($page->FBCurrency)
			){
				$tracking_code = $this->renderWith("SummitPage_FB",array(
					"FB_Data"=> new ArrayData(array(
						"FBPixelId"  => $page->FBPixelId,
						"FBValue"    => $page->FBValue,
						"FBCurrency" => $page->FBCurrency,
					))
				));
			}
		}
		return $tracking_code;
	}


}