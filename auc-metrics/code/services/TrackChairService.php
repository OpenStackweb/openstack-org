<?php

namespace OpenStack\AUC;

use \SummitTrackChair;
use \Summit;

/**
 * Class TrackChairService
 * @package OpenStack\AUC
 */
class TrackChairService extends BaseService implements MetricService
{

    /**
     * @return string
     */
    public function getMetricIdentifier()
    {
        return "SUMMIT_TRACK_CHAIR";
    }

    /**
     * @return string
     */
    public function getMetricValueDescription()
    {
        return "Categories";
    }

    /**
     * @return static
     */
    public function run()
    {
        $this->results = ResultList::create();
        $s = Summit::get_most_recent();
        $chairs = $s->Categories()->relation('TrackChairs');
        foreach ($chairs as $chair) {
            $this->results->push(
                Result::create(
                    $chair->Member(),
                    implode(', ', $chair->Categories()->column('Title'))
                )
            );
        }
    }
}