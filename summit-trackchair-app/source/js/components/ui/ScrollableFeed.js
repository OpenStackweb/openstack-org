import React from 'react';
import FullHeightScroller from './FullHeightScroller';
import BlockButton from './BlockButton';

export default ({
	children,
	onRequestMore,
	hasMore
}) => (
    <FullHeightScroller className="scrollable-feed">
    	<div className="ibox-content">
    		<div className="feed-activity-list">
    			{children}
		        {hasMore &&
		        	<BlockButton onButtonClicked={onRequestMore}>Load more</BlockButton>
		        }
    		</div>
    	</div>
    </FullHeightScroller>    
);