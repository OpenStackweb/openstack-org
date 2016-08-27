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

/**
 * Interface IRSVPMultiValueQuestionTemplate
 */
interface IRSVPMultiValueQuestionTemplate extends IRSVPQuestionTemplate
{

    /**
     * @return IRSVPQuestionValueTemplate[]
     */
    public function getValues();

    /**
     * @param IRSVPQuestionValueTemplate $value
     * @return $this
     */
    public function addValue(IRSVPQuestionValueTemplate $value);

    /**
     * @return IRSVPQuestionValueTemplate
     */
    public function getDefaultValue();

    /**
     * @param int $id
     * @return IRSVPQuestionValueTemplate
     */
    public function getValueById($id);

    /**
     * @param string $value
     * @return IRSVPQuestionValueTemplate
     */
    public function getValueByValue($value);
}