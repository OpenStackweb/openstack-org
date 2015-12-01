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
class SummitScheduleGlobalSearchTerm extends DataObject
{

    private static $db = array
    (
        'Term'       => 'Text',
        'Hits'       => 'Int',
    );

    private static $has_one = array
    (
        'Summit' => 'Summit',
    );

    public function getOpacity(){
        $total = intval(DB::query("select SUM(Hits) from SummitScheduleGlobalSearchTerm WHERE SummitID = ".$this->SummitID)->value());
        if($total === 0) return 0.0;
        $val = intval($this->Hits) / $total;
        if($val < 0.1 ) $val = 0.1;
        $val =  100.0 - ($val*100);
        if($val >= 90.0) $val = 85.0;
        return $val;
    }

    public function getFontSize()
    {
        $total = intval(DB::query("select SUM(Hits) from SummitScheduleGlobalSearchTerm WHERE SummitID = ".$this->SummitID)->value());
        if($total === 0) return 10;
        $val = intval($this->Hits) / $total;
        $val = intval($val*100);
        if($val > 40) $val = 25;
        if($val < 12) $val = 12;
        return $val;
    }
}