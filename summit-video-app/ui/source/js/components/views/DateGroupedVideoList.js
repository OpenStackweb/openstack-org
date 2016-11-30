import React from 'react';
import VideoPanel from '../containers/VideoPanel';
import moment from 'moment';
import groupedList from '../../utils/groupedList';

export default ({
	videos,
	hasMore
}) => {
	const formatTitle = function (date) {	
		let today = moment().format('YYYY-MM-DD');
		if(moment(date).format('YYYY-MM-DD') === today) {
			return 'Uploaded today';
		}
		else if(moment(date).add(1, 'days').format('YYYY-MM-DD') === today) {
			return 'Uploaded yesterday';
		}

		return 'Uploaded ' + moment(date).format('MMMM D, YYYY');		
	}
	const groupedVideos = groupedList(videos, 'date');

	return (		
		<div>
			{groupedVideos.map((group,i) => (
				<VideoPanel key={i} videos={group} title={formatTitle(group[0].date)} />				
			))}
		</div>
	);
	
};