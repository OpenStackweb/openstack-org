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
 * Class RssNewsManager
 */

final class RssNewsManager
{
    /**
     * @param IEntityRepository $news_repository
     * @param ITransactionManager $tx_manager
     */
    public function __construct(IEntityRepository $repository,
                                ITransactionManager $tx_manager) {
        $this->repository = $repository;
        $this->tx_manager = $tx_manager;
    }

    function deleteAllNewsItems() {
        DB::query("DELETE FROM RssNews");
    }

    /**
     * @param $rss_news_array
     */
    function saveNewsItems($rss_news_array) {

        $rss_news_repository = $this->repository;

        $this->tx_manager->transaction(function () use ($rss_news_repository, $rss_news_array) {
            foreach ($rss_news_array as $rss_news) {
                $rss_news_repository->add($rss_news);
            }
        });
    }

    /**
     * @param int $limit
     * @return DataList
     */
    function getNewsItems($limit = 20) {
        $rss_news = null;
        $return_array = new ArrayList();
        $outsourced_limit = 5;

        $rss_news = $this->getRssItems($outsourced_limit)->toArray();
        foreach ($rss_news as $item) {
            $rss_news = new RssNews();
            $rss_news->Date = $item->date_display;
            $rss_news->Headline = $item->title;
            $rss_news->Link = $item->link;
            $rss_news->Category = 'Planet';

            $return_array->push($rss_news);
        }

        $blog_news = $this->getBlogItems($outsourced_limit)->toArray();
        foreach ($blog_news as $item) {
            $rss_news = new RssNews();
            $rss_news->Date = $item->date_display;
            $rss_news->Headline = $item->title;
            $rss_news->Link = $item->link;
            $rss_news->Category = 'Blog';

            $return_array->push($rss_news);
        }

        $superuser_news = $this->getSuperUserItems($outsourced_limit)->toArray();
        foreach ($superuser_news as $item) {
            $rss_news = new RssNews();
            $rss_news->Date = $item->date_display;
            $rss_news->Headline = $item->title;
            $rss_news->Link = $item->link;
            $rss_news->Category = 'Superuser';

            $return_array->push($rss_news);
        }

        return $return_array->sort('timestamp', 'DESC')->limit($limit,0);
    }

    /**
     * @param int $limit
     * @return mixed
     */
    function getRssItems($limit = 7) {
        $result = $this->queryExternalSource('http://planet.openstack.org/rss20.xml', 7200, 'channel', 'item');
        if(!$result->count()) return $result;

        foreach ($result as $item) {
            $item->date_display = date("D, M jS Y", strtotime($item->pubDate));
            $item->timestamp = strtotime($item->pubDate);
        }

        return $result->limit($limit, 0);
    }

    /**
     * @param int $limit
     * @return mixed
     */
    function getBlogItems($limit = 7) {

        $result = $this->queryExternalSource('https://www.openstack.org/blog/feed/', 7200, 'channel', 'item');
        if(!$result->count()) return $result;

        foreach ($result as $item) {
            $item->date_display = date("D, M jS Y", strtotime($item->pubDate));
            $item->timestamp = strtotime($item->pubDate);
        }

        return $result->limit($limit, 0);
    }

    /**
     * @param int $limit
     * @return mixed
     */
    function getSuperUserItems($limit = 7) {

        $result = $this->queryExternalSource('http://superuser.openstack.org/articles/feed/', 7200, 'entry');
        if(!$result->count()) return $result;

        foreach ($result as $item) {
            $item->date_display = date("D, M jS Y", strtotime($item->published));
            $item->timestamp = strtotime($item->published);
        }

        return $result->limit($limit, 0);
    }

    /**
     * @param string $url
     * @param int  $expiry
     * @param null $collection
     * @param null $element
     * @return ArrayList
     */
    private function queryExternalSource($url, $expiry=3600, $collection = NULL, $element = NULL){
        $output = new ArrayList();
        try {
            $feed     = new RestfulService($url, $expiry);
            $response = $feed->request();
            if ($response->getStatusCode() == 200) {
                $body = $response->getBody();
                $output = $feed->getValues($body, $collection, $element);
            }
        }
        catch(Exception $ex){
            SS_Log::log($ex, SS_Log::WARN);
        }
        return $output;
    }
}