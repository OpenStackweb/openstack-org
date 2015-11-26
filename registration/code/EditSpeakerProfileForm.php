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
class EditSpeakerProfileForm extends SafeXSSForm {

    function __construct($controller, $name, $speaker = null, $member = null, $email = null)
    {
        // Get the city for the current member
        if($member) {
            $country = $member->Country;
        } else {
            $country = '';
        }

        // Fields
        $FirstNameField = new TextField('FirstName', "First Name");
        $LastNameField = new TextField('LastName', "Last Name");
        $TitleField = new TextField('Title',"Title");
        $BioField = new TextAreaField('Bio',"Bio");

        // Country Field
        $CountryCodes = CountryCodes::$iso_3166_countryCodes;
        $CountryField = new DropdownField('Country', 'Country', $CountryCodes);
        $CountryField->setEmptyString('-- Select One --');
        $CountryField->setValue($country);

        // ID Fields
        $SpeakerIDField = new HiddenField('SpeakerID', 'SpeakerID', "");
        $MemberIDField = new HiddenField('MemberID','MemberID');

        // Replace Fields
        $ReplaceBioField = new HiddenField('ReplaceBio', 'ReplaceBio',0);
        $ReplaceNameField = new HiddenField('ReplaceName','ReplaceName',0);
        $ReplaceSurnameField = new HiddenField('ReplaceSurname','ReplaceSurname',0);

        // IRC and Twitter
        $IRCHandleField = new TextField('IRCHandle', 'IRC Handle <em>(Optional)</em>');
        $TwiiterNameField = new TextField('TwitterName', 'Twitter Name <em>(Optional)</em>');

        // Upload Speaker Photo
        $PhotoField = new CustomUploadField('Photo', 'Upload a speaker photo');
	    $PhotoField->setCanAttachExisting(false);
	    $PhotoField->setAllowedMaxFileNumber(1);
	    $PhotoField->setAllowedFileCategories('image');
        $PhotoField->setTemplateFileButtons('CustomUploadField_FrontEndFIleButtons');
	    $PhotoField->setFolderName('profile-images');
	    $sizeMB = 2; // 1 MB
	    $size = $sizeMB * 1024 * 1024; // 1 MB in bytes
	    $PhotoField->getValidator()->setAllowedMaxFileSize($size);
	    $PhotoField->setCanPreviewFolder(false); // Don't show target filesystem folder on upload field

        // Opt In Field
        $OptInField = new CheckboxField ('AvailableForBureau',"I'd like to be in the speaker bureau.");

        // Funded Travel
        $FundedTravelField = new CheckboxField ('FundedTravel',"My Company would be willing to fund my travel to events.");

        // Willing to travel
        $WillingToTravel = new CheckboxField ('WillingToTravel',"I am willing to travel to events.");

        // Countries to travel
        $CountriesToTravelField = new MultiDropdownField('CountriesToTravel', 'Countries willing to travel to', $CountryCodes);
        $CountriesToTravelField->setEmptyString('-- Select One --');

        // Spoken Languages
        $LanguageField1 = new TextField('Language[1]','#1');
        $LanguageField2 = new TextField('Language[2]','#2');
        $LanguageField3 = new TextField('Language[3]','#3');
        $LanguageField4 = new TextField('Language[4]','#4');
        $LanguageField5 = new TextField('Language[5]','#5');

        // Area of Expertise
        $ExpertiseField1 = new TextField('Expertise[1]','#1');
        $ExpertiseField2 = new TextField('Expertise[2]','#2');
        $ExpertiseField3 = new TextField('Expertise[3]','#3');
        $ExpertiseField4 = new TextField('Expertise[4]','#4');
        $ExpertiseField5 = new TextField('Expertise[5]','#5');

        // Links To Presentations
        $PresentationLinkField1 = new TextField('PresentationLink[1]','#1');
        $PresentationLinkField2 = new TextField('PresentationLink[2]','#2');
        $PresentationLinkField3 = new TextField('PresentationLink[3]','#3');
        $PresentationLinkField4 = new TextField('PresentationLink[4]','#4');
        $PresentationLinkField5 = new TextField('PresentationLink[5]','#5');

        // Load Existing Data if present
        if($speaker) {
	        $this->record = $speaker;
            $FirstNameField->setValue($speaker->FirstName);
            $LastNameField->setValue($speaker->LastName);
            $BioField->setValue($speaker->Bio);
            $SpeakerIDField->setValue($speaker->ID);
            $MemberIDField->setValue($speaker->MemberID);
            $TitleField->setValue($speaker->Title);
            $IRCHandleField->setValue($speaker->IRCHandle);
            $TwiiterNameField->setValue($speaker->TwitterName);
            $PhotoField->setValue(null, $speaker);
            $OptInField->setValue($speaker->AvailableForBureau);
            $FundedTravelField->setValue($speaker->FundedTravel);
            $WillingToTravel->setValue($speaker->WillingToTravel);

            foreach ($speaker->AreasOfExpertise() as $key => $expertise) {
                if ($key > 4) exit;
                ${'ExpertiseField'.($key+1)}->setValue($expertise->Expertise);
            }

            foreach ($speaker->Languages() as $key => $language) {
                if ($key > 4) exit;
                ${'LanguageField'.($key+1)}->setValue($language->Language);
            }

            $country_array = array();
            foreach ($speaker->TravelPreferences() as $pref_country) {
                $country_array[] = $pref_country->Country;
            }
            $CountriesToTravelField->setValue(implode(',',$country_array));

            // first we pull the summit presentations we have
            $presentation_count = 0;
            foreach ($speaker->Presentations() as $key => $presentation) {
                if ($key > 4) exit;
                ${'PresentationLinkField'.($key+1)}->setValue(Director::absoluteURL($presentation->Link()));
                ${'PresentationLinkField'.($key+1)}->setDisabled(true);
                $presentation_count++;
            }
            // if are there any places left we see if he has his own links
            if ($presentation_count < 5) {
                foreach ($speaker->OtherPresentationLinks() as $index => $other_presentation) {
                    $new_key = $index + $presentation_count + 1;
                    ${'PresentationLinkField'.$new_key}->setValue($other_presentation->LinkUrl);
                }
            }

        } elseif($member) {
            $FirstNameField->setValue($member->FirstName);
            $LastNameField->setValue($member->LastName);
            $BioField->setValue($member->Bio);
            $MemberIDField->setValue($member->ID);
            $IRCHandleField->setValue($member->IRCHandle);
            $TwiiterNameField->setValue($member->TwitterName);
        }


        $fields = new FieldList(
            $FirstNameField,
            $LastNameField,
            $TitleField,
            $CountryField,
            $BioField,
            $SpeakerIDField,
            $MemberIDField,
            $ReplaceBioField,
            $ReplaceNameField,
            $ReplaceSurnameField,
            $IRCHandleField,
            $TwiiterNameField,
            $PhotoField,
            $OptInField,
            $FundedTravelField,
            $WillingToTravel,
            $CountriesToTravelField,
            $LanguageField1,
            $LanguageField2,
            $LanguageField3,
            $LanguageField4,
            $LanguageField5,
            $ExpertiseField1,
            $ExpertiseField2,
            $ExpertiseField3,
            $ExpertiseField4,
            $ExpertiseField5,
            $PresentationLinkField1,
            $PresentationLinkField2,
            $PresentationLinkField3,
            $PresentationLinkField4,
            $PresentationLinkField5
        );

        $actions = new FieldList(
            new FormAction('addAction', 'Save Speaker Details')
        );

        $validator = new RequiredFields(
            'FirstName',
            'LastName',
            'Title'
        );

        parent::__construct($controller, $name, $fields, $actions, $validator);
    }

    function addAction($data, $form) {

        //Check for a logged in member
        if ($CurrentMember = Member::currentUser()) {
            // Find a site member (in any group) based on the MemberID field
            $id = Convert::raw2sql($data['MemberID']);
            $member = DataObject::get_by_id("Member", $id);

            if ($data['SpeakerID'] && is_numeric($data['SpeakerID'])) {
                $speaker = PresentationSpeaker::get()->byID(intval($data['SpeakerID']));
            } elseif ($member) {
                $speaker = PresentationSpeaker::get()->filter('MemberID', $member->ID)->first();
            }

            if (!$speaker) {
                $speaker = new PresentationSpeaker();
            }

            //Find or create the 'speaker' group
            if(!$userGroup = DataObject::get_one('Group', "Code = 'speakers'"))
            {
                $userGroup = new Group();
                $userGroup->Code = "speakers";
                $userGroup->Title = "Speakers";
                $userGroup->Write();
                $member->Groups()->add($userGroup);
            }
            //Add member to the group
            $member->Groups()->add($userGroup);

            if(($data['Country'] != '') && ($data['Country'] != $member->Country)) {
                $member->Country = convert::raw2sql($data['Country']);
            }

            if ($data['ReplaceName'] == 1) {
                $member->FirstName = $data['FirstName'];
            }
            if ($data['ReplaceSurname'] == 1) {
                $member->Surname = $data['LastName'];
            }
            if ($data['ReplaceBio'] == 1) {
                $member->Bio = $data['Bio'];
            }

            $member->write();

			$form->saveInto($speaker);
            $speaker->MemberID = $member->ID;
            $speaker->AdminID = Member::currentUser()->ID;
            // Attach Photo
            if($member->PhotoID && $speaker->PhotoID == 0) {
                $speaker->PhotoID = $member->PhotoID;
            }

            $speaker->AskedAboutBureau = TRUE;

            // Languages
            foreach ($speaker->Languages() as $currentlang) {
                $currentlang->delete();
            }
            foreach ($data['Language'] as $lang) {
                if (trim($lang) != '') {
                    $spoken_lang = SpeakerLanguage::create(array(
                        'Language' => $lang
                    ));
                    $speaker->Languages()->add( $spoken_lang );
                }
            }

            // Expertise
            foreach ($speaker->AreasOfExpertise() as $currentexp) {
                $currentexp->delete();
            }
            foreach ($data['Expertise'] as $exp) {
                if (trim($exp) != '') {
                    $expertise = SpeakerExpertise::create(array(
                        'Expertise' => $exp
                    ));
                    $speaker->AreasOfExpertise()->add( $expertise );
                }
            }

            // Presentation Link
            foreach ($speaker->OtherPresentationLinks() as $currentpres) {
                $currentpres->delete();
            }
            foreach ($data['PresentationLink'] as $link) {
                if (trim($link) != '') {
                    $presentation_link = SpeakerPresentationLink::create(array(
                        'LinkUrl' => $link
                    ));
                    $speaker->OtherPresentationLinks()->add( $presentation_link );
                }
            }

            // Travel Preferences
            foreach ($speaker->TravelPreferences() as $current_tf) {
                $current_tf->delete();
            }
            foreach ($data['CountriesToTravel'] as $travel_country) {
                $travel_pref = SpeakerTravelPreference::create(array(
                    'Country' => $travel_country
                ));
                $speaker->TravelPreferences()->add( $travel_pref );
            }


            $speaker->write();


	        $this->controller->redirect($this->controller()->Link().'speaker?saved=1');

        }
        else {
            return Security::PermissionFailure($this->controller, 'You must be <a href="/join">registered</a> and logged in to edit your profile:');
        }
    }
}