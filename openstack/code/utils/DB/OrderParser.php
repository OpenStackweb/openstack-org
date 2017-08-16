<?php
/**
 * Copyright 2017 OpenStack Foundation
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

final class OrderParser
{
    /**
     * @param string $orders
     * @param array $allowed_fields
     * @return Order
     */
    public static function parse($orders, array $allowed_fields = [])
    {
        $res    = [];
        $orders = explode(',', $orders);
        //default ordering is asc
        foreach($orders as $field)
        {
            $element = null;
            if(strpos($field, '+') === 0)
            {
                $field = trim($field,'+');
                if(!in_array($field, $allowed_fields)) continue;
                $element = OrderElement::buildAscFor($field);
            }
            else if(strpos($field, '-') === 0)
            {
                $field = trim($field,'-');
                if(!in_array($field, $allowed_fields)) continue;
                $element = OrderElement::buildDescFor($field);
            }
            else
            {
                if(!in_array($field, $allowed_fields)) continue;
                $element = OrderElement::buildAscFor($field);
            }
            $res[] = $element;
        }
        return new Order($res);
    }
}