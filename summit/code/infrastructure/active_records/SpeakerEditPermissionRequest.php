<?php
/**
 * Copyright 2019 OpenStack Foundation
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

final class SpeakerEditPermissionRequest extends DataObject
{
    private static $db = [
        'Approved'       => 'Boolean',
        'ApprovedDate'   => 'SS_Datetime',
        'CreatedDate'    => 'SS_Datetime',
        'Hash'           => 'Text',
    ];

    private static $has_one = [
        'Speaker' => 'PresentationSpeaker',
        'RequestedBy' => 'Member',
    ];
}