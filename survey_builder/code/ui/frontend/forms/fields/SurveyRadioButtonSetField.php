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
class SurveyRadioButtonSetField extends OptionsetField
{
    const OrientationVertical   = 'Vertical';
    const OrientationHorizontal = 'Horizontal';

    private $orientation        = self::OrientationVertical;

    public function setOrientation($orientation){
        $this->orientation = $orientation;
    }

    public function getOrientation(){
        return $this->orientation;
    }

    public function getOrientationClass(){
        return $this->orientation == self::OrientationVertical || empty($this->orientation) ? 'vertical' : 'horizontal';
    }

    public function Field($properties = array()) {
        Requirements::css('survey_builder/css/SurveyRadioButtonSetField.css');
        return parent::Field($properties);
    }
}