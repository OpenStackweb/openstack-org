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
 * Class ReviewManager
 */
final class ReviewManager {
	/**
	 * @var IEntityRepository
	 */
	private $review_repository;
	/**
	 * @var ITransactionManager
	 */
	private $tx_manager;
	/**
	 * @var IReviewFactory
	 */
	private $review_factory;

	/**
	 * @param IEntityRepository      $review_repository
	 * @param IReviewFactory         $factory
	 * @param ITransactionManager    $tx_manager
	 */
	public function __construct(IEntityRepository $review_repository,
	                            IReviewFactory $review_factory,
	                            ITransactionManager $tx_manager){

		$this->review_repository = $review_repository;
		$this->review_factory    = $review_factory;
		$this->tx_manager        = $tx_manager;
	}

    /**
     * @param $id
     * @return IMarketPlaceReview
     */
    public function toggleApproved($id){
        $review_repository          = $this->review_repository;

        $review =  $this->tx_manager->transaction(function() use ($id, $review_repository){
            $review = $review_repository->getById($id);
            if(!$review) throw new NotFoundEntityException('MarketPlaceReview',sprintf('id %s',$id ));
            $review->toggleApproved();

            return $review;
        });


        return $review;
    }

    /**
     * @param array $data
     * @param IMarketPlaceReview $review
     * @return IMarketPlaceReview
     */
    public function updateReview(array $data, IMarketPlaceReview $review){
        $repository        = $this->review_repository ;
        $factory           = $this->review_factory;
        return $this->tx_manager->transaction(function() use($data, $review, $repository, $factory){
            if(!$review)
                throw new NotFoundEntityException('MarketPlaceReview',sprintf('id %s',$review->getIdentifier()));

            $review->registerReviewMainInfo($factory->buildReviewMainInfo($data));

            $email = EmailFactory::getInstance()->buildEmail('noreply@openstack.org', MARKETPLACE_REVIEWS_EMAIL_TO, "New review submitted for ".$review->getCompanyService()->Name);
            $email->setTemplate('MarketPlaceReviewsEmail');
            $email->populateTemplate($review);
            $email->send();

            return $review;
        });
    }

    /**
     * @param $id
     * @return void
     */
    public function deleteReview($id){
        $review_repository = $this->review_repository;

        $this->tx_manager->transaction(function() use ($id, $review_repository){
            $review = $review_repository->getById($id);
            $review_repository->delete($review);

        });

    }

    /**
     * @param array $data
     * @return IMarketPlaceReview
     */
    public function addReview(array $data){

        $this_var           = $this;
        $repository         = $this->review_repository;
        $factory            = $this->review_factory;

        return  $this->tx_manager->transaction(function() use ($this_var,$factory, $data, $repository){
            $review = new MarketPlaceReview();
            $review->registerReviewMainInfo($factory->buildReviewMainInfo($data));
            $review->registerReviewProduct($factory->buildProduct($data));
            $review_id = $repository->add($review);

            $email = EmailFactory::getInstance()->buildEmail('noreply@openstack.org', MARKETPLACE_REVIEWS_EMAIL_TO, "New review submitted for ".$review->getCompanyService()->Name);
            $email->setTemplate('MarketPlaceReviewsEmail');
            $email->populateTemplate($review);
            $email->send();

            return $review_id;
        });
    }
} 