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

class SurveyAnswerTag extends DataObject
{
    const TypeAutomatic = 'AUTOMATIC';
    const TypeCustom    = 'CUSTOM';
    const TypeRegex     = 'REGEX';

    static $db = [
        'Value' => 'Text',
        'Type' => "Enum('AUTOMATIC,CUSTOM,REGEX','AUTOMATIC')",
    ];

    static $has_one = [
        'CreatedBy' => 'Member',
    ];

    static $belongs_many_many = [
      'Answers' => 'SurveyAnswer',
    ];

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        // remove quotes to avoid js errors
        $this->Value = $this->trimTag($this->Value);
    }

    static public function trimTag($tag)
    {
        $tag = strtolower(trim($tag));
        $tag = str_replace('"', '', $tag);
        $tag = str_replace('\'', '', $tag);
        return $tag;
    }
}