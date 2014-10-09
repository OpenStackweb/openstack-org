<?php

class Org extends DataObject {

	private static $create_table_options = array('MySQLDatabase' => 'ENGINE=MyISAM');

	static $db = array(
		'Name' => 'Text',
		'IsStandardizedOrg' => 'Boolean',
		'FoundationSupportLevel' => "Enum('Platinum Member, Gold Member, Corporate Sponsor, Startup Sponsor, Supporting Organization')",
	);

	static $has_one = array(
		'OrgProfile' => 'Company'
	);

	static $has_many = array(
		'Members' => 'Member'
	);

	static $many_many = array(
		'InvolvementTypes' => 'InvolvementType'
	);

	static $singular_name = 'Org';
	static $plural_name = 'Orgs';

}