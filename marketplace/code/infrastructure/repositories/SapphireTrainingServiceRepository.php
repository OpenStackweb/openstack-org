<?php

/**
 * Class SapphireTrainingServiceRepository
 */
class SapphireTrainingServiceRepository
	extends SapphireRepository
	implements ITrainingRepository {

	public function __construct(){
		parent::__construct(new TrainingService);
	}
	/**
	 * @param ITraining $training
	 * @param  $date
	 * @return ICourse[]
	 */
	public function getCoursesByDate(ITraining  $training , $date)
	{
		$res = $training->Courses(
		$filter = " ( DATE('{$date}') < TST.StartDate AND DATE('{$date}') < TST.EndDate) OR (Online=1 AND TST.StartDate IS NULL AND TST.EndDate IS NULL) ")
		->innerJoin('TrainingCourseLevel','L.ID = TrainingCourse.LevelID','L')
		->leftJoin('TrainingCourseSchedule','TS.CourseID = TrainingCourse.ID','TS')
		->leftJoin('TrainingCourseScheduleTime','TST.LocationID = TS.ID','TST')
		->sort(array(
			'Level.SortOrder'=>'ASC',
			'Schedules.Times.StartDate'=> 'ASC',
			'Schedules.Times.EndDate'=>'ASC'
			)
		);

		return $res;
	}


	/**
	 * @return int
	 */
	public function countActives()
	{
		return (int)DB::query("SELECT COUNT(*) FROM CompanyService WHERE ClassName = 'TrainingService' AND Active =1 ; ")->value();
	}

	/**
	 * @return ITraining[]
	 */
	public function getActivesRandom()
	{
		$ds = TrainingService::get()->sort('RAND()');
		return is_null($ds)?array():$ds->toArray();
	}

	/**
	 * @param string $list
	 * @return ITraining[]
	 */
	public function getActivesByList( $list)
	{
		$ds = TrainingService::get()->sort(" FIELD(ID, {$list}) ");
		return is_null($ds)?array():$ds->toArray();
	}

	/**
	 * @param int $company_id
	 * @return int
	 */
	public function countByCompany($company_id)
	{
		// TODO: Implement countByCompany() method.
	}
}