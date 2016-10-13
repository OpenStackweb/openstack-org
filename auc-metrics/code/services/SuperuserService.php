<?php

namespace OpenStack\AUC;

use \Member;

/**
 * Class SuperUserService
 * @package OpenStack\AUC
 */
class SuperUserService extends BaseService implements MetricService
{

    /**
     * @return string
     */
    public function getMetricIdentifier()
    {
        return "SUPERUSER_CONTRIBUTOR";
    }

    /**
     * @return null
     */
    public function getMetricValueDescription()
    {
        return null;
    }

    /**
     * @return static
     */
    public function run()
    {
        $this->results = ResultList::create();
        $rss = new \SimplePie();
        $rss->set_feed_url('http://superuser.openstack.org/feed');
        $rss->init();

        foreach($rss->get_items() as $item) {        	
        	$author = $item->get_author();        	
			$members = Member::get()->where("
            	CONCAT_WS(' ', Member.FirstName, Member.Surname) LIKE '%{$author->name}%'
			");
			
			$count = (int) $members->count();
			if($count === 0) {
				$this->logError("Could not find member $author");
			}

			else if($count > 1) {
				$this->logError("Author $author matched multiple Member records (".implode(', ', $members->column('Email')).")");
			}

			else {
				$this->results->push(Result::create(
					$members->first(),
					$item->get_title()
				));
			}
        }
    }

}