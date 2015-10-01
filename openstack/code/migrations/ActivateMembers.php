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
class ActivateMembers extends BuildTask
{
    /**
     * @var string $title Shown in the overview on the {@link TaskRunner}
     * HTML or CLI interface. Should be short and concise, no HTML allowed.
     */
    protected $title = 'Activate Members';

    /**
     * @var string $description Describe the implications the task has,
     * and the changes it makes. Accepts HTML formatting.
     */
    protected $description = "Activate all Members, or by ID is one is given";

    /**
     * Execute the task
     */
    public function run($request)
    {
        set_time_limit(0);

        $member_id = intval($request->getVar('member_id'));
        if(empty($member_id)) $member_id = "ALL";
        $query = "UPDATE Member SET Active = 1";
        if($member_id !== "ALL") {
            $query .= " WHERE ID = " . $member_id;
            echo sprintf("activating member id %s <BR>",$member_id);
        }
        else{
            echo "Activating all Members<BR>";
        }

        DB::query($query);

        echo "Done!";
    }
}