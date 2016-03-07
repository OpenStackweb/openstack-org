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
interface IPresentation extends ISummitEvent
{
    /**
     * SELECTION STATUS (TRACK CHAIRS LIST)
     */
    const SelectionStatus_Accepted   = 'accepted';
    const SelectionStatus_Unaccepted = 'unaccepted';
    const SelectionStatus_Alternate  = 'alternate';

    /**
     * @return bool
     */
    public function creatorBeenEmailed();

    /**
     * @return bool
     */
    public function isNew();

    /**
     * @return mixed
     */
    public function clearBeenEmailed();

    /**
     * @return string
     */
    public function Link();

    /**
     * @return string
     */
    public function EditLink();

    /**
     * @return string
     */
    public function PreviewLink();

    /**
     * @return string
     */
    public function EditSpeakersLink();

    /**
     * @return string
     */
    public function DeleteLink();

    /**
     * @return $this
     */
    public function markReceived();
}