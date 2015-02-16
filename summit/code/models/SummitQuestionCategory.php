<?php

/*
* Used to list important dates / deadlines on the summit details page
*/


class SummitQuestionCategory extends DataObject
{

    private static $db = array (
        'Order' => 'Int',
        'Name' => 'Text'
    );

    private static $has_one = array (
        'SummitQuestionsPage' => 'SummitQuestionsPage'
    );
    
    private static $has_many = array (
        'Questions' => 'SummitQuestion'
    );
    
}