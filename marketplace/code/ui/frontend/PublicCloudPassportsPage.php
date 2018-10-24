<?php

/**
 * Class PublicCloudPassportsPage
 */
class PublicCloudPassportsPage extends Page
{
    private static $db = array(
    );

    private static $has_one = array(
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        return $fields;

    }

}


/**
 * Class PublicCloudPassportsPage_Controller
 */
class PublicCloudPassportsPage_Controller extends Page_Controller
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
        GoogleMapScriptBuilder::renderMarkersClustered();
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        Requirements::css("marketplace/code/ui/frontend/css/passports-page.css");
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
                ['label' => 'all', 'view' => 'date', 'grouped' => false, 'show' => true],
                ['label' => 'search', 'view' => 'search', 'grouped' => false, 'show' => false],
            ]
        ];

        return Convert::array2json($config);
    }

}