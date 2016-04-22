import React from 'react';
import GalleryItem from '../ui/GalleryItem';
import { connect } from 'react-redux';
import { routeActions } from 'react-router-redux';
import URL from '../../utils/url';

const SummitItem = ({
	summit,
	onItemClicked
}) => (
	<GalleryItem
		className='gallery-item summit-item'
		imageUrl={summit.imageURL}
		imageWidth={263}
		imageHeight={148}			
		title={summit.title}
		subtitle={summit.dates}
		link={URL.create(`summits/show/${summit.id}`)}
		onItemClicked={onItemClicked}
	/>
);

export default connect (
	null,
	dispatch => ({
		onItemClicked (link) {
			dispatch(routeActions.push(link));
		}
	})
)(SummitItem);
