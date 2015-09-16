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
 * Class SapphireNewsRepository
 */
final class SapphireNewsRepository extends SapphireRepository {

	public function __construct(){
        parent::__construct(new News());
	}

    /**
     * @return INews[]
     */
    public function getFeaturedNews($limit = 1000)
    {
        $query = new QueryObject(new News);
        $query->addAndCondition(QueryCriteria::equal('Featured','1'));
        $query->addAndCondition(QueryCriteria::equal('Approved','1'));
        $query->addOrder(QueryOrder::asc('Rank'));
        list($list,$count) = $this->getAll($query,0,$limit);
        return $list;
    }

    /**
     * @return INews[]
     */
    public function getRecentNews()
    {
        $query = new QueryObject(new News);
        $query->addAndCondition(QueryCriteria::equal('Featured','0'));
        $query->addAndCondition(QueryCriteria::equal('Slider','0'));
        $query->addAndCondition(QueryCriteria::equal('Approved','1'));
        $query->addAndCondition(QueryCriteria::equal('Archived','0'));
        $query->addOrder(QueryOrder::desc('Date'));
        $query->addOrder(QueryOrder::asc('Rank'));
        list($list,$count) = $this->getAll($query,0,1000);
        return $list;
    }

    /**
     * @return INews[]
     */
    public function getOldNews()
    {
        $thirty_days_ago = date('Y-m-d H:i:s',strtotime('-30 days'));
        $query = new QueryObject(new News);
        $query->addAndCondition(QueryCriteria::lower('Date',$thirty_days_ago));
        $query->addAndCondition(QueryCriteria::equal('Featured','0'));
        $query->addAndCondition(QueryCriteria::equal('Slider','0'));
        $query->addAndCondition(QueryCriteria::equal('Approved','1'));
        $query->addAndCondition(QueryCriteria::equal('Archived','0'));
        $query->addAndCondition(QueryCriteria::equal('Restored','0'));
        list($list,$count) = $this->getAll($query,0,1000);
        return $list;
    }

    public function getArchivedNews($offset = 0, $limit = 1000, $searchTerm)
    {
        if (isset($searchTerm) && trim($searchTerm)!=='') {
            $archivedNewsQuery = $this->buildGeArchivedNewsQuery($searchTerm);
            $list = $archivedNewsQuery->sort('Date DESC')->limit($limit, $offset)->toArray();
        }
        else {
            $query = new QueryObject(new News);
            $query->addAndCondition(QueryCriteria::equal('Archived','1'));
            $query->addOrder(QueryOrder::desc('Date'));
            list($list,$count) = $this->getAll($query,$offset,$limit);
        }
        return $list;
    }

    public function getArchivedNewsCount($searchTerm)
    {
        if (isset($searchTerm) && trim($searchTerm)!=='') {
            $archivedNewsQuery = $this->buildGeArchivedNewsQuery($searchTerm);
            $count = $archivedNewsQuery->count();
        }
        else {
            $count = DataList::create("News")->where("Archived = 1")->Count();
        }

        return $count;
    }

    function buildGeArchivedNewsQuery($searchTerm) {
        $searchTerm = trim($searchTerm, ' "\''); // remove double/single quotes and spaces
        $sqlSearchTerm = Convert::raw2sql($searchTerm);
        //$sqlSearchTerm = preg_replace("/\s+/", " +", $sqlSearchTerm);
        //$sqlSearchTerm = "+".$sqlSearchTerm;

        /*return DataList::create("News")->where("Archived = 1 AND
                MATCH ( Headline, SummaryHtmlFree, BodyHtmlFree )
                AGAINST ('{$sqlSearchTerm}' IN BOOLEAN MODE)");*/

        return DataList::create("News")->where("Archived = 1 AND
                (Headline LIKE '%{$sqlSearchTerm}%' OR
                SummaryHtmlFree LIKE '%{$sqlSearchTerm}%' OR
                BodyHtmlFree LIKE '%{$sqlSearchTerm}%')
                ");
    }

    /**
     * @return INews[]
     */
    public function getStandByNews()
    {
        $query = new QueryObject(new News);
        $query->addAndCondition(QueryCriteria::equal('Featured','0'));
        $query->addAndCondition(QueryCriteria::equal('Slider','0'));
        $query->addAndCondition(QueryCriteria::equal('Approved','0'));
        $query->addAndCondition(QueryCriteria::equal('Archived','0'));
        $query->addAndCondition(QueryCriteria::equal('Deleted','0'));
        $query->addOrder(QueryOrder::desc('Date'));
        //$query->addOrder(QueryOrder::asc('Rank'));
        list($list,$count) = $this->getAll($query,0,1000);
        return $list;
    }

    /**
     * @return INews[]
     */
    public function getSlideNews()
    {
        $query = new QueryObject(new News);
        $query->addAndCondition(QueryCriteria::equal('Slider','1'));
        $query->addAndCondition(QueryCriteria::equal('Approved','1'));
        $query->addOrder(QueryOrder::asc('Rank'));
        list($list,$count) = $this->getAll($query,0,1000);
        return $list;
    }

    /**
     * @return INews[]
     */
    public function getNewsByID($articleID)
    {
        $query = new QueryObject(new News);
        $query->addAndCondition(QueryCriteria::id('ID',$articleID));
        return $this->getBy($query);
    }

    /**
     * @return INews[]
     */
    public function getExpiredNews()
    {
        $today = date("Y-m-d H:i:s");
        $query = new QueryObject(new News);
        $query->addAndCondition(QueryCriteria::lower('DateExpire',$today));
        $query->addAndCondition(QueryCriteria::equal('Approved',1));
        list($expired_articles,$count) = $this->getAll($query,0,1000);


        return $expired_articles;
    }

    public function getNewsToActivate()
    {
        $today = date("Y-m-d H:i:s");
        $query = new QueryObject(new News);
        $query->addAndCondition(QueryCriteria::lower('DateEmbargo',$today));
        $query->addAndCondition(QueryCriteria::greater('DateExpire',$today));
        $query->addAndCondition(QueryCriteria::equal('Approved',0));
        list($activate_articles,$count) = $this->getAll($query,0,1000);

        return $activate_articles;
    }

    public function getArticlesBySection($section) {
        $approved = $slider = $featured = 0;
        if ($section == 'recent') {
            $approved = 1;
        } elseif ($section == 'slider') {
            $slider = 1;
            $approved = 1;
        } elseif ($section == 'featured') {
            $featured = 1;
            $approved = 1;
        }

        $query = new QueryObject(new News);
        $query->addAndCondition(QueryCriteria::equal('Slider',$slider));
        $query->addAndCondition(QueryCriteria::equal('Approved',$approved));
        $query->addAndCondition(QueryCriteria::equal('Featured',$featured));
        $query->addOrder(QueryOrder::asc('Rank'));
        list($articles,$count) = $this->getAll($query);

        return $articles;
    }

    public function getArticlesToSort($article_id,$new_rank,$old_rank,$is_new,$is_remove,$type) {

        $slider = $featured = $approved = 0;
        if ($type == 'slider') {
            $slider = 1;
            $approved = 1;
        } elseif ($type == 'featured') {
            $featured = 1;
            $approved = 1;
        } elseif ($type == 'recent') {
            $approved = 1;
        }

        $query = new QueryObject(new News);
        $query->addAndCondition(QueryCriteria::notEqual('ID',$article_id));
        $query->addAndCondition(QueryCriteria::equal('Slider',$slider));
        $query->addAndCondition(QueryCriteria::equal('Featured',$featured));
        $query->addAndCondition(QueryCriteria::equal('Approved',$approved));

        if ($is_new) {
            $query->addAndCondition(QueryCriteria::greaterOrEqual('Rank',$new_rank));
            $rank_delta = 1;
        } elseif ($is_remove) {
            $query->addAndCondition(QueryCriteria::greaterOrEqual('Rank',$old_rank));
            $rank_delta = -1;
        } else {
            if ($old_rank < $new_rank) {
                $query->addAndCondition(QueryCriteria::greaterOrEqual('Rank',$old_rank));
                $query->addAndCondition(QueryCriteria::lowerOrEqual('Rank',$new_rank));
                $rank_delta = -1;
            } else {
                $query->addAndCondition(QueryCriteria::greaterOrEqual('Rank',$new_rank));
                $query->addAndCondition(QueryCriteria::lowerOrEqual('Rank',$old_rank));
                $rank_delta = 1;
            }
        }

        list($other_articles,$count) = $this->getAll($query);

        $return_array = array($other_articles,$rank_delta);

        return $return_array;
    }

}