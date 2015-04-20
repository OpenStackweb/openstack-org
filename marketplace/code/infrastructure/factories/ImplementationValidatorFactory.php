<?php
/**
 * Copyright 2015 Openstack Foundation
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

class ImplementationValidatorFactory extends ValidatorFactory {

    public function buildValidatorForCompanyService(array $data)
    {
        $rules = array(
            'name'              => 'required|text',
            'overview'          => 'required|text|max:250',
            'active'            => 'required|boolean',
            'company_id'        => 'required|integer',
            'call_2_action_uri' => 'required|url',
            'compatible_compute'            => 'required|boolean',
            'compatible_storage'            => 'required|boolean',
            'compatible_federated_identity' => 'required|boolean',
        );

        $messages = array(
            'name.required'              => ':attribute is required',
            'name.text'                  => ':attribute should be valid text.',
            'overview.required'          => ':attribute is required',
            'overview.text'              => ':attribute should be valid text.',
            'overview.max'               => ':attribute should have less than 250 chars.',
            'call_2_action_uri.required' => ':attribute is required',
            'call_2_action_uri.url'      => ':attribute should be valid url.',
            'active.required'            => ':attribute is required',
            'active.boolean'             => ':attribute should be valid boolean value',
            'company_id.required'        => ':attribute is required',
            'company_id.boolean'         => ':attribute should be valid integer value',
        );

        return ValidatorService::make($data, $rules, $messages);
    }
}