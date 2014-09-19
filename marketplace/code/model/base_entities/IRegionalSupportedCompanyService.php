<?php
/**
 * Interface IRegionalSupportedCompanyService
 */
interface IRegionalSupportedCompanyService
	extends ICompanyService {

	/**
	 * @return IRegionalSupport[]
	 */
	public function getRegionalSupports();

	/**
	 * @param IRegionalSupport $regional_support
	 * @return void
	 */
	public function addRegionalSupport(IRegionalSupport $regional_support);

	public function clearRegionalSupports();
} 