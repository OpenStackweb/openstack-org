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
 * Class TrainingCoursesCompanyQueryHandler
 */
final class TrainingCoursesCompanyQueryHandler implements IQueryHandler {

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(?IQuerySpecification $specification){

        $params = $specification->getSpecificationParams();
        $current_date   = @$params['name_pattern'];
        $date_filter = "";
        if ($current_date) {
            $current_date   = Convert::raw2sql($current_date);
            $date_filter = "AND (
                                    (
                                        (DATE('{$current_date}') < TrainingCourseScheduleTime.EndDate)
                                        OR
                                        (TrainingCourse.Online=1 AND TrainingCourseScheduleTime.StartDate IS NULL AND TrainingCourseScheduleTime.EndDate IS NULL)
                                    )
                                )";

        }

        $sql       = <<< SQL
        SELECT C.Name AS CompanyName
        FROM TrainingCourse
        INNER JOIN CompanyService ON CompanyService.ID  = TrainingCourse.TrainingServiceID AND CompanyService.ClassName='TrainingService'
        INNER JOIN Company C on C.ID = CompanyService.CompanyID
        INNER JOIN TrainingCourseSchedule ON TrainingCourseSchedule.CourseID = TrainingCourse.ID
        LEFT JOIN TrainingCourseScheduleTime ON TrainingCourseScheduleTime.LocationID = TrainingCourseSchedule.ID
        WHERE CompanyService.Active = 1
        {$date_filter}
        GROUP BY C.Name
        ORDER BY C.Name ASC;
SQL;

        $results   = DB::query($sql);
        $companies = array();

        for ($i = 0; $i < $results->numRecords(); $i++) {
            $record = $results->nextRecord();
            $company    = $record['CompanyName'];

            $value   = sprintf('%s',$company);
            array_push($companies, new SearchDTO($value,$value));
        }
        return new OpenStackImplementationNamesQueryResult($companies);
	}
}