<?php
/**
 * Copyright 2016 OpenStack Foundation
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


final class MoveJobPage2JonMigration extends AbstractDBMigrationTask
{
    protected $title = "MoveJobPage2JonMigration";

    protected $description = "MoveJobPage2JonMigration";

    function doUp()
    {
        global $database;

        if(DBSchema::existsTable($database, "JobPage")){
            $res = DB::query("SELECT * FROM JobPage;");
            foreach ($res as $record) {

                $new_job                  = new Job();
                $new_job->ID              = (int)$record['ID'];
                $new_job->Title           = $record['Title'];
                $new_job->Description     = $record['Content'];
                $new_job->Created         = $record['Created'];
                $new_job->LastEdited      = $record['LastEdited'];
                $new_job->PostedDate      = $record['JobPostedDate'];
                $new_job->ExpirationDate  = $record['ExpirationDate'];
                // company name logic
                $company_name = $record['JobCompany'];;
                $company      = Company::get()->filter('Name', $company_name)->first();
                $new_job->CompanyID   = is_null($company)?  0 : $company->ID;
                if($new_job->CompanyID == 0)
                    $new_job->CompanyName = $company_name;

                $new_job->MoreInfoLink       = $record['JobMoreInfoLink'];
                $new_job->Location           = $record['JobLocation'];
                $new_job->IsFoundationJob    = $record['FoundationJob'];
                $new_job->IsActive           = $record['Active'];
                $new_job->Instructions2Apply = $record['JobInstructions2Apply'];
                $new_job->LocationType       = $record['LocationType'];
                $new_job->IsCOANeeded        = 0;
                $new_job->TypeID             = 0;
                //registration request
                $registration_request        = JobRegistrationRequest::get()->filter('Title', $new_job->Title)->first();
                if(!is_null($registration_request))
                    $new_job->RegistrationRequestID = $registration_request->ID;

                $new_job->write();
            }

            DBSchema::dropTable($database, "JobPage");
        }

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}

