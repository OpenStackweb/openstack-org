<?php

/**
 * Class CloudsServicesQueryHandler
 */
abstract class CloudsServicesQueryHandler implements IQueryHandler {

	/**
	 * @return string
	 */
	abstract function getClassName();

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification){

		$class_name = $this->getClassName();
		$sql1    = <<< SQL
        SELECT OpenStackComponent.Name, OpenStackComponent.CodeName FROM CompanyService
        INNER JOIN OpenStackImplementationApiCoverage  ON OpenStackImplementationApiCoverage.ImplementationID = CompanyService.ID
        INNER JOIN OpenStackReleaseSupportedApiVersion ON OpenStackReleaseSupportedApiVersion.ID = OpenStackImplementationApiCoverage.ReleaseSupportedApiVersionID
        INNER JOIN OpenStackComponent                  ON OpenStackComponent.ID = OpenStackReleaseSupportedApiVersion.OpenStackComponentID
        WHERE
        CompanyService.ClassName IN ('{$class_name}') AND
        OpenStackImplementationApiCoverage.ClassName='CloudServiceOffered'
		GROUP BY OpenStackComponent.Name, OpenStackComponent.CodeName

SQL;

		$topics  = array();
		$results = DB::query($sql1);
		for ($i = 0; $i < $results->numRecords(); $i++) {
			$record = $results->nextRecord();
			$value  = sprintf("%s - %s",$record['Name'],$record['CodeName']);
			array_push($topics, new SearchDTO($value, $value));
		}

		return new OpenStackImplementationNamesQueryResult($topics);
	}
} 