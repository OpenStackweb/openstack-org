<?php

/**
 * Copyright 2015 OpenStack Foundation
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
interface ISecurityGroupFactory
{
    const UsersGroupCode = 'users';

    const SpeakersGroupCode = 'speakers';

    const SuperAdmins = 'super-admins';

    const Administrators = "administrators";

    /**
     * @param string$code
     * @param null|string $title
     * @param null|string $description
     * @return ISecurityGroup
     */
    public function build($code, $title = null, $description = null);
}