<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class SapphireCOAExamRepository
 */
final class SapphireCOAExamRepository extends SapphireRepository implements ICertifiedOpenStackAdministratorExamRepository{

	public function __construct(){
		parent::__construct(new CertifiedOpenStackAdministratorExam());
	}

    /**
     * @param $cert
     * @param $last_name
     * @return DataList
     */
    public function getByCertAndLastName($cert, $last_name) {
        return CertifiedOpenStackAdministratorExam::get()
            ->leftJoin("Member","CertifiedOpenStackAdministratorExam.OwnerID = Member.ID")
            ->where("CertifiedOpenStackAdministratorExam.CertificationNumber = '{$cert}'
                    AND CertifiedOpenStackAdministratorExam.CandidateNameLastName= '{$last_name}'
                    AND CertifiedOpenStackAdministratorExam.CertificationExpirationDate > UTC_DATE()
                    AND CertifiedOpenStackAdministratorExam.CertificationStatus = 'Achieved' 
                    ")
            ->sort("CertifiedOpenStackAdministratorExam.LastEdited", "DESC");
    }

    /**
     * @param string $track_id
     * @return ICertifiedOpenStackAdministratorExam[]
     */
    public function getByTrackId($track_id)
    {
        return CertifiedOpenStackAdministratorExam::get()
            ->filter('TrackID', $track_id)
            ->sort('TrackModifiedDate', 'ASC')
            ->toArray();
    }
}