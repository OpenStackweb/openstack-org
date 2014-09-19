<?php
/**
 * Class GeoCodingQuery
 */
final class GeoCodingQuery
	extends DataObject
	implements IGeoCodingQuery
{
	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Query'    => 'Text',
		'Lat'      => 'Decimal',
		'Lng'      => 'Decimal',
	);

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	public function setQuery($query)
	{
		$this->setField('Query',$query);
	}

	public function setCoordinates($lat, $lng)
	{
		$this->setField('Lat',$lat);
		$this->setField('Lng',$lng);
	}
}