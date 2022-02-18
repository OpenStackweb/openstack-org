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

export default ({
	stories,
	distribution,
    group_by
}) => {

    const getTitle = function(group_by) {
        if (group_by == 'search') {
            return 'Search Results';
        }

        return '';
    }

    const sortStories = function(stories, compareProp) {
        debugger;
        compareProp = (compareProp == 'search') ? 'date' : compareProp;

        let sorted_stories = stories.sort((a,b) => {
            const aName = a[compareProp];
            const bName = b[compareProp];

            if (compareProp == 'date') { // date must be DESC
                return aName < bName ? 1 : (aName > bName ? -1 : 0)
            } else {
                return aName > bName ? 1 : (aName < bName ? -1 : 0)
            }
        });

        return sorted_stories;
    }

	return (
		<div>
            <StoryPanel
                stories={sortStories(stories,group_by)}
                distribution={distribution}
                title={getTitle(group_by)}
            />
		</div>
	);
	
};