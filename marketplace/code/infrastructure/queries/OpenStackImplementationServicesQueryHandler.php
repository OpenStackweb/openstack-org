<?php
/**
 * Class OpenStackImplementationServicesQueryHandler
 */
final class OpenStackImplementationServicesQueryHandler
implements IQueryHandler {

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification){

		$topics = array();

		$sql    = <<< SQL
        SELECT OpenStackComponent.Name, OpenStackComponent.CodeName FROM CompanyService
        INNER JOIN OpenStackImplementationApiCoverage  ON OpenStackImplementationApiCoverage.ImplementationID = CompanyService.ID
        INNER JOIN OpenStackReleaseSupportedApiVersion ON OpenStackReleaseSupportedApiVersion.ID = OpenStackImplementationApiCoverage.ReleaseSupportedApiVersionID
        INNER JOIN OpenStackComponent                  ON OpenStackComponent.ID = OpenStackReleaseSupportedApiVersion.OpenStackComponentID
        WHERE
        CompanyService.ClassName IN ('Appliance','Distribution') AND
        OpenStackImplementationApiCoverage.ClassName='OpenStackImplementationApiCoverage'
		GROUP BY OpenStackComponent.Name, OpenStackComponent.CodeName
        LIMIT 20;
SQL;
		$results = DB::query($sql);
		for ($i = 0; $i < $results->numRecords(); $i++) {
			$record = $results->nextRecord();
			$value = sprintf('%s - %s',$record['Name'],$record['CodeName']);
			array_push($topics, new SearchDTO($value, $value));
		}

		return new OpenStackImplementationNamesQueryResult($topics);
	}
}