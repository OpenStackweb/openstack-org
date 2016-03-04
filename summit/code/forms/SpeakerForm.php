<?php

/**
 * Class SpeakerForm
 */
class SpeakerForm extends BootstrapForm
{

    public function __construct($controller, $name, $actions)
    {
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");

        parent::__construct(
            $controller, 
            $name, 
            $this->getSpeakerFields(),
            $actions,
            $this->getSpeakerValidator()
        );

        $script = <<<JS
          var form_validator_{$this->FormName()} = null;
          (function( $ ){

                $(document).ready(function(){
                    form_validator_{$this->FormName()} = $('#{$this->FormName()}').validate(
                    {
                        ignore:[],
                        highlight: function(element) {
                            $(element).closest('.form-group').addClass('has-error');
                        },
                        unhighlight: function(element) {
                            $(element).closest('.form-group').removeClass('has-error');
                        },
                        errorElement: 'span',
                        errorClass: 'help-block',
                        errorPlacement: function(error, element) {
                            if(element.parent('.input-group').length) {
                                error.insertAfter(element.parent());
                            } else {
                                error.insertAfter(element);
                            }
                        },
                       invalidHandler: function(form, validator) {
                            if (!validator.numberOfInvalids())
                                return;
                            var element = $(validator.errorList[0].element);
                            if(!element.is(":visible")){
                                element = element.parent();
                            }

                            $('html, body').animate({
                                scrollTop: element.offset().top
                            }, 2000);
                        },
                    });

                     $("#SpeakerForm_BioForm_CountriesToTravel").chosen({width: '100%'});
                });
                // End of closure.
        }(jQuery ));
JS;
        Requirements::customScript($script);
    }

    protected function getSpeakerFields()
    {
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
            ->text('PresentationLink[1]','Link #1')
            ->text('PresentationTitle[1]','Title #1')
            ->text('PresentationLink[2]','Link #2')
            ->text('PresentationTitle[2]','Title #2')
            ->text('PresentationLink[3]','Link #3')
            ->text('PresentationTitle[3]','Title #3')
            ->text('PresentationLink[4]','Link #4')
            ->text('PresentationTitle[4]','Title #4')
            ->text('PresentationLink[5]','Link #5')
            ->text('PresentationTitle[5]','Title #5')
            ->literal('RecordingAndPublishingLegalAgreement',sprintf('Speakers agree that OpenStack Foundation may record and publish their talks presented during the %s OpenStack Summit. If you submit a proposal on behalf of a speaker, you represent to OpenStack Foundation that you have the authority to submit the proposal on the speaker’s behalf and agree to the recording and publication of their presentation.', Summit::ActiveSummit()->Title))
            ->header('Want to be in the Speakers\' Bureau?')
            ->checkbox('AvailableForBureau', "I'd like to be in the speaker bureau")
                ->configure()
                    ->addExtraClass('bureau-checkbox')
                ->end()
            ->checkbox('WillingToPresentVideo', "Willing to present via video conference")
                ->configure()
                    ->addExtraClass('bureau-checkbox')
                ->end()
            ->checkbox('FundedTravel', 'My Company would be willing to fund my travel to events')
                ->configure()
                    ->addExtraClass('bureau-checkbox')
                ->end()
             ->optionset('WillingToTravel', 'I am willing to travel to events:', array(
                    1 => 'Yes',
                    0 => 'No'
             ))
            ->multidropdown('CountriesToTravel', 'Countries willing to travel to: ', CountryCodes::$iso_3166_countryCodes)
                ->configure()
                    ->addExtraClass('countries-to-travel')
                ->end()
            ->tinyMCEEditor('Notes',"Notes")
                ->configure()
                    ->setRows(10)
                ->end();
        return $fields;
    }

    public function getSpeakerValidator() {
        return RequiredFields::create('FirstName','LastName','Title', 'RecordingAndPublishingLegalAgreement', 'Language[1]','Expertise[1]','WillingToTravel','Bio');
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

        foreach ($speaker->AreasOfExpertise() as $key => $expertise)
        {
            if ($key > 4) break;
            $this->fields->fieldByName('Expertise['.($key+1).']')->setValue($expertise->Expertise);
        }

        foreach ($speaker->Languages() as $key => $language)
        {
            if ($key > 4) break;
            $this->fields->fieldByName('Language['.($key+1).']')->setValue($language->Language);
        }

        $country_array = array();
        foreach ($speaker->TravelPreferences() as $pref_country) {
            $country_array[] = $pref_country->Country;
        }

        foreach ($speaker->OtherPresentationLinks() as $key => $presentation) {
            $this->fields->fieldByName('PresentationLink['.($key+1).']')->setValue($presentation->LinkUrl);
            $this->fields->fieldByName('PresentationTitle['.($key+1).']')->setValue($presentation->Title);
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

        $speaker->AreasOfExpertise()->removeAll();
        for($i = 1 ; $i <= 5 ; $i++ ){
            $field = $this->fields->fieldByName("Expertise[{$i}]");
            if(is_null($field)) continue;
            $val = $field->Value();
            if(empty($val)) continue;
            $speaker->AreasOfExpertise()->add( SpeakerExpertise::create(array('Expertise' => trim($val))));
        }

        $speaker->Languages()->removeAll();
        for($i = 1 ; $i <= 5 ; $i++ ){
            $field = $this->fields->fieldByName("Language[{$i}]");
            if(is_null($field)) continue;
            $val = $field->Value();
            if(empty($val)) continue;
            $speaker->Languages()->add( SpeakerLanguage::create(array('Language' => trim($val))));
        }

        $speaker->OtherPresentationLinks()->removeAll();
        for($i = 1 ; $i <= 5 ; $i++ ){
            $link = $this->fields->fieldByName("PresentationLink[{$i}]");
            $title = $this->fields->fieldByName("PresentationTitle[{$i}]");
            if(is_null($link)) continue;
            $link_val   = $link->Value();
            $title_val   = (is_null($title)) ? '' : $title->Value();
            if(empty($link_val)) continue;

            $speaker->OtherPresentationLinks()->add( SpeakerPresentationLink::create(array(
                'LinkUrl' => trim($link_val),
                'Title' => trim($title_val))
            ));
        }

        $countries_2_travel = $this->fields->fieldByName('CountriesToTravel');

        if(!is_null($countries_2_travel))
        {
            $speaker->TravelPreferences()->removeAll();
            $country_array  = $countries_2_travel->Value();
            foreach($country_array as $country_name)
            {
                $speaker->TravelPreferences()->add(SpeakerTravelPreference::create(array(
                    'Country' => $country_name
                )));
            }
        }
    }

}