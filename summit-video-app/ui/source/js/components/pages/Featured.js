import React from 'react';
import URL from '../../utils/url';
import { connect } from 'react-redux';
import VideoItem from '../containers/VideoItem';
import VideoPanel from '../containers/VideoPanel';
import FeatureImagePanel from '../ui/FeatureImagePanel';
import RouterLink from '../containers/RouterLink';
import Helmet from 'react-helmet';
import { 
	fetchFeaturedVideo, 
	fetchHighlightVideos, 
	fetchPopularVideos 
} from '../../actions';

class Featured extends React.Component {
	
	componentDidMount () {
		if(!this.props.featuredVideo) {
			this.props.fetchFeaturedVideo();
		}
		if(!this.props.popularVideos.length) {
			this.props.fetchPopularVideos();
		}
		if(!this.props.highlightedVideos.length) {
			this.props.requestHighlightedVideos();
		}
	}

	render () {
		if(this.props.loading) {
			return <div className="loading">
						<h1 data-text="loading…">loading…</h1>
					</div>
		}

		const {
			featuredVideo,
			popularVideos,
			highlightedVideos
		} = this.props;
		return (
			<div>
				<Helmet title="Featured videos" />
				{featuredVideo && featuredVideo.id &&
				<div className="container">
					<div className="row">
						<div className="col-sm-12">
							<RouterLink link={`video/${featuredVideo.id}`}>								
								<div className='featured-video'>	
									<h3>Don't Miss</h3>
									<FeatureImagePanel
										imageUrl={featuredVideo.thumbnailURL}
										title={featuredVideo.title}
										subtitle={featuredVideo.speakers.map(s => s.name).join(', ')}									
									 />								 
							</div>
							</RouterLink>							
						</div>
					</div>
				</div>
				}
					<div className="row">
						<div className="col-sm-12">
							<VideoPanel videos={highlightedVideos} title='More Highlighted Videos' />
						</div>
					</div>

					<div className="row">
						<div className="col-sm-12">
							<VideoPanel videos={popularVideos} title='Popular Videos' />
						</div>
					</div>
			</div>
		);
	}
}

export default connect (
	state => {
		const {popularVideos, highlightedVideos} = state.videos;
		const {featuredVideo} = state.video;
		return {
			loading: (featuredVideo && featuredVideo.loading) || popularVideos.loading || highlightedVideos.loading,
			featuredVideo,
			popularVideos: popularVideos.results,
			highlightedVideos: highlightedVideos.results
		}
	},
	dispatch => ({
		fetchFeaturedVideo () {
			dispatch(fetchFeaturedVideo());
		},		
		requestHighlightedVideos () {
			dispatch(fetchHighlightVideos());
		},
		fetchPopularVideos () {
			dispatch(fetchPopularVideos());
		}
	})
)(Featured);