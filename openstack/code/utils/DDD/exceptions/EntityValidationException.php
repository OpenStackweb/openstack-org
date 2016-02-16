<?php
/**
 * Copyright 2014 Openstack Foundation
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

/**
 * Class EntityValidationException
 */
class EntityValidationException extends Exception
{

    /**
     * @var array
     */
    private $messages;

    public function __construct($messages)
    {

        if (is_array($messages)) {
            $this->messages = $messages;
        }
        if (is_string($messages)) {
            $this->messages = self::buildMessage($messages);
        }


        parent::__construct($this->__toString());
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    public function __toString()
    {
        $str = '';
        foreach ($this->messages as $msg) {
            if (is_array($msg) && isset($msg['message'])) {
                $str .= '* ' . $msg['message'];
            }
            if (is_string($msg)) {
                $str .= '* ' . $msg;
            }
        }

        return $str;
    }

    /**
     * @param string $message
     * @return array
     */
    public static function buildMessage($message)
    {
        return array(
            array('message' => $message)
        );
    }

}