<?php

namespace OpenStack\AUC;

use \Injector;

/**
 * Class ParserCreator
 * @package OpenStack\AUC
 */
trait ParserCreator
{

    /**
     * @param $filePath
     * @return mixed
     */
    protected function getParser($filePath)
    {
        return Injector::inst()->createWithArgs('AUCCSVParser', [$filePath, ',', "'"]);
    }

}