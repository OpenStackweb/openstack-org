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

/**
 * Class CertifiedOpenStackAdministrator
 */
final class CertifiedOpenStackAdministrator extends DataExtension implements ICertifiedOpenStackAdministrator
{
    private static $has_many = array
    (
        'Exams' => 'CertifiedOpenStackAdministratorExam',
    );

    /**
     * @return ICertifiedOpenStackAdministratorExam[]
     */
    public function getExams()
    {
        return $this->owner->Exams();
    }

    /**
     * @return ICertifiedOpenStackAdministratorExam|null
     */
    public function getLatestApprovedExam()
    {
        return $this->owner->Exams()->filter('Status', 'Pass')->first();
    }

    /**
     * @param string $external_id
     * @return ICertifiedOpenStackAdministratorExam|null
     */
    public function getExamByExternalId($external_id)
    {
        return $this->owner->Exams()->filter('ExternalID', $external_id)->first();
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->owner->ID;
    }
}