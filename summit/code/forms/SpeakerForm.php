<?php

/**
 * Class SpeakerForm
 */
class SpeakerForm extends BootstrapForm
{
    /**
     * @var ISummit
     */
    private $summit;

    public function __construct($controller, $name, $actions, ISummit $summit)
    {
        $this->summit = $summit;

        parent::__construct(
            $controller, 
            $name, 
            $this->getSpeakerFields(),
            $actions,
            $this->getSpeakerValidator()
        );

        $form_id = $this->FormName();

        Requirements::customScript("var form_id = '{$form_id}';");
        JQueryValidateDependencies::renderRequirements(true,false);
        JSChosenDependencies::renderRequirements();
        BootstrapTagsInputDependencies::renderRequirements();
        Requirements::css('summit/css/speaker-form.css');
        Requirements::css('node_modules/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');

        // languages
        $languages_source = 'var language_source = [];'.PHP_EOL;
        foreach(Language::get() as $lang){
            $languages_source .= sprintf("language_source.push({id: %s, name:'%s'});".PHP_EOL, $lang->ID, $lang->Name);
        }

        Requirements::customScript($languages_source);
        Requirements::javascript('summit/javascript/speaker-form.js');
    }

    protected function getSpeakerFields()
    {
        $organizational_roles = SpeakerOrganizationalRole::get()->where('IsDefault',1)->map('ID','Role');
        $organizational_roles->push(0,'Other');

        $fields =  FieldList::create()
            ->text('FirstName',"Speaker's first name")
                ->configure()
                    ->setAttribute('autofocus','TRUE')
                ->end()            
            ->text('LastName', "Speaker's last name")
            ->text('Title', "Speaker's title (100 char max)")
                ->configure()
                    ->setMaxLength(100)
                ->end()
            ->dropdown('Country', 'Country of Residence', CountryCodes::$iso_3166_countryCodes)
                ->configure()
                     ->setEmptyString('-- Select One --')
                ->end()
            ->tinyMCEEditor('Bio',"Speaker's Bio")
                ->configure()
                    ->setRows(12)
                    ->setRequired(true)
                ->end()
            ->text('IRCHandle','IRC Handle (optional)')
                ->configure()
                    ->setMaxLength(25)
                ->end()
            ->text('TwitterName','Twitter Handle (optional)')
                ->configure()
                    ->setMaxLength(50)
                ->end()
            ->fileAttachment('Photo','Upload a speaker photo')
                ->configure()
                    ->setPermission('delete', false)
                    ->setAcceptedFiles(array('png','gif','.jpeg','.jpg'))
                    ->setView('grid')
                    // ->setThumbnailWidth(100)
                    // ->setThumbnailHeight(100)
                    ->setMaxFilesize(1)
                ->end()
            ->bootstrapIgnore('Photo')
            ->literal('DisclaimerTitle','<hr><label>Disclaimer</label>')
            ->literal('RecordingAndPublishingLegalAgreement',sprintf('<div class="disclaimer">Speakers agree that OpenStack Foundation may record and publish their talks presented during the %s Open Infrastructure Summit. If you submit a proposal on behalf of a speaker, you represent to OpenStack Foundation that you have the authority to submit the proposal on the speaker’s behalf and agree to the recording and publication of their presentation.</div>',$this->summit->Title))
            ->literal('BureauTitle','<label>Want to be in the Speakers\' Bureau?</label>')
            ->literal('BureauText','<div class="bureau-text">In addition to the OpenStack Infrastructure Summit, we regularly recruit speakers for OpenStack community events around the world. If you would like to be considered for more speaking opportunities, please indicate your interest in being listed in the speaker’s bureau and complete the below questions so event organizers can learn more about you.</div>')
            ->checkbox('AvailableForBureau', "I'd like to be in the speaker bureau")
                ->configure()
                    ->addExtraClass('bureau-checkbox')
                    ->setFieldHolderTemplate('BootstrapAwesomeCheckboxField')
                ->end()
            ->checkbox('WillingToPresentVideo', "Willing to present via video conference")
                ->configure()
                    ->addExtraClass('bureau-checkbox')
                    ->setFieldHolderTemplate('BootstrapAwesomeCheckboxField')
                ->end()
            ->literal('SpokenLanguagesTitle','<hr><label>Spoken Languages ( Up to 5)</label>')
            ->text('Language','')
                ->configure()
                    ->addHolderClass('nolabel')
                ->end()
            ->literal('ExpertiseTitle','<label>Areas of Expertise ( Up to 5)</label>')
            ->text('Expertise','')
                ->configure()
                    ->addHolderClass('nolabel')
                ->end()
            ->literal('PresentationTitle','<label>Links To Previous Presentations ( Up to 5)</label><br>')
            ->text('PresentationLink[1]','Link')
                ->configure()
                    ->addHolderClass('col-md-6')
                ->end()
            ->text('PresentationTitle[1]','Title')
                ->configure()
                    ->addHolderClass('col-md-6')
                ->end()
            ->text('PresentationLink[2]','')
                ->configure()
                    ->addHolderClass('col-md-6 nolabel')
                ->end()
            ->text('PresentationTitle[2]','')
                ->configure()
                    ->addHolderClass('col-md-6 nolabel')
                ->end()
            ->text('PresentationLink[3]','')
                ->configure()
                    ->addHolderClass('col-md-6 nolabel')
                ->end()
            ->text('PresentationTitle[3]','')
                ->configure()
                    ->addHolderClass('col-md-6 nolabel')
                ->end()
            ->text('PresentationLink[4]','')
                ->configure()
                    ->addHolderClass('col-md-6 nolabel')
                ->end()
            ->text('PresentationTitle[4]','')
                ->configure()
                    ->addHolderClass('col-md-6 nolabel')
                ->end()
            ->text('PresentationLink[5]','')
                ->configure()
                    ->addHolderClass('col-md-6 nolabel')
                ->end()
            ->text('PresentationTitle[5]','')
                ->configure()
                    ->addHolderClass('col-md-6 nolabel')
                ->end()
            ->literal('HR','<div class="clearfix"></div><hr>')
            ->checkbox('WillingToTravel', 'I do not have any travel restrictions and am willing to travel to any country')
                ->configure()
                    ->setFieldHolderTemplate('BootstrapAwesomeCheckboxField')
                ->end()
            ->multidropdown(
                'CountriesToTravel',
                'Select individual countries that you are willing to travel to. If you do not check the box above AND do not select any countries, it will be assumed you are not willing to travel.',
                CountryCodes::$iso_3166_countryCodes)
                ->configure()
                    ->addExtraClass('countries-to-travel')
                ->end()
            ->checkbox('FundedTravel', 'My Company would be willing to fund my travel to events')
                ->configure()
                    ->setFieldHolderTemplate('BootstrapAwesomeCheckboxField')
                    ->addExtraClass('bureau-checkbox')
                ->end()
            ->checkboxset('OrganizationalRole','What is your current Organizational Role at your company? (check all that apply):',$organizational_roles)
                ->configure()
                    ->setTemplate('BootstrapAwesomeCheckboxsetField')
                ->end()
            ->text('OtherOrganizationalRole','Please specify your role:')
                ->configure()
                    ->displayIf('OrganizationalRole')
                        ->hasCheckedOption(0)
                    ->end()
                ->end()
            ->literal('HR','<hr>')
            ->optionset('OrgHasCloud', 'Is your organization Operating an OpenStack cloud?', array(
                1 => 'Yes',
                0 => 'No'
            ))
                ->configure()
                    ->setTemplate('BootstrapAwesomeOptionsetField')
                ->end()
            ->hidden('HasChanged',0);

        return $fields;
    }

    public function getSpeakerValidator() {
        return RequiredFields::create('FirstName','LastName','Title', 'RecordingAndPublishingLegalAgreement', 'Language','Expertise','Bio');
    }

    public function loadDataFrom($data, $mergeStrategy = 0, $fieldList = null)
    {
        parent::loadDataFrom($data, $mergeStrategy, $fieldList);
        if(!$data instanceof PresentationSpeaker) return;

        $speaker = $data;

        if($speaker->Member()->ID > 0)
        {
            // populate from member
            if(empty($speaker->FirstName))  $this->fields->fieldByName('FirstName')->setValue($speaker->Member()->FirstName);
            if(empty($speaker->LastName))  $this->fields->fieldByName('LastName')->setValue($speaker->Member()->Surname);
            if(empty($speaker->Bio))  $this->fields->fieldByName('Bio')->setValue($speaker->Member()->Bio);
            if(empty($speaker->IRCHandle))  $this->fields->fieldByName('IRCHandle')->setValue($speaker->Member()->IRCHandle);
            if(empty($speaker->TwitterName))  $this->fields->fieldByName('TwitterName')->setValue($speaker->Member()->TwitterName);
        }

        $this->fields->fieldByName('Expertise')->setValue(implode(',', $speaker->AreasOfExpertise()->map('ID','Expertise')->toArray()));
        $this->fields->fieldByName('Language')->setValue(implode(',', $speaker->Languages()->map('ID','Name')->toArray()));

        $country_array = array();
        foreach ($speaker->TravelPreferences() as $pref_country) {
            $country_array[] = $pref_country->Country;
        }

        foreach ($speaker->OtherPresentationLinks() as $key => $presentation) {
            $this->fields->fieldByName('PresentationLink['.($key+1).']')->setValue($presentation->LinkUrl);
            $this->fields->fieldByName('PresentationTitle['.($key+1).']')->setValue($presentation->Title);
        }

        $role_ids = array();
        foreach ($speaker->OrganizationalRoles() as $role) {
            if($role->IsDefault) {
                $role_ids[] = $role->ID;
            } else { //add other
                $role_ids[] = 0;
                $this->fields->fieldByName('OtherOrganizationalRole')->setValue($role->Role);
            }
        }
        $this->fields->fieldByName('OrganizationalRole')->setValue($role_ids);

        $countries_2_travel = $this->fields->fieldByName('CountriesToTravel');
        if(!is_null($countries_2_travel))
                                                                                                                                                                                                                                                                                                                                                                                                                                                            {
            $country_array = array();
            foreach ($speaker->TravelPreferences() as $pref_country) {
                $country_array[] = $pref_country->Country;
            }
            $this->fields->fieldByName('CountriesToTravel')->setValue(implode(',', $country_array));
        }
        return $this;
    }

    public function saveInto(DataObjectInterface $dataObject, $fieldList = null) {

        parent::saveInto($dataObject, $fieldList);

        if(!$dataObject instanceof PresentationSpeaker) return;

        $speaker = $dataObject;

        // Expertise
        $expertise_csv   = $this->fields->fieldByName("Expertise")->Value();
        if ($expertise_csv) {
            $expertises = explode(',', $expertise_csv);

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
                    $exp->delete();
                }
            }
        } else {
            // remove all
            foreach($speaker->AreasOfExpertise() as $exp){
                $exp->delete();
            }
        }


        // Languages
        $language = $this->fields->fieldByName("Language")->Value();
        $speaker->Languages()->removeAll();
        foreach(explode(',',$language) as $lang_name) {
            $lang = Language::get()->where(sprintf("LOWER(Name) = '%s'", strtolower($lang_name)))->first();
            if(!$lang) continue;
            $speaker->Languages()->add($lang);
        }

        // Presentation Link
        $links = [];
        for($i = 1 ; $i <= 5 ; $i++ ){
            $link = $this->fields->fieldByName("PresentationLink[{$i}]");
            $title = $this->fields->fieldByName("PresentationTitle[{$i}]");
            if(is_null($link)) continue;
            $link_val  = Convert::raw2sql(trim($link->Value()));

            if(empty($link_val)) continue;
            $title_val = (is_null($title)) ? '' : Convert::raw2sql(trim($title->Value()));

            if (!$alink = $speaker->OtherPresentationLinks()->find('LinkUrl',$link_val)) {
                $alink = SpeakerPresentationLink::create(array('LinkUrl' => $link_val, 'Title' => $title_val));
            } else {
                $alink->Title = $title_val;
            }

            $links[] = $alink->LinkUrl;
            $alink->write();
            $speaker->OtherPresentationLinks()->add( $alink );
        }

        // remove missing links
        foreach($speaker->OtherPresentationLinks() as $pl){
            if (!in_array($pl->LinkUrl, $links)) {
                $pl->delete();
            }
        }


        // Org Roles
        $roles = $this->fields->fieldByName("OrganizationalRole")->Value();
        if ($roles && in_array(0,$roles)) { // 0 is the id for Other
            $other_role = $this->fields->fieldByName("OtherOrganizationalRole")->Value();
            $other_role = Convert::raw2sql(trim($other_role));
            $new_role   = SpeakerOrganizationalRole::get()->where("Role = '$other_role' ")->first();
            if (!$new_role) {
                $new_role = new SpeakerOrganizationalRole(array('Role' => $other_role, 'IsDefault' => 0));
                $new_role->write();
            }
            array_pop($roles);
            $roles[] = $new_role->ID;
        }
        $speaker->OrganizationalRoles()->setByIdList($roles);


        // Travel Preferences
        $countries_2_travel = $this->fields->fieldByName('CountriesToTravel');
        if(!is_null($countries_2_travel)) {
            $country_array  = $countries_2_travel->Value();
            if ($country_array) {
                foreach($country_array as $country_name)
                {
                    $country_name = Convert::raw2sql(trim($country_name));
                    if (!$acountry = $speaker->TravelPreferences()->find('Country',$country_name)) {
                        $acountry = SpeakerTravelPreference::create(array('Country' => $country_name));
                        $speaker->TravelPreferences()->add($acountry);
                    }
                }
                // remove missing
                foreach($speaker->TravelPreferences() as $tp){
                    if (!in_array($tp->Country, $country_array)) {
                        $tp->delete();
                    }
                }
            } else {
                // remove all
                foreach($speaker->TravelPreferences() as $tp){
                    $tp->delete();
                }
            }
        } else {
            // remove all
            foreach($speaker->TravelPreferences() as $tp){
                $tp->delete();
            }
        }


    }

}
