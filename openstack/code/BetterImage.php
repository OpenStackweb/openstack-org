<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Prevents creation of resized images if the uploaded file already
 * fits the requested dimensions
 */
class BetterImage extends CloudImage
{   
    public function SetWidth($width) {
        if($width == $this->getWidth()){
            return $this;
        }
             
        return parent::SetWidth($width);
    }
     
    public function SetHeight($height) {
        if($height == $this->getHeight()){
            return $this;
        }
             
        return parent::ScaleHeight($height);
    }
     
    public function SetSize($width, $height) {
        if($width == $this->getWidth() && $height == $this->getHeight()){
            return $this;
        }
         
        return parent::Pad($width, $height);
    }
     
    public function SetRatioSize($width, $height) {
        if($width == $this->getWidth() && $height == $this->getHeight()){
            return $this;
        }
         
        return parent::Fit($width, $height);
    }

}