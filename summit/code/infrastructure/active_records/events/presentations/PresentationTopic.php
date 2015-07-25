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

    /**
     * @param Member $member
     * @return boolean
     */
    public function canView($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canEdit($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }
}