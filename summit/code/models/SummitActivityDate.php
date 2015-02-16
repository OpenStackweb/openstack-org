<?php

/*
* Used to list important dates / deadlines on the summit details page
*/


class SummitActivityDate extends DataObject
{

    private static $db = array (
        'Date' => 'Date',
        'Description' => 'HTMLText'
    );

    private static $has_one = array (
        'SummitUpdatesPage' => 'SummitUpdatesPage'
    );
    
    private static $summary_fields = array(
        'Date',
        'Description'
    );    

}