<?php

/**
 * Class UserStoriesPage
 */
class UserStoriesPage extends Page
{
    private static $db = array(
        'HeaderText'    => 'HTMLText',
        'HeroText'      => 'HTMLText',
        'YouTubeID'     => 'Varchar(255)',
    );

    private static $has_one = array(
        'HeroImage'     => 'Image'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('Content');
        $fields->addFieldToTab('Root.Main', $header_text = new HtmlEditorField('HeaderText', 'Header Text'));
        $header_text->setRows(4);
        $fields->addFieldToTab('Root.Main', $hero_text = new HtmlEditorField('HeroText', 'Hero Text'));
        $hero_text->setRows(4);
        $fields->addFieldToTab('Root.Main', new TextField('YouTubeID', 'YouTubeID'));

        $hero_image = new UploadField('HeroImage','Hero Image');
        $hero_image->setAllowedMaxFileNumber(1);
        $hero_image->setAllowedFileCategories('image');
        $fields->addFieldToTab('Root.Main', $hero_image);

        return $fields;

    }

}


/**
 * Class UserStoriesPage_Controller
 */
class UserStoriesPage_Controller extends Page_Controller
{

    /**
     * @var array
     */
    private static $url_handlers = [];

    /**
     * @var array
     */
    private static $allowed_actions = [];


    public function init()
    {
        parent::init();
        Requirements::javascript("user-stories/js/user-stories.js");
        Requirements::css("user-stories/css/user-stories.css");
    }

    /**
     * @return mixed
     */
    public function getJSONConfig()
    {
        $config = [
            'baseURL'       => rtrim($this->Link(), '/'),
            'securityToken' => SecurityToken::inst()->getValue(),
            'views'         => [
                ['label' => 'recent', 'view' => 'date'],
                ['label' => 'alphabetical', 'view' => 'name'],
                ['label' => 'location', 'view' => 'location'],
                ['label' => 'industry', 'view' => 'industry'],
            ]
        ];

        return Convert::array2json($config);
    }

    public function getEnterpriseEvents($limit = 3)
    {
        $next_summit = $this->getSummitEvent();
        $filter = array("EventEndDate:GreaterThan" => date('Y-m-d H:i:s'), "ID:not" => $next_summit->ID);
        return EventPage::get()
            ->where("EventCategory IN('Enterprise','Summit','OpenStack Days')")
            ->filter($filter)
            ->sort('EventStartDate')
            ->limit($limit);
    }

    public function getEnterpriseFeaturedEvents($limit = 3)
    {
        $next_summit = $this->getSummitEvent();
        $filter = array("EventEndDate:GreaterThan" => date('Y-m-d H:i:s'), "ID:not" => $next_summit->ID);
        return EventPage::get()
            ->where("EventCategory IN('Enterprise','Summit') AND EventSponsorLogoUrl IS NOT NULL")
            ->filter($filter)
            ->sort('EventStartDate')
            ->limit($limit);

    }

    public function getSummitEvent()
    {
        return EventPage::get()->where("IsSummit = 1 AND EventStartDate > NOW()")->sort('EventStartDate')->first();
    }

}