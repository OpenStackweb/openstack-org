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
class PresentationSlide extends PresentationMaterial
{
    /**
     * @var array
     */
    private static $db = [
        'Link' => 'Text',
    ];


    /**
     * @var array
     */
    private static $has_one = [
        'Slide' => 'File'
    ];


    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $f = parent::getCMSFields();
        $f->addFieldToTab('Root.Main', new TextField('Link', 'Slide Link'));
        $f->addFieldToTab('Root.Main', $upload = new UploadField('Slide', 'Slide File'));
        $upload->setAllowedMaxFileNumber(1);
        return $f;
    }

    /**
     * @return mixed
     */
    public function getSlideUrl()
    {
        if ($this->Link) {
            return $this->Link;
        } else {
            return $this->Slide()->URL;
        }
    }

    /**
     * @return bool
     */
    public function IsLink()
    {
        return !empty($this->Link);
    }

    /**
     * @return mixed
     */
    public function IsUpload()
    {
        return $this->Slide()->exists();
    }
}