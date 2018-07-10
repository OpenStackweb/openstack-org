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
 * Class PaperSection
 */
class PaperSection extends DataObject
{
    static $db = [
        'Title' => 'Text',
        'Subtitle' => 'Text',
        'Order' => 'Int',
    ];

    static $has_one = [
        'Paper' => 'Paper',
    ];

    static $has_many = [
        'Contents' => 'PaperParagraph'
    ];

    public function getSlug(){
        $slug = singleton('SiteTree')->generateURLSegment($this->Title);
        return $slug;
    }

    public function getOrderedContents(){
        return $this->Contents()->sort('Order','ASC');
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

        $subtitle = trim($this->Subtitle);
        if (strlen($subtitle) > GetTextTemplateHelpers::MAX_MSG_ID_LEN) {
            return $valid->error('Subtitle is too long!');
        }

        return $valid;
    }
}