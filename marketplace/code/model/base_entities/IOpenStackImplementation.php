<?php
/**
 * Interface IOpenStackImplementation
 */
interface IOpenStackImplementation extends IRegionalSupportedCompanyService {

	const AbstractMarketPlaceType = 'Implementation';
	/**
	 * @return IHyperVisorType[]
	 */
	public function getHyperVisors();

	/**
	 * @param IHyperVisorType $hypervisor
	 * @return void
	 */
	public function addHyperVisor(IHyperVisorType $hypervisor);

	/**
	 * @return IGuestOSType[]
	 */
	public function getGuests();

	/**
	 * @param IGuestOSType $guest
	 * @return void
	 */
	public function addGuest(IGuestOSType $guest);



	/**
	 * @return IOpenStackImplementationApiCoverage[]
	 */
	public function getCapabilities();

	/**
	 * @param IOpenStackImplementationApiCoverage $capability
	 * @return void
	 */
	public function addCapability(IOpenStackImplementationApiCoverage $capability);

	public function clearCapabilities();
	public function clearHypervisors();
	public function clearGuests();
}