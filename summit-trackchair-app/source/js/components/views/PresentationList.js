import React from 'react';
import {connect} from 'react-redux';
import FullHeightScroller from '../ui/FullHeightScroller';
import ListItem from '../ui/ListItem';
import BlockButton from '../ui/BlockButton';
import RouterLink from '../containers/RouterLink';

const PresentationList = ({
    presentations,
    hasMore,
    onRequestMore
}) => (
    <FullHeightScroller>
        {presentations.map(presentation => (
        	<RouterLink key={presentation.id}  link={`browse/${presentation.id}`}>
            	<ListItem active={presentation.active} title={presentation.title} />
            </RouterLink>
        ))}
        {hasMore &&
        	<BlockButton onButtonClicked={onRequestMore}>Load more</BlockButton>
        }
    </FullHeightScroller>    
);

PresentationList.propTypes = {
	presentations: React.PropTypes.array	
}

export default PresentationList;