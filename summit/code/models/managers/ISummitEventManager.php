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
interface ISummitEventManager
{
    /**
     * @param ISummit $summit
     * @param array $event_data
     * @return mixed
     */
    public function publishEvent(ISummit $summit, array $event_data);

    /**
     * @param ISummit $summit
     * @param ISummitEvent $event
     * @return mixed
     */
    public function unpublishEvent(ISummit $summit, ISummitEvent $event);

    /**
     * @param ISummit $summit
     * @param array $event_data
     * @return mixed
     */
    public function updateEvent(ISummit $summit, array $event_data);

    /**
     * @param ISummit $summit
     * @param array $event_data
     * @return mixed
     */
    public function createEvent(ISummit $summit, array $event_data);

    /**
     * @param ISummit $summit
     * @param $event_id
     * @param $tmp_file
     * @param int $max_file_size
     * @return File
     */
    public function uploadAttachment(ISummit $summit, $event_id, $tmp_file, $max_file_size = 10*1024*1024);

    /**
     * @param ISummit $summit
     * @param array $data
     */
    public function updateAndPublishBulkEvents(ISummit $summit, array $data);

    /**
     * @param ISummit $summit
     * @param array $data
     */
    public function updateBulkEvents(ISummit $summit, array $data);

    /**
     * @param ISummit $summit
     * @param array $event_ids
     */
    public function unPublishBulkEvents(ISummit $summit, array $event_ids);

    /**
     * @param ISummit $summit
     * @param array $assistance_data
     * @return mixed
     */
    public function updateAssistance(ISummit $summit, array $assistance_data);

    /**
     * @param ISummit $summit
     * @param $data
     */
    public function updateHeadCount(ISummit $summit, $data);

    /**
     * @param ISummit $summit
     * @param $data
     */
    public function updateVideoDisplay(ISummit $summit, $data);

    /**
     * @param $report_name
     * @param $data
     */
    public function updateReportConfig($report_name, $data);

    /**
     * @param ISummit $summit
     * @param array $data
     */
    public function updateBulkPresentations(ISummit $summit, array $data);

}