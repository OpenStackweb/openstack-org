<?php

/**
 * Copyright 2018 OpenStack Foundation
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
interface ISelectionPlan extends IEntity
{

    /**
     * @return DateTime
     */
    public function getSubmissionBeginDate();

    /**
     * @param $value DateTime
     */
    public function setSubmissionBeginDate($value);

    /**
     * @return DateTime
     */
    public function getSubmissionEndDate();

    /**
     * @param $value DateTime
     */
    public function setSubmissionEndDate($value);

    /**
     * @return DateTime
     */
    public function getVotingBeginDate();

    /**
     * @param $value DateTime
     */
    public function setVotingBeginDate($value);

    /**
     * @return DateTime
     */
    public function getVotingEndDate();

    /**
     * @param $value DateTime
     */
    public function setVotingEndDate($value);

    /**
     * @return DateTime
     */
    public function getSelectionBeginDate();

    /**
     * @param $value DateTime
     */
    public function setSelectionBeginDate($value);

    /**
     * @return DateTime
     */
    public function getSelectionEndDate();

    /**
     * @param $value DateTime
     */
    public function setSelectionEndDate($value);

    /**
     * @return PresentationCategoryGroup[]
     */
    public function getPublicCategoryGroups();

    /**
     * @return PresentationCategoryGroup[]
     */
    public function getPrivateCategoryGroups();

    /**
     * @return PresentationCategory[]
     */
    public function getCategories();

    /**
     * @return PresentationCategory[]
     */
    public function getVotingCategories();

    /**
     * @return PresentationCategory[]
     */
    public function getSelectionCategories();

    /**
     * @param $key String
     * @return STAGE_UNSTARTED , STAGE_OPEN , STAGE_FINISHED, null
     */
    public function getStageStatus($key);

    /**
     * @return boolean
     */
    public function isVotingOpen();

    /**
     * @return boolean
     */
    public function isCallForPresentationsOpen();

    /**
     * @return boolean
     */
    public function isSelectionOpen();


}