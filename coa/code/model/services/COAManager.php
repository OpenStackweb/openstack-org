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
 * Class COAManager
 */
final class COAManager implements ICOAManager
{

    /**
     * @var IMemberRepository
     */
    private $member_repository;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var ICOAFileApi
     */
    private $coa_file_api;

    /**
     * @var ICertifiedOpenStackAdministratorExamRepository
     */
    private $exam_repository;

    /**
     * @var ICertifiedOpenStackAdministratorExamFactory
     */
    private $factory;

    public function __construct
    (
        IMemberRepository $member_repository,
        ICertifiedOpenStackAdministratorExamRepository $exam_repository,
        ICertifiedOpenStackAdministratorExamFactory $factory,
        ICOAFileApi $coa_file_api,
        ITransactionManager $tx_manager
    )
    {
        $this->member_repository = $member_repository;
        $this->exam_repository   = $exam_repository;
        $this->coa_file_api      = $coa_file_api;
        $this->factory           = $factory;
        $this->tx_manager        = $tx_manager;
    }

    /**
     * @return int
     */
    public function processFiles()
    {
        $coa_file_api      = $this->coa_file_api;
        $member_repository = $this->member_repository;
        $exam_repository   = $this->exam_repository;
        $factory           = $this->factory;

        return $this->tx_manager->transaction(function() use($coa_file_api, $member_repository, $exam_repository, $factory) {
            try {

                $files           = $coa_file_api->getFilesList();
                $processed_files = 0;

                foreach ($files as $file_info) {
                    if ($this->isFileProcessed($file_info)) continue;

                    $content = $coa_file_api->getFileContent($file_info['filename']);
                    $rows = CSVReader::load($content);

                    foreach ($rows as $row) {
                        $exam_ext_id               = $row['candidate_exam_id'];
                        $modified_date             = $row['candidate_exam_date_modified'];
                        // this groups all candidate exams attempts together ...
                        $track_id                  = $row['certification_track_status_id'];
                        $track_id_modified_date    = $row['certification_track_status_date_modified'];
                        $candidate_name            = $row['candidate_exam_name'];
                        $candidate_fname           = $row['first_name'];
                        $candidate_lname           = $row['last_name'];
                        $email                     = $row['open_stack_id'];
                        $exam_expiration_date      = $row['exam_expiration_date'];
                        $status                    = $row['exam_status'];
                        $pass_date                 = $row['pass_fail_date_c'];
                        $cert_nbr                  = $row['certification_number'];
                        $code                      = $row['exam_code'];
                        $cert_status               = $row['certification_status'];
                        $cert_exam_expiration_date = $row['certificate_expiration_date'];

                        $member = null;
                        // first attempt, try to find member by email ...
                        if(!empty($email)){
                            $member = $member_repository->findByEmail($email);
                        }

                        // second attempt, try to find member by former exams
                        if(is_null($member)){
                            // possible a retake ? check by $track_id
                            $former_exams = $exam_repository->getByTrackId($track_id);
                            foreach($former_exams as $former_exam){
                                if($former_exam->hasOwner()) {
                                    $member = $former_exam->Owner();
                                    break;
                                }
                            }
                        }
                        // we werent able to find member ... skip it
                        if (is_null($member)) continue;
                        // try to find if we have a former exam ...
                        $exam = $member->getExamByExternalId($exam_ext_id);

                        if (is_null($exam))
                        {
                            $exam = $factory->build($member, $exam_ext_id);
                        }

                        $exam->setState
                        (
                            $track_id,
                            $track_id_modified_date,
                            $candidate_name,
                            $candidate_fname,
                            $candidate_lname,
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
                    }
                    $this->markFileAsProcessed($file_info);
                    $processed_files++;
                }

                return $processed_files;
            }
            catch(Exception $ex)
            {
                SS_Log::log($ex->getMessage(), SS_Log::WARN);
                throw $ex;
            }
        });

    }

    private function isFileProcessed(array $file_info)
    {
        return intval(COAProcessedFile::get()->filter('Name', trim($file_info['filename']))->count()) > 0;
    }

    private function markFileAsProcessed(array $file_info)
    {
        $file            = COAProcessedFile::create();
        $file->Name      = trim($file_info['filename']);
        $file->TimeStamp = intval($file_info['timestamp']);
        $file->write();
    }
}