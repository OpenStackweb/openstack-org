<?php

/**
 * Class CompanyCountQuery
 */
final class CompanyCountQuery implements IQueryHandler {

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification)
	{
		$res = 0;
		if($specification instanceof CompanyCountQuerySpecification){
			$params  = $specification->getSpecificationParams();
			$filter = '';

			if(!is_null($params[0]))
				$filter .= " AND MemberLevel = '".$params[0]."' ";
			if(!is_null($params[1]))
				$filter .= " AND Country != 'NULL' and Country != '".$params[1]."' ";

			$sql = <<< SQL
			SELECT COUNT(C.ID) FROM Company C WHERE DisplayOnSite = 1 {$filter}

SQL;
			$res = (int)DB::query($sql)->value();
		}
		return new AbstractQueryResult(array($res));
	}
}