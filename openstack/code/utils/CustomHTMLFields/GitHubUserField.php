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

class GitHubUserField extends TextField
{
    public function validate($validator) {

        if ($this->value == '') return true;

        $service = new RestfulService('https://api.github.com');
        $response = $service->request('/users/'.$this->value);

        $status_code = $response->getStatusCode();
        $user_data   = json_decode($response->getBody());

        if($status_code == 200 && $user_data->id) {
            return true;
        } else {
            $validator->validationError(
                $this->name, "Please enter a valid GitHub username.", "validation", false
            );

            return false;
        }
    }
}