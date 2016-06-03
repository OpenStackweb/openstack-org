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
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript('themes/openstack/javascript/chosen.jquery.min.js');
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('summit/javascript/speaker-form.js');

        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::css('summit/css/speaker-form.css');

    }

    protected function getSpeakerFields()
    {
        $organizational_roles = SpeakerOrganizationalRole::get()->where('IsDefault',1)->map('ID','Role');
        $organizational_roles->push(0,'Other');
        $active_involvements = SpeakerActiveInvolvement::get()->where('IsDefault',1)->map('ID','Involvement');
        $active_involvements->push(0,'Other');

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
            ->literal('RecordingAndPublishingLegalAgreement',sprintf('<div class="disclaimer">Speakers agree that OpenStack Foundation may record and publish their talks presented during the %s OpenStack Summit. If you submit a proposal on behalf of a speaker, you represent to OpenStack Foundation that you have the authority to submit the proposal on the speaker’s behalf and agree to the recording and publication of their presentation.</div>',$this->summit->Title))
            ->literal('BureauTitle','<label>Want to be in the Speakers\' Bureau?</label>')
            ->literal('BureauText','<div class="bureau-text">In addition to the OpenStack Summit, we regularly recruit speakers for OpenStack community events around the world. If you would like to be considered for more speaking opportunities, please indicate your interest in being listed in the speaker’s bureau and complete the below questions so event organizers can learn more about you.</div>')
            ->checkbox('AvailableForBureau', "I'd like to be in the speaker bureau")
                ->configure()
                    ->addExtraClass('bureau-checkbox')
                ->end()
            ->checkbox('WillingToPresentVideo', "Willing to present via video conference")
                ->configure()
                    ->addExtraClass('bureau-checkbox')
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
             ->optionset('WillingToTravel', 'I am willing to travel to events:', array(
                    1 => 'Yes',
                    0 => 'No'
             ))
            ->multidropdown('CountriesToTravel', 'Countries willing to travel to: ', CountryCodes::$iso_3166_countryCodes)
                ->configure()
                    ->addExtraClass('countries-to-travel')
                ->end()
            ->checkbox('FundedTravel', 'My Company would be willing to fund my travel to events')
                ->configure()
                    ->addExtraClass('bureau-checkbox')
                ->end()
            ->checkboxset('OrganizationalRole','What is your current Organizational Role at your company? (check all that apply):',$organizational_roles)
            ->text('OtherOrganizationalRole','Please specify your role:')
                ->configure()
                    ->displayIf('OrganizationalRole')
                        ->hasCheckedOption(0)
                    ->end()
                ->end()
            ->checkboxset('ActiveInvolvement','What is your Active Involvement in the OpenStack Community?  (check all that apply):',$active_involvements)
            ->text('OtherActiveInvolvement','Please specify your involvement:')
                ->configure()
                    ->displayIf('ActiveInvolvement')
                        ->hasCheckedOption(0)
                    ->end()
                ->end()
            ->literal('HR','<hr>')
            ->tinyMCEEditor('Notes',"Notes")
                ->configure()
                    ->setRows(10)
                ->end()
            ->hidden('HasChanged',0);

        return $fields;
    }

    public function getSpeakerValidator() {
        return RequiredFields::create('FirstName','LastName','Title', 'RecordingAndPublishingLegalAgreement', 'Language','Expertise','WillingToTravel','Bio');
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

        $this->fields->fieldByName('Expertise')->setValue(implode(',',$speaker->AreasOfExpertise()->map('ID','Expertise')->toArray()));
        $this->fields->fieldByName('Language')->setValue(implode(',',$speaker->Languages()->map('ID','Language')->toArray()));

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

        $inv_ids = array();
        foreach ($speaker->ActiveInvolvements() as $involvement) {
            if($involvement->IsDefault) {
                $inv_ids[] = $involvement->ID;
            } else { //add other
                $inv_ids[] = 0;
                $this->fields->fieldByName('OtherActiveInvolvement')->setValue($involvement->Involvement);
            }
        }
        $this->fields->fieldByName('ActiveInvolvement')->setValue($inv_ids);

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

        $expertise_csv = $this->fields->fieldByName("Expertise")->Value();
        $expertise_array = explode(',',$expertise_csv);
        $exp_ids = array();
        if ($expertise_array) {
            foreach ($expertise_array as $expertise) {
                if(empty($expertise)) continue;
                $expertise = trim($expertise);
                if (!$anexp = $speaker->AreasOfExpertise()->find('Expertise',$expertise)) {
                    $anexp = SpeakerExpertise::create(array('Expertise' => $expertise));
                }

                $anexp->write();
                $exp_ids[] = $anexp->ID;
            }
        }
        $speaker->AreasOfExpertise()->setByIdList($exp_ids);


        $language_csv = $this->fields->fieldByName("Language")->Value();
        $language_array = explode(',',$language_csv);
        $lang_ids = array();
        if ($language_array) {
            foreach ($language_array as $language) {
                if(empty($language)) continue;
                $language = trim($language);
                if (!$alang = $speaker->Languages()->find('Language',$language)) {
                    $alang = SpeakerLanguage::create(array('Language' => $language));
                }

                $alang->write();
                $lang_ids[] = $alang->ID;
            }
        }
        $speaker->Languages()->setByIdList($lang_ids);

        $link_ids = array();
        for($i = 1 ; $i <= 5 ; $i++ ){
            $link = $this->fields->fieldByName("PresentationLink[{$i}]");
            $title = $this->fields->fieldByName("PresentationTitle[{$i}]");
            if(is_null($link)) continue;
            $link_val  = trim($link->Value());
            if(empty($link_val)) continue;
            $title_val = (is_null($title)) ? '' : trim($title->Value());

            if (!$alink = $speaker->OtherPresentationLinks()->find('LinkUrl',$link_val)) {
                $alink = SpeakerPresentationLink::create(array('LinkUrl' => $link_val, 'Title' => $title_val));
            } else {
                $alink->Title = $title_val;
            }

            $alink->write();
            $link_ids[] = $alink->ID;
        }
        $speaker->OtherPresentationLinks()->setByIdList($link_ids);


        $roles = $this->fields->fieldByName("OrganizationalRole")->Value();
        if ($roles && in_array(0,$roles)) { // 0 is the id for Other
            $other_role = $this->fields->fieldByName("OtherOrganizationalRole")->Value();
            $new_role = SpeakerOrganizationalRole::get()->where("Role = '$other_role'")->first();
            if (!$new_role) {
                $new_role = new SpeakerOrganizationalRole(array('Role' => $other_role, 'IsDefault' => 0));
                $new_role->write();
            }
            array_pop($roles);
            $roles[] = $new_role->ID;
        }
        $speaker->OrganizationalRoles()->setByIdList($roles);

        $involvements = $this->fields->fieldByName("ActiveInvolvement")->Value();
        if ($involvements && in_array(0,$involvements)) { // 0 is the id for Other
            $other_involvement = $this->fields->fieldByName("OtherActiveInvolvement")->Value();
            $new_inv = SpeakerActiveInvolvement::get()->where("Involvement = '$other_involvement'")->first();
            if (!$new_inv) {
                $new_inv = new SpeakerActiveInvolvement(array('Involvement' => $other_involvement, 'IsDefault' => 0));
                $new_inv->write();
            }
            array_pop($involvements);
            $involvements[] = $new_inv->ID;
        }
        $speaker->ActiveInvolvements()->setByIdList($involvements);

        $countries_2_travel = $this->fields->fieldByName('CountriesToTravel');
        $country_ids = array();
        if(!is_null($countries_2_travel)) {
            $country_array  = $countries_2_travel->Value();
            if ($country_array) {
                foreach($country_array as $country_name)
                {
                    if (!$acountry = $speaker->TravelPreferences()->find('Country',$country_name)) {
                        $acountry = SpeakerTravelPreference::create(array('Country' => $country_name));
                    }

                    $acountry->write();
                    $country_ids[] = $acountry->ID;
                }
            }
        }
        $speaker->TravelPreferences()->setByIdList($country_ids);

    }

}