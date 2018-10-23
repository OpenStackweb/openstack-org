<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
require_once(Director::baseFolder().'/marketplace/code/utils/helpers.php');

//extensions

SS_Object::add_extension('Company', 'MarketPlaceCompany');
SS_Object::add_extension('Member', 'MarketPlaceAdminMember');
SS_Object::add_extension('Project', 'TrainingCourseRelatedProject');

//Admin UI
SS_Object::add_extension('MarketPlaceType', 'MarketPlaceTypeAdminUI');
SS_Object::add_extension('TrainingService', 'TrainingServiceAdminUI');
SS_Object::add_extension('TrainingCourse', 'TrainingCourseAdminUI');
SS_Object::add_extension('TrainingCourseSchedule', 'TrainingCourseScheduleAdminUI');
SS_Object::add_extension('GuestOSType', 'GuestOSTypeAdminUI');
SS_Object::add_extension('HyperVisorType', 'HyperVisorTypeAdminUI');
SS_Object::add_extension('PricingSchemaType', 'PricingSchemaTypeAdminUI');
SS_Object::add_extension('SpokenLanguage', 'SpokenLanguageAdminUI');
SS_Object::add_extension('Region', 'RegionAdminUI');
SS_Object::add_extension('ConfigurationManagementType', 'ConfigurationManagementTypeAdminUI');
SS_Object::add_extension('SupportChannelType', 'SupportChannelTypeAdminUI');
SS_Object::add_extension('MarketPlaceVideoType', 'MarketPlaceVideoTypeAdminUI');
SS_Object::add_extension('MarketPlaceAllowedInstance', 'MarketPlaceAllowedInstanceAdminUI');

define('EXPIRED_POWERED_OPENSTACK_IMPLEMENTATION_EMAIL','EXPIRED_POWERED_OPENSTACK_IMPLEMENTATION_EMAIL');