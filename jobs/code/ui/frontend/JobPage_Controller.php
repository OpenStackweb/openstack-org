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
 * Class JobPage_Controller
 */
class JobPage_Controller extends Page_Controller {
    //Allow our form as an action
    static $allowed_actions = array(
        'JobForm',
        'saveJob',
    );

    /**
     * @var IJobManager
     */
    private $manager;

    public function getJobManager(){
        return $this->manager;
    }

    public function setJobManager(IJobManager $manager){
        $this->manager = $manager;
    }

    function init()	{
        parent::init();

        Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
        Requirements::css("themes/openstack/css/chosen.css", "screen,projection");

        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript(Director::protocol()."maps.googleapis.com/maps/api/js?sensor=false");
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');

        Requirements::javascript("themes/openstack/javascript/chosen.jquery.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.ui.datepicker.validation.package-1.0.1/jquery.ui.datepicker.validation.js");
        Requirements::javascript("themes/openstack/javascript/jquery.validate.custom.methods.js");
        Requirements::javascript("marketplace/code/ui/admin/js/geocoding.jquery.js");
        Requirements::javascript("marketplace/code/ui/admin/js/utils.js");
        Requirements::javascript('themes/openstack/javascript/pure.min.js');
        Requirements::javascript("jobs/js/job.registration.request.page.js");

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