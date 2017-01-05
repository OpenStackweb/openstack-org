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
class LinkFormAction extends FormAction
{
    private $css_class;

    /**
     * @param string $css_class
     * @return $this
     */
    public function setCSSClass($css_class){
        $this->css_class = $css_class;
        return $this;
    }

    /**
     * @return string
     */
    public function getCSSClass(){
        return $this->css_class;
    }
}