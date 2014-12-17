<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class JobPage
 */
class JobPage
	extends Page
	implements IJob {


	static $db = array(
		'JobPostedDate'         => 'Date',
		'ExpirationDate'        => 'Date',
		'JobCompany'            => 'Text',
		'JobMoreInfoLink'       => 'Text',
		'JobLocation'           => 'Text',
		'FoundationJob'         => 'Boolean',
		'Active'                => 'Boolean',
		'JobInstructions2Apply' => 'HTMLText',
		'LocationType'          =>  "Enum('N/A, Remote, Various', 'N/A')",
	);


	static $has_many = array(
		'Locations'  => 'JobLocation',
	);

	private static $defaults = array(
		"Active" => 1,
	);

	private static $has_one = array();

	protected function onBeforeWrite() {
		parent::onBeforeWrite();
		if(empty($this->ExpirationDate)){
			$expiration_date = new DateTime;
			$expiration_date->add(new DateInterval('P2M'));
			$this->ExpirationDate = $expiration_date->format('Y-m-d');
		}
	}

	/**
	 * @return string
	 */
	public function getFormattedLocation(){
		if(!empty($this->LocationType)){
			switch($this->LocationType){
				case 'Various':{
					$res = '';
					foreach($this->locations() as $location){
						$str_location = $location->city();
						$state = $location->state();
						if(!empty($state))
							$str_location .= ', '.$state;
						$str_location .= ', '.$location->country();
						$res .= $str_location.'<BR>';
					}
					return $res;
				}
				break;
				case 'N/A':
					if(!emptY($this->JobLocation)){
						return $this->JobLocation;
					}
					return $this->LocationType;
					break;
				default:
					return $this->LocationType;
					break;
			}

		}
	}

	public function isExpired(){
		if(!empty($this->JobExpired)){
			$expiration_date = new DateTime($this->ExpirationDate);
			$now             = new DateTime;
			return $expiration_date <  $now;
		}
		return false;
	}

	public function RecentJob() {
		//check if the job posting is less than two weeks old
		return $this->JobPostedDate > date('Y-m-d H:i:s',strtotime('-2 weeks'));
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		// the date field is added in a bit more complex manner so it can have the dropdown date picker
		$JobPostedDate = new DateField('JobPostedDate','Date Posted');
		$JobPostedDate->setConfig('showcalendar', true);
		$JobPostedDate->setConfig('showdropdown', true);

		$fields->addFieldToTab('Root.Main', $JobPostedDate, 'Content');
		$fields->addFieldToTab('Root.Main', new DateField_Disabled('ExpirationDate','Expiration Date'), 'Content');
		$fields->addFieldToTab('Root.Main', new TextField('JobMoreInfoLink','More Information About This Job (URL)'), 'Content');
		$fields->addFieldToTab('Root.Main', new TextField('JobCompany','Company'), 'Content');
		$fields->addFieldToTab('Root.Main', new HtmlEditorField('JobInstructions2Apply','Job Instructions to Apply'), 'Content');
		$fields->addFieldToTab('Root.Main', new CheckboxField ('FoundationJob','This is a job with the OpenStack Foundation'));
		$fields->addFieldToTab('Root.Main', new CheckboxField ('Active','Is Active?'));
		$fields->addFieldToTab('Root.Main', new DropdownField('LocationType','Location Type',singleton('JobPage')->dbObject('LocationType')->enumValues()));
		// remove unneeded fields
		$fields->removeFieldFromTab("Root.Main","MenuTitle");


		// rename fields
		$fields->renameField("Content", "Job Description");
		$fields->renameField("Title", "Job Title");
		return $fields;
	}

	/**
	 * @return int
	 */
	public function getIdentifier()	{
		return (int)$this->getField('ID');
	}

	/**
	 * @return void
	 */
	public function deactivate(){
		$this->Active = 0;
	}

    public function toggleFoundation() {
        $this->FoundationJob = !$this->FoundationJob;
    }


	/**
	 * @return IJobLocation[]
	 */
	public function locations()
	{
		return AssociationFactory::getInstance()->getOne2ManyAssociation($this,'Locations')->toArray();
	}

	public function addLocation(IJobLocation $location)
	{
		AssociationFactory::getInstance()->getOne2ManyAssociation($this,'Locations')->add($location);
	}

    /**
     * @return JobMainInfo
     */
    function getMainInfo()
    {
        return new JobMainInfo($this->Title, $this->JobCompany,$this->JobMoreInfoLink, $this->Content, $this->JobInstructions2Apply, $this->LocationType, new DateTime($this->ExpirationDate));
    }

    /**
     * @param JobMainInfo $info
     * @return void
     */
    public function registerMainInfo(JobMainInfo $info)	{
        $this->Title              = $info->getTitle();
        $this->JobMoreInfoLink    = $info->getUrl();
        $this->Content            = $info->getDescription();
        $this->Instructions2Apply = $info->getInstructions();
        $this->LocationType       = $info->getLocationType();
        $this->JobCompany         = $info->getCompany()->Name;
    }

	/**
	 * @return void
	 */
	public function clearLocations()
	{
		AssociationFactory::getInstance()->getOne2ManyAssociation($this,'Locations')->removeAll();
	}

    public function getMoreInfoLink() {
        if(filter_var($this->JobMoreInfoLink, FILTER_VALIDATE_EMAIL)) {
            return '<a rel="nofollow" href="mailto:'.$this->JobMoreInfoLink.'" >More About This Job</a>';
        }
        else {
            return '<a rel="nofollow" href="'.$this->JobMoreInfoLink.'" target="_blank" >More About This Job</a>';
        }
    }

    public function getTitleForUrl() {
        $lcase_title = strtolower(trim($this->Title));
        $title_for_url = str_replace(' ','-',$lcase_title);
        return $title_for_url;
    }
}



final class JobPage_Controller extends Page_Controller {
    //Allow our form as an action
    static $allowed_actions = array(
        'JobForm',
        'saveJob',
    );

    /**
     * @var JobManager
     */
    private $manager;

    function init()	{
        parent::init();
        Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');
        Requirements::css("themes/openstack/css/chosen.css", "screen,projection");
        Requirements::javascript("themes/openstack/javascript/chosen.jquery.min.js");
        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.ui.datepicker.validation.package-1.0.1/jquery.ui.datepicker.validation.js");
        Requirements::javascript("themes/openstack/javascript/jquery.validate.custom.methods.js");
        Requirements::javascript(Director::protocol()."maps.googleapis.com/maps/api/js?sensor=false");
        Requirements::javascript("marketplace/code/ui/admin/js/geocoding.jquery.js");
        Requirements::javascript("marketplace/code/ui/admin/js/utils.js");
        Requirements::javascript('themes/openstack/javascript/pure.min.js');
        Requirements::javascript("jobs/js/job.registration.request.page.js");

        $this->manager = new JobManager(
            new SapphireJobRepository,
            new SapphireJobAlertEmailRepository,
            new JobFactory,
            new JobsValidationFactory,
            new SapphireJobPublishingService,
            SapphireTransactionManager::getInstance()
        );
    }

    function JobForm(){
        $data = Session::get("FormInfo.Form_JobForm.data");
        Requirements::css('jobs/css/job.registration.form.css');
        Requirements::javascript("jobs/js/job.registration.form.js");
        $form = new JobForm($this, 'JobForm');
        // we should also load the data stored in the session. if failed
        if(is_array($data)) {
            $form->loadDataFrom($data);
        }
        // Optional spam protection
        if(class_exists('SpamProtectorManager')) {
            SpamProtectorManager::update_form($form);
        }
        return $form;
    }

    function saveJob($data, Form $form){
        /*try{
            $this->manager->registerJob($data);
            Session::clear("FormInfo.Form_JobForm.data");
            return $this->redirect($this->Link('?saved=1'));
        }
        catch(EntityValidationException $ex1){
            $messages = $ex1->getMessages();
            $msg = $messages[0];
            $form->addErrorMessage('Title',$msg['message'] ,'bad');
            SS_Log::log($msg['message'] ,SS_Log::ERR);
            // Load errors into session and post back
            Session::set("FormInfo.Form_JobForm.data", $data);
            return $this->redirectBack();
        }
        catch(Exception $ex){
            $form->addErrorMessage('Title','Server Error','bad');
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            // Load errors into session and post back
            Session::set("FormInfo.Form_JobForm.data", $data);
            return $this->redirectBack();
        }*/
    }

    //Check for just saved
    function Saved(){
        return $this->request->getVar('saved');
    }
}