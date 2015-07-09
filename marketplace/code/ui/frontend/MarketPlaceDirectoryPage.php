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
class MarketPlaceDirectoryPage extends MarketPlacePage
{
	static $db = array(
		'GAConversionId'       => 'Text',
		'GAConversionLanguage' => 'Text',
		'GAConversionFormat'   => 'Text',
		'GAConversionColor'    => 'Text',
		'GAConversionLabel'    => 'Text',
		'GAConversionValue'    => 'Int',
		'GARemarketingOnly'    => 'Boolean',
		'RatingCompanyID'      => 'Int',
		'RatingBoxID'          => 'Int',
	);

	static $defaults = array(
		'RatingCompanyID' => 4398,
		'RatingBoxID'     => 11919,
	);

	function getCMSFields()
	{
		$fields = parent::getCMSFields();
		//Google Conversion Tracking Params
		$fields->addFieldToTab("Root.GoogleConversionTracking",new TextField("GAConversionId","Conversion Id"));
		$fields->addFieldToTab("Root.GoogleConversionTracking",new TextField("GAConversionLanguage","Conversion Language","en"));
		$fields->addFieldToTab("Root.GoogleConversionTracking",new TextField("GAConversionFormat","Conversion Format","3"));
		$fields->addFieldToTab("Root.GoogleConversionTracking",new ColorField("GAConversionColor","Conversion Color","ffffff"));
		$fields->addFieldToTab("Root.GoogleConversionTracking",new TextField("GAConversionLabel","Conversion Label"));
		$fields->addFieldToTab("Root.GoogleConversionTracking",new TextField("GAConversionValue","Conversion Value","0"));
		$fields->addFieldToTab("Root.GoogleConversionTracking",new CheckboxField("GARemarketingOnly","Remarketing Only"));
		$fields->addFieldToTab("Root.RatingBoxWidget",new LiteralField('Label','** more info at <a href="http://www.rating-system.com/integration/UserGuide.aspx">User Guide</a>'));
		$fields->addFieldToTab("Root.RatingBoxWidget",new TextField("RatingCompanyID","Company ID",4398));
		$fields->addFieldToTab("Root.RatingBoxWidget",new TextField("RatingBoxID","Rating Box ID",11919));
		return $fields;
	}

	static $allowed_children = "none";

}

class MarketPlaceDirectoryPage_Controller extends MarketPlacePage_Controller {

    /**
     * @var IEntityRepository
     */
    private $review_repository;

    /**
     * @var ReviewManager
     */
    private $review_manager;

	private static $allowed_actions = array();

    function init(){
        parent::init();

        Requirements::javascript("marketplace/code/ui/frontend/js/marketplace.common.js");

        $this->review_repository = new SapphireReviewRepository;

        $this->review_manager = new ReviewManager(
            new SapphireReviewRepository,
            new SapphireJobAlertEmailRepository,
            new ReviewFactory,
            SapphireTransactionManager::getInstance()
        );
    }

	/**
	 * @return string
	 */
	protected function GATrackingCode(){

		$tracking_code = '';
		//add GA tracking script
		$page = $this->data();

		if($page && !empty($page->GAConversionId)
			&& !empty($page->GAConversionLanguage)
			&& !empty($page->GAConversionFormat)
			&& !empty($page->GAConversionColor)
			&& !empty($page->GAConversionLabel)){

			$tracking_code = $this->renderWith("MarketPlaceDirectoryPage_GA",array(
				"GA_Data"=> new ArrayData(array(
						"GAConversionId"       => $page->GAConversionId,
						"GAConversionLanguage" => $page->GAConversionLanguage,
						"GAConversionFormat"   => $page->GAConversionFormat,
						"GAConversionColor"    => $page->GAConversionColor,
						"GAConversionLabel"    => $page->GAConversionLabel,
						"GAConversionValue"    => $page->GAConversionValue,
						"GARemarketingOnly"    => $page->GARemarketingOnly?"true":"false",
					))
			));
		}

		return $tracking_code;
	}

    public function MarketPlaceReviewForm(){
        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");

        Requirements::combine_files('marketplace_review_form.js', array(
                "themes/openstack/javascript/jquery.validate.custom.methods.js",
                "marketplace/code/ui/frontend/js/star-rating.min.js",
                "marketplace/code/ui/frontend/js/marketplace.review.js"
            )
        );

        $css_files =  array(
            "marketplace/code/ui/frontend/css/star-rating.min.css",
            "marketplace/code/ui/frontend/css/marketplace-review.css"
        );

        foreach($css_files as $css_file)
            Requirements::css($css_file);

        $form            = new MarketPlaceReviewForm($this, 'MarketPlaceReviewForm');
        $data            = Session::get("FormInfo.Form_MarketPlaceReviewForm.data");
        $review          = $this->review_repository->getReview($this->company_service_ID,Member::CurrentUserID());

        if(is_array($data)) { //get data from cache
            $form->loadDataFrom($data);
        } elseif ($review) { // get submitted review
            $form->loadDataFrom($review);
        }

        // Optional spam protection
        if(class_exists('SpamProtectorManager')) {
            SpamProtectorManager::update_form($form);
        }
        return $form;
    }

    public function MarketPlaceReviews(){
        $output = '';
        $reviews = $this->ProductReviews();
        $old_reviews = array();

        $ds = new CvsDataSourceReader("\t");
        $cur_path = Director::baseFolder();
        $ds->Open($cur_path . "/marketplace/code/migrations/data/reviews.csv");
        $headers = $ds->getFieldsInfo();

        while (($row = $ds->getNextRow()) !== FALSE) {
            $review = explode(',',$row[0]);
            $created = date('M jS Y',strtotime($review[1]));
            $title = $review[5];
            $comment = $review[3];
            $rating = $review[0];
            $user_name = $review[6];
            $product_id = $review[2];
            if ($this->company_service_ID == $product_id) {
                $old_reviews[] = new ArrayData(array(
                    "IsImported" => 1,
                    "Created" => $created,
                    "Title" => $title,
                    "Comment" => $comment,
                    "Rating" => $rating*20,
                    "UserName" => $user_name
                ));
            }
        }

        $reviews = array_merge($old_reviews,$reviews->toArray());
        usort($reviews,array($this, 'sort_reviews'));


        foreach ($reviews as $review) {
            if ($review->IsImported) {
                $output .= $review->renderWith('MarketPlaceReviews_review_import');
            } else {
                $output .= $review->renderWith('MarketPlaceReviews_review');
            }
        }

        return $output;
    }

    public function ProductReviews(){
        list($reviews,$size) = $this->review_repository->getAllApprovedByProduct($this->company_service_ID);
        return new ArrayList($reviews);
    }

    private function sort_reviews($a,$b)
    {
        if ($a->Created==$b->Created) return 0;
        return ($a->Created<$b->Created)?-1:1;
    }
}