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
 * Class SilverStripeSessionWrapper
 */
final class SilverStripeSessionWrapper extends Auth_Yadis_PHPSession {

    /**
     * Set a session key/value pair.
     *
     * @param string $name The name of the session key to add.
     * @param string $value The value to add to the session.
     */
    public function set($name, $value) {
        Session::set($name, $value);
    }


    /**
     * Get a key's value from the session.
     *
     * @param string $name The name of the key to retrieve.
     * @param string $default The optional value to return if the key
     * is not found in the session.
     * @return string $result The key's value in the session or
     * $default if it isn't found.
     */
    public function get($name, $default=null) {
        $value = Session::get($name);
        if(is_null($value))
            $value = $default;

        return $value;
    }


    /**
     * Remove a key/value pair from the session.
     *
     * @param string $name The name of the key to remove.
     */
    public function del($name) {
        Session::clear($name);
    }


    /**
     * Return the contents of the session in array form.
     */
    public function contents() {
        return Session::get_all();
    }

}