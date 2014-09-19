<?php
/**
 * Interface ITrainingRepository
 */
interface ITrainingRepository extends ICompanyServiceRepository {
	/**
	 * @param  ITraining $training
	 * @param  string $date
	 * @return ICourse[]
	 */
	public function getCoursesByDate(ITraining $training , $date);
}