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

	private static $allowed_actions = [
	    'ViewReviews',
        'ViewPoweredOpenStackProducts',
        'ViewPoweredOpenStackProductDetail',
        'ViewOpenStackProductsByRegion',
    ];

	public function onBeforeInit(){
		Config::inst()->update(get_class($this), 'allowed_actions', self::$allowed_actions);
		Config::inst()->update(get_class($this->owner), 'allowed_actions', self::$allowed_actions);
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

	public function ViewPoweredOpenStackProducts(){
	    Requirements::clear();
	    // css

        Requirements::css('marketplace/ui/source/css/sangria.css');
        Requirements::css("themes/openstack/bower_assets/bootstrap/dist/css/bootstrap.min.css");
        Requirements::css("themes/openstack/bower_assets/fontawesome/css/font-awesome.min.css");
        Requirements::css('//fonts.googleapis.com/css?family=Open+Sans:300,400,700');
        Requirements::css('themes/openstack/bower_assets/datetimepicker/build/jquery.datetimepicker.min.css');

        // js
        Requirements::javascript("themes/openstack/bower_assets/jquery/dist/jquery.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jquery-migrate/jquery-migrate.min.js");
        Requirements::javascript("themes/openstack/bower_assets/bootstrap/dist/js/bootstrap.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jquery-cookie/jquery.cookie.js");
        Requirements::javascript('themes/openstack/bower_assets/jquery-mousewheel/jquery.mousewheel.js');
        Requirements::javascript('themes/openstack/bower_assets/php-date-formatter/js/php-date-formatter.min.js');
        Requirements::javascript("themes/openstack/bower_assets/datetimepicker/build/jquery.datetimepicker.min.js");
        Requirements::javascript('themes/openstack/javascript/jquery.tablednd.js');

        return $this->owner->getViewer('ViewPoweredOpenStackProducts')->process
        (
            $this->owner->Customise([
                'InteropProgramVersions' => InteropProgramVersion::get()->sort('Name', 'ASC')
            ])
        );
    }

    public function ViewPoweredOpenStackProductDetail(SS_HTTPRequest $request){
        Requirements::clear();
        // css

        Requirements::css('marketplace/ui/source/css/sangria.css');
        Requirements::css("themes/openstack/bower_assets/bootstrap/dist/css/bootstrap.min.css");
        Requirements::css("themes/openstack/bower_assets/fontawesome/css/font-awesome.min.css");
        Requirements::css('//fonts.googleapis.com/css?family=Open+Sans:300,400,700');
        Requirements::css('themes/openstack/bower_assets/datetimepicker/build/jquery.datetimepicker.min.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert2/dist/sweetalert2.min.css');
        Requirements::css("themes/openstack/css/validation.errors.css");

        // js
        Requirements::javascript("themes/openstack/bower_assets/jquery/dist/jquery.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jquery-migrate/jquery-migrate.min.js");
        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.validate.custom.methods.js");
        Requirements::javascript("themes/openstack/bower_assets/bootstrap/dist/js/bootstrap.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jquery-cookie/jquery.cookie.js");
        Requirements::javascript('themes/openstack/bower_assets/jquery-mousewheel/jquery.mousewheel.js');
        Requirements::javascript('themes/openstack/bower_assets/php-date-formatter/js/php-date-formatter.min.js');
        Requirements::javascript("themes/openstack/bower_assets/datetimepicker/build/jquery.datetimepicker.min.js");
        Requirements::javascript('themes/openstack/bower_assets/sweetalert2/dist/sweetalert2.min.js');
        Requirements::javascript("marketplace/code/ui/admin/js/utils.js");
        Requirements::javascript("marketplace/ui/source/js/ViewPoweredOpenStackProductDetail.js");

        $service_id = intval($request->param("ID"));
        if($service_id <= 0 ) return $this->owner->httpError(404);
        $service = OpenStackImplementation::get()->byID($service_id);
        if(is_null($service)) return $this->owner->httpError(404);

        return $this->owner->getViewer('ViewPoweredOpenStackProductDetail')->process
        (
            $this->owner->Customise([
                'Product'  => $service,
                'Programs' => InteropProgramVersion::get()->sort('Name', 'ASC'),
                'Releases' => OpenStackRelease::get()->sort('ReleaseDate', 'ASC'),
            ])
        );
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

    public function ViewOpenStackProductsByRegion(){
        Requirements::clear();
        // css

        Requirements::css('marketplace/ui/source/css/sangria.css');
        Requirements::css("themes/openstack/bower_assets/bootstrap/dist/css/bootstrap.min.css");
        Requirements::css("themes/openstack/bower_assets/fontawesome/css/font-awesome.min.css");
        Requirements::css('//fonts.googleapis.com/css?family=Open+Sans:300,400,700');

        // js
        Requirements::javascript("themes/openstack/bower_assets/jquery/dist/jquery.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jquery-migrate/jquery-migrate.min.js");
        Requirements::javascript("themes/openstack/bower_assets/bootstrap/dist/js/bootstrap.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jquery-cookie/jquery.cookie.js");
        Requirements::javascript('themes/openstack/bower_assets/jquery-mousewheel/jquery.mousewheel.js');
        Requirements::javascript('themes/openstack/bower_assets/php-date-formatter/js/php-date-formatter.min.js');
        Requirements::javascript('themes/openstack/javascript/jquery.tablednd.js');

        $repository = new SapphireRegionalServiceRepository();
        $regions = $repository->getAllRegions();

        return $this->owner->getViewer('ViewOpenStackProductsByRegion')->process
            (
                $this->owner->Customise([
                    'Regions'                => $regions
                ])
            );
    }

}