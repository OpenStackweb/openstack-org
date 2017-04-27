<?php
/**
 * Copyright 2016 OpenStack Foundation
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


class FuturePublishDate extends DataExtension
{
    private static $db = array(
        'PublishDate' => 'SS_DateTime'
    );

    public function updateCMSFields(FieldList $fields) {
        $datetimeField = new DatetimeField( 'PublishDate', 'Publish From (UTC)' );

        $dateField = $datetimeField->getDateField();
        $dateField->setConfig( 'dateformat', 'yyyy-MM-dd' );
        $dateField->setConfig( 'showcalendar', true );

        $timeField = $datetimeField->getTimeField();
        $timeField->setConfig( 'timeformat', 'H:m:s' );

        $fields->insertBefore( $datetimeField, 'Content' );
    }

    public function onAfterWrite() {
        if ($this->owner->ClassName == 'RedirectorPage') {
            $page = $this->owner->LinkTo();
            $page->PublishDate = $this->owner->PublishDate;
            $page->write();
        }

        parent::onAfterWrite();
    }

}