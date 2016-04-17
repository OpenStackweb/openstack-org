import React from 'react';
import GalleryPanel from '../ui/GalleryPanel';
import VideoItem from './VideoItem';
import FadeAnimation from '../ui/FadeAnimation';

export const VideoPanel = ({
	title,
	videos
}) => (
	<div className="container">
		<GalleryPanel title={title} className='video-panel'>
				<div className="row">
					<FadeAnimation>
						{videos.map((video, i) => (
							<VideoItem key={i} video={video} />
						))}
					</FadeAnimation>
				</div>
		</GalleryPanel>
	</div>
);

VideoPanel.defaultProps = {
	videos: []
};

export default VideoPanel;