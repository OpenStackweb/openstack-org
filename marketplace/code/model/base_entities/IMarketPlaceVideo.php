<?php
interface IMarketPlaceVideo extends IEntity {
	/**
	 * @param IMarketPlaceVideoType $type
	 * @return void
	 */
	public function setType(IMarketPlaceVideoType $type);

	/**
	 * @return IMarketPlaceVideoType
	 */
	public function getType();

	public function getName();
	public function setName($name);

	public function getDescription();
	public function setDescription($description);

	public function getLength();
	public function getFormattedLength();
	public function setLength($length);

	public function setYouTubeId($you_tube_id);
	public function getYouTubeId();

	/**
	 * @return ICompanyService
	 */
	public function getOwner();

	/**
	 * @param ICompanyService $owner
	 * @return void
	 */
	public function setOwner(ICompanyService $owner);
} 