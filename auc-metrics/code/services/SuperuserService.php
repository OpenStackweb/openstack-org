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
			$member = $members->first();
			$count  = (int) $members->count();

            if($count === 0 && !$member) {
                // check translation table
                $trans  = \AUCMetricTranslation::get()->filter(['UserIdentifier' => $author->name])->first();
                $member = $trans ? $trans->MappedFoundationMember() : null;
            }

			if($count === 0 && !$member) {
                if(\AUCMetricMissMatchError::get()->filter
                    (
                        [
                            "ServiceIdentifier" => $this->getMetricIdentifier(),
                            "UserIdentifier"    => $author->name
                        ]
                    )->count() == 0 ) {
                    $error = new \AUCMetricMissMatchError();
                    $error->ServiceIdentifier = $this->getMetricIdentifier();
                    $error->UserIdentifier = $author->name;
                    $error->write();
                }
				$this->logError("Could not find member $author");
				continue;
			}

            if($count > 1) {
                if(\AUCMetricMissMatchError::get()->filter
                    (
                        [
                            "ServiceIdentifier" => $this->getMetricIdentifier(),
                            "UserIdentifier"    => $author->name
                        ]
                    )->count() == 0 ) {
                    $error = new \AUCMetricMissMatchError();
                    $error->ServiceIdentifier = $this->getMetricIdentifier();
                    $error->UserIdentifier = $author->name;
                    $error->write();
                }
				$this->logError("Author $author matched multiple Member records (".implode(', ', $members->column('Email')).")");
				continue;
			}

			$this->results->push(Result::create(
			    $member,
				$item->get_title()
            ));
        }
    }

}