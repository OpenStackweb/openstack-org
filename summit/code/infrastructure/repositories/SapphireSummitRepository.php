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
 * Class SapphireSummitRepository
 */
final class SapphireSummitRepository extends SapphireRepository implements ISummitRepository
{

    public function __construct()
    {
        parent::__construct(new Summit);
    }

    public function isDuplicated(ISummit $summit)
    {
        $start_date = $summit->getBeginDate();
        $end_date = $summit->getEndDate();
        $dupe = Summit::get_one("Summit",
            "Name = '" . $summit->getName() . "' AND SummitBeginDate = '" . $start_date . "'  AND SummitEndDate = '" . $end_date . "'");

        return !empty($dupe);
    }

    /**
     * @param string $external_event_id
     * @return ISummit
     */
    public function getByExternalEventId($external_event_id)
    {
        return Summit::get()->filter('ExternalEventId', $external_event_id)->first();
    }
}