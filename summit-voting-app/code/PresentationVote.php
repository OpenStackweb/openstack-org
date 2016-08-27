<?php


class PresentationVote extends DataObject
{

    private static $db = array (
        'Vote' => 'Int',
        'Content' => 'Text'
    );


    private static $has_one = array (
        'Member' => 'Member',
        'Presentation' => 'Presentation'
    );
}