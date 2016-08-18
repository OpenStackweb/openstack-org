<?php

namespace OpenStack\AUC;

/**
 * Defines the base functionality for all AUC metric services
 */
class BaseService
{
    /**
     * @var ResultList
     */
    protected $results;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @return ResultList
     */
    public function getResults()
    {
    	return $this->results;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
    	return $this->errors;
    }

    /**
     * @param  string $err
     */
    protected function logError($err)
    {
    	$this->errors[] = $err;
    }

}