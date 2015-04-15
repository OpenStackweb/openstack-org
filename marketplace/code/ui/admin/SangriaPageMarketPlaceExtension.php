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
 * Class SangriaPageMarketPlaceExtension
 */
final class SangriaPageMarketPlaceExtension extends Extension {

	private $repository;

	public function __construct(){
		$this->repository = new SapphireReviewRepository;
		parent::__construct();
	}

	public function onBeforeInit(){
		Config::inst()->update(get_class($this), 'allowed_actions', array('ViewReviews'));
		Config::inst()->update(get_class($this->owner), 'allowed_actions', array('ViewReviews'));
	}

	public function onAfterInit(){

	}

	private function commonScripts(){
		Requirements::css("themes/openstack/css/chosen.css", "screen,projection");
		Requirements::javascript("themes/openstack/javascript/chosen.jquery.min.js");
        Requirements::javascript('marketplace/code/ui/admin/js/utils.js');
	}

	public function ViewReviews(){
		$this->commonScripts();
		Requirements::css("marketplace/code/ui/admin/css/sangria.page.view.reviews.css");
        Requirements::css("marketplace/code/ui/frontend/css/star-rating.min.css");
		Requirements::javascript('marketplace/code/ui/admin/js/sangria.page.marketplace.extension.js');
		return $this->owner->getViewer('ViewReviews')->process($this->owner);
	}

    public function getMarketPlaceTypeLink($type){
        $class = '';
        $link = '#';
        switch($type){
            case 'TrainingService':
                $class = 'TrainingDirectoryPage';
                break;
            case 'Distribution':
                $class = 'DistributionsDirectoryPage';
                break;
            case 'PublicCloudService':
                $class = 'PublicCloudsDirectoryPage';
                break;
            case 'Consultant':
                $class = 'ConsultantsDirectoryPage';
                break;
            case 'PrivateCloudService':
                $class = 'PrivateCloudsDirectoryPage';
                break;
        }
        if(!empty($class)){
            $page  = $class::get()->first();
            if($page)
                $link  = $page->Link();
        }
        return $link;
    }

	public function getApprovedReviews(){
		list($list,$size) = $this->repository->getAllApproved(0,1000);
		return new ArrayList($list);
	}

    public function getNotApprovedReviews(){
        list($list,$size) = $this->repository->getAllNotApproved(0,1000);
        return new ArrayList($list);
    }

	public function getQuickActionsExtensions(&$html){
		$view = new SSViewer('SangriaPage_MarketPlaceLinks');
		$html .= $view->process($this->owner);
	}

}