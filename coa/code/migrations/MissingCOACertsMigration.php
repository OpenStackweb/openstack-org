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
 * Class MissingCOACertsMigration
 * @package coa\code\migrations
 */
final class MissingCOACertsMigration extends AbstractDBMigrationTask
{
    protected $title = "MissingCOACertsMigration";

    protected $description = "Missing COA Certs Migration";

    /**
     * @var IMemberRepository
     */
    private $member_repository;

    public function __construct()
    {
        parent::__construct();
        $this->member_repository= Injector::inst()->get('MemberRepository');
    }

    function doUp()
    {
        $ds       = new CvsDataSourceReader(",");
        $cur_path = Director::baseFolder();

        $ds->Open($cur_path . "/coa/code/migrations/data/austinsummit-allCOAusers.csv");
        $headers = $ds->getFieldsInfo();

        try {
            $exams = 0;
            while (($row = $ds->getNextRow()) !== FALSE) {
                $code = trim($row[$headers["exam_code"]]);
                if ($code != 'COA-EVENT') continue;

                    $email                     = trim($row[$headers['open_stack_id']]);
                    $exam_ext_id               = trim($row[$headers['candidate_exam_id']]);
                    $status                    = trim($row[$headers['exam_status']]);
                    $pass_date                 = trim($row[$headers['pass_fail_date_c']]);
                    $cert_nbr                  = trim($row[$headers['certification_number']]);
                    $modified_date             = trim($row[$headers['candidate_exam_date_modified']]);
                    $exam_expiration_date      = trim($row[$headers['exam_expiration_date']]);
                    $cert_exam_expiration_date = trim($row[$headers['certificate_expiration_date']]);
                    $cert_status               = trim($row[$headers['certification_status']]);

                    $member =  $this->member_repository->findByEmail($email);
                    if (is_null($member)) continue;
                    $exam = $member->getExamByExternalId($exam_ext_id);
                    if (is_null($exam))
                    {
                        //create exam
                        $exam             = CertifiedOpenStackAdministratorExam::create();
                        $exam->OwnerID    = $member->ID;
                        $exam->ExternalID = $exam_ext_id;
                    }
                    $exam->setState
                    (
                        $status,
                        $modified_date,
                        $exam_expiration_date,
                        $pass_date,
                        $code,
                        $cert_nbr,
                        $cert_exam_expiration_date,
                        $cert_status
                    );
                    $exam->write();
                    $exams++;

            }
        } catch (Exception $e) {
            $status = 0;
        }
        echo sprintf("created %s exams", $exams);
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}