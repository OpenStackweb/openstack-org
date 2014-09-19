<?php
/**
 * Interface ITraining
 */
interface ITraining extends ICompanyService  {

	const MarketPlaceType           = 'Training';
	const MarketPlaceGroupSlug      = 'marketplace-training-administrators';
	const MarketPlacePermissionSlug = 'MANAGE_MARKETPLACE_TRAINING';

	public function getDescription();
	public function setDescription($description);

	/**
	 * @return ICourse[]
	 */
	public function getAssociatedCourses();

	/**
	 * @param ICourse $course
	 * @return void
	 */
	public function addAssociatedCourse(ICourse $course);

	/**
	 * @return void
	 */
	public function clearAssociatedCourses();
} 