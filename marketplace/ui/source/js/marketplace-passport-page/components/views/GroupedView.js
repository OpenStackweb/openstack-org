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
import ListPanel from '../containers/ListPanel';
import groupedList from "~core-utils/groupedList";
import {formatTextForHash} from '../../actions';

export default ({
	items,
	distribution,
    group_by
}) => {

    const getTitle = function(group_by, group) {
        return group[0][group_by];
    }

	const groupedItems = groupedList(items, group_by);

	return (		
		<div>
			{groupedItems.map((group,i) => (
				<ListPanel
                    key={i}
                    items = {group}
                    distribution = {distribution}
                    title = {getTitle(group_by, group)}
                    panel_id = {formatTextForHash(getTitle(group_by, group))}
                    grouped = {true}
                />
			))}
		</div>
	);
	
};