<?php
/**
 * Copyright 2020 Open Infrastructure Foundation
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

/**
 * Class SangriaAUCExtension
 */
final class SangriaAUCExtension extends Extension
{
    static $allowed_actions =  [
      'AUCFixMissMatchConflicts',
      'AUCMetricsList',
    ];

    public function onBeforeInit()
    {
        Config::inst()->update(get_class($this), 'allowed_actions', self::$allowed_actions);
        Config::inst()->update(get_class($this->owner), 'allowed_actions', self::$allowed_actions);
    }

    public function AUCFixMissMatchConflicts(SS_HTTPRequest $request)
    {
        Requirements::css("sangria/ui/source/css/sangria.css");
        return $this->owner->Customise
        (
            ['OpenStackResourceApiBaseUrl' => OPENSTACK_RESOURCE_API_BASE_URL]
        )->renderWith(array('SangriaPage_AUCFixMissMatchConflicts', 'SangriaPage', 'SangriaPage'));
    }

    public function AUCMetricsList(SS_HTTPRequest $request){

        Requirements::css("sangria/ui/source/css/sangria.css");
        Requirements::css('node_modules/jquery-datetimepicker/build/jquery.datetimepicker.min.css');

        Requirements::javascript('node_modules/php-date-formatter/js/php-date-formatter.min.js');
        Requirements::javascript('node_modules/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js');

        return $this->owner->Customise
        (
            []
        )->renderWith(array('SangriaPage_AUCMetricsList', 'SangriaPage', 'SangriaPage'));
    }
}