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
        'Answer',
        'Category.Name'
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('CategoryID');

        $pageID = isset($_REQUEST['PageID']) ? $_REQUEST['PageID'] : null;

        if ($pageID) {
            $dropdown = DropdownField::create(
                'CategoryID',
                'Choose a question category',
                SummitQuestionCategory::get()->filter('SummitQuestionsPageID', $pageID)->map("ID", "Name")
            );

            $fields->add($dropdown);

        }

        return $fields;
    }
    
    public function getCategoryName() {
        return $this->Category()->Name;
    }

    public function getCategorySlug($CategoryName) {
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $CategoryName);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
        return $clean;
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