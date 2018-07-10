<?php
/**
 * Copyright 2018 OpenStack Foundation
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
 * Class IndexItem
 */
class IndexItem extends DataObject
{
    private static $db = [
        'Title' => 'Text',
        'Content' => 'HTMLText',
        'Order' => 'Int',
    ];

    private static $has_one = [
        'Section' => 'IndexSection',
    ];

    public function getSlug(){
        $slug = singleton('SiteTree')->generateURLSegment($this->Title);
        return $slug;
    }

    protected function validate()
    {
        $valid = parent::validate();
        if (!$valid->valid()) {
            return $valid;
        }

        $title = trim($this->Title);
        if (strlen($title) > GetTextTemplateHelpers::MAX_MSG_ID_LEN) {
            return $valid->error('Title is too long!');
        }

        $content = trim($this->Content);
        if (strlen($content) > GetTextTemplateHelpers::MAX_MSG_ID_LEN) {
            return $valid->error('Content is too long!');
        }

        return $valid;
    }
}