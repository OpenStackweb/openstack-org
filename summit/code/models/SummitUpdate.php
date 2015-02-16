<?php


class SummitUpdate extends DataObject
{

    private static $db = array (
        'Title' => 'Text',
        'Category' => "Enum('News,Speakers,Sponsors,Attendees')",
        'Description' => 'HTMLText'
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

}