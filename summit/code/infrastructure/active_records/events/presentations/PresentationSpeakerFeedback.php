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
class PresentationSpeakerFeedback extends SummitEventFeedback
{
    private static $has_one = array
    (
        'Speaker' => 'PresentationSpeaker',
    );

    private static $summary_fields = array
    (
        'Rate',
        'Owner.Email',
        'Approved',
        'ApprovedDate',
        'Speaker.Member.Email'
    );

    public function getCMSFields()
    {
        $f = parent::getCMSFields();
        $f->add(new HiddenField('SpeakerID','SpeakerID'));
        return $f;
    }

    public function setSpeaker(IPresentationSpeaker $speaker)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Speaker')->setTarget($speaker);
    }
}