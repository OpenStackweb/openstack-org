<?php

/**
 * Copyright 2016 OpenStack Foundation
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
final class HTMLCleanner
{
    public static function cleanData(array $data, array $fields)
    {
        $config = HTMLPurifier_Config::createDefault();
        // Remove any CSS or inline styles
        $config->set('CSS.AllowedProperties', array());
        $purifier = new HTMLPurifier($config);
        foreach($fields as $field){
            if(!isset($data[$field])) continue;
            $data[$field] = $purifier->purify($data[$field]);
        }
        return $data;
    }
}