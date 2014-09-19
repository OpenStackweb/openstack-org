<?php

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

	    return EntityCounterHelper::getInstance()->EntityCount('FoundationMember',function (){
		    $query =  new IndividualFoundationMemberCountQuery();
		    $res   = $query->handle(null)->getResult();
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