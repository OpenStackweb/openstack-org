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
class SummitLocationImage extends DataObject
{
    private static $db = array
    (
        'Name'         => 'Varchar(255)',
        'Description'  => 'HTMLText',
        'Order'        => 'Int',
    );

    private static $summary_fields = array
    (
        'Name'      => 'Name',
        'Thumbnail' => 'Thumbnail',
    );

    private static $has_one = array
    (
        'Picture'  => 'BetterImage',
        'Location' => 'SummitGeoLocatedLocation'
    );

    public function getThumbnail() {
        if ($this->Picture()->exists()) {
            return $this->Picture()->SetWidth(100);
        } else {
            return '(No Image)';
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if($this->Picture()->exists())
        {
            return $this->Picture()->Link();
        }
        return null;
    }

    public function getCMSFields()
    {
        $f = new FieldList();

        $f->add(new TextField('Name','Name'));
        $f->add(new HtmlEditorField('Description','Description'));

        $map_field = new UploadField('Picture','Picture');
        $map_field->setAllowedMaxFileNumber(1);
        $map_field->setFolderName(sprintf('summits/%s/locations/images/', $this->Location()->SummitID));

        $f->add($map_field );

        $f->add(new HiddenField('LocationID', 'LocationID') );

        return $f;
    }

}