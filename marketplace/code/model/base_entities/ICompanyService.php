<?php

/**
 * Interface ICompanyService
 */
interface ICompanyService extends IManipulableEntity {
	/**
	 * @param ICompany $company
	 * @return void
	 */
	public function setCompany(ICompany $company);

	/**
	 * @return ICompany
	 */
	public function getCompany();

	/**
	 * @param IMarketPlaceType $marketplace
	 * @return void
	 */
	public function setMarketplace(IMarketPlaceType $marketplace);

	/**
	 * @return IMarketPlaceType
	 */
	public function getMarketplace();


	public function getName();
	public function setName($name);

	/**
	 * @return string
	 */
	public function getSlug();

	public function getOverview();
	public function setOverview($overview);

	public function getCall2ActionUri();
	public function setCall2ActionUri($call_2_action_uri);

	/**
	 * @param ICompanyServiceResource $resource
	 * @return void
	 */
	public function addResource(ICompanyServiceResource $resource);

	/**
	 * @return ICompanyServiceResource[]
	 */
	public function getResources();

	public function sortResources(array $new_sort);

	/**
	 * @param IMarketPlaceVideo $video
	 * @return void
	 */
	public function addVideo(IMarketPlaceVideo $video);

	/**
	 * @return IMarketPlaceVideo[]
	 */
	public function getVideos();

	public function clearVideos();
	public function clearResources();
}