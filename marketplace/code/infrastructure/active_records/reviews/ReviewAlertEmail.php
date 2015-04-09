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
 * Class ReviewAlertEmail
 */
final class ReviewAlertEmail extends DataObject
	implements IReviewAlertEmail {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	private static $db = array();

	static $has_one = array(
		'LastReview' => 'MarketPlaceReview',
	);
	/**
	 * @return int
	 */
	public function getIdentifier(){
		return (int)$this->getField('ID');
	}
	/**
	 * @return IMarketPlaceReview
	 */
	public function getLastReview()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'LastReview')->getTarget();
	}

	/**
	 * @param IMarketPlaceReview $review
	 * @return void
	 */
	public function setLastReview(IMarketPlaceReview $review)
	{
		AssociationFactory::getInstance()->getMany2OneAssociation($this,'LastReview')->setTarget($review);
	}
}