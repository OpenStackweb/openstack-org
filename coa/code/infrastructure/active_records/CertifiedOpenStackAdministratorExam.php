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
 * Class CertifiedOpenStackAdministratorExam
 */
final class CertifiedOpenStackAdministratorExam
    extends DataObject
    implements ICertifiedOpenStackAdministratorExam
{
    private static $db = [

        'ExternalID'                   => 'Varchar(255)',
        'ExpirationDate'               => 'SS_Datetime',
        'PassFailDate'                 => 'SS_Datetime',
        'ModifiedDate'                 => 'SS_Datetime',
        'Status'                       => "Enum('None,New,Pending,Pass,No Pass,No Pending,Invalidated,Cancelled','None')",
        'Code'                         => 'Varchar(255)',
        'CertificationNumber'          => 'Varchar(255)',
        'CertificationStatus'          => "Enum('None,Achieved,InProgress,Expired,Renewed,In Appeals,Revoked','None')",
        'CertificationExpirationDate'  => "SS_Datetime",
        'TrackID'                      => 'Varchar(512)',
        'TrackModifiedDate'            => 'SS_Datetime',
        'CandidateName'                => 'Varchar(512)',
        'CandidateNameFirstName'       => 'Varchar(512)',
        'CandidateNameLastName'        => 'Varchar(512)',
        'CandidateEmail'               => 'Varchar(512)',
        'CandidateExternalID'          => 'Varchar(512)',
        'CompletedDate'                => "SS_Datetime",
    ];

    public static $valid_status                  = ['None','New','Pending','Pass','No Pass','No Pending','Invalidated', 'Cancelled'];
    public static $valid_certification_status    = ['Achieved','InProgress','Expired','Renewed','In Appeals','Revoked'];
    public static $approved_certification_status = ['Renewed', 'Achieved', 'InProgress'];

    private static $has_one = [
        'Owner' => 'Member',
    ];

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return intval($this->ID);
    }

    /**
     * @param string $track_id
     * @param string $track_id_modified_date
     * @param string $candidate_name
     * @param string $candidate_fname
     * @param string $candidate_lname
     * @param string $status
     * @param string $modified_date
     * @param string $exam_expiration_date
     * @param string $pass_date
     * @param string $code
     * @param string $cert_nbr
     * @param string $cert_expiration_date
     * @param string $cert_status
     * @param string $completed_date
     * @param string $email
     * @param string $external_id
     * @return $this
     * @throws EntityValidationException
     */
    public function setState
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
        $cert_expiration_date,
        $cert_status,
        $completed_date,
        $email,
        $external_id
    )
    {
        if(!empty($status) && $status != 'NULL') {
            if (!$this->isValidStatus($status))
                throw new EntityValidationException(sprintf("invalid status %s", $status));

            $this->Status = $status;
        }

        $this->ModifiedDate = $modified_date;

        if(!empty($code))
            $this->Code = $code;

        if(!empty($track_id))
            $this->TrackID = $track_id;

        if(!empty($track_id_modified_date))
            $this->TrackModifiedDate = $track_id_modified_date;

        if(!empty($candidate_name))
            $this->CandidateName = $candidate_name;

        if(!empty($candidate_fname))
            $this->CandidateNameFirstName = $candidate_fname ;

        if(!empty($candidate_lname))
            $this->CandidateNameLastName = $candidate_lname ;

        if(!empty($exam_expiration_date))
            $this->ExpirationDate = $exam_expiration_date;

        if(!empty($pass_date))
            $this->PassFailDate = $pass_date;

        if(!empty($cert_nbr) && $cert_nbr != 'NULL' )
            $this->CertificationNumber = $cert_nbr;

        if(!empty($cert_status))
            $this->CertificationStatus = $cert_status;

        if(!empty($cert_expiration_date))
            $this->CertificationExpirationDate = $cert_expiration_date;

        if(!empty($completed_date))
            $this->CompletedDate = $completed_date;

        if(!empty($email))
            $this->CandidateEmail = $email;

        if(!empty($external_id))
            $this->CandidateExternalID = $external_id;

        return $this;
    }

    /**
     * @param string $status
     * @return bool
     */
    public function isValidStatus($status){
        return in_array($status, self::$valid_status);
    }

    /**
     * @return COACertification
     */
    public function getCertification()
    {
        if(!in_array($this->CertificationStatus, self::$approved_certification_status)) return null;
        $expiration_date = empty($this->CertificationExpirationDate) ? null: new DateTime($this->CertificationExpirationDate);
        return new COACertification($this->Code,  $this->CertificationNumber, $this->CertificationStatus, $expiration_date );
    }

    /**
     * @return bool
     */
    public function hasOwner()
    {
        return intval($this->OwnerID) > 0;
    }
}