<?php
Object::add_extension('Member', 'MemberDecorator');
Object::add_extension('SecurityAdmin', 'SecurityAdminDecorator');
Object::add_extension('SiteConfig', 'CustomSiteConfigRegistration');

Director::addRules(100, array(
    'userprofile' => 'AffiliationController'
));