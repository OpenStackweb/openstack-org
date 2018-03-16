<?php


class SummitAboutPage extends SummitPage {
    private static $db = array(
        'WhoShouldAttendText'   => 'HTMLText',
        'RegisterLink'          => 'Varchar(255)',
        'AcademyText'           => 'HTMLText',
        'AcademyLink'           => 'Varchar(255)',
        'ForumText'             => 'HTMLText',
        'ForumLink'             => 'Varchar(255)',
        'FeaturedSpeakersTitle' => 'Varchar(255)',
        'HighLightsTitle'       => 'Varchar(255)',
        'JoinMovementText1'     => 'HTMLText',
        'JoinMovementText2'     => 'HTMLText',
        'JoinUsTitle'           => 'Varchar(255)',
        'JoinUsText'            => 'HTMLText',
    );

    private static $many_many = array(
        'FeaturedSpeakers'   => 'PresentationSpeaker',
        'Highlights'         => 'VideoLink',
        'Links'              => 'Link'
    );

    private static $has_one = array(
        'JoinMovementImage' => 'File'
    );

    private static $many_many_extraFields = array(
        'FeaturedSpeakers' => array(
            'Order' => "Int",
        ),
        'Highlights' => array(
            'Order' => 'Int',
        ),
        'Links' => array(
            'Order' => 'Int',
        )
    );


    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Content');
        $fields->addFieldToTab('Root.Main', $text = new HtmlEditorField('WhoShouldAttendText','Who Should Attend Text'));
        $text->setRows(4);

        $fields->addFieldToTab('Root.Main', new TextField('RegisterLink','Register Link'));

        $fields->addFieldToTab('Root.Main', $text = new HtmlEditorField('AcademyText','Academy Text'));
        $text->setRows(4);
        $fields->addFieldToTab('Root.Main', new TextField('AcademyLink','Academy Link'));
        $fields->addFieldToTab('Root.Main', $text = new HtmlEditorField('ForumText','Forum Text'));
        $text->setRows(4);
        $fields->addFieldToTab('Root.Main', new TextField('ForumLink','Forum Link'));
        $fields->addFieldToTab('Root.Main', new TextField('FeaturedSpeakersTitle','Featured Speakers Title'));
        $fields->addFieldToTab('Root.Main', new TextField('HighLightsTitle','HighLights Title'));

        $fields->addFieldToTab('Root.Main', $text = new HtmlEditorField('JoinMovementText1','Join Movement Text 1'));
        $text->setRows(4);
        $fields->addFieldToTab('Root.Main', $text = new HtmlEditorField('JoinMovementText2','Join Movement Text 2'));
        $text->setRows(4);
        $join_image = new UploadField('JoinMovementImage', 'Join Movement Image');
        $join_image->setAllowedMaxFileNumber(1);
        $fields->addFieldToTab('Root.Main', $join_image);

        $fields->addFieldToTab('Root.Main', new TextField('JoinUsTitle','Join Us Title'));
        $fields->addFieldToTab('Root.Main', $text = new HtmlEditorField('JoinUsText','Join Us Text'));
        $text->setRows(4);

        $config = GridFieldConfig_RelationEditor::create();
        $config->addComponent(new GridFieldSortableRows('Order'));
        $auto_completer = $config->getComponentByType('GridFieldAddExistingAutocompleter');
        $auto_completer->setResultsFormat('$FirstName $LastName');
        $config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
            [
                'FirstName' => 'FirstName',
                'LastName'  => 'LastName',
                'Title'     => 'Title',
            ]);
        $gridField = new BetterGridField('FeaturedSpeakers', 'Featured Speakers', $this->FeaturedSpeakers(), $config);
        $fields->addFieldToTab('Root.Main', $gridField);

        $config = GridFieldConfig_RecordEditor::create(4);
        $config->addComponent(new GridFieldSortableRows('Order'));
        $gridField = new GridField('Highlights', 'Highlights', $this->Highlights(), $config);
        $fields->addFieldToTab('Root.Main', $gridField);

        $config = GridFieldConfig_RecordEditor::create(4);
        $config->addComponent(new GridFieldSortableRows('Order'));
        $gridField = new GridField('Links', 'Links', $this->Links(), $config);
        $fields->addFieldToTab('Root.Main', $gridField);

        return $fields;
    }
}


class SummitAboutPage_Controller extends SummitPage_Controller {

    public function init()
    {
        $this->top_section = 'full';
        parent::init();
        Requirements::block('summit/css/combined.css');
        Requirements::css('themes/openstack/static/css/combined.css');
        Requirements::css('themes/openstack/javascript/secondary-nav.jquery/secondary-nav.jquery.css');
        Requirements::javascript('themes/openstack/javascript/secondary-nav.jquery/secondary-nav.jquery.js');
        Requirements::javascript('summit/javascript/summit-about-page.js');
    }

}