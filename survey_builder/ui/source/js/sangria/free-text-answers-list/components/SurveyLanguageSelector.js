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
import React from 'react';
import Select from 'react-select';
import 'react-select/dist/react-select.css';

export const SurveyLanguageSelector = ({onChange, className, defaultValue, items}) => {
    let options = [];

    for (let language of items) {
        options.push({value: language, label: language});
    }

    return (
        <Select
            name="language-dropdown"
            placeholder="--SELECT LANGUAGE --"
            value={defaultValue}
            options={options}
            multi={true}
            simpleValue={true}
            onChange={onChange}
        />
    );

}