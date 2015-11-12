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
final class SummitKeynoteHighlight extends DataObject
{
    private static $db = array
    (
        'Title'       => 'Text',
        'Day'         => 'Enum(array("Day1","Day2","Day3","Day4","Day5"), "Day1")',
        'Description' => 'HTMLText',
        'Order'       => 'Int',
    );

    private static $has_one = array
    (
        'SummitHighlightsPage' => 'SummitHighlightsPage',
        'Image'                => 'BetterImage',
        'Thumbnail'            => 'BetterImage',
    );

    public function getCMSFields()
    {
        $f = new FieldList();
        $f->add(new TextField('Title','Title'));
        $f->add(new DropdownField('Day', 'Day',  $this->dbObject('Day')->enumValues()));
        $f->add(new HtmlEditorField('Description','Description'));

        $image = new UploadField('Image','Pic');
        $image->setAllowedMaxFileNumber(1);
        $image->setFolderName(sprintf('summits/%s/keynotes/pics', $this->SummitHighlightsPage()->SummitID));
        $f->add($image);

        $image = new UploadField('Thumbnail','Thumbnail');
        $image->setAllowedMaxFileNumber(1);
        $image->setFolderName(sprintf('summits/%s/keynotes/thumbs', $this->SummitHighlightsPage()->SummitID));
        $f->add($image);

        $f->add(new HiddenField('SummitHighlightsPageID','SummitHighlightsPageID'));

        return $f;
    }

    public static function getAvailableDays()
    {
        $res = singleton('SummitKeynoteHighlight')->dbObject('Day')->enumValues();
        return $res;
    }

    public function getThumbnailLink()
    {
        $thumb = $this->Thumbnail();
        if(!is_null($thumb) && $thumb->ID > 0)
        {
            return $thumb->Link();
        }
        $image = $this->Image();
        if(!is_null($image) && $image->ID > 0)
        {
            return $image->Link();
        }
        return '#';
    }
}