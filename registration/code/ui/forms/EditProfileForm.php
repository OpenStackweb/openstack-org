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
class EditProfileForm extends SafeXSSForm
{


    function __construct($controller, $name)
    {
        Requirements::css('node_modules/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');

        // Name Set
        $MemberIDField = new TextField('MemberID', "Member ID");
        $MemberIDField->setDisabled(true);
        $FirstNameField = new TextField('FirstName', "First Name");
        $LastNameField = new TextField('Surname', "Last Name");

        // Email Addresses
        $PrimaryEmailField = new TextField('Email', "Primary Email Address");
        $SecondEmailField = new TextField('SecondEmail', "Second Email Address");
        $ThirdEmailField = new TextField('ThirdEmail', "Third Email Address");

        // Replace Fields
        $ReplaceBioField = new HiddenField('ReplaceBio', 'ReplaceBio', 0);
        $ReplaceNameField = new HiddenField('ReplaceName', 'ReplaceName', 0);
        $ReplaceSurnameField = new HiddenField('ReplaceSurname', 'ReplaceSurname', 0);

        // Shirt Size Field
        $ShirtSizeField = new DropdownField(
            'ShirtSize',
            'Choose A Shirt Size',
            array(
                'Small' => "Men's Small",
                'Medium' => "Men's Medium",
                'Large' => "Men's Large",
                'XL' => "Men's XL",
                'XXL' => "Men's XXL",
                'WS' => "Womens Small",
                'WM' => "Womens Medium",
                'WL' => "Womens Large",
                'WXL' => "Womens XL",
                'WXXL' => 'Womens XXL'
            ));


        $StatementOfInterestField = new TextField('StatementOfInterest', 'Statement of Interest');
        $StatementOfInterestField->addExtraClass('autocompleteoff');

        // Photo
        $PhotoField = new CustomUploadField('Photo', 'Photo <em>(Optional)</em>');
        $PhotoField->setCanAttachExisting(false);
        $PhotoField->setAllowedMaxFileNumber(1);
        $PhotoField->setAllowedFileCategories('image');
        $PhotoField->setTemplateFileButtons('CustomUploadField_FrontEndFIleButtons');
        $PhotoField->setFolderName('profile-images');
        $sizeMB = 1; // 1 MB
        $size = $sizeMB * 1024 * 1024; // 1 MB in bytes
        $PhotoField->getValidator()->setAllowedMaxFileSize($size);
        $PhotoField->setCanPreviewFolder(false); // Don't show target filesystem folder on upload field

        // Biography
        $BioField = new TextAreaField('Bio', 'Bio: A Little Bit About You <em>(Optional)</em>', 10, 15);

        // Food Preference
        $FoodPreferenceField = new CheckboxSetField('FoodPreference',
            'Do you have any food preferences we can help accomodate?', array(
                'Vegan' => 'Vegan',
                'Vegetarian' => 'Vegetarian',
                'Gluten' => 'Gluten allergy',
                'Peanut' => 'Peanut allergy'
            ));
        $FoodPreferenceField->setTemplate('BootstrapAwesomeCheckboxsetField');

        // Other Field
        $OtherFoodField = new TextField('OtherFood', 'Other Food Considerations');
        $OtherFoodField->addExtraClass('other-field');

        // Github, IRC and Twitter
        $GitHubUserField = new GitHubUserField('GitHubUser', 'GitHub User <em>(Optional)</em>');
        $IRCHandleField = new TextField('IRCHandle', 'IRC Handle <em>(Optional)</em>');
        $TwitterNameField = new TextField('TwitterName', 'Twitter Name <em>(Optional)</em>');
        $LinkedInProfileField = new TextField('LinkedInProfile', 'LinkedIn Profile - full URL <em>(Optional)</em>');
        $LinkedInProfileField->setAttribute('type', 'url')->setAttribute('pattern', 'https?://.+');
        $ContactEmailField = new TextField('ContactEmail', 'Contact Email <em>(Optional)</em>');
        $WeChatUserField = new TextField('WeChatUser', 'WeChat User <em>(Optional)</em>');


        // Associated Projects
        $release = OpenStackRelease::get()->filter('Status', 'Current')->sort('ReleaseDate','DESC')->first();
        $components = $release->OpenStackComponents()->sort(array('IsCoreService'=>'DESC', 'Order'=>'ASC'))->map('CodeName','Name')->toArray();
        $component_list = array();
        array_walk($components, function (&$value,$key) use (&$component_list) {
            $new_key = $value.' ('.$key.')';
            $component_list[$new_key] = $new_key;
        });

        /*$component_list = array(
            'Nova' => 'Compute (Nova)',
            'Swift' => 'Object Storage (Swift)',
            'Glance' => 'Image Service (Glance)',
            'Keystone' => 'Identity Service (Keystone)',
            'Horizon' => 'Dashboard (Horizon)',
            'Quantum' => 'Networking (Quantum)',
            'Cinder' => 'Block Storage (Cinder)',
            'Ceilometer' => 'Metering/Monitoring (Ceilometer)',
            'Heat' => 'Orchestration (Heat)',
            'Trove' => 'Database Service (Trove)',
            'Ironic' => 'Bare Metal (Ironic)',
            'Queue' => 'Queue Service (Queue)',
            'DataProcessing' => 'Data Processing (DataProcessing)',
            'Oslo' => 'Common Libraries (Oslo)',
            'Openstack-ci' => 'Infrastructure (Openstack-ci)',
            'Openstack-manuals' => 'Documentation (Openstack-manuals)',
            'QA' => 'Quality Assurance (QA)',
            'Deployment' => 'Deployment (Deployment)',
            'DevStack' => 'DevStack (DevStack)',
            'Release' => 'Release Cycle Management (Release)'
        );*/

        $ProjectsField = new CheckboxSetField('Projects', 'What programs are you involved with? <em>(Optional)</em>',$component_list);
        $ProjectsField->setTemplate('BootstrapAwesomeCheckboxsetField');
        $ProjectsField->addExtraClass('columned-list');

        // Other Projects Field
        $OtherProjectField = new TextField('OtherProject', 'Other Project (if one above does not match)');
        $OtherProjectField->addExtraClass('other-field');

        //Newsletter Field
        $subscribedToNewsletterField = new CheckboxField('SubscribedToNewsletter',
            'I don\'t mind occasionally receiving updates and communications from the OpenStack Foundation.');
        $subscribedToNewsletterField->addExtraClass('checkbox');


        $DisplayOnSiteField = new CheckboxField('DisplayOnSite', 'Include this bio on openstack.org.');
        $DisplayOnSiteField->addExtraClass('checkbox');

        // New Gender Field
        $GenderField = new OptionSetField('Gender', 'I identify my gender as:', array(
            'Male' => 'Male',
            'Female' => 'Female',
            'Specify' => 'Let me specify',
            'Prefer not to say' => 'Prefer not to say'
        ));
        $GenderField->setTemplate('BootstrapAwesomeOptionsetField');

        $GenderSpecifyField = new TextField('GenderSpecify', 'Specify your gender');
        $GenderSpecifyField->addExtraClass('hide');

        $fields = new FieldList(
            new LiteralField('header', '<h3 class="section-divider">Email Addresses</h3>'),
            new LiteralField('instructions',
                '<p class="info"><strong>If you\'re an active developer on the OpenStack project, please list any email addresses you use to commit code.</strong> (This will really help us avoid duplicates!) If you contributed code ONLY using gerrit, all email addresses you used will be listed on the <a href="https://review.openstack.org/#/settings/web-identities" target="_blank">web identities page</a>. If you have contributed also <em>before</em> gerrit was put in place, please make an effort to remember other email addresses you may have used. Interested in how to <a href="http://wiki.openstack.org/HowToContribute" target="_blank">become a contributor</a>?</p>'),
            $PrimaryEmailField,
            new LiteralField('instructions',
                '<p class="info username_warning">This email address is also the account name you use to login.</p>'),
            $SecondEmailField,
            $ThirdEmailField,
            new LiteralField('header', '<h3 class="section-divider">Public Information</h3>'),
            $MemberIDField,
            new HeaderField("First & Last Name"),
            $FirstNameField,
            $LastNameField,
            $ReplaceBioField,
            $ReplaceNameField,
            $ReplaceSurnameField,
            new LiteralField('break', '<hr/>'),
            $affiliations = new AffiliationField("Affiliations", "Affiliations"),
            new LiteralField('break', '<hr/>'),
            $StatementOfInterestField,
            new LiteralField('instructions',
                '<p>Your statement of interest should be a few words describing your objectives or plans for OpenStack.</p>'),
            new LiteralField('break', '<hr/>'),
            $ContactEmailField,
            $GitHubUserField,
            $IRCHandleField,
            $TwitterNameField,
            $LinkedInProfileField,
            $WeChatUserField,
            $BioField,
            $PhotoField,

            new LiteralField('break', '<hr/>'),
            $ProjectsField,
            $OtherProjectField,

            new LiteralField('header', '<h3 class="section-divider">Private Information</h3>'),

            $GenderField,
            $GenderSpecifyField,
            new LiteralField('instructions',
                '<p>It\'s perfectly acceptable if you choose not to tell us: we appreciate you becoming a member of OpenStack Foundation. The information will remain private and only used to monitor our effort to improve gender diversity in our community.</p>'),

            new LiteralField('break', '<hr/>'),
            $FoodPreferenceField,
            $OtherFoodField,

            new LiteralField('break', '<hr/>'),
            $ShirtSizeField,
            $subscribedToNewsletterField,
            $DisplayOnSiteField,
            new LiteralField('break', '<hr/>'),
            new TextField('Address', _t('Addressable.ADDRESS', 'Street Address (Line1)')),
            new TextField('Suburb', _t('Addressable.SUBURB', 'Street Address (Line2)')),
            new TextField('City', _t('Addressable.CITY', 'City'))

        );

        // Address Continued
        $label = _t('Addressable.STATE', 'State');
        if (is_array($this->allowedStates)) {
            $fields->push(new DropdownField('State', $label, $this->allowedStates));
        } elseif (!is_string($this->allowedStates)) {
            $fields->push(new TextField('State', $label));
        }

        $fields->push(new TextField(
            'Postcode', _t('Addressable.POSTCODE', 'Postcode')
        ));

        $label = _t('Addressable.COUNTRY', 'Country');
        if (is_array($this->allowedCountries)) {
            $countryField = new DropdownField('Country', $label, $this->allowedCountries);
            $countryField->setEmptyString('-- Select One --');
            $fields->push($countryField);
        } elseif (!is_string($this->allowedCountries)) {
            $countryField = new CountryDropdownField('Country', $label);
            $countryField->setEmptyString('-- Select One --');
            $fields->push($countryField);
        }

        $fields->push(new LiteralField('break', '<hr/>'));


        $fields->push(new LiteralField('changepassword',
            '<a href="/Security/changepassword">Click here to change your password</a>'));


        // Create action
        $actions = new FieldList(
            new FormAction('SaveProfile', 'Save')
        );

        // Create validators

        $validator = new ConditionalAndValidationRule(array(
            new HtmlPurifierRequiredValidator('FirstName', 'Surname', 'StatementOfInterest', 'Address', 'City'),
            new RequiredFields('Email', 'Country')
        ));

        parent::__construct($controller, $name, $fields, $actions, $validator);
    }

    public function loadDataFrom($data, $mergeStrategy = 0, $fieldList = null)
    {
        if (count($_POST) == 0) {
            $Gender = is_array($data) ? @$data['Gender'] : $data->Gender;

            if ($Gender != 'Male' && $Gender != 'Female' && $Gender != 'Prefer not to say') {
                $this->fields->dataFieldByName('GenderSpecify')->setValue($Gender);
                $this->fields->dataFieldByName('GenderSpecify')->removeExtraClass('hide');
            }

        }

        $MemberID = is_array($data) ? @$data['ID'] : $data->ID;
        $this->fields->dataFieldByName('MemberID')->setValue($MemberID);

        parent::loadDataFrom($data, $mergeStrategy, $fieldList);
    }

}
