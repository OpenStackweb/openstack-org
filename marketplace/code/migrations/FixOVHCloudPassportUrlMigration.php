<?php
/**
 * Copyright 2026 Open Infrastructure Foundation
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
final class FixOVHCloudPassportUrlMigration extends AbstractDBMigrationTask
{
    protected $title = "FixOVHCloudPassportUrlMigration";

    protected $description = "Update the obsolete OVHcloud Passport Learn More URL";

    public function doUp()
    {
        DB::query(<<<SQL
UPDATE PublicCloudPassport
SET LearnMore = 'https://www.ovhcloud.com/en/public-cloud/openstack/'
WHERE LearnMore = 'https://www.ovh.com/public-cloud/openstack-passport.xml'
SQL
        );
    }
}
