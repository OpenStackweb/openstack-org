<?php

/**
 * Copyright 2017 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
final class FireBasePushNotificationSerializationStrategyFactory implements IFireBasePushNotificationSerializationStrategyFactory
{

    /**
     * @var IFireBasePushNotificationSerializationStrategyFactory
     */
    private $factories;
    /**
     * FireBasePushNotificationSerializationStrategyFactory constructor.
     */
    public function __construct()
    {
        // this is done in this way bc framework/control/injector/InjectionCreator.php
        // does not support pass array as constructor param
        $this->factories = func_get_args();
    }

    /**
     * @param IPushNotificationMessage $message
     * @return IFireBasePushNotificationSerializationStrategy;
     */
    function build(IPushNotificationMessage $message)
    {
       $strategy = null;
       foreach($this->factories as $factory){
           $strategy = $factory->build($message);
           if(!is_null($strategy)) break;
       }
       return $strategy;
    }
}