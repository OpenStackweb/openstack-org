<?php


class SummitPage extends Page
{

    const PageCustomTitle = 'Open Infrastructure Summit';

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
        // Twitter Conversion tracking for websites
        //https://support.twitter.com/articles/20170807-conversion-tracking-for-websites#
        'TwitterPixelId' => 'Text',
        'HeroCSSClass' => 'Text',
        'HeaderText' => 'HTMLText',
        'HeaderMessage' => 'HTMLText',
        'FooterLinksLeft' => 'HTMLText',
        'FooterLinksRight' => 'HTMLText',
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
        'FBPixelId' => '610497449158652',
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

            $fields->addFieldToTab('Root.Header&Footer', $header_text = new HtmlEditorField('HeaderText','Header Text'));
            $header_text->setRows(5);
            $fields->addFieldToTab('Root.Header&Footer', $header_msg = new HtmlEditorField('HeaderMessage','Header Message'));
            $header_msg->setRows(3);

            $fields->addFieldToTab('Root.Header&Footer', $footer_left = new HtmlEditorField('FooterLinksLeft','Footer Links Left'));
            $footer_left->setRows(5);
            $fields->addFieldToTab('Root.Header&Footer', $footer_right = new HtmlEditorField('FooterLinksRight','Footer Links Right'));
            $footer_right->setRows(5);

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
        $fields->addFieldToTab("Root.FacebookConversionTracking", new TextField("FBPixelId", "Pixel Id", "610497449158652"));
        //Twitter
        $fields->addFieldToTab("Root.TwitterConversionTracking", new TextField("TwitterPixelId", "Pixel Id", "l5lav"));
        return $fields;
    }

    public function onBeforeWrite() {
        parent::onBeforeWrite();
        $parent = $this->Parent();
        if($parent && ($parent instanceof SummitPage) && $parent->SummitID > 0){
            $this->SummitID = $parent->SummitID;
        }
    }

    public function LinkingMode() {
        if($this->isSection()) {
            return 'current';
        } else {
            return 'link';
        }
    }

}


class SummitPage_Controller extends Page_Controller
{

    public function init()
    {
        parent::init();
        SweetAlert2Dependencies::renderRequirements();
        Requirements::javascript("summit/javascript/summit.js");
        // Summit pages are so different visually we don't pull in the main css file
        Requirements::block("themes/openstack/css/combined.css");
        Requirements::css("summit/css/combined.css");
        Requirements::css("themes/openstack/css/static.combined.css");
        FontAwesomeDependencies::renderRequirements();

        Requirements::css('summit/css/static-summit-about-page.css');

    }

    public function CurrentSummit()
    {
        $summit = Summit::get_active();

        return $summit->isInDB() ? $summit : false;
    }

    public function getPageTitle()
    {
        return SummitPage::PageCustomTitle ." | {$this->Title}";
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

    public function getOrder(){
       $order = $this->request->getVar('order');
       return isset($order) && $order == "complete";
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
        $request       = $this->request;
        $order         = $request->requestVar("order");
        $tracking_code = '';

        if (isset($order) && $order == "complete") {
            //add FB tracking script
            $page = SummitPage::get()->byID($this->ID);
            if ($page && !empty($page->FBPixelId)) {
                $tracking_code = $this->renderWith("SummitPage_FBPixelCode",[
                    "FB_Data" => new ArrayData([
                        "FBPixelId" => $page->FBPixelId,
                    ])
                ]);
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

    public function getSummitAboutPageLink() {
        return $this->Summit()->Link;
    }

    public function getAboutPageNavClass(){
        if($this->Summit()->Link == $this->Link())
            return 'current';
        else {
            return 'link';
        }
    }

    public function MetaTags()
    {
        $tags = parent::MetaTags();
        return $tags;
    }

    public function getSummitPageText($field) {
        $header_text = $this->getField($field);

        if ($header_text) {
            return $header_text;
        } else if (is_a($this->Parent(),'SummitPage')) {
            return $this->Parent()->getField($field);
        }

        return '';
    }

    public function isMultiRegister() {
        if ($this->Summit()->ID == 27) return true;
        return false;

    }

    public function getRegisterButtonLabel() {
        return 'Register Now';
    }

}
