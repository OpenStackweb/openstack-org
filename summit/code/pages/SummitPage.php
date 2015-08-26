<?php


class SummitPage extends Page
{

    private static $has_one = array(
        'SummitImage'   => 'SummitImage',
        'Summit'        => 'Summit',
    );

    private static $db = array(
        //Google Analitycs Params
        'GAConversionId' => 'Text',
        'GAConversionLanguage' => 'Text',
        'GAConversionFormat' => 'Text',
        'GAConversionColor' => 'Text',
        'GAConversionLabel' => 'Text',
        'GAConversionValue' => 'Int',
        'GARemarketingOnly' => 'Boolean',
        //Facebook Conversion Params
        'FBPixelId' => 'Text',
        'FBValue' => 'Text',
        'FBCurrency' => 'Text',
        // Twitter Conversion tracking for websites
        //https://support.twitter.com/articles/20170807-conversion-tracking-for-websites#
        'TwitterPixelId' => 'Text',
        'HeroCSSClass' => 'Text',
    );

    static $defaults = array(
        //Google
        "GAConversionId" => "994798451",
        "GAConversionLanguage" => "en",
        "GAConversionFormat" => "3",
        "GAConversionColor" => "ffffff",
        "GAConversionLabel" => "IuM5CK3OzQYQ89at2gM",
        "GAConversionValue" => 0,
        "GARemarketingOnly" => false,
        //FB
        'FBPixelId' => '6013247449963',
        'FBValue' => '0.00',
        'FBCurrency' => 'USD',
        //Twitter
        'TwitterPixelId' => 'l5lav',
    );


    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        if ($this->ID) {

            // Summit Images
            $summitImageField = singleton('SummitImage')->getCMSFields();
            $config = GridFieldConfig_RelationEditor::create();
            $config->getComponentByType('GridFieldDetailForm')->setFields($summitImageField);
            $gridField = new GridField('SummitImage', 'SummitImage', SummitImage::get(), $config);
            $fields->addFieldToTab('Root.SummitPageImages', $gridField);

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
        $fields->addFieldsToTab('Root.Main', new TextField('HeroCSSClass', 'Hero CSS Class'));
        //Google Conversion Tracking params
        $fields->addFieldToTab("Root.GoogleConversionTracking", new TextField("GAConversionId", "Conversion Id", "994798451"));
        $fields->addFieldToTab("Root.GoogleConversionTracking", new TextField("GAConversionLanguage", "Conversion Language", "en"));
        $fields->addFieldToTab("Root.GoogleConversionTracking", new TextField("GAConversionFormat", "Conversion Format", "3"));
        $fields->addFieldToTab("Root.GoogleConversionTracking", new ColorField("GAConversionColor", "Conversion Color", "ffffff"));
        $fields->addFieldToTab("Root.GoogleConversionTracking", new TextField("GAConversionLabel", "Conversion Label", "IuM5CK3OzQYQ89at2gM"));
        $fields->addFieldToTab("Root.GoogleConversionTracking", new TextField("GAConversionValue", "Conversion Value", "0"));
        $fields->addFieldToTab("Root.GoogleConversionTracking", new CheckboxField("GARemarketingOnly", "Remarketing Only"));
        //Facebook Conversion Params
        $fields->addFieldToTab("Root.FacebookConversionTracking", new TextField("FBPixelId", "Pixel Id", "6013247449963"));
        $fields->addFieldToTab("Root.FacebookConversionTracking", new TextField("FBValue", "Value", "0.00"));
        $fields->addFieldToTab("Root.FacebookConversionTracking", new TextField("FBCurrency", "Currency", "USD"));
        //Twitter
        $fields->addFieldToTab("Root.TwitterConversionTracking", new TextField("TwitterPixelId", "Pixel Id", "l5lav"));
        return $fields;
    }

    public function onBeforeWrite() {
        parent::onBeforeWrite();
        $parent = $this->Parent();
        if($parent && ($parent instanceof SummitPage) && $parent->SummitID > 0)
        {
            $this->SummitID = $parent->SummitID;
        }
    }

}


class SummitPage_Controller extends Page_Controller
{

    public function init()
    {
        parent::init();
        Requirements::javascript("summit/bower_components/sweetalert/lib/sweet-alert.js");
        Requirements::css("summit/bower_components/sweetalert/lib/sweet-alert.css");
        Requirements::javascript("summit/javascript/summit.js");
        // Summit pages are so different visually we don't pull in the main css file
        Requirements::block("themes/openstack/css/combined.css");
        Requirements::css("summit/css/combined.css");
    }

    public function CurrentSummit()
    {
        $summit = Summit::get_active();

        return $summit->isInDB() ? $summit : false;
    }


    public function PreviousSummit()
    {
        return Summit::get()
            ->sort('SummitEndDate', 'DESC')
            ->filter('SummitEndDate:LessThan', date('Y-m-D'))
            ->first();
    }


    public function CountdownDigits($current_submit_id = 0)
    {
        $summit  = $current_submit_id  > 0 ? Summit::get()->byId($current_submit_id): $this->Summit();
        if (is_null($summit)) return null;

        $date = $summit->obj('SummitBeginDate');

        if ($date->InPast()) {
            return;
        }

        $exploded_date = explode(' ', $date->TimeDiffIn('days'), 3);

        $days = str_pad($exploded_date[0], 3, '0', STR_PAD_LEFT);
        $html = '';

        foreach (str_split($days) as $digit) {
            $html .= sprintf('<span>%s</span>', $digit);
        }

        return $html;
    }

    function getTopSection()
    {
        return $this->Link();
    }


    public function IsWelcome()
    {
        return $this->request->getVar('welcome');
    }

    /*
    * Return google tracking script if ?order=complete query string param is present
    *  using settings of current conference page
    */
    function GATrackingCode()
    {
        $request = $this->request;
        $order = $request->requestVar("order");
        $tracking_code = '';
        if (isset($order) && $order == "complete") {
            //add GA tracking script
            $page = SummitPage::get()->byID($this->ID);
            if ($page && !empty($page->GAConversionId)
                && !empty($page->GAConversionLanguage)
                && !empty($page->GAConversionFormat)
                && !empty($page->GAConversionColor)
                && !empty($page->GAConversionLabel)
            ) {
                $tracking_code = $this->renderWith("SummitPage_GA", array(
                    "GA_Data" => new ArrayData(array(
                        "GAConversionId" => $page->GAConversionId,
                        "GAConversionLanguage" => $page->GAConversionLanguage,
                        "GAConversionFormat" => $page->GAConversionFormat,
                        "GAConversionColor" => $page->GAConversionColor,
                        "GAConversionLabel" => $page->GAConversionLabel,
                        "GAConversionValue" => $page->GAConversionValue,
                        "GARemarketingOnly" => $page->GARemarketingOnly ? "true" : "false",
                    ))
                ));
            }
        }
        return $tracking_code;
    }

    function FBTrackingCode()
    {
        $request = $this->request;
        $order = $request->requestVar("order");
        $tracking_code = '';
        if (isset($order) && $order == "complete") {
            //add FB tracking script
            $page = SummitPage::get()->byID($this->ID);
            if ($page && !empty($page->FBPixelId)
                && !empty($page->FBValue)
                && !empty($page->FBCurrency)
            ) {
                $tracking_code = $this->renderWith("SummitPage_FB", array(
                    "FB_Data" => new ArrayData(array(
                        "FBPixelId" => $page->FBPixelId,
                        "FBValue" => $page->FBValue,
                        "FBCurrency" => $page->FBCurrency,
                    ))
                ));
            }
        }
        return $tracking_code;
    }

    function TwitterTrackingCode()
    {
        $request = $this->request;
        $order = $request->requestVar("order");
        $tracking_code = '';
        if (isset($order) && $order == "complete") {
            //add FB tracking script
            $page = SummitPage::get()->byID($this->ID);
            if ($page && !empty($page->TwitterPixelId)
            ) {
                $tracking_code = $this->renderWith("SummitPage_Twitter", array(
                    "Twitter_Data" => new ArrayData(array(
                        "TwitterPixelId" => $page->TwitterPixelId,
                    ))
                ));
            }
        }
        return $tracking_code;
    }

    public function getSummitRoot(){
        if($this->ClassName === 'SummitOverviewPage')
            return $this->Link();
        else{
            //childs page
            return $this->Parent()->Link();
        }
    }

    public function MainNavClass(){
        if($this->ClassName === 'SummitOverviewPage')
            return 'current';
        else{
            //childs page
            return 'link';
        }
    }
}