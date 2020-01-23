<?php
/**
 * Copyright 2020 Openstack Foundation
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
 * Class JobCompanyMigrationTask
 */
final class JobCompanyMigrationTask extends AbstractDBMigrationTask {

    protected $title = "JobCompanyMigrationTask";

    protected $description = "populate company name for jobs with company id";

    function doUp(){

        $jobs = Job::get()->filter('CompanyName', '-- Select Your Company --');

        foreach ($jobs as $job) {
            if ($job->CompanyID || $job->RegistrationRequest()->CompanyID) {
                $companyId = $job->CompanyID || $job->RegistrationRequest()->CompanyID;
                $company = Company::get()->byID($companyId);
                $job->CompanyName = $company->Name;
                $job->write();
                echo 'setting company '.$company->Name.' for job id '.$job->ID.PHP_EOL;
            }
        }

    }

    function doDown()	{

    }
}