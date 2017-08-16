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
import StoryItem from './StoryItem';
import StoryListItem from './StoryListItem';

export const StoryPanel = ({
	title,
	stories,
    distribution,
    panel_id,
    grouped
}) => (
	<div className="container">
        <div className={"story-panel " + (grouped ? 'grouped' : '')} style={{clear:'both'}} id={panel_id}>
            <h4>{title}</h4>
            <div className="items">
                {distribution == 'tiles' &&
                    <div className="row">
                        {stories.map((story, i) => (
                            <StoryItem key={i} story={story} />
                        ))}
                    </div>
                }
                {distribution == 'list' &&
                    <div className="list-group">
                        {stories.map((story, i) => (
                            <StoryListItem key={i} story={story} />
                        ))}
                    </div>
                }
            </div>
        </div>
	</div>
);

StoryPanel.defaultProps = {
	stories: []
};



export default StoryPanel;