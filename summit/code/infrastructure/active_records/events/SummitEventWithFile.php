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
 * Class SummitEventWithFile
 */
final class SummitEventWithFile extends SummitEvent
{
    /**
     * @var array
     */
    private static $many_many = array
    (
    );

    private static $has_one = array
    (
        'Attachment'  => 'File',
    );

    public function getCMSFields()
    {
        $f = parent::getCMSFields();

        $file = new UploadField('Attachment','Attachment');
        $file->setFolderName('summit-event-attachments');
        $file->getValidator()->setAllowedMaxFileSize(4*1024*1024);

        $f->addFieldToTab('Root.Main',$file);

        $other_event_types = SummitEventType::get()
            ->filter('SummitID', $this->Summit()->ID)
            ->exclude('Type',$this->validTypes());

        $type_field = $f->dataFieldByName('TypeID');
        $type_field->setDisabledItems( $other_event_types->column() );

        return $f;
    }

    /**
     * @return ValidationResult
     */
    protected function validate()
    {
        $valid = parent::validate();

        if(!$valid->valid()) return $valid;

        $summit_id = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;

        $default_event_type_ids = SummitEventType::get()->filter(
            [
                'SummitID' => $summit_id,
                'Type'     => $this->validTypes()
            ])->column();

        if (!in_array($this->TypeID, $default_event_type_ids)) {
            return $valid->error
            (
                sprintf('This type of event has to be of type %s or %s.', ISummitEventType::Lunch, ISummitEventType::Breaks)
            );
        }

        return $valid;
    }

    public function validTypes() {
        return [ISummitEventType::Lunch, ISummitEventType::Breaks];
    }

}