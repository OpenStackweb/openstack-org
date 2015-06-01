<?php

/**
 * Class SummitOverviewPage
 */
class SummitOverviewPage extends SummitPage {

    private static $has_one = array(
        'GrowthBoxBackground'     => 'BetterImage',
        'GrowthBoxChartLegend'    => 'File',
        'GrowthBoxChartLegendPng' => 'BetterImage',
        'GrowthBoxChart'          => 'File',
        'GrowthBoxChartPng'       => 'BetterImage',
        'EventOneLogo'            => 'File',
        'EventOneLogoPng'         => 'BetterImage',
        'EventTwoLogo'            => 'File',
        'EventTwoLogoPng'         => 'BetterImage',
    );

    private static $db = array(
        'GrowthBoxTextTop'    => 'HTMLText',
        'GrowthBoxTextBottom' => 'HTMLText',
        'VideoRecapCaption'   => 'Text',
        'VideoRecapYouTubeID' => 'Text',
        'ScheduleTitle'       => 'Text',
        'ScheduleText'        => 'HTMLText',
        'ScheduleUrl'         => 'Text',
        'ScheduleBtnText'     => 'Text',
        'NetworkingContent'   => 'HTMLText',
        // two main events
        'TwoMainEventsTitle'  => 'Text',
        'EventOneTitle'       => 'Text',
        'EventOneSubTitle'    => 'Text',
        'EventOneContent'     => 'HTMLText',
        'EventTwoTitle'       => 'Text',
        'EventTwoSubTitle'    => 'Text',
        'EventTwoContent'     => 'HTMLText',
    );

    private static $has_many = array(
        'NetworkingPhotos' => 'SummitNetworkingPhoto',
        'HelpMenuItems'    => 'SummitOverviewPageHelpMenuItem',
    );

    // private static $allowed_children = array ('ConferenceSubPage', 'CallForSpeakersPage', '');

    private static $default_parent = 'SummitHomePage';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if($this->ID > 0){
            $fields->addFieldToTab('Root.Networking', new HtmlEditorField('NetworkingContent', 'Content'));
            //networking photos
            $config = GridFieldConfig_RecordEditor::create();
            $config->addComponent(new GridFieldSortableRows('Order'));
            $gridField = new GridField('NetworkingPhotos', 'Photos', $this->NetworkingPhotos(), $config);
            $fields->addFieldToTab('Root.Networking', $gridField);

            $fields->addFieldToTab('Root.Networking', new HtmlEditorField('NetworkingContent', 'Content'));
            //menu items
            $config = GridFieldConfig_RecordEditor::create();
            $config->addComponent(new GridFieldSortableRows('Order'));
            $gridField = new GridField('HelpMenuItems', 'Help Menu Items', $this->HelpMenuItems(), $config);
            $fields->addFieldToTab('Root.HelpSideBarMenu', $gridField);
        }

        $fields->addFieldsToTab('Root.Schedule', new TextField('ScheduleTitle','Title'));
        $fields->addFieldsToTab('Root.Schedule', new HtmlEditorField('ScheduleText','Text'));
        $fields->addFieldsToTab('Root.Schedule', new TextField('ScheduleUrl','Url'));
        $fields->addFieldsToTab('Root.Schedule', new TextField('ScheduleBtnText','Button Caption'));


        // GrowthBox
        $fields->addFieldsToTab('Root.VideoRecap', new TextField('VideoRecapCaption','Caption Text'));
        $fields->addFieldsToTab('Root.VideoRecap', new TextField('VideoRecapYouTubeID','YouTubeID'));

        // GrowthBox
        $fields->addFieldToTab("Root.GrowthBox", new HtmlEditorField('GrowthBoxTextTop','Text Top'));
        $fields->addFieldToTab("Root.GrowthBox", new HtmlEditorField('GrowthBoxTextBottom','Text Bottom'));
        $fields->addFieldsToTab("Root.GrowthBox", $upload_0 = new UploadField('GrowthBoxBackground','Background Image'));
        $fields->addFieldsToTab("Root.GrowthBox", $upload_1 = new UploadField('GrowthBoxChartLegend','Chart Legend (SVG)'));
        $fields->addFieldsToTab("Root.GrowthBox", $upload_2 = new UploadField('GrowthBoxChartLegendPng','Chart Legend (PNG)'));
        $fields->addFieldsToTab("Root.GrowthBox", $upload_3 = new UploadField('GrowthBoxChart','Chart (SVG)'));
        $fields->addFieldsToTab("Root.GrowthBox", $upload_4 = new UploadField('GrowthBoxChartPng','Chart (PNG)'));

        $upload_0->setFolderName('summits/overview');
        $upload_0->setAllowedMaxFileNumber(1);
        $upload_0->setAllowedFileCategories('image');

        $upload_1->setFolderName('summits/overview');
        $upload_1->setAllowedMaxFileNumber(1);
        $upload_1->setAllowedExtensions(array('svg'));

        $upload_2->setFolderName('summits/overview');
        $upload_2->setAllowedMaxFileNumber(1);
        $upload_3->setAllowedExtensions(array('png'));

        $upload_3->setFolderName('summits/overview');
        $upload_3->setAllowedMaxFileNumber(1);
        $upload_3->setAllowedExtensions(array('svg'));

        $upload_4->setFolderName('summits/overview');
        $upload_4->setAllowedMaxFileNumber(1);
        $upload_4->setAllowedExtensions(array('png'));

        //two main events

        $fields->addFieldsToTab('Root.TwoMainEvents', new TextField('TwoMainEventsTitle','Title'));
        $fields->addFieldsToTab('Root.TwoMainEvents', new TextField('EventOneTitle','Event One - Title'));
        $fields->addFieldsToTab('Root.TwoMainEvents', new TextField('EventOneSubTitle','Event One - SubTitle'));
        $fields->addFieldsToTab('Root.TwoMainEvents', new HtmlEditorField('EventOneContent','Event One - Content'));
        $fields->addFieldsToTab('Root.TwoMainEvents', new TextField('EventTwoTitle','Event Two - Title'));
        $fields->addFieldsToTab('Root.TwoMainEvents', new TextField('EventTwoSubTitle','Event Two - SubTitle'));
        $fields->addFieldsToTab('Root.TwoMainEvents', new HtmlEditorField('EventTwoContent','Event Two - Content'));

        $fields->addFieldsToTab("Root.TwoMainEvents", $upload_5 = new UploadField('EventOneLogo','Event One Logo (SVG)'));
        $fields->addFieldsToTab("Root.TwoMainEvents", $upload_6 = new UploadField('EventOneLogoPng','Event One Logo (PNG)'));
        $fields->addFieldsToTab("Root.TwoMainEvents", $upload_7 = new UploadField('EventTwoLogo','Event One Logo (SVG)'));
        $fields->addFieldsToTab("Root.TwoMainEvents", $upload_8 = new UploadField('EventTwoLogoPng','Event Two Logo (PNG)'));

        $upload_5->setFolderName('summits/overview/events');
        $upload_5->setAllowedMaxFileNumber(1);
        $upload_5->setAllowedExtensions(array('svg'));

        $upload_6->setFolderName('summits/overview/events');
        $upload_6->setAllowedMaxFileNumber(1);
        $upload_6->setAllowedExtensions(array('png'));

        $upload_7->setFolderName('summits/overview/events');
        $upload_7->setAllowedMaxFileNumber(1);
        $upload_7->setAllowedExtensions(array('svg'));

        $upload_8->setFolderName('summits/overview/events');
        $upload_8->setAllowedMaxFileNumber(1);
        $upload_8->setAllowedExtensions(array('png'));

        return $fields;
    }

    public function getGrowthBoxTextTop(){
        $res = $this->getField('GrowthBoxTextTop');
        if(empty($res))
            return '<h2>Join The Movement</h2><p>In 2010, 75 people met in Austin, Texas for the very first OpenStack Summit. Four years later, almost 5,000 attendees joined us in Paris for our second international summit and the first OpenStack Summit held in Europe.</p>';
        return $res;
    }

    public function getGrowthBoxTextBottom(){
        $res = $this->getField('GrowthBoxTextBottom');
        if(!empty($res))
            return ' <p>The OpenStack summit is a unique opportunity for the developers and users of OpenStack software to meet and exchange ideas. Hundreds of the core developers will be on site to discuss all things OpenStack. Summits include in-depth technical discussions, hands-on workshops, and the full presence of almost every player in the OpenStack Ecosystem. If you are deploying OpenStack—or considering how it can help your enterprise—there’s no better way to connect with the community than the OpenStack Summit.</p>';
        return $res;
    }

    public function BoxChartLegendImageUrl(){
        if($this->GrowthBoxChartLegend()->exists()){
            return $this->GrowthBoxChartLegend()->getURL();
        }
        return '/summit/images//line-growth-legend.svg';
    }

    public function BoxChartLegendImagePngUrl(){
        if($this->GrowthBoxChartLegendPng()->exists()){
            return $this->GrowthBoxChartLegendPng()->getURL();
        }
        return '/summit/images//line-growth-legend.png';
    }

    public function BoxChartImageUrl(){
        if($this->GrowthBoxChart()->exists()){
            return $this->GrowthBoxChart()->getURL();
        }
        return '/summit/images/line-growth-chart.svg';
    }

    public function BoxChartImagePngUrl(){
        if($this->GrowthBoxChartPng()->exists()){
            return $this->GrowthBoxChartPng()->getURL();
        }
        return '/summit/images/line-growth-chart.png';
    }

    public function BoxChartBackgroundImageUrl(){
        if($this->GrowthBoxBackground()->exists()){
            return $this->GrowthBoxBackground()->getURL();
        }
        return '/summit/images/growth-bkgd.jpg';
    }

    public function getVideoRecapCaption(){

        $res = $this->getField('VideoRecapCaption');
        if(empty($res))
            return 'Video: See a recap of our May 2015 summit in Vancouver, BC.';
        return $res;
    }

    public function getVideoRecapYouTubeID(){
        $res = $this->getField('VideoRecapYouTubeID');
        if(empty($res))
            return 'iZdEwQ-76P4';
        return $res;
    }

    public function getScheduleText(){
        $res = $this->getField('ScheduleText');
        if(empty($res))
        return '<p>Get a glimpse into the wealth of speakers, topics and sessions happening at OpenStack Summit Vancouver.</p>';
        return $res;
    }

    public function getScheduleUrl(){
        $res = $this->getField('ScheduleUrl');
        if(empty($res))
        return '/summit/vancouver-2015/schedule/';
        return $res;
    }

    public function getScheduleTitle(){
        $res = $this->getField('ScheduleTitle');
        if(empty($res))
        return 'Schedule';
        return $res;
    }

    public function getScheduleBtnText(){
        $res = $this->getField('ScheduleBtnText');
        if(empty($res))
            return 'View The Schedule';
        return $res;
    }

    public function getNetworkingContent(){
        $res = $this->getField('NetworkingContent');
        if(empty($res))
        return '<h1>Knowledge, Nightlife, &amp; New Friends.</h1>
                <p>
                    The Summit is about more than great sessions and speakers. Join the OpenStack community with
                    networking events and fantastic nightlife.
                </p>';
        return $res;
    }

    // two main events

    public function getTwoMainEventsTitle(){
        $res = $this->getField('TwoMainEventsTitle');
        if(empty($res))
            $res = 'One Week, Two Main Events';
        return $res;
    }

    public function getEventOneTitle(){
        $res = $this->getField('EventOneTitle');
        if(empty($res))
            $res = 'The OpenStack Conference';
        return $res;
    }

    public function getEventOneSubTitle(){
        $res = $this->getField('EventOneSubTitle');
        if(empty($res))
            $res = 'For Everyone';
        return $res;
    }

    public function getEventOneContent(){
        $res = $this->getField('EventOneContent');
        if(empty($res))
            $res = '<p><strong>Held Monday - Thursday</strong><br/>
                    Classic track with speakers and sessions. The perfect place for developers, users, and
                    administrators of OpenStack Cloud Software. This is great for those looking for the best way to get
                    started.
                </p>';
        return $res;
    }

    public function getEventTwoTitle(){
        $res = $this->getField('EventTwoTitle');
        if(empty($res))
            $res = 'The OpenStack Design Summit';
        return $res;
    }

    public function getEventTwoSubTitle(){
        $res = $this->getField('EventTwoSubTitle');
        if(empty($res))
            $res = 'For Contributors';
        return $res;
    }

    public function getEventTwoContent(){
        $res = $this->getField('EventTwoContent');
        if(empty($res))
            $res = '<p><strong>Held Tuesday - Friday</strong><br/>
                    Collaborative working sessions where OpenStack developers come together twice annually to discuss
                    the requirements for the next software release and connect with other community members.
                </p>';
        return $res;
    }

    public function getEventOneLogoUrl(){
        if($this->EventOneLogo()->exists()){
            return $this->EventOneLogo()->getURL();
        }
        return '/summit/images//grey-conference-logo.svg';
    }

    public function getEventOneLogoPngUrl(){
        if($this->EventOneLogoPng()->exists()){
            return $this->EventOneLogoPng()->getURL();
        }
        return '/images/grey-conference-logo.png';
    }

    public function getEventTwoLogoUrl(){
        if($this->EventTwoLogo()->exists()){
            return $this->EventTwoLogo()->getURL();
        }
        return '/summit/images//grey-summit-logo.svg';
    }

    public function getEventTwoLogoPngUrl(){
        if($this->EventTwoLogoPng()->exists()){
            return $this->EventTwoLogoPng()->getURL();
        }
        return '/images/grey-summit-logo.png';
    }

    public function onAfterWrite() {
        parent::onAfterWrite();
        foreach($this->Children() as $child){
            if($child instanceof SummitPage) {
                $child->SummitID = $this->SummitID;
                $child->write();
            }
        }
    }

    public function validate() {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;

        if(empty($this->SummitID)){
            return $valid->error('You must select a valid Summit!');
        }
        return $valid;
    }

    public function getOrderedHelpMenuItems(){
        return $this->HelpMenuItems()->sort('Order');
    }

}


class SummitOverviewPage_Controller extends SummitPage_Controller {

	public function init() {
        $this->top_section = 'full';
        parent::init();
	}

}