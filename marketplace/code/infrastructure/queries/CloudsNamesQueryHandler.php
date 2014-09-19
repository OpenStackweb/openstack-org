<?php

/**
 * Class CloudsNamesQueryHandler
 */
abstract class CloudsNamesQueryHandler implements ICloudsNamesQueryHandler  {

	abstract function getClassName();
	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification){
		$params     = $specification->getSpecificationParams();
		$term       = @$params['name_pattern'];
		$term       = Convert::raw2sql($term);
		$topics     = array();
		$class_name = $this->getClassName();

		$sql1    = <<< SQL
        SELECT DISTINCT (CompanyService.Name) AS Value FROM CompanyService
        WHERE CompanyService.ClassName IN ('{$class_name}') AND
        ( CompanyService.Name LIKE '%{$term}%' OR CompanyService.Overview LIKE '%{$term}%' )
        LIMIT 20;
SQL;

		$results = DB::query($sql1);
		for ($i = 0; $i < $results->numRecords(); $i++) {
			$record = $results->nextRecord();
			array_push($topics, new SearchDTO($record['Value'],$record['Value']));
		}

		$sql2    = <<< SQL
        SELECT DISTINCT (Company.Name) AS Value FROM CompanyService
        INNER JOIN Company ON Company.ID = CompanyService.CompanyID
        WHERE CompanyService.ClassName IN ('{$class_name}') AND
        ( Company.Name LIKE '%{$term}%' )
        LIMIT 20;
SQL;

		$results = DB::query($sql2);
		for ($i = 0; $i < $results->numRecords(); $i++) {
			$record = $results->nextRecord();
			array_push($topics, new SearchDTO($record['Value'],$record['Value']));
		}
		return new OpenStackImplementationNamesQueryResult($topics);
	}
}