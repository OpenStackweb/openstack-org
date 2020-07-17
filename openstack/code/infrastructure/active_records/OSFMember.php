<?php
/**
 * Copyright 2020 OpenStack Foundation
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

class OSFMember extends DataObject
{
    static $db = [
        'Paragraph1' => 'HTMLText',
        'Paragraph2' => 'HTMLText',
        'Link' => "Text",
    ];

    static $has_one = [
        'Company'  => 'Company',
        'HomePage' => 'NewHomePage',
        'Image' => 'CloudImage',
    ];

    public function getParagraph2(){
        $res = $this->getField("Paragraph2");
        if(!empty($res)){
            $res = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $res);
        }
        return $res;
    }


    private static $summary_fields = array(
        'Company.Name' => 'Company',
    );

    public function getCMSFields()
    {

        $fields = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $fields->addFieldToTab('Root.Main', new HtmlEditorField('Paragraph1', 'Paragraph1'));
        $fields->addFieldToTab('Root.Main', new HtmlEditorField('Paragraph2', 'Paragraph2'));
        $fields->addFieldToTab('Root.Main', new TextField('Link', 'Link'));
        $image  = new CustomUploadField('Image', 'Image');
        $image->setFolderName('osf-members');
        $image->setAllowedFileCategories('image');

        $fields->addFieldToTab('Root.Main', $image);
        $fields->addFieldToTab('Root.Main', AutoCompleteField::create('CompanyID','Company')
            ->setSourceClass('Company')
            ->setSourceFields([
                'Name',
            ])
            ->setLabelField('Name')
            ->setLimit(10));
        return $fields;
    }
}