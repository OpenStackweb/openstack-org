<?php
/**
 * Class Project
 */
class Project extends DataObject {
 
    private static $db = array(
        'Name'        => 'Varchar(255)',
        'Description' => 'HTMLText',
        'Codename'    => 'Text'
    );

    private static $belongs_many_many = array (
    	'OpenstackUser' => 'OpenstackUser',
    );
 
    public function getCMSFields() {
 
        $fields = new FieldList();
 
        $fields->push(new TextField('Name', 'Name of the project'));
        $fields->push(new TextField('Codename', 'CodeName'));
        $fields->push(new TextareaField('Description', 'Short description'));
 
        return $fields;
    }
}