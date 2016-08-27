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
 * Class EntityCounter
 */
final class EntityCounter extends Extension {
	/**
	 * @return mixed|string
	 */
	public function CountryCount(){
        $payload = function(){
            $Count = DB::query('select count(distinct(Member.Country)) from Member left join Group_Members on Member.ID = Group_Members.MemberID where Group_Members.GroupID = 5;')->value();
            return $Count;
        };
        return EntityCounterHelper::getInstance()->EntityCount("Country",$payload);
    }

    public function MembersCount(){

	    return EntityCounterHelper::getInstance()->EntityCount('Member',function (){
		    $query =  new IndividualMemberCountQuery();
		    $res   = $query->handle()->getResult();
		    return $res[0];
	    });
    }

    public function OrganizationCount(){
        $payload = function(){
            $sqlQuery = new SQLQuery(
                "COUNT(DISTINCT(ID))",
                "Company",
                "DisplayOnSite = TRUE"
            );
            return $sqlQuery->execute()->value();
        };
        return EntityCounterHelper::getInstance()->EntityCount("Company",$payload);
    }

}