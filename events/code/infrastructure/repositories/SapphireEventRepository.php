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
 * Class SapphireEventRepository
 */
final class SapphireEventRepository extends SapphireRepository {

	public function __construct(){
		parent::__construct(new EventPage);
	}

    public function getAllPosted($offset = 0, $limit = 10) {

        return EventPage::get()->where("EventEndDate >= now()")->sort('EventStartDate', 'ASC')->limit($limit)->toArray();
    }

    public function countAllPosted() {

        return EventPage::get()->where("EventEndDate >= now()")->sort('EventStartDate', 'ASC')->count();
    }

    public function getRssForPurge($pulled_events) {
        $external_ids = [];
        foreach ($pulled_events as $event) {
            $external_ids[] = $event->ExternalSourceId;
        }

        $in_external_ids = implode("','",$external_ids);

        return EventPage::get()->where("ExternalSourceId IS NOT NULL AND ExternalSourceId NOT IN ('".$in_external_ids."')")->toArray();
    }
}