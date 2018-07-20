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

class PaperParagraph extends DataObject
{
    private static $db = [
        'Type'    => "Enum('P,LIST,IMG,H5,H4','P')",
        'Content' => 'HTMLText',
        'Order'   => 'Int',
    ];

    private static $has_one = [
        'Section' => 'PaperSection',
    ];

    public function setContent($value){
        $value = preg_replace ( "/<p[^>]*?>(.*)<\/p>/" , "$1" , $value );
        $this->setField('Content', $value);
    }

    protected function validate()
    {
        $valid = parent::validate();
        if (!$valid->valid()) {
            return $valid;
        }

        $content = trim($this->Content);
        if (strlen($content) > GetTextTemplateHelpers::MAX_MSG_ID_LEN) {
            return $valid->error('Content is too long!');
        }

        return $valid;
    }
}