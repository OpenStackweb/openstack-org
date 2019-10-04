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
 * Interface ICompanyServiceRepository
 */
interface ICompanyServiceRepository extends IEntityRepository {
	/**
	 * @param int $company_id
	 * @return int
	 */
	public function countByCompany($company_id);

	/**
	 * @param string $list
	 * @return ICompanyService[]
	 */
	public function getActivesByList($list);

	/**
	 * @return ICompanyService[]
	 */
	public function getActivesRandom();

	/**
	 * @return int
	 */
	public function countActives();

    /**
     * @param string $slug
     * @param string $companySlug
     * @return ICompanyService
     */
    public function getBySlugAndCompanySlug(string $slug, string $companySlug);
}