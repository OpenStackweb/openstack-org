import React from 'react';
import {connect} from 'react-redux';
import FeedItem from '../ui/FeedItem';
import ScrollableFeed from '../ui/ScrollableFeed';
import RouterLink from '../containers/RouterLink';

const PresentationList = ({
    presentations,
    hasMore,
    onRequestMore
}) => (
	<ScrollableFeed onRequestMore={onRequestMore} hasMore={hasMore}>
        {presentations.map(presentation => (
        	<RouterLink  link={`browse/${presentation.id}`} key={presentation.id}>
            	<FeedItem
            		key={presentation.id} 
            		active={presentation.active}
            		description={presentation.speakers}
            		notes={`Avg. vote: ${presentation.vote_average}`}
            		title={presentation.title} />
            </RouterLink>
        ))}
	</ScrollableFeed>
);

PresentationList.propTypes = {
	presentations: React.PropTypes.array,
	hasMore: React.PropTypes.bool,
	onRequestMore: React.PropTypes.func	
};

export default PresentationList;