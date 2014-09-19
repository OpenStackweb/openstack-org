<?php 

class AutocompleteMemberDecorator extends DataExtension
{
	private static $indexes =  array(
		"SearchFields" => "fulltext (FirstName,Surname)"
	);
}