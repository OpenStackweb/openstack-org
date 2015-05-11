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

class SummitPackageAssembler {

    public static function toArray(ISummitPackage $package){
        $res = array();
        $res['id']       = (int)$package->getIdentifier();
        $res['title']    = $package->Title;
        $res['cost']    =  DBField::create_field('Currency',$package->Cost)->Nice();
        $res['max_available'] = (int)$package->MaxAvailable;
        $res['available']      = (int)$package->CurrentlyAvailable;
        $res['show_availability'] = (bool)$package->ShowQuantity;
        return $res;
    }
}