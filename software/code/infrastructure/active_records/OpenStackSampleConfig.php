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
final class OpenStackSampleConfig extends DataObject
{

    private static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    private static $db = array
    (
        'Title'       => 'Varchar',
        'Summary'     => 'HTMLText',
        'Description' => 'HTMLText',
        'IsDefault'   => 'Boolean',
        'Order'       => 'Int',
    );

    private static $has_one = array
    (
        "Curator" => "Member",
        "Release" => "OpenStackRelease",
        "Type"    => 'OpenStackSampleConfigurationType',
    );

    private static $has_many = array
    (
        'RelatedNotes' => 'OpenStackSampleConfigRelatedNote',
    );

    protected function onBeforeDelete() {
        parent::onBeforeDelete();
        foreach($this->RelatedNotes() as $item) {
            $item->delete();
        }
        $this->OpenStackComponents()->removeAll();
    }

    static $many_many = array
    (
        'OpenStackComponents' => 'OpenStackComponent',
    );

    private static $many_many_extraFields = array
    (
        'OpenStackComponents'  => array
        (
            'Order' => 'Int',
        )
    );

    public function getOptionalComponents()
    {
        return $this->OpenStackComponents()->filter('IsCoreService', 0);
    }

    public function getCoreComponents()
    {
        return $this->OpenStackComponents()->filter('IsCoreService', 1);
    }

    public function getMissingCoreComponents()
    {
        $res = new ArrayList();
        $release_component = $this->Release()->getOpenStackCoreComponents();
        foreach($release_component as $rc)
        {
            if(intval($this->OpenStackComponents()->filter(array( 'IsCoreService' => 1 , 'ID' => $rc->ID ))->count()) === 0){
                $res->add($rc);
            }
        }
        return $res;
    }

}