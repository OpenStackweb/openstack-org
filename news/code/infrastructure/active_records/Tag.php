<?php

/**
 * Class Tag
 */
final class Tag extends DataObject implements ITag {

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $db = array(
		'Tag'  => 'Varchar',
	);

    static $belongs_many_many = array(
        'News' => 'News'
    );

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}
}