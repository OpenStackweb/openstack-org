<?php

/**
 * Displays a {@link SS_List} in a grid format.
 *
 * GridField is a field that takes an SS_List and displays it in an table with rows and columns.
 * It reminds of the old TableFields but works with SS_List types and only loads the necessary
 * rows from the list.
 *
 * The minimum configuration is to pass in name and title of the field and a SS_List.
 *
 * <code>
 * $gridField = new GridField('ExampleGrid', 'Example grid', new DataList('Page'));
 * </code>
 *
 * @see SS_List
 *
 * @package forms
 * @subpackage fields-gridfield
 */
class BetterGridField extends GridField {

	/**
	 * Returns the whole gridfield rendered with all the attached components.
	 *
	 * @param array $properties
	 *
	 * @return string
	 */
	public function FieldHolder($properties = array()) {
		$field = parent::FieldHolder($properties);

        //Requirements::block(FRAMEWORK_DIR . '/javascript/GridField.js');
        Requirements::javascript('openstack/code/utils/BetterGridField/BetterGridField.js');

        return $field;
    }


}
