<?php

//routing
Director::addRules(100, array(
    'news' => 'NewsPage_Controller',
    'news-add' => 'NewsRequestPage_Controller',
    'news-edit' => 'NewsRequestPage_Controller',
    'news-manage' => 'NewsAdminPage_Controller',
));


