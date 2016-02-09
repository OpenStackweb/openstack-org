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
class SapphireMemberRepository extends SapphireRepository implements IMemberRepository {

    public function __construct($entity = null){
        if(is_null($entity)) {
            $entity = new FoundationMember();
            $entity->setOwner(new Member());
        }
        parent::__construct($entity);
    }

    /**
     * @param string $email
     * @return ICLAMember
     */
    public function findByEmail($email)
    {
        $member = Member::get()->filter('Email', $email )->first();
        if(is_null($member))
        {
            $member = Member::get()->filter('SecondEmail', $email )->first();
        }
        if(is_null($member))
        {
            $member = Member::get()->filter('ThirdEmail', $email )->first();
        }
        if(!is_null($member))
            UnitOfWork::getInstance()->scheduleForUpdate($member);
        return $member;
    }

    /**
     * @param string $first_name
     * @param string $last_name
     * @return ICommunityMember[]
     */
    public function getAllByName($first_name, $last_name)
    {
        $query = new QueryObject(new Member());
        $query->addAndCondition(QueryCriteria::equal('FirstName',$first_name));
        $query->addAndCondition(QueryCriteria::equal('Surname',$last_name));
        return $this->getAll($query,0,999999);
    }

    /**
     * @param string $email_verification_token
     * @return Member|null
     */
    public function getByEmailVerificationToken($email_verification_token)
    {
        $member = Member::get()
            ->filter('EmailVerifiedTokenHash', MemberDecorator::HashConfirmationToken($email_verification_token) )
            ->first();
        if(!is_null($member))
            UnitOfWork::getInstance()->scheduleForUpdate($member);
        return $member;
    }
}