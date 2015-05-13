<?php

/**
 * Class SummitUpdate
 */

class SummitUpdate extends DataObject
{

    private static $db = array (
        'Title'       => 'Text',
        'Category'    => "Enum('News,Speakers,Sponsors,Attendees')",
        'Description' => 'HTMLText',
        'Order'       => 'Int',
    );

    private static $has_one = array (
        'SummitUpdatesPage' => 'SummitUpdatesPage',
        'Image' => 'Image'
    );
    
    private static $summary_fields = array(
        'Created',
        'Title',
        'Category'
    );

    public function getCMSFields() {
        $fields = new FieldList();
        $fields->add(new TextField('Title','Title'));
        $fields->add(new HtmlEditorField('Description','Description'));
        $fields->add(new DropdownField('Category', 'Category', $this->dbObject('Category')->enumValues()));
        return $fields;
    }
}