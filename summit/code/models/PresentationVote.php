<?php


class PresentationVote extends DataObject
{

    private static $db = array (
        'Vote' => 'Int',
        'Content' => 'HTMLText'
    );


    private static $has_one = array (
        'Member' => 'Member',
        'Presentation' => 'Presentation'
    );
}