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


    public function __construct
    (
        IMemberRepository $member_repository,
        ICOAFileApi $coa_file_api,
        ITransactionManager $tx_manager
    )
    {
        $this->member_repository = $member_repository;
        $this->coa_file_api      = $coa_file_api;
        $this->tx_manager        = $tx_manager;
    }

    /**
     * @return int
     */
    public function processFiles()
    {
        $coa_file_api      = $this->coa_file_api;
        $member_repository = $this->member_repository;

        return $this->tx_manager->transaction(function() use($coa_file_api, $member_repository) {
            try {
                $files = $coa_file_api->getFilesList();
                $processed_files = 0;

                foreach ($files as $file_info) {
                    if ($this->isFileProcessed($file_info)) continue;

                    $content = $coa_file_api->getFileContent($file_info['filename']);
                    $rows = CSVReader::load($content);

                    foreach ($rows as $row) {
                        $email                     = $row['open_stack_id'];
                        $exam_ext_id               = $row['candidate_exam_id'];
                        $status                    = $row['exam_status'];
                        $pass_date                 = $row['pass_fail_date_c'];
                        $cert_nbr                  = $row['certification_number'];
                        $code                      = $row['exam_code'];
                        $modified_date             = $row['candidate_exam_date_modified'];
                        $exam_expiration_date      = $row['exam_expiration_date'];
                        $cert_exam_expiration_date = $row['certificate_expiration_date'];
                        $cert_status               = $row['certification_status'];

                        $member = $member_repository->findByEmail($email);
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