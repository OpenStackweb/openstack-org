import React from 'react';
import { connect } from 'react-redux';
import { routeActions } from 'react-router-redux';
import URL from '../../utils/url';
import PreviewImagePanel from '../ui/PreviewImagePanel';
import moment from 'moment';

const LatestUploadPanel = ({
	video,
	onLinkClicked
}) => (
	<PreviewImagePanel
		className='latest-upload'
		imageUrl={video.thumbnailURL}
		title={video.title}
		subtitle={`
			${moment(video.date).format('MMMM D, YYYY')} | 
			${video.speakers.map(s => s.name).join(', ')} 
		`}
		link={URL.create(`video/${video.slug}`)}
		onLinkClicked={onLinkClicked}
	 />
);

export default connect (
	(state, ownProps) => ({
	}),
	dispatch => ({
		onLinkClicked (link) {
			dispatch(routeActions.push(link));
		}
	})
)(LatestUploadPanel);