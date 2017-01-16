<?php

/**
 * Copyright 2017 OpenStack Foundation
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

/**
 * Class SummitGroupEvent
 */
final class SummitGroupEvent extends SummitEvent
{
    /**
     * @var array
     */
    private static $many_many = array
    (
       'Groups' => 'Group'
    );

    private static $has_one = array
    (
        'CreatedBy'  => 'Member',
    );

    public function getCMSFields()
    {
        $f = parent::getCMSFields();
        if($this->ID > 0) {
            $groupsMap = array();
            foreach(Group::get() as $group) {
                $groupsMap[$group->ID] = $group->getBreadcrumbs(' > ');
            }
            asort($groupsMap);
            $f->addFieldToTab('Root.Main',
                ListboxField::create('Groups', singleton('Group')->i18n_plural_name())
                    ->setMultiple(true)
                    ->setSource($groupsMap)
                    ->setAttribute(
                        'data-placeholder',
                        _t('Member.ADDGROUP', 'Add group', 'Placeholder text for a dropdown')
                    )
            );
        }
        $f->removeFieldFromTab('Root.Main','TypeID');
        return $f;
    }

    /**
     * @return ValidationResult
     */
    protected function validate()
    {
        $this->exclude_type_validation = true;
        $valid = parent::validate();

        if(!$valid->valid()) return $valid;

        $summit_id = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;

        $default_event_type = SummitEventType::get()->filter(
            [
                'SummitID' => $summit_id,
                'Type'     => ISummitEventType::GroupsEvents
            ])->first();

        if(is_null($default_event_type)){
            return $valid->error
            (
                sprintf('Missing default event type %s on summit id %s, please re seed default types.', ISummitEventType::GroupsEvents, $summit_id)
            );
        }

        return $valid;
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $summit_id = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $default_event_type = SummitEventType::get()->filter(
            [
                'SummitID' => $summit_id,
                'Type'     => ISummitEventType::GroupsEvents
            ])->first();

        $this->TypeID = $default_event_type->ID;

        $this->CreateByID = Member::currentUserID();
    }
}