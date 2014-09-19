<?php

/**
 * Class UserStoriesCountQuery
 */
final class UserStoriesCountQuery implements IQueryHandler {

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification)
	{
		$res = 0;
		if($specification instanceof UserStoriesCountQuerySpecification){
			$params  = $specification->getSpecificationParams();
			$filter = '';
			if($params[0]== true){
				$filter = ' AND FeaturedOnSite = 1 ';
			}
			$sql = <<< SQL
			SELECT COUNT(U.ID) FROM OpenstackUser U
			inner join Page P ON P.ID = U.ID
			inner join SiteTree ST on ST.ID = U.ID
			WHERE ListedOnSite = 1 AND Status in ('Published','Saved (update) ') {$filter}

SQL;
			$res = (int)DB::query($sql)->value();
		}
		return new AbstractQueryResult(array($res));
	}
}