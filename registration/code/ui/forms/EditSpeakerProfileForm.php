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
        $LastNameField  = new TextField('LastName', "Last Name");
        $TitleField     = new TextField('Title',"Title");
        $BioField       = new TinyMCEEditorField('Bio',"Bio");

        $BioField->addExtraClass('bio');

        // Country Field
        $CountryCodes = CountryCodes::$iso_3166_countryCodes;
        $CountryField = new DropdownField('Country', 'Country of Residence', $CountryCodes);
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
        $WillingVideoField = new CheckboxField ('WillingToPresentVideo',"Willing to present via video conference.");

        // Funded Travel
        $FundedTravelField = new CheckboxField ('FundedTravel',"My Company would be willing to fund my travel to events.");

        // Willing to travel
        $WillingToTravel = new CheckboxField ('WillingToTravel',"I do not have any travel restrictions and am willing to travel to any country.");

        // Countries to travel
        $CountriesToTravelField = new MultiDropdownField(
            'CountriesToTravel',
            'Select individual countries that you are willing to travel to. If you do not check the box above AND do not select any countries, it will be assumed you are not willing to travel.',
            $CountryCodes
        );
        $CountriesToTravelField->addExtraClass('travel-field');

        // Spoken Languages
        $LanguageField = new TextField('Languages','Languages');

        // Area of Expertise
        $ExpertiseField = new TextField('Expertise','Expertise');

        // Links To Presentations
        $PresentationLinkField1  = new TextField('PresentationLink[1]','#1');
        $PresentationTitleField1 = new TextField('PresentationTitle[1]','');
        $PresentationLinkField2  = new TextField('PresentationLink[2]','#2');
        $PresentationTitleField2 = new TextField('PresentationTitle[2]','');
        $PresentationLinkField3  = new TextField('PresentationLink[3]','#3');
        $PresentationTitleField3 = new TextField('PresentationTitle[3]','');
        $PresentationLinkField4  = new TextField('PresentationLink[4]','#4');
        $PresentationTitleField4 = new TextField('PresentationTitle[4]','');
        $PresentationLinkField5  = new TextField('PresentationLink[5]','#5');
        $PresentationTitleField5 = new TextField('PresentationTitle[5]','');

        $NotesField = new TinyMCEEditorField('Notes',"Notes");
        $NotesField->addExtraClass('notes');

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
            $WillingVideoField->setValue($speaker->WillingToPresentVideo);
            $FundedTravelField->setValue($speaker->FundedTravel);
            $WillingToTravel->setValue($speaker->WillingToTravel);
            $NotesField->setValue($speaker->Notes);

            $expertise = implode(',', $speaker->AreasOfExpertise()->limit(5)->column('Expertise'));
            $ExpertiseField->setValue($expertise);

            $languages = implode(',', $speaker->Languages()->limit(5)->column('Name'));
            $LanguageField->setValue($languages);

            $country_array = array();
            foreach ($speaker->TravelPreferences() as $pref_country) {
                $country_array[] = $pref_country->Country;
            }
            $CountriesToTravelField->setValue(implode(',',$country_array));

            foreach ($speaker->OtherPresentationLinks() as $key => $presentation) {
                ${'PresentationLinkField'.($key+1)}->setValue($presentation->LinkUrl);
                ${'PresentationTitleField'.($key+1)}->setValue($presentation->Title);
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
            $WillingVideoField,
            $FundedTravelField,
            $WillingToTravel,
            $CountriesToTravelField,
            $LanguageField,
            $ExpertiseField,
            $PresentationLinkField1,
            $PresentationTitleField1,
            $PresentationLinkField2,
            $PresentationTitleField2,
            $PresentationLinkField3,
            $PresentationTitleField3,
            $PresentationLinkField4,
            $PresentationTitleField4,
            $PresentationLinkField5,
            $PresentationTitleField5,
            $NotesField
        );

        $save_action = new FormAction('addAction', 'Save Speaker Details');
        $save_action->addExtraClass('btn btn-primary');
        $actions = new FieldList($save_action);

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
            $speaker->Languages()->removeAll();
            if ($data['Languages']) {
                $languages = explode(',', $data['Languages']);
                foreach ($languages as $lang) {
                    if (trim($lang) != '') {
                        $spoken_lang = Language::get()->where(sprintf("LOWER(Name) = '%s'", strtolower(trim($lang))))->first();
                        if(is_null($spoken_lang)) continue;
                        $speaker->Languages()->add( $spoken_lang );
                    }
                }
            }


            // Expertise
            if (isset($data['Expertise'])) {
                $expertises = explode(',', $data['Expertise']);

                foreach ($expertises as $exp) {
                    if (trim($exp) != '') {
                        if(!$expertise = $speaker->AreasOfExpertise()->find('Expertise', $exp)) {
                            $expertise = SpeakerExpertise::create(['Expertise' => $exp]);
                            $speaker->AreasOfExpertise()->add( $expertise );
                        }
                    }
                }
                // remove missing
                foreach($speaker->AreasOfExpertise() as $exp){
                    if (!in_array($exp->Expertise, $expertises)) {
                        if(!$exp->exists()) continue;
                        $exp->delete();
                    }
                }
            } else {
                // remove all
                foreach($speaker->AreasOfExpertise() as $exp){
                    if(!$exp->exists()) continue;
                    $exp->delete();
                }
            }


            // Presentation Link
            if(isset($data['PresentationLink'])) {
                foreach ($data['PresentationLink'] as $key => $link) {
                    if (trim($link) != '') {
                        $presentation_title = trim($data['PresentationTitle'][$key]);
                        if ($has_link = $speaker->OtherPresentationLinks()->find('LinkUrl',$link)) {
                            $has_link->Title = $presentation_title;
                            $has_link->write();
                        } else {
                            $presentation_link = SpeakerPresentationLink::create(array(
                                'LinkUrl' => $link,
                                'Title'   => $presentation_title
                            ));
                            $speaker->OtherPresentationLinks()->add( $presentation_link );
                        }

                    }
                }
                // remove missing
                foreach($speaker->OtherPresentationLinks() as $pl){
                    if (!in_array($pl->LinkUrl, $data['PresentationLink'])) {
                        if(!$pl->exists()) continue;
                        $pl->delete();
                    }
                }
            }


            // Travel Preferences
            $current_items = $speaker->TravelPreferences()->column('Country');
            if(isset($data['CountriesToTravel'])) {
                //$countries = explode(',', $data['CountriesToTravel']);
                $countries = $data['CountriesToTravel'];
                foreach ($countries as $travel_country) {
                    if (!in_array($travel_country, $current_items)) {
                        $travel_pref = SpeakerTravelPreference::create(array(
                            'Country' => $travel_country
                        ));
                        $speaker->TravelPreferences()->add($travel_pref);
                    }
                }

                // remove missing
                foreach($speaker->TravelPreferences() as $tp){
                    if (!in_array($tp->Country, $countries)) {
                        if(!$tp->exists()) continue;
                        $tp->delete();
                    }
                }
            } else {
                // remove all
                foreach($speaker->TravelPreferences() as $tp){
                    if(!$tp->exists()) continue;
                    $tp->delete();
                }
            }


            $speaker->write();

            $form->sessionMessage('Your profile has been updated', 'good');
            Session::clear("FormInfo.{$form->FormName()}.data", $data);

            return $this->controller()->redirectBack();

        }
        return Security::PermissionFailure($this->controller, 'You must be <a href="/join">registered</a> and logged in to edit your profile:');
    }
}
