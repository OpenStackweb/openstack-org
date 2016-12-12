<?php

/* 
   Static categories page for Austin
*/

class SummitCategoriesPage extends SummitPage {

    private static $db = [
        'HeaderTitle' => 'Text',
        'HeaderText'  => 'HTMLText',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('GoogleConversionTracking');
        $fields->removeByName('FacebookConversionTracking');
        $fields->removeByName('TwitterConversionTracking');
        $fields->removeByName('SummitPageImages');
        $fields->removeFieldFromTab('Root.Main','Content');
        $fields->removeFieldFromTab('Root.Main','SummitImageID');
        $fields->removeFieldFromTab('Root.Main','SummitID');
        $fields->removeFieldFromTab('Root.Main','HeroCSSClass');

        $fields->addFieldsToTab('Root.Main', new LiteralField('breakline', '<br>'));
        $fields->addFieldsToTab('Root.Main', new TextField('HeaderTitle', 'H1 Title'));
        $fields->addFieldsToTab('Root.Main', $header_text = new HtmlEditorField('HeaderText', 'Header Text'));
        $header_text->setRows(4);

        return $fields;
    }
}


class SummitCategoriesPage_Controller extends SummitPage_Controller {

  public function init() {
        parent::init();
        Requirements::css("themes/openstack/static/css/combined.css");
  }

}
