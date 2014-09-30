<?php

//routing
Director::addRules(100, array(
    'news' => 'NewsPage_Controller',
    'news-add' => 'NewsRequestPage_Controller',
    'news-edit' => 'NewsRequestPage_Controller',
    'news-manage' => 'NewsAdminPage_Controller',
));


Object::add_extension('Member', 'NewsManager');


define("NEWS_SUBMISSION_EMAIL_ALERT_TO", 'santipalenque@gmail.com');
define('NEWS_SUBMISSION_EMAIL_FROM','secretary@openstack.org');
define('NEWS_SUBMISSION_EMAIL_SUBJECT','New News item on Openstack.org');


