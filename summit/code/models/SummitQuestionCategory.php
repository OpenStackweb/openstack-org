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

    public function canEdit($member = null) {
        return Permission::check('ADMIN') || Permission::check('CMS_ACCESS_CMSMain');
    }

    public function canCreate($member = null) {
        return Permission::check('ADMIN') || Permission::check('CMS_ACCESS_CMSMain');
    }

    public function canDelete($member = null) {
        return Permission::check('ADMIN') || Permission::check('CMS_ACCESS_CMSMain');
    }

    public function canView($member = null) {
        return Permission::check('ADMIN') || Permission::check('CMS_ACCESS_CMSMain');
    }
}