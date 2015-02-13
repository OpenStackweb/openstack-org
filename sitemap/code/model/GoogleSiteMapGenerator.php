<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class GoogleSiteMapGenerator
 */
final class GoogleSiteMapGenerator
{


    /**
     * How frequently the page is likely to change. This value provides general information to search engines and may
     * not correlate exactly to how often they crawl the page.
     */
    const CHANGE_FREQ_MONTHLY = 'monthly';
    const CHANGE_FREQ_ALWAYS = 'always';
    const CHANGE_FREQ_HOURLY = 'hourly';
    const CHANGE_FREQ_DAILY = 'daily';
    const CHANGE_FREQ_WEEKLY = 'weekly';
    const CHANGE_FREQ_YEARLY = 'yearly';
    const CHANGE_FREQ_NEVER = 'never';
        /**
     * The priority of this URL relative to other URLs on your site. Valid values range from 0.0 to 1.0.
     * This value does not affect how your pages are compared to pages on other sites—it only lets the search engines
     * know which pages you deem most important for the crawlers.
     * Please note that the priority you assign to a page is not likely to influence the position of your URLs in a
     * search engine's result pages.
     * Search engines may use this information when selecting between URLs on the same site, so you can use this tag to
     * increase the likelihood that your
     * most important pages are present in a search index.
     * Also, please note that assigning a high priority to all of the URLs on your site is not likely to help you. Since
     * the priority is relative, it is only used
     * to select between URLs on your site.
     */
    const PRIORITY_1_0 = 1.0; // should be used to describe documents that change each time they are accessed
    const PRIORITY_0_9 = 0.9;
    const PRIORITY_0_8 = 0.8;
    const PRIORITY_0_7 = 0.7;
    const PRIORITY_0_6 = 0.6;
    const PRIORITY_0_5 = 0.5; // should be used to describe archived URLs.
    const PRIORITY_0_4 = 0.4;
    const PRIORITY_0_3 = 0.3;
    const PRIORITY_0_2 = 0.2;
    const PRIORITY_0_1 = 0.1;
    const PRIORITY_0_0 = 0.0;
        private static $instance; //The default priority of a page is 0.5
    private $data_objects = array();
    private $plain_urls = array();

    private function __construct()
    {


        $this_var = $this;
        PublisherSubscriberManager::getInstance()->subscribe('dataobject_publish', function ($do) use ($this_var) {
            $this_var->update();
        });

        PublisherSubscriberManager::getInstance()->subscribe('dataobject_unpublish', function ($do) use ($this_var) {
            $this_var->update();
        });
    }

    /**
     * Submitting your Sitemap via an HTTP request
     * To submit your Sitemap using an HTTP request (replace <searchengine_URL> with the URL provided by the search
     * engine), issue your request to the following URL:
     * earchengine_URL>/ping?sitemap=sitemap_url
     * For example, if your Sitemap is located at http://www.example.com/sitemap.gz, your URL will become:
     * <searchengine_URL>/ping?sitemap=http://www.example.com/sitemap.gz
     * URL encode everything after the /ping?sitemap=:
     * <searchengine_URL>/ping?sitemap=http%3A%2F%2Fwww.yoursite.com%2Fsitemap.gz
     * You can issue the HTTP request using wget, curl, or another mechanism of your choosing. A successful request will
     * return an HTTP 200 response code; if you receive a different response, you should resubmit your request. The HTTP
     * 200 response code only indicates that the search engine has received your Sitemap, not that the Sitemap itself or
     * the URLs contained in it were valid. An easy way to do this is to set up an automated job to generate and submit
     * Sitemaps on a regular basis.
     * Note: If you are providing a Sitemap index file, you only need to issue one HTTP request that includes the
     * location of the Sitemap index file; you do not need to issue individual requests for each Sitemap listed in the
     * index.
     * @return string|void
     */
    public function update()
    {
        // Don't ping if the site has disabled it, or if the site is in dev mode
        if (Director::isDev()) {
            return;
        }
        $location = urlencode(Controller::join_links(
            Director::absoluteBaseURL(),
            'sitemap.xml'
        ));
        $response = $this->send_ping(
            "www.google.com", "/webmasters/sitemaps/ping", sprintf("sitemap=%s", $location)
        );
        return $response;
    }

    private function send_ping($host, $path, $query)
    {

        $socket = fsockopen($host, 80, $errno, $error);
        if (!$socket) {
            return $error;
        }
        if ($query) {
            $query = '?' . $query;
        }
        $request = "GET {$path}{$query} HTTP/1.1\r\nHost: $host\r\nConnection: Close\r\n\r\n";
        fwrite($socket, $request);
        $response = stream_get_contents($socket);
        return $response;
    }

    /**
     * @return GoogleSiteMapGenerator
     */
    public static function getInstance()
    {
        if (!is_object(self::$instance)) {
            self::$instance = new GoogleSiteMapGenerator();
        }
        return self::$instance;
    }

    /**
     * @param string $class_name
     * @param string $change_freq
     * @param float  $priority
     * @param null   $build_url_function
     * @param null   $fetch_function
     * @return bool
     */
    public function registerDataObject($class_name, $change_freq = GoogleSiteMapGenerator::CHANGE_FREQ_ALWAYS , $priority = GoogleSiteMapGenerator::PRIORITY_0_5,$build_url_function = null, $fetch_function = null)
    {
        if (isset($this->data_objects[$class_name])) return false;
        $entry = new GoogleSiteMapEntryDataObjectTemplate($class_name, $change_freq, $priority, $build_url_function, $fetch_function );
        $class_name::add_extension('GoogleSiteMapExtension');
        $this->data_objects[$class_name] = $entry;
        return true;
    }

    public function registerPlainUrl($url, $change_freq, $priority)
    {
        $key = md5($url);
        if (isset($this->plain_urls[$key])) return false;

        $entry = new GoogleSiteMapEntry($url, $change_freq, $priority);

        $this->plain_urls[$key] = $entry;

        return true;
    }

    public function isRegisteredDataObject($class_name)
    {
        return isset($this->data_objects[$class_name]);
    }

    public function Entries()
    {
        $member = Member::currentUser();
        if($member){
            $member->logOut();
        }

        $list = new ArrayList();
        //first check registered_data objects
        if (class_exists('Translatable')) {
            Translatable::disable_locale_filter();
        }
        foreach ($this->data_objects as $class_name => $template) {
            if ($class_name == 'SiteTree' || is_subclass_of($class_name, 'SiteTree')) {
                $objects = Versioned::get_by_stage($class_name, 'Live', "CanViewType = 'Anyone' || CanViewType = 'Inherit'");
            } else {
                if($template->hasFetchFunction())
                    $objects = $template->fetch();
                else
                    $objects = $class_name::get();
            }
            foreach ($objects as $obj) {
                if ($obj instanceof ErrorPage) continue;
                if ($obj instanceof SiteTree && (!$obj->canIncludeInGoogleSiteMap() || !$obj->canView())) continue;
                $url = $template->buildUrl($obj);
                if(!$url) continue;
                $change_freq = $obj->hasMethod('getChangeFrequency')? $obj->getChangeFrequency() : false;
                if (!$change_freq) $change_freq = $template->change_freq;
                $priority = $obj->hasMethod('getGooglePriority')? $obj->getGooglePriority() : false;
                if (!$priority) $priority = $template->priority;
                $list->add(new GoogleSiteMapEntry($url, $change_freq, $priority));
            }
        }
        //then plain urls...
        foreach ($this->plain_urls as $key => $entry) {
            $list->add($entry);
        }
        return $list;
    }

    private function __clone()
    {
    }
}

class GoogleSiteMapEntry extends ViewableData
{

    protected $url;
    protected $change_freq;
    protected $priority;

    public function __construct($url, $change_freq, $priority)
    {

        $this->url = $url;
        $this->change_freq = $change_freq;
        $this->priority = $priority;
    }

    public function getUrl()
    {
        return htmlspecialchars($this->url);
    }

    public function getChangeFreq(){
        return $this->change_freq;
    }

    public function getPriority(){
        return $this->priority;
    }

}

class GoogleSiteMapEntryDataObjectTemplate extends GoogleSiteMapEntry
{

    private $class_name;
    private $get_url_function;
    private $fetch_function;

    public function __construct($class_name, $change_freq, $priority, $get_url_function, $fetch_function = null)
    {
        $this->class_name       = $class_name;
        $this->get_url_function = $get_url_function;
        $this->fetch_function   = $fetch_function;
        parent::__construct('', $change_freq, $priority);
    }

    public function buildUrl($entity)
    {
        return $this->get_url_function->__invoke($entity);
    }

    public function hasFetchFunction(){
        return $this->fetch_function != null;
    }

    public function fetch(){
        return $this->fetch_function->__invoke();
    }
}

