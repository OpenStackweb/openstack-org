<?php

/**
 * Copyright 2015 OpenStack Foundation
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
final class MemberFactory implements IMemberFactory
{

    /**
     * @param array $data
     * @return Member
     */
    public function build(array $data)
    {
        $member = $this->buildReduced($data);
        $gender = $data['Gender'];
        if ($gender != 'Male' && $gender != 'Female' && $gender != 'Prefer not to say') {
            $member->Gender = $data['GenderSpecify'];
        }
        else
        $member->Gender = trim($gender);
        $member->StatementOfInterest = trim($data['StatementOfInterest']);
        $member->Address = trim($data['Address']);
        $member->Suburb = trim($data['Suburb']);
        $member->City = trim($data['City']);
        $member->State = trim($data['State']);
        $member->Postcode = trim($data['Postcode']);

        return $member;
    }

    /**
     * @param array $data
     * @return Member
     */
    public function buildReduced(array $data)
    {
        $member = Member::create();
        $member->FirstName = trim($data['FirstName']);
        $member->Surname = trim($data['Surname']);
        $member->Email = trim($data['Email']);
        $member->Country = trim($data['Country']);
        $member->Password = trim($data['Password']['_Password']);
        return $member;
    }
}