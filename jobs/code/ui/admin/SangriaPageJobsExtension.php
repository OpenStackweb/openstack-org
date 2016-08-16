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
 * Class SangriaPageJobsExtension
 */
final class SangriaPageJobsExtension extends Extension {

    /**
     * @var IJobRegistrationRequestRepository
     */
	private $repository;
    /**
     * @var IJobRepository
     */
    private $live_repository;

	public function __construct(IJobRegistrationRequestRepository $repository, IJobRepository $live_repository){
		$this->repository      = $repository;
        $this->live_repository = $live_repository;
		parent::__construct();
	}

	public function onBeforeInit(){
		Config::inst()->update(get_class($this), 'allowed_actions', array('ViewJobsDetails','ViewPostedJobs'));
		Config::inst()->update(get_class($this->owner), 'allowed_actions', array('ViewJobsDetails','ViewPostedJobs'));
	}

	public function onAfterInit(){

	}

	private function commonScripts(){
		Requirements::css("themes/openstack/css/chosen.css", "screen,projection");
		Requirements::javascript("themes/openstack/javascript/chosen.jquery.min.js");
		Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
		Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
		Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');
		Requirements::javascript("themes/openstack/javascript/jquery.ui.datepicker.validation.package-1.0.1/jquery.ui.datepicker.validation.js");
		Requirements::javascript("themes/openstack/javascript/jquery.validate.custom.methods.js");
		Requirements::javascript('marketplace/code/ui/admin/js/utils.js');
		Requirements::javascript('themes/openstack/javascript/jquery.cleanform.js');
		Requirements::javascript('themes/openstack/javascript/pure.min.js');
	}

	public function ViewJobsDetails(){
		$this->commonScripts();
		Requirements::css("jobs/css/sangria.page.view.job.details.css");
		Requirements::javascript('jobs/js/admin/sangria.page.job.extension.js');
		return $this->owner->getViewer('ViewJobsDetails')->process($this->owner);
	}

	public function getJobRegistrationRequests(){
		list($list,$size) = $this->repository->getAllNotPostedAndNotRejected(0,1000);
		return new ArrayList($list);
	}

    public function ViewPostedJobs(){
        $this->commonScripts();
        Requirements::css("jobs/css/sangria.page.view.job.details.css");
        Requirements::javascript('jobs/js/admin/sangria.page.job.extension.js');
        return $this->owner->getViewer('ViewPostedJobs')->process($this->owner);
    }

    public function getPostedJobs(){
        list($list, $size) = $this->live_repository->getAllPosted(0, PHP_INT_MAX);
        return new ArrayList($list);
    }

    public function getPostedJobsCount(){
        list($list, $size) = $this->live_repository->getAllPosted(0, PHP_INT_MAX);
        return count($list);
    }

	public function getQuickActionsExtensions(&$html){
		$view = new SSViewer('SangriaPage_JobsLinks');
		$html .= $view->process($this->owner);
	}

	function JobRegistrationRequestForm(){
		$this->commonScripts();
		Requirements::javascript(Director::protocol()."maps.googleapis.com/maps/api/js?sensor=false");
		Requirements::javascript("marketplace/code/ui/admin/js/geocoding.jquery.js");
		Requirements::css('jobs/css/job.registration.form.css');
		Requirements::javascript("jobs/js/job.registration.form.js");
		$data = Session::get("FormInfo.Form_JobRegistrationRequestForm.data");
		$form = new JobRegistrationRequestForm($this->owner, 'JobRegistrationRequestForm',false);
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

    function JobForm(){
        $this->commonScripts();
        Requirements::javascript(Director::protocol()."maps.googleapis.com/maps/api/js?sensor=false");
        Requirements::javascript("marketplace/code/ui/admin/js/geocoding.jquery.js");
        Requirements::css('jobs/css/job.registration.form.css');
        Requirements::javascript("jobs/js/job.registration.form.js");
        $data = Session::get("FormInfo.Form_JobForm.data");
        $form = new JobForm($this->owner, 'JobForm',false);
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

}