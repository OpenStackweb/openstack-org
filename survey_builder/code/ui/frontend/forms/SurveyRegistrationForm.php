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

final class SurveyRegistrationForm extends Form
{

    /**
     * @var IMemberManager
     */
    private $member_manager;


    function __construct($controller, $name, IMemberManager $member_manager)
    {

        // Define fields //////////////////////////////////////
        $this->member_manager = $member_manager;
        $fields = new FieldList (
            new TextField('FirstName', 'First Name'),
            new TextField('Surname', 'Last Name'),
            new EmailField('Email', 'Email'),
            new HiddenField('BackURL', 'BackURL', Controller::curr()->Link()),
            new ConfirmedPasswordField('Password', 'Password')
        );

        $startSurveyButton = new FormAction('StartSurvey', 'Start Survey');
        $actions = new FieldList(
            $startSurveyButton
        );


        $validator = new RequiredFields("FirstName", "Surname", "Email","Password");


        parent::__construct($controller, $name, $fields, $actions, $validator);

    }

    function forTemplate()
    {
        return $this->renderWith(array(
            $this->class,
            'Form'
        ));
    }

    function StartSurvey($data, $form)
    {

         try {
             $data                   = SQLDataCleaner::clean($data);
             $data['MembershipType'] = 'community';

             Session::set("FormInfo.{$form->FormName()}.data", $data);
             $profile_page = EditProfilePage::get()->first();
             $member = $this->member_manager->registerMobile($data, new MemberRegistrationSenderService);
             //Get profile page
             if (!is_null($profile_page)) {
                 //Redirect to profile page with success message
                 Session::clear("FormInfo.{$form->FormName()}.data");

                 $request         = Controller::curr()->getRequest();
                 $former_back_url = $request->postVar('BackURL');
                 $back_url        = $profile_page->Link('?success=1');

                 if(!empty($former_back_url)) $back_url .= "&BackURL=".$former_back_url;
                 return OpenStackIdCommon::loginMember($member, $back_url);
             }
         }
         catch(EntityValidationException $ex1){
             Form::messageForForm($form->FormName() ,$ex1->getMessage(), 'bad');
             //Return back to form
             SS_Log::log($ex1->getMessage(), SS_Log::WARN);
             return Controller::curr()->redirectBack();
         }
         catch(Exception $ex){
             Form::messageForForm($form->FormName(), "There was an error with your request, please contact your admin.", 'bad');
             //Return back to form
             SS_Log::log($ex->getMessage(), SS_Log::ERR);
             return Controller::curr()->redirectBack();
         }
    }
}