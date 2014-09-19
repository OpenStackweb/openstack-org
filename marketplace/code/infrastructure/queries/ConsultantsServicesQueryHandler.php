<?php
/**
 * Class ConsultantsServicesQueryHandler
 */
final class ConsultantsServicesQueryHandler implements IQueryHandler {

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification){
		$params = $specification->getSpecificationParams();
		$topics = array();
		$sql    = <<< SQL
        SELECT ConsultantServiceOfferedType.Type AS Value FROM CompanyService
        INNER JOIN Consultant_ServicesOffered ON Consultant_ServicesOffered.ConsultantID = CompanyService.ID
        INNER JOIN ConsultantServiceOfferedType ON ConsultantServiceOfferedType.ID = Consultant_ServicesOffered.ConsultantServiceOfferedTypeID
        WHERE
        CompanyService.ClassName IN ('Consultant')
		GROUP BY ConsultantServiceOfferedType.Type

SQL;

		$results = DB::query($sql);
		for ($i = 0; $i < $results->numRecords(); $i++) {
			$record = $results->nextRecord();
			array_push($topics, new SearchDTO($record['Value'],$record['Value']));
		}
		return new OpenStackImplementationNamesQueryResult($topics);
	}
}