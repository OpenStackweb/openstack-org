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

    public function getCMSFields() {
        $f = new FieldList();

        $f->add(new ReadonlyField('SummitQuestionsPageID'));
        $f->add(new TextField('Order'));
        $f->add(new TextField('Name'));

        if ($this->ID > 0) {
            $config = GridFieldConfig_RecordEditor::create(10);
            $gridField = new GridField('Questions', 'Questions', $this->Questions(), $config);
            $f->add($gridField);
        }

        return $f;
    }

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