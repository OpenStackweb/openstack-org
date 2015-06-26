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
 * Class NewsPage_Controller
 */
final class NewsArchivedPage_Controller extends Page_Controller {

    /**
     * @var array
     */
    static $allowed_actions = array(
        'logout',
        'ViewArticle',
        'signup',
        'sendSignupConfirmation',
        'newsPage'
    );

    /**
     * @var SecurityToken
     */
    private $securityToken;
    private $news_per_page = 10;

    static $url_handlers = array(
        'view/$NEWS_ID/$NEWS_TITLE'   => 'ViewArticle',
    );

    function init() {
        parent::init();
        $this->securityToken = new SecurityToken();
        Requirements::css('news/code/ui/frontend/css/news.css');
        Requirements::css(Director::protocol().'://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css');
        Requirements::javascript("themes/openstack/javascript/bootstrap-paginator.js");
        Requirements::javascript("news/code/ui/frontend/js/news.archived.js");
    }

    public function __construct(){
        parent::__construct();
        $this->news_repository = new SapphireNewsRepository();
    }

    public function index(){
        $searchTerm = isset($_GET["searchTerm"]) ? $_GET["searchTerm"] : '';
        $archived_news = new ArrayList($this->news_repository->getArchivedNews(0, $this->news_per_page,$searchTerm));
        $archived_news_pages = ceil($this->news_repository->getArchivedNewsCount($searchTerm) / $this->news_per_page);

        return $this->renderWith(array('NewsArchivePage','Page'), array('ArchivedNews' => $archived_news, 'ArchivedNewsPages' => $archived_news_pages));
    }

    public function newsPage()
    {
        $number = isset($_GET["number"]) ? intval($_GET["number"]) : 0;
        $searchTerm = isset($_GET["searchTerm"]) ? $_GET["searchTerm"] : '';
        $archived_news = new ArrayList($this->news_repository->getArchivedNews(($number - 1) * $this->news_per_page, $this->news_per_page, $searchTerm));
        return $this->renderWith(array('NewsArchivePage_Articles'), array('ArchivedNews' => $archived_news));
    }
}