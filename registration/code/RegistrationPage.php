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
        'results',
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

    function init()
    {
        parent::init();

        Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');

        $css_files =  array(
            "themes/openstack/css/chosen.css",
            "registration/css/affiliations.css",
            'registration/css/registration.page.css',
        );

        foreach($css_files as $css_file)
            Requirements::css($css_file);

        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');

        Requirements::combine_files('registration.js', array(
            "themes/openstack/javascript/jquery.ui.datepicker.validation.package-1.0.1/jquery.ui.datepicker.validation.js",
            "themes/openstack/javascript/jquery.validate.custom.methods.js",
            "themes/openstack/javascript/chosen.jquery.min.js",
            "themes/openstack/javascript/pure.min.js",
            "themes/openstack/javascript/jquery.serialize.js",
            "themes/openstack/javascript/jquery.cleanform.js",
            "registration/javascript/affiliations.js",
            "registration/javascript/registration.page.js",
            "themes/openstack/javascript/tag-it.js"
        ));
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

    //Submit the registration form
    function doRegister($data, $form)
    {
        try {
            $data = SQLDataCleaner::clean($data, $non_check_keys = array('HiddenAffiliations'));

            Session::set("FormInfo.{$form->FormName()}.data", $data);
            $profile_page = EditProfilePage::get()->first();
            $member = $this->member_manager->register($data, $profile_page, new MemberRegistrationSenderService);
            //Get profile page
            if (!is_null($profile_page)) {
                //Redirect to profile page with success message
                Session::clear("FormInfo.{$form->FormName()}.data");
                return OpenStackIdCommon::loginMember($member, $profile_page->Link('?success=1'));
            }
        }
        catch(EntityValidationException $ex1){
            Form::messageForForm('HoneyPotForm_RegistrationForm',$ex1->getMessage(), 'bad');
            //Return back to form
            SS_Log::log($ex1->getMessage(), SS_Log::ERR);
            return $this->redirectBack();
        }
        catch(Exception $ex){
            Form::messageForForm('HoneyPotForm_RegistrationForm', "There was an error with your request, please contact your admin.", 'bad');
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