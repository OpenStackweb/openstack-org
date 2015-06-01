<?php


class SummitHomePage extends SummitPage {

    private static $db = array (
        'IntroText' => 'Varchar(255)'
    );


    private static $hide_ancestor = 'SummitPage';


    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->add(new TextField('IntroText', 'Intro Text'));
        return $fields;
    }

}


class SummitHomePage_Controller extends SummitPage_Controller {


}