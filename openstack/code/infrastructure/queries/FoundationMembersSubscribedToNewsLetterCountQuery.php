<?php
/**
 * Class FoundationMembersSubscribedToNewsLetterCountQuery
 */
final class FoundationMembersSubscribedToNewsLetterCountQuery implements IQueryHandler {

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification)
	{
		$res = 0;

		if($specification instanceof FoundationMembersSubscribedToNewsLetterCountQuerySpecification){

			$params    = $specification->getSpecificationParams();
			$filter    = '';

			if(!is_null($params[0])){
				$filter = " AND M.Country != '". $params[0]."' ";
			}

			$sql = <<< SQL
			SELECT COUNT(M.ID) From Member M
			LEFT  JOIN Group_Members GM ON GM.MemberID = M.ID
			INNER JOIN `Group` G on G.ID = GM.GroupID
			WHERE G.Code = 'foundation-members' AND M.SubscribedToNewsletter = 1 {$filter};
SQL;

			$res = (int)DB::query($sql)->value();

		}

		return new AbstractQueryResult(array($res));
	}
}