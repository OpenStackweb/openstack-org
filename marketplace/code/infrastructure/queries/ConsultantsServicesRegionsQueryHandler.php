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
 * Class ConsultantsServicesRegionsQueryHandler
 */
final class ConsultantsServicesRegionsQueryHandler implements IConsultantsServicesRegionsQueryHandler {

    /**
     * @param IQuerySpecification $specification
     * @return IQueryResult
     */
    public function handle(IQuerySpecification $specification){
        $params = $specification->getSpecificationParams();
        $topics = array();
        $sql    = <<< SQL
        SELECT Region.ID AS ID, Region.Name AS Value
        FROM CompanyService
        INNER JOIN Consultant_ServicesOffered ON Consultant_ServicesOffered.ConsultantID = CompanyService.ID
        INNER JOIN ConsultantServiceOfferedType ON ConsultantServiceOfferedType.ID = Consultant_ServicesOffered.ConsultantServiceOfferedTypeID
        INNER JOIN Region ON Region.ID = Consultant_ServicesOffered.RegionID
        WHERE
        CompanyService.ClassName IN ('Consultant')
		GROUP BY Region.ID

SQL;

        $results = DB::query($sql);
        for ($i = 0; $i < $results->numRecords(); $i++) {
            $record = $results->nextRecord();
            array_push($topics, new SearchDTO($record['Value'],$record['ID']));
        }
        return new OpenStackImplementationNamesQueryResult($topics);
    }
} 