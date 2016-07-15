<?php

namespace OpenStack\AUC;

/**
 * Interface MetricService
 * @package OpenStack\AUC
 */
interface MetricService
{

    /**
     * @return string
     */
    public function getMetricIdentifier();

    /**
     * @return OpenStack\AUC\ResultList
     */
    public function getResults();

    /**
     * @return string
     */
    public function getMetricValueDescription();

}