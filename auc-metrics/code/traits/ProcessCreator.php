<?php

namespace OpenStack\AUC;

use \Injector;

/**
 * Class ProcessCreator
 * @package OpenStack\AUC
 */
trait ProcessCreator
{

    /**
     * @param $cmd
     * @return mixed
     */
    protected function getProcess($cmd)
    {
        return Injector::inst()->createWithArgs('AUCProcess', [$cmd]);
    }

}