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
class SummitPresentationComment extends DataObject
{

    static $db = array
    (
        'Body' => 'Text',
        'IsCategoryChangeSuggestion' => 'Boolean',
    );

    static $has_one = array(
        'Presentation' => 'Presentation',
        'Commenter' => 'Member',
    );

    public function getDateNice()
    {
        $timestamp = strtotime($this->Created);
        $diff = time() - $timestamp;

        if ($diff < 60) {
            $date_nice = $diff . ' seconds ago';
        } else {
            if ($diff < (60 * 60)) {
                $int_diff = floor($diff / 60);
                $plural = ($int_diff > 1) ? 's' : '';
                $date_nice = $int_diff . ' minute' . $plural . ' ago';
            } else {
                if ($diff < (60 * 60 * 24)) {
                    $int_diff = floor($diff / (60 * 60));
                    $plural = ($int_diff > 1) ? 's' : '';
                    $date_nice = $int_diff . ' hour' . $plural . ' ago';
                } else {
                    $int_diff = floor($diff / (60 * 60 * 24));
                    $plural = ($int_diff > 1) ? 's' : '';
                    $date_nice = $int_diff . ' day' . $plural . ' ago';
                }
            }
        }

        return $date_nice;

    }
}