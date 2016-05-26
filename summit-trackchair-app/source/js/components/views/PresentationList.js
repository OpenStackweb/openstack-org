import React from 'react';
import {connect} from 'react-redux';
import FullHeightScroller from '../ui/FullHeightScroller';
import FeedItem from '../ui/FeedItem';
import BlockButton from '../ui/BlockButton';
import RouterLink from '../containers/RouterLink';

const PresentationList = ({
    presentations,
    hasMore,
    onRequestMore
}) => (
    <FullHeightScroller>
    	<div className="ibox-content">
    		<div className="feed-activity-list">
		        {presentations.map(presentation => (
		        	<RouterLink  link={`browse/${presentation.id}`}>
		            	<FeedItem
		            		key={presentation.id} 
		            		active={presentation.active}
		            		description={presentation.speakers}
		            		notes={`Avg. vote: ${presentation.vote_average}`}
		            		title={presentation.title} />
		            </RouterLink>
		        ))}
		        {hasMore &&
		        	<BlockButton onButtonClicked={onRequestMore}>Load more</BlockButton>
		        }
    		</div>
    	</div>
    </FullHeightScroller>    
);

PresentationList.propTypes = {
	presentations: React.PropTypes.array	
}

export default PresentationList;