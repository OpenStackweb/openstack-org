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


//sangria configuration
Object::add_extension('SangriaPage_Controller', 'SangriaPageDeploymentExtension');
Object::add_extension('SangriaPage_Controller', 'SangriaPageSurveyDetailsExtension');
Object::add_extension('SangriaPage_Controller', 'SangriaPageStandardizeOrgNamesExtension');
Object::add_extension('SangriaPage_Controller', 'SangriaPageViewCurrentStoriesExtension');
Object::add_extension('SangriaPage_Controller', 'SangriaPageExportDataExtension');
Object::add_extension('SangriaPage_Controller', 'SangriaPageViewSpeakingSubmissionsExtension');
Object::add_extension('SangriaPage_Controller', 'SangriaSetCategorySponsorsExtension');
Object::add_extension('SangriaPage_Controller', 'SangriaPageInvolvementTypeExtension');
Object::add_extension('SangriaPage_Controller', 'SangriaPageSurveyBuilderStatisticsExtension');