<?php
/**
 * Copyright 2017 OpenStack Foundation
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

final class AssetsSyncRequest extends DataObject
{
    private static $db = [
        'Origin'        => 'Text',
        'Destination'   => 'Text',
        'Processed'     => 'Boolean',
        'ProcessedDate' => 'SS_Datetime',
    ];

    /**
     * @return Object
     */
    public function markAsProcessed(){
        if($this->Processed) return $this;

        $this->Processed     = true;
        $this->ProcessedDate = MySQLDatabase56::nowRfc2822();
        return $this;
    }
}