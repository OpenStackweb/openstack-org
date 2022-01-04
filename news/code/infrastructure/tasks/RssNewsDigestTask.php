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
 * Class RssNewsDigestTask
 */

final class RssNewsDigestTask extends CronTask {
    function run(){

        try{

            $repository = new SapphireRssNewsRepository();
            $tx_manager = SapphireTransactionManager::getInstance();
            $rss_news_manager = new RssNewsManager(
                $repository,
                $tx_manager
            );

            $rss_news = $rss_news_manager->getNewsItemsFromSource();
            $rss_news_manager->deleteAllNewsItems();
            $rss_news_manager->saveNewsItems($rss_news);

            return 'OK';
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            echo $ex->getMessage();
        }
    }
}