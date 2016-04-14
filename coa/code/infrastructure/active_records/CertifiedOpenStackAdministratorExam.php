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
final class CertifiedOpenStackAdministratorExam extends DataObject implements ICertifiedOpenStackAdministratorExam
{
    private static $db = array
    (
        'ExternalID'                   => 'Varchar(255)',
        'ExpirationDate'               => 'SS_Datetime',
        'PassFailDate'                 => 'SS_Datetime',
        'ModifiedDate'                 => 'SS_Datetime',
        'Status'                       => "Enum('New,Pending,Pass,No Pass','New')",
        'Code'                         => 'Varchar(255)',
        'CertificationNumber'          => 'Varchar(255)',
        'CertificationStatus'          => "Enum('None,Achieved,InProgress,Expired,Renewed,In Appeals,Revoked','None')",
        'CertificationExpirationDate'  => "SS_Datetime",
    );


    public static $valid_status = array('New','Pending','Pass','No Pass');
    public static $valid_certification_status = array('Achieved','InProgress','Expired','Renewed','In Appeals','Revoked');
    public static $approved_certification_status = array('Renewed', 'Achieved', 'InProgress');
    private static $has_one = array
    (
        'Owner' => 'Member',
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        // TODO: Implement getIdentifier() method.
    }

    /**
     * @param string $status
     * @param string $modified_date
     * @param string $exam_expiration_date
     * @param string $pass_date
     * @param string $code
     * @param string $cert_nbr
     * @param string $cert_expiration_date
     * @param string $cert_status
     * @return $this
     * @throws EntityValidationException
     */
    public function update($status, $modified_date, $exam_expiration_date, $pass_date,$code, $cert_nbr,$cert_expiration_date, $cert_status)
    {
        if(!$this->isValidStatus($status)) throw new EntityValidationException(sprintf("invalid status %s", $status));
        $this->Status              = $status;

        $this->ModifiedDate        = $modified_date;
        if(!empty($code))
            $this->Code = $code;

        if(!empty($expiration_date))
            $this->ExpirationDate = $expiration_date;

        if(!empty($pass_date))
            $this->PassFailDate = $pass_date;

        if(!empty($cert_nbr))
            $this->CertificationNumber = $cert_nbr;

        if(!empty($cert_status))
            $this->CertificationStatus = $cert_status;

        if(!empty($cert_expiration_date))
            $this->CertificationExpirationDate = $cert_expiration_date;

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
}