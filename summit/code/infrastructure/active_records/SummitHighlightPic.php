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
class SummitHighlightPic extends DataObject
{
    private static $db = array
    (
        'Title'       => 'Text',
        'Order'       => 'Int',
    );

    private static $has_one = array
    (
        'SummitHighlightsPage' => 'SummitHighlightsPage',
        'Image'                => 'CloudImage',
    );

    public function getCMSFields()
    {
        $f = new FieldList();
        $f->add(new TextField('Title','Title'));
        $image = UploadField::create('Image','Pic');
        $image->setAllowedMaxFileNumber(1);
        $image->setFolderName(sprintf('summits/%s/highlights/pics', $this->SummitHighlightsPage()->SummitID));
        $f->add($image);
        $f->add(new HiddenField('SummitHighlightsPageID','SummitHighlightsPageID'));
        return $f;
    }
}