<?php

/**
 * Copyright 2018 Openstack Foundation
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
class SummitBanner extends DataObject implements ISummitBanner
{

    private static $create_table_options = array('MySQLDatabase' => 'ENGINE=MyISAM');

    static $db = array
    (
        'Name'              => 'Varchar(255)',
        'MainText'          => 'HTMLText',
        'MainTextColor'     => 'Color',
        'SeparatorColor'    => 'Color',
        'BackgroundColor'   => 'Color',
        'ButtonText'        => 'Text',
        'ButtonLink'        => 'Varchar(255)',
        'ButtonColor'       => 'Color',
        'ButtonTextColor'   => 'Color',
        'SmallText'         => 'Text',
        'SmallTextColor'    => 'Color',
        'Template'          => 'Enum(array("HighlightBar","Editorial"), "HighlightBar")',
        'Enabled'           => 'Boolean(1)',
    );

    static $has_one = array
    (
        'Logo'          => 'File',
        'Picture'       => 'File',
        'ParentPage'    => 'Page'
    );

    private static $summary_fields = array
    (
        'ID'        => 'ID',
        'Name'      => 'Name',
        'Template'  => 'Template',
        'Enabled'   => 'Enabled'
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function renderBanner() {
        return $this->renderWith('SummitBanner_'.$this->Template, array(
            'Banner'  => $this
        ));
    }


    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->add(new TextField('ParentPageID','ParentPageID'));

        return $fields;

    }

}