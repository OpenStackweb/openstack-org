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
 * Class MarketPlaceReview
 */
class MarketPlaceReview extends DataObject implements IMarketPlaceReview {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Title'     => 'Varchar',
        'Comment'   => 'Text',
        'Rating'    => 'Float',
        'Approved'  => 'Boolean'
	);

    static $has_one = array(
        'Member' => 'Member',
        'CompanyService' => 'CompanyService'
    );


	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->getField('Title');
	}

    /**
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->setField('Title',$title);
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->getField('Comment');
    }

    /**
     * @param string $comment
     * @return void
     */
    public function setComment($comment)
    {
        $this->setField('Comment',$comment);
    }

    /**
     * @return float
     */
    public function getRating()
    {
        return (float)$this->getField('Rating');
    }

    /**
     * @return int
     */
    public function getRatingAsWidth()
    {
        return (float)$this->getField('Rating')*20;
    }

    /**
     * @param string $rating
     * @return void
     */
    public function setRating($rating)
    {
        $this->setField('Rating',$rating);
    }

    /**
     * @return int
     */
    public function getApproved()
    {
        return (int)$this->getField('Approved');
    }

    /**
     * @param string $approved
     * @return void
     */
    public function setApproved($approved)
    {
        $this->setField('Approved',$approved);
    }

    /**
     * @return void
     */
    public function toggleApproved()
    {
        $this->Approved = !$this->Approved;
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Member')->getTarget();
    }

    /**
     * @param Member $member
     * @return void
     */
    public function setMember(Member $member){
        AssociationFactory::getInstance()->getMany2OneAssociation($this,'Member')->setTarget($member);
    }

    /**
     * @return ICompanyService
     */
    public function getCompanyService()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this,'CompanyService','Reviews')->getTarget();
    }

    /**
     * @param ICompanyService $companyService
     * @return void
     */
    public function setCompanyService(ICompanyService $companyService)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this,'CompanyService','Reviews')->setTarget($companyService);
    }

    /**
     * @param ReviewMainInfo $info
     * @return void
     */
    public function registerReviewMainInfo(ReviewMainInfo $info) {
        if ($this->Title != $info->getTitle() || $this->Comment != $info->getComment() || $this->Rating != $info->getRating()) {
            $this->Approved = 0;
        }

        $this->Title   = $info->getTitle();
        $this->Comment = $info->getComment();
        $this->Rating  = $info->getRating();

        $this->setMember(Member::CurrentUser());
    }

    /**
     * @param ReviewMainInfo $info
     * @return void
     */
    public function registerReviewProduct(ICompanyService $companyService) {
        $this->CompanyServiceID = $companyService->getIdentifier();
    }

}