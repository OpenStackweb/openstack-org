<?php

//routing
Director::addRules(100, array(
    'news' => 'NewsPage_Controller',
    'news-add' => 'NewsRegistrationRequestPage_Controller',
    'news-edit' => 'NewsRegistrationRequestPage_Controller',
    'news-manage' => 'NewsAdminPage_Controller',
));


