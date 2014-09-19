<?php

/**
 * Class JobLocation
 */
final class JobLocation
	extends DataObject
	implements IJobLocation {

	static $db = array(
		'City'          => 'Text',
		'State'         => 'Text',
		'Country'       => 'Text',
	);

	static $has_one = array(
		'Job'     => 'JobPage',
		'Request' => 'JobRegistrationRequest',
	);

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @return string
	 */
	public function state()
	{
		return (string)$this->getField('State');
	}

	/**
	 * @return string
	 */
	public function city()
	{
		return (string)$this->getField('City');
	}

	/**
	 * @return string
	 */
	public function country()
	{
		return (string)$this->getField('Country');
	}
}