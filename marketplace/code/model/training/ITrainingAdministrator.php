<?php

interface ITrainingAdministrator extends IEntity {
	/**
	 * @return ICompany[]
	 */
	public function getAdministeredTrainingCompanies();

	/**
	 * @return bool
	 */
	public function isTrainingAdmin();
} 