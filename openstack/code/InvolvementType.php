<?php

class InvolvementType extends DataObject {

	static $db = array(
		'Name' => 'Text',
	);

	static $has_one = array(
	);

	static $belongs_many_many = array(
		'Orgs' => 'Org'
	);

	static $singular_name = 'Involvement Type';
	static $plural_name = 'Involvement Types';

}