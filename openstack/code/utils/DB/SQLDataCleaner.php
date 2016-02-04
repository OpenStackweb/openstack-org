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
final class SQLDataCleaner
{
    /**
     * @param array $data
     * @param array $non_check_keys
     * @return array
     */
    public static function clean(array $data, array $non_check_keys = array())
    {
        if(self::isAssoc($data))
        {
            foreach($data as $k => $v)
            {
                if(in_array($k, $non_check_keys)) continue;
                $data[$k] = is_array($v) ? self::clean($v): Convert::raw2sql($v);
            }
        }
        return $data;
    }

    public static function isAssoc(array $arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}