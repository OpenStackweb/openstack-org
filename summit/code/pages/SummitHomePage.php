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

    private static $allowed_children = array ('SummitOverviewPage', 'SummitFutureLanding', 'RedirectorPage');

    private static $default_child = "SummitOverviewPage";

}


class SummitHomePage_Controller extends SummitPage_Controller {


}