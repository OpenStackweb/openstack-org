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
    public function build(array $data):Member
    {
        return $this->populate(new Member, $data);
    }

    /**
     * @param Member $member
     * @param array $data
     * @return Member
     */
    public function populate(Member $member, array $data):Member{
        if(isset($data['Country']))
            $member->Country   = trim($data['Country']);

        $gender = $data['Gender'];
        if ($gender != 'Male' && $gender != 'Female' && $gender != 'Prefer not to say') {
            $member->Gender = $data['GenderSpecify'];
        }
        else
            $member->Gender = trim($gender);

        if(isset($data['StatementOfInterest']))
            $member->StatementOfInterest = trim($data['StatementOfInterest']);
        if(isset($data['Address']))
            $member->Address = trim($data['Address']);
        if(isset($data['Suburb']))
            $member->Suburb = trim($data['Suburb']);
        if(isset($data['City']))
            $member->City = trim($data['City']);
        if(isset($data['State']))
            $member->State = trim($data['State']);
        if(isset($data['Postcode']))
            $member->Postcode = trim($data['Postcode']);

        return $member;
    }
}