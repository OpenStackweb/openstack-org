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
 * Class ReviewCrudApi
 */
final class ReviewCrudApi extends AbstractRestfulJsonApi {

	const ApiPrefix = 'api/v1/marketplace/reviews';

    /**
     * @var array
     */
    static $url_handlers = array(
        'POST ' => 'addReview',
        'POST reject/$REVIEW_ID!' => 'deleteReview',
        'POST approve/$REVIEW_ID!' => 'approveReview'
    );

    /**
     * @var array
     */
    static $allowed_actions = array(
        'deleteReview',
        'addReview',
        'approveReview'
    );

    /**
     * @var ReviewManager
     */
    private $review_manager;

    /**
     * @var IReviewFactory
     */
    private $review_factory;

    /**
     * @var IEntityRepository
     */
    private $review_repository;

    /**
     * @var SecurityToken
     */
    private $securityToken;


    public function __construct() {
        parent::__construct();

        $this->securityToken     = new SecurityToken();
        $this->review_repository = new SapphireReviewRepository();
        $this->review_factory    = new ReviewFactory();

        $this->review_manager = new ReviewManager ($this->review_repository, new ReviewAlertEmail,
                                                   $this->review_factory, SapphireTransactionManager::getInstance());

        // filters ...
        $this_var           = $this;
        $current_user       = $this->current_user;
        $security_token     = $this->securityToken;


        $this->addBeforeFilter('addReview','check_access_reject',function ($request) use($this_var, $current_user, $security_token){
            $data = $this_var->getJsonRequest();
            if (!$data) return $this->serverError();
            if (!$security_token->checkRequest($request)) return $this->forbiddenError();
            if ($data['field_98438688'] != '') return $this->forbiddenError();
        });

    }

	protected function isApiCall(){
		$request = $this->getRequest();
		if(is_null($request)) return false;
		return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
	}

	/**
	 * @return bool
	 */
	protected function authorize(){
		//check permissions
		if(!$this->current_user)
			return false;
		return true;
	}

    /**
     * @return SS_HTTPResponse
     */
    public function addReview(){
        try {
            $data = $this->getJsonRequest();
            if (!$data) return $this->serverError();

            $review = $this->review_repository->getReview($this->review_factory->buildProduct($data)->getIdentifier(),Member::CurrentUserID());
            if ($review) {
                $this->review_manager->updateReview($data,$review);
                return $this->updated();
            } else {
                return $this->created($this->review_manager->addReview($data));
            }
        }
        catch (EntityAlreadyExistsException $ex1) {
            SS_Log::log($ex1,SS_Log::ERR);
            return $this->addingDuplicate($ex1->getMessage());
        }
        catch (PolicyException $ex2) {
            SS_Log::log($ex2,SS_Log::ERR);
            return $this->validationError($ex2->getMessage());
        }
        catch (EntityValidationException $ex3) {
            SS_Log::log($ex3,SS_Log::ERR);
            return $this->validationError($ex3->getMessages());
        }
    }

    /**
     * @return SS_HTTPResponse
     */
    public function deleteReview(){
        try {
            $review_id = intval($this->request->param('REVIEW_ID'));
            $this->review_manager->deleteReview($review_id);
            return $this->deleted();
        }
        catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1,SS_Log::ERR);
            return $this->notFound($ex1->getMessage());
        }
        catch (Exception $ex) {
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @return SS_HTTPResponse
     */
    public function approveReview(){
        try {
            $review_id = intval($this->request->param('REVIEW_ID'));
            $this->review_manager->toggleApproved($review_id);
            return $this->updated();
        }
        catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1,SS_Log::ERR);
            return $this->notFound($ex1->getMessage());
        }
        catch (Exception $ex) {
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }

}