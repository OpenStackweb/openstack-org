<?php

namespace OpenStack\AUC;

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
    }

}