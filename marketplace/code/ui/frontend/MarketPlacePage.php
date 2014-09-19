<?php
/**
 * Class MarketPlacePage
 */
class MarketPlacePage extends Page {
}
/**
 * Class MarketPlacePage_Controller
 */
class MarketPlacePage_Controller extends Page_Controller {

	function init(){
		parent::init();
		Requirements::css("marketplace/code/ui/frontend/css/marketplace.css");
	}

	public function getDirectoryPages(){
		return MarketPlaceDirectoryPage::get();
	}

	public function canViewTab($type){
		$class = '';
		switch($type){
			case 1:
				$class = 'TrainingDirectoryPage';
				break;
			case 2:
				$class = 'DistributionsDirectoryPage';
				break;
			case 3:
				$class = 'PublicCloudsDirectoryPage';
				break;
			case 4:
				$class = 'ConsultantsDirectoryPage';
				break;
			case 5:
				$class = 'MarketPlaceDriverPage';
				break;
			case 6:
				$class = 'PrivateCloudsDirectoryPage';
				break;
		}
		if(!empty($class)){
			$page  = $class::get()->first();
			if($page)
			{
				$view_type = $page->CanViewType;
				switch($view_type){
					case 'LoggedInUsers':{
						$member = Member::currentUser();
						if(!$member) return false;
						return $member->isAdmin();
					}
					break;
				}
				return true;
			}
			return false;
		}
		return false;
	}

	public function getMarketPlaceTypeLink($type){
		$class = '';
		$link = '#';
		switch($type){
			case 1:
				$class = 'TrainingDirectoryPage';
				break;
			case 2:
				$class = 'DistributionsDirectoryPage';
				break;
			case 3:
				$class = 'PublicCloudsDirectoryPage';
				break;
			case 4:
				$class = 'ConsultantsDirectoryPage';
				break;
			case 5:
				$class = 'MarketPlaceDriverPage';
				break;
			case 6:
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
}