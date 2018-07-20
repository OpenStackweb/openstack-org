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

class PaperParagraphListItem extends DataObject
{
    static $db = [
        'SubItemsContainerType' => "Enum('UL,OL,NONE','NONE')",
        'Content' => 'HTMLText',
        'Order' => 'Int',
    ];

    static $has_one = [
        'Owner' => 'PaperParagraphList',
        'Parent' => 'PaperParagraphListItem',
    ];

    public function setContent($value){
        $value = preg_replace ( "/<p[^>]*?>(.*)<\/p>/" , "$1" , $value );
        $this->setField('Content', $value);
    }

    private static $has_many = [
        'SubItems' => 'PaperParagraphListItem',
    ];

}