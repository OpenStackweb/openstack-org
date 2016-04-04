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
interface ICertifiedOpenStackAdministratorExam extends IEntity
{
    /**
     * @param string $status
     * @param string $pass_date
     * @param string $cert_nbr
     * @param string $code
     * @param string $modified_date
     * @return $this
     */
    public function update($status, $pass_date, $cert_nbr, $code, $modified_date);
}