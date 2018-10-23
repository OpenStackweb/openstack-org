<?php
/**
 * Copyright 2015 OpenStack Foundation
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

define('MAX_ALLOWED_MATURITY_POINTS', 7);
define('OpsTagsTeamRepositoryUrl', 'https://raw.githubusercontent.com/openstack/ops-tags-team/master');


SS_Object::add_extension('OpenStackComponent', 'OpenStackComponentAdminUI');
SS_Object::add_extension('OpenStackComponentCategory', 'OpenStackComponentCategoryAdminUI');
SS_Object::add_extension('OpenStackComponentTag', 'OpenStackComponentTagAdminUI');
SS_Object::add_extension('OpenStackApiVersion', 'OpenStackApiVersionAdminUI');
SS_Object::add_extension('OpenStackRelease', 'OpenStackReleaseAdminUI');
SS_Object::add_extension('OpenStackReleaseSupportedApiVersion', 'OpenStackReleaseSupportedApiVersionAdminUI');
SS_Object::add_extension('OpenStackSampleConfig', 'OpenStackSampleConfigAdminUI');
SS_Object::add_extension('OpenStackSampleConfigurationType', 'OpenStackSampleConfigurationTypeAdminUI');