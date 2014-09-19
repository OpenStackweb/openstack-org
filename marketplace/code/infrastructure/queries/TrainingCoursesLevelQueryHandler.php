<?php
/**
 * Class TrainingCoursesLevelQueryHandler
 */
final class TrainingCoursesLevelQueryHandler implements IQueryHandler {

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification){

		$levels = array();
		$result = TrainingCourseLevel::get();
		if ($result) {
			foreach ($result->toArray() as $r) {
				array_push($levels, new SearchDTO( $r->Level, $r->Level));
			}
		}
		return new OpenStackImplementationNamesQueryResult($levels);
	}
}