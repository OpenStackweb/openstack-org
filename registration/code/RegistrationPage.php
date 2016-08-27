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
class RegistrationPage extends Page
{

}

class RegistrationPage_Controller extends Page_Controller
{

    //Allow our form as an action
    static $allowed_actions = array(
        'RegistrationForm',
        'MobileRegistrationForm',
        'MobileRegistrationPage',
        'results',
        'CheckEmail'
    );

    private static $url_handlers = array (
        'mobile/$MEMBERSHIP!' => 'MobileRegistrationPage'
    );

    /**
     * @var IMemberManager
     */
    private $member_manager;

    /**
     * @return IMemberManager
     */
    public function getMemberManager()
    {
        return $this->member_manager;
    }

    /**
     * @param IMemberManager $manager
     */
    public function setMemberManager(IMemberManager $manager)
    {
        $this->member_manager = $manager;
    }

    public function CheckEmail()
    {
        $email = $this->request->getVar('Email');
        //Check for existing member email address
        $res = true;
        if ($member = Member::get()->filter('Email', Convert::raw2sql($email))->first()) {
            $res = false;
        }
        echo json_encode($res);
    }

    function init()
    {
        parent::init();

        Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');

        $css_files =  array(
            "themes/openstack/css/chosen.css",
            'registration/css/registration.page.css',
        );

        foreach($css_files as $css_file)
            Requirements::css($css_file);

        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');

        $js_scripts = array(
            "themes/openstack/javascript/jquery.ui.datepicker.validation.package-1.0.1/jquery.ui.datepicker.validation.js",
            "themes/openstack/javascript/jquery.validate.custom.methods.js",
            "themes/openstack/javascript/chosen.jquery.min.js",
            "themes/openstack/javascript/pure.min.js",
            "themes/openstack/javascript/jquery.serialize.js",
            "themes/openstack/javascript/jquery.cleanform.js",
            "registration/javascript/registration.page.js",
            "themes/openstack/javascript/tag-it.js"
        );
        foreach($js_scripts as $js)
            Requirements::javascript($js);

    }

    //Generate the registration form
    function RegistrationForm()
    {

        // Name Set
        $FirstNameField = new TextField('FirstName', "First Name");
        $LastNameField = new TextField('Surname', "Last Name");

        // Email Addresses
        $PrimaryEmailField = new TextField('Email', "Primary Email Address");
        // New Gender Field
        $GenderField = new OptionSetField('Gender', 'I identify my gender as:', array(
            'Male' => 'Male',
            'Female' => 'Female',
            'Specify' => 'Let me specify',
            'Prefer not to say' => 'Prefer not to say'
        ));
        $GenderSpecifyField = new TextField('GenderSpecify', 'Specify your gender');
        $GenderSpecifyField->addExtraClass('hide');

        $StatementOfInterestField = new TextField('StatementOfInterest', 'Statement of Interest');
        $StatementOfInterestField->addExtraClass('autocompleteoff');


        $affiliations = new AffiliationField('Affiliations', 'Affiliations');
        $affiliations->setMode('local');

        $fields = new FieldList(
            $FirstNameField,
            $LastNameField,
            new LiteralField('break', '<hr/>'),
            $PrimaryEmailField,
            new LiteralField('instructions', '<p>This will also be your login name.</p>'),
            new LiteralField('break', '<hr/>'),
            $GenderField,
            $GenderSpecifyField,
            new LiteralField('instructions', '<p>It\'s perfectly acceptable if you choose not to tell us: we appreciate you becoming a member of OpenStack Foundation. The information will remain private and only used to monitor our effort to improve gender diversity in our community.</p>'),
            new LiteralField('break', '<hr/>'),
            $affiliations,
            $StatementOfInterestField,
            new LiteralField('instructions', '<p>Your statement of interest should be a few words describing your objectives or plans for OpenStack.</p>'),
            new LiteralField('break', '<hr/>'),
            new TextField('Address', _t('Addressable.ADDRESS', 'Street Address (Line1)')),
            new TextField('Suburb', _t('Addressable.SUBURB', 'Street Address (Line2)')),
            new TextField('City', _t('Addressable.CITY', 'City'))
        );

        $label = _t('Addressable.STATE', 'State');
        if (is_array($this->allowedStates)) {
            $fields->push(new DropdownField('State', $label, $this->allowedStates));
        } elseif (!is_string($this->allowedStates)) {
            $fields->push(new TextField('State', $label));

        }

        $AdressField = new TextField(
            'Postcode', _t('Addressable.POSTCODE', 'Postcode')
        );

        $fields->push($AdressField);

        $label = _t('Addressable.COUNTRY', 'Country');
        if (is_array($this->allowedCountries)) {
            $countryField = new DropdownField('Country', $label, $this->allowedCountries);
            $countryField->addExtraClass('chzn-select');
            $countryField->setEmptyString('-- Select One --');
            $fields->push($countryField);
        } elseif (!is_string($this->allowedCountries)) {
            $countryField = new CountryDropdownField('Country', $label);
            $countryField->setEmptyString('-- Select One --');
            $countryField->addExtraClass('chzn-select');
            $fields->push($countryField);
        }

        $fields->push(new LiteralField('break', '<hr/>'));

        $fields->push(new ConfirmedPasswordField('Password', 'Password'));

        $fields->push(new HiddenField('MembershipType', 'MembershipType', 'foundation'));

        $request  = Controller::curr()->getRequest();
        $back_url = $request->getVar('BackURL');
        if(!empty($back_url))
        {
            $fields->push(new HiddenField('BackURL', 'BackURL', $back_url));
        }

        $actions = new FieldList(
            new FormAction('doRegister', 'Submit My Application')
        );

        $validator = new Member_Validator(
            'FirstName',
            'Surname',
            'Email',
            'StatementOfInterest',
            'Address',
            'City',
            'Country',
            'Password'
        );

        $form =  new HoneyPotForm($this, 'RegistrationForm', $fields, $actions, $validator);

        if ($data = Session::get("FormInfo.{$form->FormName()}.data")) {
            if(isset($data['HiddenAffiliations']))
            {
                $affiliations->setValue($data['HiddenAffiliations']);
            }
            return $form->loadDataFrom($data);
        }

        return $form;
    }

    function doRegister($data, $form)
    {
        try {
            $data = SQLDataCleaner::clean($data, $non_check_keys = array('HiddenAffiliations'));

            Session::set("FormInfo.{$form->FormName()}.data", $data);
            $profile_page = EditProfilePage::get()->first();
            $member = $this->member_manager->register($data, new MemberRegistrationSenderService);
            //Get profile page
            if (!is_null($profile_page)) {
                //Redirect to profile page with success message
                Session::clear("FormInfo.{$form->FormName()}.data");
                $request  = Controller::curr()->getRequest();
                $back_url = $request->postVar('BackURL');
                $link     = $profile_page->Link('?success=1');
                if(!empty($back_url)) $link .= "&BackURL=".$back_url;
                return OpenStackIdCommon::loginMember($member, $link);
            }
        }
        catch(EntityValidationException $ex1){
            Form::messageForForm('HoneyPotForm_RegistrationForm',$ex1->getMessage(), 'bad');
            //Return back to form
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->redirectBack();
        }
        catch(Exception $ex){
            Form::messageForForm('HoneyPotForm_RegistrationForm', "There was an error with your request, please contact your admin.", 'bad');
            //Return back to form
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->redirectBack();
        }
    }

    function MobileRegistrationPage()
    {
        $membership = $this->request->param('MEMBERSHIP');
        Requirements::block("registration/javascript/affiliations.js");
        Requirements::block("registration/javascript/registration.page.js");
        Requirements::css('registration/css/mobile.registration.page.css');
        Requirements::javascript("registration/javascript/mobile.registration.page.js");

        echo $this->renderWith(array('MobileRegistrationPage','RegistrationPage','Page'), array('Membership' => $membership));
    }

    //Generate the registration form for mobile
    function MobileRegistrationForm($membership)
    {
        //Membership
        $membershipField = new HiddenField('MembershipType',null,$membership);
        // Name Set
        $FirstNameField = new TextField('FirstName', "First Name");
        $LastNameField = new TextField('Surname', "Last Name");
        // Country
        $label = _t('Addressable.COUNTRY', 'Country');
        $countryField = new CountryDropdownField('Country', $label);
        $countryField->setEmptyString('-- Select One --');
        $countryField->addExtraClass('chzn-select');
        // Email Addresses
        $PrimaryEmailField = new TextField('Email', "Primary Email Address");

        $fields = new FieldList(
            $membershipField,
            $FirstNameField,
            $LastNameField,
            $countryField,
            new LiteralField('break', '<hr/>'),
            $PrimaryEmailField,
            new LiteralField('login_text','<div class="login_lit"> You will use this to login. </div>')
        );

        $fields->push(new ConfirmedPasswordField('Password', 'Password'));

        $actions = new FieldList(
            new FormAction('doRegisterMobile', 'Complete Registration')
        );

        $validator = new Member_Validator(
            'FirstName',
            'Surname',
            'Email',
            'Country',
            'Password'
        );

        $form =  new HoneyPotForm($this, 'MobileRegistrationForm', $fields, $actions, $validator);

        if ($data = Session::get("FormInfo.{$form->FormName()}.data")) {
            return $form->loadDataFrom($data);
        }

        return $form;
    }



    //Submit the registration form
    function doRegisterMobile($data, $form)
    {
        try {
            $data = SQLDataCleaner::clean($data);

            Session::set("FormInfo.{$form->FormName()}.data", $data);
            $profile_page = EditProfilePage::get()->first();
            $member = $this->member_manager->registerMobile($data, new MemberRegistrationSenderService);
            //Get profile page
            if (!is_null($profile_page)) {
                //Redirect to profile page with success message
                Session::clear("FormInfo.{$form->FormName()}.data");
                $request  = Controller::curr()->getRequest();
                $back_url = $request->postVar('BackURL');
                $link     = $profile_page->Link('?success=1');
                if(!empty($back_url)) $link .= "&BackURL=".$back_url;
                return OpenStackIdCommon::loginMember($member, $link);
            }
        }
        catch(EntityValidationException $ex1){
            Form::messageForForm('HoneyPotForm_MobileRegistrationForm',$ex1->getMessage(), 'bad');
            //Return back to form
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->redirectBack();
        }
        catch(Exception $ex){
            Form::messageForForm('HoneyPotForm_MobileRegistrationForm', "There was an error with your request, please contact your admin.", 'bad');
            //Return back to form
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->redirectBack();
        }
    }

    function LegalTerms()
    {
        return LegalDocumentPage::get()->byID(422);
    }

    // This method is used to autocomplete match org names as they are entered
    // It's called via Ajax on the OrgName field

    public function results()
    {
        if ($query = $this->getSearchQuery()) {
            $query = Convert::raw2xml($query);

            // Search Orgs against the query.

            $Results = Org::get()->filter('Name:PartialMatch', $query);

            // For AutoComplete
            if (Director::is_ajax() && $Results) {

                $Orgs = $Results->map('ID', 'Name');
                $Suggestions = "";

                foreach ($Orgs as $Org) {
                    $Suggestions = $Suggestions . $Org . '|' . '1' . "\n";
                }

                return $Suggestions;
            }

        }
    }

    function getSearchQuery()
    {
        if ($this->request)
            return $this->request->getVar("q");
    }
}