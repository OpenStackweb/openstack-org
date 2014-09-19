<?php 

class AutocompleteOrgDecorator extends DataExtension {

	private static $indexes=  array(
	"SearchFields" => "fulltext (Name)"
	);

}