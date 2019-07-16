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
        'CheckEmail',
        'signUp',
    );

    private static $url_handlers = array (
        'mobile/$MEMBERSHIP!' => 'MobileRegistrationPage',
        'signup' => 'signUp'
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
        $request = $this->getRequest();
        $params  = $request->params();
        $action  = $params['Action']??null;
        if($action == 'signup') return;
        $currentMember = Member::currentUser();
        if(is_null($currentMember)) {
            if($request->getURL() == "join/register"){
                return $this->signUp($request);
            }
            return $this->redirect(Director::absoluteBaseURL());
        }

        if($currentMember->hasMembershipTypeSet()){
            return $this->redirect(Director::absoluteURL("/profile"));
        }

        Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
        Requirements::block(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');

        if(Director::isLive()) {
            Requirements::javascript('node_modules/jquery-ui-dist/jquery-ui.min.js');
        }
        else{
            Requirements::javascript('node_modules/jquery-ui-dist/jquery-ui.js');
        }

        JSChosenDependencies::renderRequirements();
        JQueryValidateDependencies::renderRequirements();

        $css_files =  array(
            'registration/css/registration.page.css',
        );

        foreach($css_files as $css_file)
            Requirements::css($css_file);

        $js_scripts = array(
            "node_modules/pure/libs/pure.min.js",
            "themes/openstack/javascript/jquery.serialize.js",
            "themes/openstack/javascript/jquery.cleanform.js",
            "registration/javascript/registration.page.js",
            "themes/openstack/javascript/tag-it.js"
        );
        foreach($js_scripts as $js)
            Requirements::javascript($js);

    }

    function signUp($request){
        $type = $request->getVar("membership-type");
        if(empty($type)) $type = "foundation";
        $url = OpenStackIdCommon::getRegistrationUrl(Director::absoluteURL('/Security/login?BackURL='.urlencode('join/register/?membership-type='.$type)));
        return $this->redirect($url);
    }

    //Generate the registration form
    function RegistrationForm()
    {
        $currentMember = Member::currentUser();
        if(is_null($currentMember))
            throw new InvalidArgumentException();

        // Name Set
        $FirstNameField = new ReadonlyField('FirstName', "First Name", $currentMember->FirstName);
        $LastNameField  = new ReadonlyField('Surname', "Last Name", $currentMember->Surname);

        // Email Addresses
        $PrimaryEmailField = new ReadonlyField('Email', "Primary Email Address",  $currentMember->Email);
        // New Gender Field
        $GenderField = new OptionSetField('Gender', 'I identify my gender as:', [
            'Male'              => 'Male',
            'Female'            => 'Female',
            'Specify'           => 'Let me specify',
            'Prefer not to say' => 'Prefer not to say'
        ]);

        $GenderSpecifyField = new TextField('GenderSpecify', 'Specify your gender');
        $GenderSpecifyField->addExtraClass('hide');
        $gender = $currentMember->Gender;
        if ($gender != 'Male' && $gender != 'Female' && $gender != 'Prefer not to say') {
            $GenderSpecifyField->setValue($gender);
            $GenderSpecifyField->removeExtraClass('hide');
        }
        else{
            $GenderField->setValue($gender);
        }


        $affiliations = new AffiliationField('Affiliations', 'Affiliations');
        $affiliations->setMode('local');

        $fields = new FieldList(
            $FirstNameField,
            $LastNameField,
            new LiteralField('break', '<hr/>'),
            $PrimaryEmailField,
            new LiteralField('break', '<hr/>'),
            $GenderField,
            $GenderSpecifyField,
            new LiteralField('instructions', '<p>It\'s perfectly acceptable if you choose not to tell us: we appreciate you becoming a member of OpenStack Foundation. The information will remain private and only used to monitor our effort to improve gender diversity in our community.</p>'),
            new LiteralField('break', '<hr/>'),
            $affiliations,
            new ReadonlyField('StatementOfInterest', 'Statement of Interest', $currentMember->StatementOfInterest),
            new LiteralField('break', '<hr/>'),
            new ReadonlyField('Address', _t('Addressable.ADDRESS', 'Street Address (Line1)'), $currentMember->Address),
            new ReadonlyField('Suburb', _t('Addressable.SUBURB', 'Street Address (Line2)'), $currentMember->Suburb),
            new ReadonlyField('City', _t('Addressable.CITY', 'City'), $currentMember->City),
            new ReadonlyField('State', "State", $currentMember->State),
            new ReadonlyField('Postcode', 'Postcode', $currentMember->Postcode),
            new ReadonlyField('Country', 'Country', $currentMember->Country),
            new LiteralField('instructions', sprintf('<p>** all readonly fields are editable from <a href="%s/accounts/user/profile" target="_top">%s/accounts/user/profile</a></p>',IDP_OPENSTACKID_URL,IDP_OPENSTACKID_URL))
        );

        $fields->push(new LiteralField('break', '<hr/>'));
        $fields->push(new HiddenField('MembershipType', 'MembershipType', 'foundation'));

        $request  = Controller::curr()->getRequest();
        $back_url = $request->requestVar('BackURL');
        if(!empty($back_url))
        {
            $fields->push(new HiddenField('BackURL', 'BackURL', $back_url));
        }

        $actions = new FieldList(
            new FormAction('doRegister', 'Submit My Application')
        );

        $form =  new HoneyPotForm($this, 'RegistrationForm', $fields, $actions);

        if ($data = Session::get("FormInfo.{$form->FormName()}.data")) {
            if(isset($data['HiddenAffiliations']))
            {
                $affiliations->setValue($data['HiddenAffiliations']);
            }
            return $form->loadDataFrom($data);
        }
        $form->addExtraClass("registration-form");
        return $form;
    }

    function doRegister($data, $form)
    {
        try {
            // member must be logged already
            $currentMember = Member::currentUser();
            if(is_null($currentMember))
                throw new InvalidArgumentException();

            $data = SQLDataCleaner::clean($data, $non_check_keys = array('HiddenAffiliations'));

            Session::set("FormInfo.{$form->FormName()}.data", $data);
            $profile_page = EditProfilePage::get()->first();
            $member = $this->member_manager->register($currentMember, $data);
            //Get profile page
            if (!is_null($profile_page)) {
                //Redirect to profile page with success message
                Session::clear("FormInfo.{$form->FormName()}.data");
                $request         = Controller::curr()->getRequest();
                $former_back_url = $request->requestVar('BackURL');
                $back_url        = $profile_page->Link('?success=1');
                if(!empty($former_back_url)) $back_url .= "&BackURL=".$former_back_url;

                return OpenStackIdCommon::loginMember($member, $back_url);
            }
        }
        catch(EntityValidationException $ex1){
            Form::messageForForm($form->FormName(), $ex1->getMessage(), 'bad');
            //Return back to form
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->redirectBack();
        }
        catch(Exception $ex){
            Form::messageForForm($form->FormName(), "There was an error with your request, please contact your admin.", 'bad');
            //Return back to form
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->redirectBack();
        }
    }


    function MobileRegistrationPage()
    {
        $membership = $this->request->param('MEMBERSHIP');
        $url = OpenStackIdCommon::getRegistrationUrl(Director::absoluteURL('/Security/login?BackURL=join/register/?membership-type='.$membership));
        return $this->redirect($url);
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