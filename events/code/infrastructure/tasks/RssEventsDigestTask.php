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
 * Class RssEventsDigestTask
 */

final class RssEventsDigestTask extends CronTask {

    /**
     * @var IEventManager
     */
    private $manager;

    /**
     * RssEventsDigestTask constructor.
     * @param IEventManager $manager
     */
    public function __construct(IEventManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    function run(){

        try{

            $rss_events   = $this->manager->rssEvents(PHP_INT_MAX);
            $events_array = $this->manager->rss2events($rss_events);
            $this->manager->saveRssEvents($events_array);

            // purge events that no longer come in the xml
            if (count($events_array) > 0) {
                $this->manager->purgeRssEvents($events_array);
            }

            return 'OK';
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            echo $ex->getMessage();
        }
    }
}