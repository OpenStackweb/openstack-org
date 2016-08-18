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
     * @return void;
     */
    public function run();

    /**
     * @return OpenStack\AUC\ResultList
     */
    public function getResults();

    /**
     * @return array
     */
    public function getErrors();

    /**
     * @return string
     */
    public function getMetricValueDescription();

}