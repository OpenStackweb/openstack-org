<?php

class SummitOverviewPage extends SummitPage {

    private static $has_one = array(
        'GrowthBoxBackground' => 'BetterImage',
        'GrowthBoxChartLegend' => 'File',
        'GrowthBoxChart'       => 'File',
        'GrowthBoxChartLegendPng' => 'File',
        'GrowthBoxChartPng'       => 'File',
    );

    private static $db = array(
        'GrowthBoxTextTop'    => 'HTMLText',
        'GrowthBoxTextBottom' => 'HTMLText',
        'VideoRecapCaption'   => 'Text',
        'VideoRecapYouTubeID' => 'Text',
        'ScheduleTitle' => 'Text',
        'ScheduleText' => 'HTMLText',
        'ScheduleUrl' => 'Text',
        'NetworkingContent' => 'HTMLText',
    );

    private static $has_many = array(
        'NetworkingPhotos' => 'SummitNetworkingPhoto',
    );

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
        }

        $fields->addFieldsToTab('Root.Schedule', new TextField('ScheduleTitle','Title'));
        $fields->addFieldsToTab('Root.Schedule', new HtmlEditorField('ScheduleText','Text'));
        $fields->addFieldsToTab('Root.Schedule', new TextField('ScheduleUrl','Url'));

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
}


class SummitOverviewPage_Controller extends SummitPage_Controller {

	public function init() {
        $this->top_section = 'full';
        parent::init();
	}

}