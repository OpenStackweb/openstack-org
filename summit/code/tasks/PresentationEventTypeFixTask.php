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
class PresentationEventTypeFixTask extends BuildTask
{
    /**
     * @var string $title Shown in the overview on the {@link TaskRunner}
     * HTML or CLI interface. Should be short and concise, no HTML allowed.
     */
    protected $title = "Presentation EventType Fix Task";

    /**
     * @var string $description Describe the implications the task has,
     * and the changes it makes. Accepts HTML formatting.
     */
    protected $description = 'Assign event type to presentations';


    public function run($request) {
        $summit_id = intval($request->requestVar('SummitID'));

        if($summit_id <= 0)
        {
            throw new RuntimeException("invalid summit id");
        }

        $summit = Summit::get()->byID($summit_id);

        if(is_null($summit))
            throw new RuntimeException("invalid summit");
        $count = 0;
        $presentations = Presentation::get()
            ->filter(array('SummitID' => $summit_id, 'TypeID' => 0))
            ->where(" Title IS NOT NULL AND Title <>'' ");
        Summit::seedBasicEventTypes($summit_id);
        $type = SummitEventType::get()->filter(array('Type'=>'Presentation', 'SummitID'=>$summit_id))->first();
        if(is_null($type))  throw new RuntimeException("invalid event type");
        foreach($presentations as $p)
        {
            $p->TypeID = $type->ID;
            $p->write();
            ++$count;
        }

        echo "Fixed $count presentations.";
    }


}