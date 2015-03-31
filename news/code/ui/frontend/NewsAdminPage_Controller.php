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
final class NewsAdminPage_Controller extends AdminController {

	/**
	 * @var array
	 */
	static $allowed_actions = array(
		'logout',
        'setArticleRank',
        'deleteArticle',
        'removeArticle'
	);

    /**
     * @var ISapphireNewsRepository
     */
    private $news_repository;

    /**
     * @var NewsRequestManager
     */
    private $news_manager;


    function init() {
        parent::init();

        Requirements::css(Director::protocol()."code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css");
        Requirements::css('news/code/ui/frontend/css/news.admin.css');

        Requirements::javascript(Director::protocol()."code.jquery.com/ui/1.10.4/jquery-ui.min.js");
        Requirements::javascript('news/code/ui/frontend/js/news.admin.js');
    }

	public function __construct(){
		parent::__construct();
		$this->news_repository = new SapphireNewsRepository();
        $this->news_manager = new NewsRequestManager(
            new SapphireNewsRepository,
            new SapphireSubmitterRepository,
            new NewsFactory,
            new NewsValidationFactory,
            new SapphireFileUploadService(),
            SapphireTransactionManager::getInstance()
        );
	}

	public function logout(){
		$current_member = Member::currentUser();
		if($current_member){
			$current_member->logOut();
			return Controller::curr()->redirect("Security/login?BackURL=" . urlencode($_SERVER['HTTP_REFERER']));
		}
		return Controller::curr()->redirectBack();
	}

    public function index(){

        $recent_news = $this->news_repository->getRecentNews();
        $standby_news = $this->news_repository->getStandByNews();

        return $this->renderWith(array('NewsAdminPage','Page'), array('RecentNews' => new ArrayList($recent_news),
                                                                      'RecentCount' => count($recent_news),
                                                                      'StandByNews' => new ArrayList($standby_news),
                                                                      'StandByCount' => count($standby_news)));
    }

    public function getArticles($type) {
        $output = '';
        $counter = 0;
        switch ($type) {
            case 'slider' :
                $articles = $this->news_repository->getSlideNews();
                break;
            case 'featured' :
                $articles = $this->news_repository->getFeaturedNews();
                break;
            case 'recent' :
                $articles = $this->news_repository->getRecentNews();
                break;
            case 'standby' :
                $articles = $this->news_repository->getStandByNews();
                break;
        }

        foreach ($articles as $article) {
            $counter++;
            $link = ($article->Link) ? $article->Link : '';
            $data = array('Id'=>$article->Id,'Rank'=>$article->Rank,'Link'=>$link,
                'Image'=>$article->getImage(),'Headline'=>$article->Headline,'Type'=>$type);
            $output .= $article->renderWith('NewsAdminPage_article', $data);
        }

        return $output;
    }

    public function setArticleRank() {
        $article_id = intval($this->request->postVar('id'));
        $old_rank = intval($this->request->postVar('old_rank'));
        $new_rank = intval($this->request->postVar('new_rank'));
        $type = $this->request->postVar('type');
        $target = $this->request->postVar('target');
        $is_new = $this->request->postVar('is_new');

        if ($is_new == 1) {
            // new item coming in, add and reorder
            $this->news_manager->moveNewsArticle($article_id,$new_rank,$target);
            $this->news_manager->sortNewsArticles($article_id,$new_rank,$old_rank,true,false,$target);
        } elseif ($type == $target) {
            //sorting within section, reorder
            $this->news_manager->sortNewsArticles($article_id,$new_rank,$old_rank,false,false,$type);
            $this->news_manager->moveNewsArticle($article_id,$new_rank,$target);
        } else {
            //item removed, reorder
            $this->news_manager->sortNewsArticles($article_id,$new_rank,$old_rank,false,true,$type);
        }
    }

    public function deleteArticle() {
        $article_id = intval($this->request->postVar('id'));

        $this->news_repository->deleteArticle($article_id);
    }

    public function removeArticle() {
        $article_id = intval($this->request->postVar('id'));
        $type = $this->request->postVar('type');
        $old_rank = intval($this->request->postVar('old_rank'));

        $this->news_manager->moveNewsArticle($article_id,0,'standby');
        $this->news_manager->sortNewsArticles($article_id,0,$old_rank,false,true,$type);
    }

} 