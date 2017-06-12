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
        $this->exam_repository = $exam_repository;
        $this->coa_file_api = $coa_file_api;
        $this->factory = $factory;
        $this->tx_manager = $tx_manager;
    }


    /**
     * @param string $content
     * @param string $filename
     * @param int $timestamp
     */
    private function processFile($content, $filename, $timestamp)
    {

        $rows = CSVReader::load($content);

        foreach ($rows as $row) {
            $exam_ext_id                = $row['candidate_exam_id'];
            $modified_date              = $row['candidate_exam_date_modified'];
            // this groups all candidate exams attempts together ...
            $track_id                   = $row['certification_track_status_id'];
            $track_id_modified_date     = $row['certification_track_status_date_modified'];
            $candidate_name             = $row['candidate_exam_name'];
            $candidate_fname            = $row['first_name'];
            $candidate_lname            = $row['last_name'];
            $external_id                = $row['open_stack_id'];
            $email                      = $row['open_stack_email'];
            $exam_expiration_date       = $row['exam_expiration_date'];
            $status                     = $row['exam_status'];
            $pass_date                  = $row['pass_fail_date_c'];
            $cert_nbr                   = $row['certification_number'];
            $code                       = $row['exam_code'];
            $cert_status                = $row['certification_status'];
            $cert_exam_expiration_date  = $row['certificate_expiration_date'];
            $certificate_completed_date = $row['certificate_completed_date'];

            $member = null;

            // first attempt, try to find member by id ...
            if(!empty($external_id) && intval($external_id) > 0 ){
                $member = $this->member_repository->getById($external_id);
            }

            // second attempt, try to find member by email ...
            if (is_null($member) && !empty($email)) {
                $member = $this->member_repository->findByEmail($email);
            }

            // third attempt, try to find member by former exams
            if (is_null($member)) {
                // possible a retake ? check by $track_id
                $former_exams = $this->exam_repository->getByTrackId($track_id);
                foreach ($former_exams as $former_exam) {
                    if ($former_exam->hasOwner()) {
                        $member = $former_exam->Owner();
                        break;
                    }
                }
            }
            // we werent able to find member ... skip it
            if (is_null($member) || !$member->exists()) {

                echo sprintf("missing member %s - track_id %s - filename %s", $email, $track_id, $filename) . PHP_EOL;
                continue;
            }

            // try to find if we have a former exam ...
            $exam = $member->getExamByExternalId($exam_ext_id);

            if (is_null($exam)) {
                $exam = $this->factory->build($member, $exam_ext_id);
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
                $cert_status,
                $certificate_completed_date,
                $email,
                $external_id
            );

            $exam->write();
        }
        $this->markFileAsProcessed($filename, $timestamp);
    }

    /**
     * @return int
     */
    public function processFiles()
    {
        return $this->tx_manager->transaction(function () {
            try {

                $files = $this->coa_file_api->getFilesList();
                $processed_files = 0;

                foreach ($files as $file_info) {
                    $filename  = $file_info['filename'];
                    $timestamp = intval($file_info['timestamp']);

                    if ($this->isFileProcessed($filename)) continue;

                    $content = $this->coa_file_api->getFileContent($filename);
                    $this->processFile($content, $filename, $timestamp);

                    $processed_files++;
                }

                return $processed_files;
            } catch (Exception $ex) {
                SS_Log::log($ex->getMessage(), SS_Log::WARN);
                throw $ex;
            }
        });
    }

    /**
     * @param string $filename
     * @return void
     */
    public function processExternalInitialDump($filename)
    {
        return $this->tx_manager->transaction(function () use ($filename) {
            try {

                $timestamp = time();
                if ($this->isFileProcessed($filename)) return;
                if (!file_exists($filename)) return;
                $content = file_get_contents($filename);

                // delete all former exams ...
                $this->exam_repository->deleteAll();

                $this->processFile($content, $filename, $timestamp);
            } catch (Exception $ex) {
                SS_Log::log($ex->getMessage(), SS_Log::WARN);
                throw $ex;
            }
        });
    }

    /**
     * @param string $filename
     * @return bool
     */
    private function isFileProcessed($filename)
    {
        return intval(COAProcessedFile::get()->filter('Name', trim($filename))->count()) > 0;
    }

    /**
     * @param string $filename
     * @param int $timestamp
     */
    private function markFileAsProcessed($filename, $timestamp)
    {
        $file            = COAProcessedFile::create();
        $file->Name      = trim($filename);
        $file->TimeStamp = intval($timestamp);
        $file->write();
    }
}