<?php

/**
 * Class CompaniesNamesQueryHandler
 */
final class CompaniesNamesQueryHandler
	implements IQueryHandler {

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification){
		$params = $specification->getSpecificationParams();
		$term   = @$params['name_pattern'];
		$term   = Convert::raw2sql($term);
		$topics = array();

		$sql    = <<< SQL
        SELECT DISTINCT (Company.Name) AS Value FROM Company WHERE
        Company.Name LIKE '%{$term}%'
        LIMIT 20;
SQL;
		$results = DB::query($sql);
		for ($i = 0; $i < $results->numRecords(); $i++) {
			$record = $results->nextRecord();
			array_push($topics, new SearchDTO($record['Value'],$record['Value']));
		}

		return new OpenStackImplementationNamesQueryResult($topics);
	}
}