<?php

/**
 * Copyright 2016 OpenStack Foundation
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
class GetTextSession extends Session {

    /**
     * Current session
     *
     * @var Session
     */
    protected static $old_session = null;

    /**
     * Allows session to be temporarily injected into default_session prior to
     * the existence of a controller
     */
    public static function with_session(Session $session, $callback) {
        self::$old_session = self::$default_session;
        self::$default_session = $session;
        try {
            $callback();
        } catch(Exception $ex) {
            self::$default_session = self::$old_session;
            throw $ex;
        }
        self::$default_session = self::$old_session;
    }

}
