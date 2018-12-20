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
 * Class TrainingCoursesTopicQueryHandler
 */
final class TrainingCoursesTopicQueryHandler implements IQueryHandler{

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(?IQuerySpecification $specification)
	{
		$params = $specification->getSpecificationParams();
		$term   = @$params['term'];
		$term   = Convert::raw2sql($term);
		$topics = array();
		$sql    = <<< SQL
        SELECT DISTINCT (Name) AS Value FROM TrainingCourse WHERE Name LIKE '%{$term}%'
        UNION
        SELECT DISTINCT (Name) AS Value FROM Project WHERE Name LIKE '%{$term}%'
        UNION
        SELECT DISTINCT (Type) AS Value FROM TrainingCourseType WHERE Type LIKE '%{$term}%'
        UNION
        SELECT DISTINCT (Name) AS Value FROM Company WHERE Name LIKE '%{$term}%'
        LIMIT 20;
SQL;

		$results = DB::query($sql);
		for ($i = 0; $i < $results->numRecords(); $i++) {
			$record = $results->nextRecord();
			array_push($topics, new SearchDTO($record['Value'],$record['Value']));
		}
		return new OpenStackImplementationNamesQueryResult($topics);
	}
}