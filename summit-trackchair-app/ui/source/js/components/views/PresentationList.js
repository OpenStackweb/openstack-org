import React from 'react';
import {connect} from 'react-redux';
import FeedItem from '../ui/FeedItem';
import ScrollableFeed from '../ui/ScrollableFeed';
import RouterLink from '../containers/RouterLink';
import URL from '../../utils/url';
import PresentationMetrics from '../views/PresentationMetrics';

const PresentationList = ({
    presentations,
    category,
    search,
    hasMore,
    onRequestMore
}) => (
	<ScrollableFeed onRequestMore={onRequestMore} hasMore={hasMore}>
        {presentations.map(presentation => {        	
        	let link = URL.addQueryParams(`browse/${presentation.id}`, {category, search});        	
        	const notes = <PresentationMetrics presentation={presentation} />
        	return (
	        	<RouterLink link={link} key={presentation.id}>
	            	<FeedItem
	            		className={`presentation-feed-item ${presentation.selected}`}
	            		key={presentation.id} 
	            		active={presentation.active}
	            		description={presentation.speakers}
	            		muted={!!presentation.viewed}
	            		notes={notes}
	            		title={presentation.title} />
	            </RouterLink>
            );
        })}
	</ScrollableFeed>
);

PresentationList.propTypes = {
	presentations: React.PropTypes.array,
	hasMore: React.PropTypes.bool,
	onRequestMore: React.PropTypes.func	
};

export default PresentationList;