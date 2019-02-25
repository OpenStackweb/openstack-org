<?php
/**
 * Copyright 2019 Openstack Foundation
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
 * Class CloudAssetTemplateHelpers
 */
final class CloudAssetTemplateHelpers implements TemplateGlobalProvider
{

    /**
     * Called by SSViewer to get a list of global variables to expose to the template, the static method to call on
     * this class to get the value for those variables, and the class to use for casting the returned value for use
     * in a template
     *
     * If the method to call is not included for a particular template variable, a method named the same as the
     * template variable will be called
     *
     * If the casting class is not specified for a particular template variable, ViewableData::$default_cast is used
     *
     * The first letter of the template variable is case-insensitive. However the method name is always case sensitive.
     *
     * @return array Returns an array of items. Each key => value pair is one of three forms:
     *  - template name (no key)
     *  - template name => method name
     *  - template name => array(), where the array can contain these key => value pairs
     *     - "method" => method name
     *     - "casting" => casting class to use (i.e., Varchar, HTMLText, etc)
     */
    public static function get_template_global_variables()
    {
        return [
            'CloudUrl' => [
                'method' => 'cloud_url',
                'casting' => 'Text',
            ],
        ];
    }

    public static function cloud_url($filename)
    {
        $maps = Config::inst()->get('CloudAssets', 'map');
        foreach ($maps as $path => $cfg) {
            if (strpos($filename, $path) === 0) {
                // remove base path
                $filename = str_replace($path.'/', $cfg["BaseURL"], $filename);
            }
        }
        return $filename;
    }
}