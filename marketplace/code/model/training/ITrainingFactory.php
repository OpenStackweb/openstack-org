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
interface ITrainingFactory extends ICompanyServiceFactory {

	/**
	 * @param string  $name
	 * @param string $description
	 * @param bool $active
	 * @param Company $company
	 * @return ITraining|TrainingService
	 */
	public function buildTraining($name ,$description, $active, Company $company);

	/**
	 * @param string $city
	 * @param string $state
	 * @param string $country
	 * @return ICourseLocation
	 */
	public function buildCourseLocation($city, $state, $country);

	/**
	 * @param string $start_date
	 * @param string $end_date
	 * @param string $link
	 * @return IScheduleTime
	 */
	public function buildCourseScheduleTime($start_date, $end_date, $link);

	/**
	 * @param int $project_id
	 * @return ICourseRelatedProject
	 */
	public function buildCourseRelatedProject($project_id);

	/**
	 * @return ICourse
	 */
	public function buildCourse();

} 