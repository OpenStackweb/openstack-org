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
final class EventFeedbackFactory implements IEventFeedbackFactory {

	/**
	 * @param array $data
	 * @return ISummitEventFeedback
	 */
	public function buildEventFeedback(array $data){
        $event_feedback = new SummitEventFeedback();
        $event_feedback->Note = trim($data['comment']);
        $event_feedback->Rate = $data['rating'];
        $event_feedback->Approved = 1;
        $event_feedback->OwnerID = $data['member_id'];
        $event_feedback->EventID = $data['event_id'];
		return $event_feedback;
	}

    /**
     * @param array $data
     * @param IPresentationSpeaker $speaker
     * @return ISummitEventFeedback
     */
    public function buildSpeakerFeedback(array $data, IPresentationSpeaker $speaker) {
        $speaker_feedback = new PresentationSpeakerFeedback();
        $speaker_feedback->Note = trim($data['comment']);
        $speaker_feedback->Rate = $data['rating'];
        $speaker_feedback->Approved = 1;
        $speaker_feedback->OwnerID = $data['member_id'];
        $speaker_feedback->EventID = $data['event_id'];
        $speaker_feedback->setSpeaker($speaker);
        return $speaker_feedback;
    }
}