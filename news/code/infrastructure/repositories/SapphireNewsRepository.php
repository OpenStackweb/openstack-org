<?php

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
    public function getFeaturedNews()
    {
        $query = new QueryObject(new News);
        $query->addAddCondition(QueryCriteria::equal('Featured','1'));
        $query->addAddCondition(QueryCriteria::equal('Approved','1'));
        $query->addOrder(QueryOrder::asc('Rank'));
        list($list,$count) = $this->getAll($query,0,1000);
        return $list;
    }

    /**
     * @return INews[]
     */
    public function getRecentNews()
    {
        $query = new QueryObject(new News);
        $query->addAddCondition(QueryCriteria::equal('Featured','0'));
        $query->addAddCondition(QueryCriteria::equal('Slider','0'));
        $query->addAddCondition(QueryCriteria::equal('Approved','1'));
        $query->addOrder(QueryOrder::desc('DateTime'));
        $query->addOrder(QueryOrder::asc('Rank'));
        list($list,$count) = $this->getAll($query,0,1000);
        return $list;
    }

    /**
     * @return INews[]
     */
    public function getStandByNews()
    {
        $query = new QueryObject(new News);
        $query->addAddCondition(QueryCriteria::equal('Featured','0'));
        $query->addAddCondition(QueryCriteria::equal('Slider','0'));
        $query->addAddCondition(QueryCriteria::equal('Approved','0'));
        $query->addOrder(QueryOrder::desc('DateTime'));
        $query->addOrder(QueryOrder::asc('Rank'));
        list($list,$count) = $this->getAll($query,0,1000);
        return $list;
    }

    /**
     * @return INews[]
     */
    public function getSlideNews()
    {
        $query = new QueryObject(new News);
        $query->addAddCondition(QueryCriteria::equal('Slider','1'));
        $query->addAddCondition(QueryCriteria::equal('Approved','1'));
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
        $query->addAddCondition(QueryCriteria::equal('ID',$articleID));
        return $this->getBy($query);
    }


    public function setArticle($article_id,$new_rank,$type) {

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

        $article = $this->getById($article_id);
        $article->Slider = $slider;
        $article->Featured = $featured;
        $article->Rank = $new_rank;
        $article->Approved = $approved;
        $article->write();
    }

    public function sortArticle($article_id,$new_rank,$old_rank,$is_new,$is_remove,$type) {

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
        $query->addAddCondition(QueryCriteria::notEqual('ID',$article_id));
        $query->addAddCondition(QueryCriteria::equal('Slider',$slider));
        $query->addAddCondition(QueryCriteria::equal('Featured',$featured));
        $query->addAddCondition(QueryCriteria::equal('Approved',$approved));

        if ($is_new) {
            $query->addAddCondition(QueryCriteria::greaterOrEqual('Rank',$new_rank));
            $rank_delta = 1;
        } elseif ($is_remove) {
            $query->addAddCondition(QueryCriteria::greaterOrEqual('Rank',$old_rank));
            $rank_delta = -1;
        } else {
            if ($old_rank < $new_rank) {
                $query->addAddCondition(QueryCriteria::greaterOrEqual('Rank',$old_rank));
                $query->addAddCondition(QueryCriteria::lowerOrEqual('Rank',$new_rank));
                $rank_delta = -1;
            } else {
                $query->addAddCondition(QueryCriteria::greaterOrEqual('Rank',$new_rank));
                $query->addAddCondition(QueryCriteria::lowerOrEqual('Rank',$old_rank));
                $rank_delta = 1;
            }
        }

        list($other_articles,$count) = $this->getAll($query);

        foreach ($other_articles as $article) {
            $article->Rank = $article->Rank + $rank_delta;
            $article->write();
        }
    }
}