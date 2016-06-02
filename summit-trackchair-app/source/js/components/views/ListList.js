import React from 'react';
import {connect} from 'react-redux';
import FeedItem from '../ui/FeedItem';
import ScrollableFeed from '../ui/ScrollableFeed';
import RouterLink from '../containers/RouterLink';
import URL from '../../utils/url';

const ListList = ({
    lists,
    category
}) => (
	<ScrollableFeed hasMore={false}>
        {lists.map(list => (
        	<RouterLink link={URL.addQueryParams(`selections/${list.id}`, {category})} key={list.id}>
            	<FeedItem
            		key={list.id} 
            		active={list.active}
            		notes={`${list.total} of ${list.slots}`}
            		description='Description here'
            		title={list.list_name} 
            		/>
            </RouterLink>
        ))}
	</ScrollableFeed>
);

ListList.propTypes = {
	lists: React.PropTypes.array	
};

export default ListList;