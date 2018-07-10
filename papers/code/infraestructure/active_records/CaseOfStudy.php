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
 * Class CaseOfStudy
 */
final class CaseOfStudy extends DataObject
{
    private static $db = [
        'Title' => 'Text',
        'Order' => 'Int',
    ];

    private static $has_one = [
        'Logo' => 'BetterImage',
        'Section' => 'CaseOfStudySection',
    ];

    static $has_many = [
        'Contents' => 'CaseOfStudyParagraph'
    ];

    public function getLogoUrl(){
        return Director::absoluteURL($this->Logo()->Url);
    }

    public function getSlug(){
        $slug = singleton('SiteTree')->generateURLSegment($this->Title);
        return $slug;
    }

    public function getOrderedContents(){
        return $this->Contents()->sort('Order','ASC');
    }
}