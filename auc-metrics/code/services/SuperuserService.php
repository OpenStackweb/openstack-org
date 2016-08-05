<?php

namespace OpenStack\AUC;

/**
 * Class SuperUserService
 * @package OpenStack\AUC
 */
class SuperUserService implements MetricService
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
    public function getResults()
    {
        return ResultList::create();
    }

}