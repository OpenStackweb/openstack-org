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
class SummitLocationMap extends SummitLocationImage
{

    private static $db = array
    (
    );

    private static $summary_fields = array
    (
        'Name'       => 'Name',
        'Thumbnail'  => 'Thumbnail',
    );

    private static $has_one = array
    (
    );

    public function getCMSFields()
    {
        $f = parent::getCMSFields();
        $f->removeByName('Picture');
        $map_field = new UploadField('Picture','Map');
        $map_field->setAllowedMaxFileNumber(1);
        $map_field->setFolderName(sprintf('summits/%s/locations/maps/', $this->Location()->SummitID));
        $map_field->getValidator()->setAllowedMaxFileSize(array('*' => 500 * 1024));

        $f->add($map_field );
        return $f;
    }

}