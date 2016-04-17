import React from 'react';
import GalleryItem from '../ui/GalleryItem';
import { connect } from 'react-redux';
import { routeActions } from 'react-router-redux';
import URL from '../../utils/url';
import Animate from 'react-addons-css-transition-group';

const VideoItem = ({
	video,
	onItemClicked
}) => (
	<Animate transitionName="vertical-fade" transitionAppear transitionAppearTimeout={1000} transitionEnterTimeout={1000} transitionLeaveTimeout={1000}>
		<GalleryItem
			className='gallery-item video-item'
			imageUrl={video.thumbnailURL}
			imageCaption={video.summit ? video.summit.title : ''}
			title={video.title}
			subtitle={video.speakers && video.speakers.map(s => s.name).join(', ')}
			link={URL.create(`video/${video.slug}`)}	
			onItemClicked={onItemClicked}
			badge={video.isNew ? 'new' : null}
		/>
	</Animate>
);

export default connect (
	state => ({

	}),
	dispatch => ({
		onItemClicked (link) {
			dispatch(routeActions.push(link));
		}
	})
)(VideoItem);
