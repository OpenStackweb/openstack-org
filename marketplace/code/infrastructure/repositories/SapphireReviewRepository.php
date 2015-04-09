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
 * Class SapphireReviewRepository
 */
final class SapphireReviewRepository extends SapphireRepository {

	public function __construct(){
		parent::__construct(new MarketPlaceReview);
	}

    public function add(IEntity $entity) {
        parent::add($entity);
    }

	public function delete(IEntity $entity){
		parent::delete($entity);
	}

    /**
     * @param int $productID
     * @param int $userID
     * @return array
     */
    public function getReview($productID, $userID)	{
        $query = new QueryObject(new MarketPlaceReview);
        $query->addAddCondition(QueryCriteria::equal('MemberID',$userID));
        $query->addAddCondition(QueryCriteria::equal('CompanyServiceID',$productID));
        return  $this->getBy($query);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getAllApprovedByProduct($productID, $offset = 0, $limit = 100)	{
        $query = new QueryObject(new MarketPlaceReview);
        $query->addAddCondition(QueryCriteria::equal('Approved',1));
        $query->addAddCondition(QueryCriteria::equal('CompanyServiceID',$productID));
        $query->addOrder(QueryOrder::desc('Created'));
        return  $this->getAll($query,$offset,$limit);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getAllNotApproved($offset = 0, $limit = 100)	{
        $query = new QueryObject(new MarketPlaceReview);
        $query->addAddCondition(QueryCriteria::equal('Approved',0));
        $query->addOrder(QueryOrder::desc('Created'));
        return  $this->getAll($query,$offset,$limit);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getAllApproved($offset = 0, $limit = 100)	{
        $query = new QueryObject(new MarketPlaceReview);
        $query->addAddCondition(QueryCriteria::equal('Approved',1));
        $query->addOrder(QueryOrder::desc('Created'));
        return  $this->getAll($query,$offset,$limit);
    }

} 