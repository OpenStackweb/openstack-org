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
import StoryPanel from '../containers/StoryPanel';
import moment from 'moment';
import groupedList from '../../utils/groupedList';

export default ({
	stories,
	distribution,
    group_by
}) => {
	const formatTitle = function (date) {
		return moment(date).format('MMMM D, YYYY');
	}

    const getTitle = function(group_by, group) {
        if (group_by == 'date') {
            return formatTitle(group[0].date);
        } else if (group_by == 'search') {
            return 'Search Results';
        }

        return group[0][group_by];
    }

	const groupedStories = groupedList(stories, group_by);

	return (		
		<div>
			{groupedStories.map((group,i) => (
				<StoryPanel
                    key={i}
                    stories={group}
                    distribution={distribution}
                    title={getTitle(group_by, group)}
                />
			))}
		</div>
	);
	
};