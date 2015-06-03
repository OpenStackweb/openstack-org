<?php

class PresentationTopic extends DataObject
{

    private static $db = array (
        'Title' => 'Varchar'
    );


    private static $belongs_many_many = array (
        'Presentations' => 'Presentation'
    );


    public function getCMSFields() {
        return FieldList::create(TabSet::create("Root"))
            ->text('Title');
    }


    /**
     * Gets a readable label for this topic
     * 
     * @return  string
     */
    public function getLabel() {
        return $this->Title;
    }
}