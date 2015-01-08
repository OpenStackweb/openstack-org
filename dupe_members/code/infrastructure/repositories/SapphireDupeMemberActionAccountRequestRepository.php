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
 * Class SapphireDupeMemberActionAccountRequestRepository
 */
class SapphireDupeMemberActionAccountRequestRepository
    extends SapphireRepository
    implements IDupeMemberActionAccountRequestRepository
{

    public function __construct(IEntity $entity)
    {
        parent::__construct($entity);
    }

    /**
     * @param string $token
     * @return bool
     */
    public function existsConfirmationToken($token)
    {
        $query = new QueryObject;
        $query->addAddCondition(QueryCriteria::equal('ConfirmationHash', DupeMemberActionRequest::HashConfirmationToken($token)));
        return  !is_null( $this->getBy($query));
    }

    /**
     * @param string $token
     * @return IDupeMemberActionAccountRequest
     */
    public function findByConfirmationToken($token)
    {
        $query = new QueryObject;
        $query->addAddCondition(QueryCriteria::equal('ConfirmationHash', DupeMemberActionRequest::HashConfirmationToken($token)));
        return $this->getBy($query);
    }

    /**
     * @param string $dupe_account_email
     * @return IDupeMemberActionAccountRequest
     */
    public function findByDupeAccount($dupe_account_email)
    {
        $query = new QueryObject(new $this->entity_class);
        $query->addAlias(QueryAlias::create('DupeAccount'));
        $query->addAddCondition(QueryCriteria::equal('Member.Email', $dupe_account_email));
        return $this->getBy($query);
    }
}