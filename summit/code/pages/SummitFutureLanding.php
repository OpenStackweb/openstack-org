<?php

/* 
   Splash page for future summits
*/

class SummitFutureLanding extends SummitPage {

    private static $db = array(
        'BGImageOffset' => 'Int',
        'IntroText'     => 'Text',
        'MainTitle'     => 'Text',
        'LocSubtitle'   => 'Text',
        'ProspectusUrl' => 'Text',
        'RegisterUrl'   => 'Text',
        'ShareText'     => 'Text',
        'PhotoTitle'    => 'Text',
        'PhotoUrl'      => 'Text',
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('GoogleConversionTracking');
        $fields->removeByName('FacebookConversionTracking');
        $fields->removeByName('TwitterConversionTracking');
        $fields->removeByName('SummitPageImages');
        $fields->removeFieldFromTab('Root.Main','Content');
        $fields->removeFieldFromTab('Root.Main','Metadata');
        $fields->removeFieldFromTab('Root.Main','HeroCSSClass');


        if ($this->ID) {
            // Summit Image has_one selector
            $dropdown = DropdownField::create(
                'SummitImageID',
                'Please choose an image for this page',
                SummitImage::get()->map("ID", "Title", "Please Select")
            )
                ->setEmptyString('(None)');

            $fields->addFieldToTab('Root.Main', $dropdown);
            $fields->addFieldsToTab('Root.Main', $ddl_summit = new DropdownField('SummitID', 'Summit', Summit::get()->map('ID', 'Name')));
            $ddl_summit->setEmptyString('(None)');
        }

        $fields->addFieldsToTab('Root.Main', new NumericField('BGImageOffset', 'Top Offset for image'));
        $fields->addFieldsToTab('Root.Main', new TextField('IntroText', 'Intro Text (above title)'));
        $fields->addFieldsToTab('Root.Main', new TextField('MainTitle', 'Main Title'));
        $fields->addFieldsToTab('Root.Main', new TextField('LocSubtitle', 'Location Subtitle'));
        $fields->addFieldsToTab('Root.Main', new TextField('ProspectusUrl', 'Prospectus Url'));
        $fields->addFieldsToTab('Root.Main', new TextField('RegisterUrl', 'Register Url'));
        $fields->addFieldsToTab('Root.Main', new TextField('ShareText', 'Text to share'));
        $fields->addFieldsToTab('Root.Main', new TextField('PhotoTitle', 'Photo info text'));
        $fields->addFieldsToTab('Root.Main', new TextField('PhotoUrl', 'Photo info url'));

        return $fields;
    }
}


class SummitFutureLanding_Controller extends SummitPage_Controller {

}