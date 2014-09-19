<?php
/**
 * Class IndividualFoundationMemberCountQuery
 */
final class IndividualFoundationMemberCountQuery implements IQueryHandler {

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification)
	{
		$sql = <<< SQL
		SELECT COUNT(M.ID) From Member M
		LEFT  JOIN Group_Members GM ON GM.MemberID = M.ID
		INNER JOIN `Group` G on G.ID = GM.GroupID
		WHERE G.Code = 'foundation-members';
SQL;

		$res = (int)DB::query($sql)->value();

		return new AbstractQueryResult(array($res));
	}
}