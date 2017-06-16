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

export const SurveyTemplateQuestionSelector = ({onChange, className, defaultValue, items}) => {
    let options = [];

    for (let question of items) {
        options.push(<option key={question.id} value={question.id}>({question.step_name})&nbsp;{question.name}</option>)
    }

    return (
        <select defaultValue={defaultValue} className={className} onChange={onChange}>
            <option value="">--SELECT QUESTION --</option>
            {options}
        </select>
    );

}