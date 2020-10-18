/**
 * Copyright 2020 Open Infrastructure Foundation
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
import Item from './Item';
import ListItem from './ListItem';
import MarketplaceItem from './MarketplaceItem';
import MarketplaceListItem from './MarketplaceListItem';

export const ListPanel = ({
	title,
	items,
    distribution,
    panel_id,
    grouped
}) => (
	<div className="container">
        <div className={"list-panel " + (grouped ? 'grouped' : '')} style={{clear:'both'}} id={panel_id}>
            <h4>{title}</h4>
            <div className="items row row-eq-height">
                {distribution == 'tiles' &&
                    <div className="grid">
                        {items.map((item, i) => (
                            <Item key={i} item={item} />
                        ))}
                        <MarketplaceItem />
                    </div>
                }
                {distribution == 'list' &&
                    <div className="list">
                        {items.map((item, i) => (
                            <ListItem key={i} item={item} />
                        ))}
                        <MarketplaceListItem />
                    </div>
                }
            </div>
        </div>
	</div>
);

ListPanel.defaultProps = {
	items: []
};



export default ListPanel;