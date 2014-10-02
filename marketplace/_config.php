<?php

require_once(Director::baseFolder().'/marketplace/code/utils/helpers.php');

//extensions
Object::add_extension('Group', 'SecurityGroupDecorator');
Object::add_extension('Company', 'MarketPlaceCompany');
Object::add_extension('Member', 'MarketPlaceAdminMember');
Object::add_extension('Project', 'TrainingCourseRelatedProject');
//Admin UI
Object::add_extension('MarketPlaceType', 'MarketPlaceTypeAdminUI');
Object::add_extension('TrainingService', 'TrainingServiceAdminUI');
Object::add_extension('TrainingCourse', 'TrainingCourseAdminUI');
Object::add_extension('TrainingCourseSchedule', 'TrainingCourseScheduleAdminUI');
Object::add_extension('GuestOSType', 'GuestOSTypeAdminUI');
Object::add_extension('HyperVisorType', 'HyperVisorTypeAdminUI');
Object::add_extension('PricingSchemaType', 'PricingSchemaTypeAdminUI');
Object::add_extension('SpokenLanguage', 'SpokenLanguageAdminUI');
Object::add_extension('Region', 'RegionAdminUI');
Object::add_extension('ConfigurationManagementType', 'ConfigurationManagementTypeAdminUI');
Object::add_extension('SupportChannelType', 'SupportChannelTypeAdminUI');
Object::add_extension('MarketPlaceVideoType', 'MarketPlaceVideoTypeAdminUI');
Object::add_extension('OpenStackComponent', 'OpenStackComponentAdminUI');
Object::add_extension('OpenStackApiVersion', 'OpenStackApiVersionAdminUI');
Object::add_extension('OpenStackRelease', 'OpenStackReleaseAdminUI');
Object::add_extension('OpenStackReleaseSupportedApiVersion', 'OpenStackReleaseSupportedApiVersionAdminUI');
Object::add_extension('MarketPlaceAllowedInstance', 'MarketPlaceAllowedInstanceAdminUI');

define('GOOGLE_GEO_CODING_API_KEY','AIzaSyBsGLoRoLAerygzBQ4KytbbA3tjZVb4Hws');