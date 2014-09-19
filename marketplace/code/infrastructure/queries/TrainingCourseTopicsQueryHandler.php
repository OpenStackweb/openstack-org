<?php
/**
 * Class TrainingCoursesTopicQueryHandler
 */
final class TrainingCoursesTopicQueryHandler implements IQueryHandler{

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification)
	{
		$params = $specification->getSpecificationParams();
		$term   = @$params['term'];
		$term   = Convert::raw2sql($term);
		$topics = array();
		$sql    = <<< SQL
        SELECT DISTINCT (Name) AS Value FROM TrainingCourse WHERE Name LIKE '%{$term}%'
        UNION
        SELECT DISTINCT (Name) AS Value FROM Project WHERE Name LIKE '%{$term}%'
        UNION
        SELECT DISTINCT (Type) AS Value FROM TrainingCourseType WHERE Type LIKE '%{$term}%'
        UNION
        SELECT DISTINCT (Name) AS Value FROM Company WHERE Name LIKE '%{$term}%'
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