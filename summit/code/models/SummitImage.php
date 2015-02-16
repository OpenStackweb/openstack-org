<?php

/*
* Used for page top area backgrounds
*/


class SummitImage extends DataObject
{

    private static $db = array (
        'Title' => 'Text',
        'Attribution' => 'Text',
        'Description' => 'HTMLText',
        'OriginalURL' => 'Text'
    );

    private static $has_one = array (
        'Image' => 'Image'
    );
    
    private static $has_many = array (
		'SummitPages' => 'SummitPage'
	);    
    
            
}