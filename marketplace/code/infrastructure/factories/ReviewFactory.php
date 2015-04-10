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
 * Class ReviewFactory
 */
final class ReviewFactory implements IReviewFactory {

	/**
	 * @param array $data
	 * @return ReviewMainInfo
	 */
	public function buildReviewMainInfo(array $data){
		return new ReviewMainInfo(trim($data['title']), trim($data['comment']), $data['rating']);
	}

    /**
     * @param array $data
     * @return ICompanyService
     */
    public function buildProduct(array $data) {
        $company = CompanyService::get()->byId($data['company_service_ID']);
        return $company;
    }

	/**
	 * @param IMarketPlaceReview $review
	 * @return IReviewAlertEmail
	 */
	public function buildReviewAlertEmail(IMarketPlaceReview $review)
	{

	}
}