<?php

/*
* Used to list important dates / deadlines on the summit details page
*/


class SummitQuestion extends DataObject
{

    private static $db = array (
        'Order' => 'Int',
        'Question' => 'Text',
        'Answer' => 'HTMLText',
        'ExtendedAnswer' => 'HTMLText'
    );

    private static $has_one = array (
        'SummitQuestionsPage' => 'SummitQuestionsPage',
        'Category' => 'SummitQuestionCategory'
    );
    
    private static $summary_fields = array(
        'Question',
        'Answer'
    ); 
    
    public function getCategoryName() {
        return $this->Category()->Name;
    }

    public function getCategorySlug($CategoryName) {
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $CategoryName);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
        return $clean;
    }   
        
}