import React from 'react';
import GalleryItem from '../ui/GalleryItem';
import { connect } from 'react-redux';
import { routeActions } from 'react-router-redux';
import URL from '../../utils/url';

const SpeakerItem = ({
	speaker,
	onItemClicked
}) => (
	<GalleryItem
		className='gallery-item speaker-item'
		imageUrl={speaker.imageURL}	
		imageWidth={263}
		imageHeight={148}	
		title={`${speaker.name} (${speaker.videoCount} videos)`}
		subtitle={speaker.jobTitle || ''}
		link={URL.create(`speakers/show/${speaker.id}`)}
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
)(SpeakerItem);
