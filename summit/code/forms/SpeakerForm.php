<?php

/**
 * Class SpeakerForm
 */
class SpeakerForm extends BootstrapForm
{

    /**
     * @var bool
     */
    private $show_bureau;

    public function __construct($controller, $name, $actions, $show_bureau = false) {
        $this->show_bureau = $show_bureau;
        parent::__construct(
            $controller, 
            $name, 
            $this->getSpeakerFields(),
            $actions,
            $this->getSpeakerValidator()
        );
    }

    protected function getSpeakerFields() {
        $fields =  FieldList::create()
            ->text('FirstName',"Speaker's first name")
                ->configure()
                    ->setAttribute('autofocus','TRUE')
                ->end()            
            ->text('LastName', "Speaker's last name")
            ->text('Title', "Speaker's title")
            ->dropdown('Country', 'Country of Residence', CountryCodes::$iso_3166_countryCodes)
                ->configure()
                     ->setEmptyString('-- Select One --')
                ->end()
            ->tinyMCEEditor('Bio',"Speaker's Bio")
                ->configure()
                    ->setRows(25)
                ->end()
            ->text('IRCHandle','IRC Handle (optional)')
             ->configure()
                ->setMaxLength(25)
            ->end()
            ->text('TwitterHandle','Twitter Handle (optional)')
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
            ->literal('SpokenLanguagesTitle','<h3>Spoken Languages ( Up to 5)</h3>')
            ->text('Language[1]','#1')
            ->text('Language[2]','#2')
            ->text('Language[3]','#3')
            ->text('Language[4]','#4')
            ->text('Language[5]','#5')
            ->literal('ExpertiseTitle','<h3>Areas of Expertise ( Up to 5)</h3>')
            ->text('Expertise[1]','#1')
            ->text('Expertise[2]','#2')
            ->text('Expertise[3]','#3')
            ->text('Expertise[4]','#4')
            ->text('Expertise[5]','#5')
            ->literal('PresentationTitle','<h3>Links To Previous Presentations ( Up to 5)</h3>')
            ->text('PresentationLink[1]','#1')
            ->text('PresentationLink[2]','#2')
            ->text('PresentationLink[3]','#3')
            ->text('PresentationLink[4]','#4')
            ->text('PresentationLink[5]','#5')
            ->literal('RecordingAndPublishingLegalAgreement','Speakers agree that OpenStack Foundation may record and publish their talks presented during the October 2015 OpenStack Summit. If you submit a proposal on behalf of a speaker, you represent to OpenStack Foundation that you have the authority to submit the proposal on the speaker’s behalf and agree to the recording and publication of their presentation.');

        if($this->show_bureau)
        {
            $fields = $fields
            ->header('Want to be in the Speakers\' Bureau?')
            ->checkbox('AvailableForBureau', "I'd like to be in the speaker bureau")
            ->configure()
            ->addExtraClass('bureau-checkbox')
            ->end()
            ->checkbox('FundedTravel', 'My company would be willing to fund my travel to events')
            ->configure()
                ->addExtraClass('bureau-checkbox')
            ->end()
             ->optionset('WillingToTravel', 'I am willing to travel to events:', array(
                    1 => 'Yes',
                    0 => 'No'
             ))
            ->multidropdown('CountriesToTravel', 'Countries willing to travel to (Use Ctrl + C to select more than one):', CountryCodes::$iso_3166_countryCodes);

        }
        return $fields;
    }

    public function getSpeakerValidator() {
        return RequiredFields::create('FirstName','LastName','Title','RecordingAndPublishingLegalAgreement');
    }

    public function loadDataFrom($data, $mergeStrategy = 0, $fieldList = null)
    {
        parent::loadDataFrom($data, $mergeStrategy, $fieldList);
        if(!$data instanceof PresentationSpeaker) return;

        $speaker = $data;

        foreach ($speaker->AreasOfExpertise() as $key => $expertise)
        {
            if ($key > 4) break;
            $this->fields->fieldByName('Expertise['.($key+1).']')->setValue($expertise->Expertise);
        }

        foreach ($speaker->Languages() as $key => $language)
        {
            if ($key > 4) exit;
            $this->fields->fieldByName('Language['.($key+1).']')->setValue($language->Language);
        }

        $country_array = array();
        foreach ($speaker->TravelPreferences() as $pref_country) {
            $country_array[] = $pref_country->Country;
        }

        foreach ($speaker->MixedPresentationLinks(5) as $key => $presentation) {
            if ($presentation->Source == 'summit')
            {
                $this->fields->fieldByName('PresentationLink['.($key+1).']')->setValue($presentation->Link);
                $this->fields->fieldByName('PresentationLink['.($key+1).']')->setDisabled(true);
            }
            else
            {
                $this->fields->fieldByName('PresentationLink['.($key+1).']')->setValue($presentation->Link);
            }
        }

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
    }

    public function saveExtraData($speaker, $data)
    {

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
    }

}