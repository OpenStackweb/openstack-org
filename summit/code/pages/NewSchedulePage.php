<?php

/**
 * Class NewSchedulePage
 */
class NewSchedulePage extends SummitPage
{
    private static $db = array
    (
        'EnableMobileSupport' => 'Boolean',
    );

    /**
     * @param ISummit $summit
     * @return SummitAppSchedPage
     */
    static public function getBy(ISummit $summit){
        $page = Versioned::get_by_stage('SummitAppSchedPage', 'Live')->filter('SummitID', $summit->getIdentifier())->first();
        if(is_null($page))
            $page = Versioned::get_by_stage('SummitAppSchedPage', 'Stage')->filter('SummitID', $summit->getIdentifier())->first();
        return $page;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab('Root.Main', new CheckboxField('EnableMobileSupport', 'Enable Mobile App Download Dialog'));
        return $fields;
    }
}

/**
 * Class NewSchedulePage_Controller
 */
class NewSchedulePage_Controller extends SummitPage_Controller
{

    public function init()
    {

        $this->top_section = 'short'; //or full

        parent::init();
        Requirements::css('summit/css/install_mobile_app.css');

        Requirements::javascript('node_modules/js-cookie/src/js.cookie.js');
        // browser detection
        Requirements::javascript('node_modules/bowser/bowser.min.js');
        if($this->EnableMobileSupport) {
            Requirements::javascript('summit/javascript/schedule/install_mobile_app.js');
        }

        Requirements::css(Director::protocol() . 'maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css?'.time());
        Requirements::css("marketplace/code/ui/frontend/css/star-rating.min.css");
        Requirements::css('node_modules/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
        Requirements::css('node_modules/summit-schedule-app/lib/main.css');
        Requirements::javascript('node_modules/summit-schedule-app/lib/main.js');

    }

    public function getPageTitle()
    {
        $entity  = $this->getSummitEntity($this->getRequest());

        if(!is_null($entity)) {
            $title = Convert::raw2att($entity->getOGTitle());
            if (!empty($title)) {
                return SummitPage::PageCustomTitle .' | ' .$title;
            }
        }

        return parent::getPageTitle();
    }

    public function MetaTags()
    {
        $request = $this->getRequest();
        $action  = $request->param("Action");
        $entity  = $this->getSummitEntity($request);

        if(!is_null($entity)){
            $tags = "<meta name=\"title\" content=\"" . Convert::raw2att($entity->getOGTitle()) . "\" />".PHP_EOL;
            $description = $entity->getOGDescription();
            if(!empty($description))
                $tags .= "<meta name=\"description\" content=\"" . Convert::raw2att($description) . "\" />".PHP_EOL;
            $tags .= $entity->MetaTags();
            return $tags;
        }

        $tags = parent::MetaTags();
        // default one
        $url_path = "schedule";
        if(!empty($action) && $action == 'global-search'){
            $term = Convert::raw2sql($request->requestVar('t'));
            $url_path = "search";
            if(!empty($term)){
                $url_path .= "/".urlencode($term);
            }
        }
        // IOS
        $tags .= AppLinkIOSMetadataBuilder::buildAppLinksMetaTags($tags, $url_path);
        // Android
        $tags .= AppLinkIAndroidMetadataBuilder::buildAppLinksMetaTags($tags, $url_path);
        return $tags;
    }

    public function index(SS_HTTPRequest $request){
        // only send meta tags ( needed for android deep linking)
        if($request->getHeader("Prefer-Html-Meta-Tags")){
            return $this->buildOnlyMetaTagsResponse($this->MetaTags());
        }

        return $this->getViewer('index')->process($this);
    }

    public function getAccessToken() {
        $accessToken = Session::get('access_token');
        return $accessToken;
    }

    public function getApiUrl() {
        return OPENSTACK_RESOURCE_API_BASE_URL;
    }

}