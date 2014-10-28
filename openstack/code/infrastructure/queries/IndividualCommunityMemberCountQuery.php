<?php

/**
 * Class IndividualCommunityMemberCountQuery
 */
final class IndividualCommunityMemberCountQuery implements IQueryHandler {

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification)
	{
		$code = IFoundationMember::CommunityMemberGroupSlug;
		$sql = <<< SQL
		SELECT COUNT(M.ID) From Member M
		LEFT  JOIN Group_Members GM ON GM.MemberID = M.ID
		INNER JOIN `Group` G on G.ID = GM.GroupID
		WHERE G.Code = '{$code}';
SQL;

		$res = (int)DB::query($sql)->value();

		return new AbstractQueryResult(array($res));
	}
}