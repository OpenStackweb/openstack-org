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
        'Atendees1Chart'          => 'File',
        'Atendees1ChartPng'       => 'BetterImage',
        'Atendees2Chart'          => 'File',
        'Atendees2ChartPng'       => 'BetterImage',
        'Atendees3Chart'          => 'File',
        'Atendees3ChartPng'       => 'BetterImage',
        'Atendees4Chart'          => 'File',
        'Atendees4ChartPng'       => 'BetterImage',
        'AtendeesChartRef'        => 'File',
        'AtendeesChartRefPng'     => 'BetterImage',
        'TimelineImage'           => 'File',
        'TimelineImagePng'        => 'BetterImage',
    );

    private static $db = array(
        'OverviewIntro'       => 'HTMLText',
        'GrowthBoxTextTop'    => 'HTMLText',
        'GrowthBoxTextBottom' => 'HTMLText',
        'RecapTitle'          => 'Text',
        'VideoRecapCaption1'  => 'Text',
        'VideoRecapYouTubeID1'=> 'Text',
        'VideoRecapCaption2'  => 'Text',
        'VideoRecapYouTubeID2'=> 'Text',
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
        'Atendees1Label'      => 'Text',
        'Atendees2Label'      => 'Text',
        'Atendees3Label'      => 'Text',
        'Atendees4Label'      => 'Text',
        'TimelineCaption'     => 'Text',
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

        $fields->addFieldsToTab('Root.Main', new HtmlEditorField('OverviewIntro','Overview Intro'));

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

        // Video Recap
        $fields->addFieldsToTab('Root.VideoRecap', new TextField('RecapTitle','Title'));
        $fields->addFieldsToTab('Root.VideoRecap', new TextField('VideoRecapCaption1','Caption Text 1'));
        $fields->addFieldsToTab('Root.VideoRecap', new TextField('VideoRecapYouTubeID1','YouTubeID 1'));
        $fields->addFieldsToTab('Root.VideoRecap', new TextField('VideoRecapCaption2','Caption Text 2'));
        $fields->addFieldsToTab('Root.VideoRecap', new TextField('VideoRecapYouTubeID2','YouTubeID 2'));

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
        $upload_0->setOverwriteWarning(false);
        $upload_0->getUpload()->setReplaceFile(true);

        $upload_1->setFolderName('summits/overview');
        $upload_1->setAllowedMaxFileNumber(1);
        $upload_1->setAllowedExtensions(array('svg'));
        $upload_1->setOverwriteWarning(false);
        $upload_1->getUpload()->setReplaceFile(true);

        $upload_2->setFolderName('summits/overview');
        $upload_2->setAllowedMaxFileNumber(1);
        $upload_2->setAllowedExtensions(array('png'));
        $upload_2->setOverwriteWarning(false);
        $upload_2->getUpload()->setReplaceFile(true);

        $upload_3->setFolderName('summits/overview');
        $upload_3->setAllowedMaxFileNumber(1);
        $upload_3->setAllowedExtensions(array('svg'));
        $upload_3->setOverwriteWarning(false);
        $upload_3->getUpload()->setReplaceFile(true);

        $upload_4->setFolderName('summits/overview');
        $upload_4->setAllowedMaxFileNumber(1);
        $upload_4->setAllowedExtensions(array('png'));
        $upload_4->setOverwriteWarning(false);
        $upload_4->getUpload()->setReplaceFile(true);

        //two main events
        $fields->addFieldsToTab('Root.TwoMainEvents', new TextField('TwoMainEventsTitle','Title'));
        $fields->addFieldsToTab('Root.TwoMainEvents', new TextField('EventOneTitle','Event One - Title'));
        $fields->addFieldsToTab('Root.TwoMainEvents', new TextField('EventOneSubTitle','Event One - SubTitle'));
        $fields->addFieldsToTab('Root.TwoMainEvents', new HtmlEditorField('EventOneContent','Event One - Content'));
        $fields->addFieldsToTab('Root.TwoMainEvents', new TextField('EventTwoTitle','Event Two - Title'));
        $fields->addFieldsToTab('Root.TwoMainEvents', new TextField('EventTwoSubTitle','Event Two - SubTitle'));
        $fields->addFieldsToTab('Root.TwoMainEvents', new HtmlEditorField('EventTwoContent','Event Two - Content'));

        $fields->addFieldsToTab("Root.TwoMainEvents", $upload_5 = new UploadField('EventOneLogo','Event Two Logo (SVG)'));
        $fields->addFieldsToTab("Root.TwoMainEvents", $upload_6 = new UploadField('EventOneLogoPng','Event Two Logo (PNG)'));
        $fields->addFieldsToTab("Root.TwoMainEvents", $upload_7 = new UploadField('EventTwoLogo','Event Two Logo (SVG)'));
        $fields->addFieldsToTab("Root.TwoMainEvents", $upload_8 = new UploadField('EventTwoLogoPng','Event Two Logo (PNG)'));

        $upload_5->setFolderName('summits/overview/events');
        $upload_5->setAllowedMaxFileNumber(1);
        $upload_5->setAllowedExtensions(array('svg'));
        $upload_5->setOverwriteWarning(false);
        $upload_5->getUpload()->setReplaceFile(true);

        $upload_6->setFolderName('summits/overview/events');
        $upload_6->setAllowedMaxFileNumber(1);
        $upload_6->setAllowedExtensions(array('png'));
        $upload_6->setOverwriteWarning(false);
        $upload_6->getUpload()->setReplaceFile(true);

        $upload_7->setFolderName('summits/overview/events');
        $upload_7->setAllowedMaxFileNumber(1);
        $upload_7->setAllowedExtensions(array('svg'));
        $upload_7->setOverwriteWarning(false);
        $upload_7->getUpload()->setReplaceFile(true);

        $upload_8->setFolderName('summits/overview/events');
        $upload_8->setAllowedMaxFileNumber(1);
        $upload_8->setAllowedExtensions(array('png'));
        $upload_8->setOverwriteWarning(false);
        $upload_8->getUpload()->setReplaceFile(true);

        //atendees chart
        $fields->addFieldsToTab("Root.AtendeesChart", $upload_13 = new UploadField('AtendeesChartRef','Atendees Chart Legend (SVG)'));
        $fields->addFieldsToTab("Root.AtendeesChart", $upload_19 = new UploadField('AtendeesChartRefPng','Atendees Chart Legend (PNG)'));
        $fields->addFieldToTab("Root.AtendeesChart", new TextField('Atendees1Label','Atendees 1 Label'));
        $fields->addFieldsToTab("Root.AtendeesChart", $upload_9 = new UploadField('Atendees1Chart','Atendees 1 Chart (SVG)'));
        $fields->addFieldsToTab("Root.AtendeesChart", $upload_15 = new UploadField('Atendees1ChartPng','Atendees 1 Chart (PNG)'));
        $fields->addFieldToTab("Root.AtendeesChart", new TextField('Atendees2Label','Atendees 2 Label'));
        $fields->addFieldsToTab("Root.AtendeesChart", $upload_10 = new UploadField('Atendees2Chart','Atendees 2 Chart (SVG)'));
        $fields->addFieldsToTab("Root.AtendeesChart", $upload_16 = new UploadField('Atendees2ChartPng','Atendees 2 Chart (PNG)'));
        $fields->addFieldToTab("Root.AtendeesChart", new TextField('Atendees3Label','Atendees 3 Label'));
        $fields->addFieldsToTab("Root.AtendeesChart", $upload_11 = new UploadField('Atendees3Chart','Atendees 3 Chart (SVG)'));
        $fields->addFieldsToTab("Root.AtendeesChart", $upload_17 = new UploadField('Atendees3ChartPng','Atendees 3 Chart (PNG)'));
        $fields->addFieldToTab("Root.AtendeesChart", new TextField('Atendees4Label','Atendees 4 Label'));
        $fields->addFieldsToTab("Root.AtendeesChart", $upload_12 = new UploadField('Atendees4Chart','Atendees 4 Chart (SVG)'));
        $fields->addFieldsToTab("Root.AtendeesChart", $upload_18 = new UploadField('Atendees4ChartPng','Atendees 4 Chart (PNG)'));

        $upload_9->setFolderName('summits/overview');
        $upload_9->setAllowedMaxFileNumber(1);
        $upload_9->setAllowedExtensions(array('svg'));
        $upload_9->setOverwriteWarning(false);
        $upload_9->getUpload()->setReplaceFile(true);

        $upload_10->setFolderName('summits/overview');
        $upload_10->setAllowedMaxFileNumber(1);
        $upload_10->setAllowedExtensions(array('svg'));
        $upload_10->setOverwriteWarning(false);
        $upload_10->getUpload()->setReplaceFile(true);

        $upload_11->setFolderName('summits/overview');
        $upload_11->setAllowedMaxFileNumber(1);
        $upload_11->setAllowedExtensions(array('svg'));
        $upload_11->setOverwriteWarning(false);
        $upload_11->getUpload()->setReplaceFile(true);

        $upload_12->setFolderName('summits/overview');
        $upload_12->setAllowedMaxFileNumber(1);
        $upload_12->setAllowedExtensions(array('svg'));
        $upload_12->setOverwriteWarning(false);
        $upload_12->getUpload()->setReplaceFile(true);

        $upload_13->setFolderName('summits/overview');
        $upload_13->setAllowedMaxFileNumber(1);
        $upload_13->setAllowedExtensions(array('svg'));
        $upload_13->setOverwriteWarning(false);
        $upload_13->getUpload()->setReplaceFile(true);

        $upload_15->setFolderName('summits/overview');
        $upload_15->setAllowedMaxFileNumber(1);
        $upload_15->setAllowedExtensions(array('png'));
        $upload_15->setOverwriteWarning(false);
        $upload_15->getUpload()->setReplaceFile(true);

        $upload_16->setFolderName('summits/overview');
        $upload_16->setAllowedMaxFileNumber(1);
        $upload_16->setAllowedExtensions(array('png'));
        $upload_16->setOverwriteWarning(false);
        $upload_16->getUpload()->setReplaceFile(true);

        $upload_17->setFolderName('summits/overview');
        $upload_17->setAllowedMaxFileNumber(1);
        $upload_17->setAllowedExtensions(array('png'));
        $upload_17->setOverwriteWarning(false);
        $upload_17->getUpload()->setReplaceFile(true);

        $upload_18->setFolderName('summits/overview');
        $upload_18->setAllowedMaxFileNumber(1);
        $upload_18->setAllowedExtensions(array('png'));
        $upload_18->setOverwriteWarning(false);
        $upload_18->getUpload()->setReplaceFile(true);

        $upload_19->setFolderName('summits/overview');
        $upload_19->setAllowedMaxFileNumber(1);
        $upload_19->setAllowedExtensions(array('png'));
        $upload_19->setOverwriteWarning(false);
        $upload_19->getUpload()->setReplaceFile(true);

        //summit timeline
        $fields->addFieldToTab("Root.Timeline", new TextField('TimelineCaption','Timeline Caption'));
        $fields->addFieldsToTab("Root.Timeline", $upload_14 = new UploadField('TimelineImage','Timeline (SVG)'));
        $fields->addFieldsToTab("Root.Timeline", $upload_20 = new UploadField('TimelineImagePng','Timeline (PNG)'));

        $upload_14->setFolderName('summits/overview');
        $upload_14->setAllowedMaxFileNumber(1);
        $upload_14->setAllowedExtensions(array('svg'));
        $upload_14->setOverwriteWarning(false);
        $upload_14->getUpload()->setReplaceFile(true);

        $upload_20->setFolderName('summits/overview');
        $upload_20->setAllowedMaxFileNumber(1);
        $upload_20->setAllowedExtensions(array('png'));
        $upload_20->setOverwriteWarning(false);
        $upload_20->getUpload()->setReplaceFile(true);

        return $fields;
    }

    public function getGrowthBoxTextTop(){
        $res = $this->getField('GrowthBoxTextTop');
        return $res;
    }

    public function getOverviewIntro(){
        $res = $this->getField('OverviewIntro');
        if(empty($res))
            return '<p><strong>The OpenStack Summit</strong> is a five-day conference for developers, users, and
                    administrators of OpenStack Cloud Software. It’s a great place to get started with OpenStack.</p>';
        return $res;
    }

    public function getGrowthBoxTextBottom(){
        $res = $this->getField('GrowthBoxTextBottom');
        return $res;
    }

    public function BoxChartLegendImageUrl(){
        return $this->GrowthBoxChartLegend()->exists() ? $this->GrowthBoxChartLegend()->getURL() : null;
    }

    public function BoxChartLegendImagePngUrl(){
        return $this->GrowthBoxChartLegendPng()->exists() ? $this->GrowthBoxChartLegendPng()->getURL() : null;
    }

    public function BoxChartImageUrl(){
        return $this->GrowthBoxChart()->exists() ?  $this->GrowthBoxChart()->getURL() : null;
    }

    public function BoxChartImagePngUrl(){
        return $this->GrowthBoxChartPng()->exists() ? $this->GrowthBoxChartPng()->getURL() : null;
    }

    public function BoxChartBackgroundImageUrl(){
        return $this->GrowthBoxBackground()->exists() ? $this->GrowthBoxBackground()->getURL() : null;
    }

    public function getRecapTitle(){

        $res = $this->getField('RecapTitle');
        if(empty($res))
            return 'Watch Video Recaps From Previous Summits';
        return $res;
    }

    public function getVideoRecapCaption1(){

        $res = $this->getField('VideoRecapCaption1');
        if(empty($res))
            return 'May 2015 in Vancouver, BC.';
        return $res;
    }

    public function getVideoRecapYouTubeID1(){
        $res = $this->getField('VideoRecapYouTubeID1');
        if(empty($res))
            return 'iZdEwQ-76P4';
        return $res;
    }

    public function getVideoRecapCaption2(){

        $res = $this->getField('VideoRecapCaption2');
        if(empty($res))
            return 'October 2014 in Hong Kong.';
        return $res;
    }

    public function getVideoRecapYouTubeID2(){
        $res = $this->getField('VideoRecapYouTubeID2');
        if(empty($res))
            return 'VA-8K4_4NIg';
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

    // atendees charts

    public function Atendees1ChartImageUrl(){
        return $this->Atendees1Chart()->exists() ? $this->Atendees1Chart()->getURL() : null;
    }

    public function Atendees1ChartImagePngUrl(){
        return $this->Atendees1ChartPng()->exists() ? $this->Atendees1ChartPng()->getURL() : null;
    }

    public function getAtendees1Label(){
        $res = $this->getField('Atendees1Label');
        if(empty($res))
            return 'Openstack Summit';
        return $res;
    }

    public function Atendees2ChartImageUrl(){
        return $this->Atendees2Chart()->exists() ? $this->Atendees2Chart()->getURL() : null;
    }

    public function Atendees2ChartImagePngUrl(){
        return $this->Atendees2ChartPng()->exists() ? $this->Atendees2ChartPng()->getURL() : null;
    }

    public function getAtendees2Label(){
        $res = $this->getField('Atendees2Label');
        if(empty($res))
            return 'Openstack Summit';
        return $res;
    }

    public function Atendees3ChartImageUrl(){
        return $this->Atendees3Chart()->exists() ? $this->Atendees3Chart()->getURL() : null;
    }

    public function Atendees3ChartImagePngUrl(){
        return $this->Atendees3ChartPng()->exists() ? $this->Atendees3ChartPng()->getURL() : null;
    }

    public function getAtendees3Label(){
        $res = $this->getField('Atendees3Label');
        if(empty($res))
            return 'Openstack Summit';
        return $res;
    }

    public function Atendees4ChartImageUrl(){
        return $this->Atendees4Chart()->exists() ? $this->Atendees4Chart()->getURL() : null;
    }

    public function Atendees4ChartImagePngUrl(){
        return $this->Atendees4ChartPng()->exists() ? $this->Atendees4ChartPng()->getURL() : null;
    }

    public function getAtendees4Label(){
        $res = $this->getField('Atendees4Label');
        if(empty($res))
            return 'Openstack Summit';
        return $res;
    }

    public function AtendeesChartRefImageUrl(){
        return $this->AtendeesChartRef()->exists() ? $this->AtendeesChartRef()->getURL() : null;
    }

    public function AtendeesChartRefImagePngUrl(){
        return $this->AtendeesChartRefPng()->exists() ? $this->AtendeesChartRefPng()->getURL() : null;
    }

    //summit timeline

    public function getTimelineCaption(){
        $res = $this->getField('TimelineCaption');
        if(empty($res))
            return 'The Full Access Pass gives you the ability to attend the keynotes, marketplace,
            business sessions and working groups, encompassing everything the SUmmit has to offer in Tokyo.';
        return $res;
    }

    public function TimelineImageUrl(){
        return $this->TimelineImage()->exists() ? $this->TimelineImage()->getURL() : null;
    }

    public function TimelineImagePngUrl(){
        return $this->TimelineImagePng()->exists() ? $this->TimelineImagePng()->getURL() : null;
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

        /*if(empty($this->SummitID)){
            return $valid->error('You must select a valid Summit!');
        }*/
        return $valid;
    }

    public function getOrderedHelpMenuItems(){
        return $this->HelpMenuItems()->sort('Order', 'ASC');
    }

}


class SummitOverviewPage_Controller extends SummitPage_Controller {

	public function init() {
        $this->top_section = 'full';
        parent::init();
	}

}