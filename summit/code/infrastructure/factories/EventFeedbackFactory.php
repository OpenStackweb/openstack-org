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
 * Class EventFeedbackFactory
 */
final class EventFeedbackFactory implements IEventFeedbackFactory
{

    /**
     * @param array $data
     * @return ISummitEventFeedback
     */
    public function buildEventFeedback(array $data)
    {
        $event_feedback           = new SummitEventFeedback();
        $event_feedback->Note     = trim($data['comment']);
        $event_feedback->Rate     = intval($data['rating']);
        $event_feedback->Approved = 1;
        $event_feedback->OwnerID  = intval($data['member_id']);
        $event_feedback->EventID  = intval($data['event_id']);

        return $event_feedback;
    }

}