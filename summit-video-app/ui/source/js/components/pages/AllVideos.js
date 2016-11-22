import React from 'react';
import URL from '../../utils/url';
import { connect } from 'react-redux';
import VideoItem from '../containers/VideoItem';
import GalleryPanel from '../ui/GalleryPanel';
import BlockButton from '../ui/BlockButton';
import Loader from '../ui/Loader';
import moment from 'moment';
import DateGroupedVideoList from '../views/DateGroupedVideoList';
import { fetchAllVideos } from '../../actions';
import Helmet from 'react-helmet';

class AllVideos extends React.Component {
	
	constructor (props) {
		super(props);
		this.loadMoreVideos = this.loadMoreVideos.bind(this);
	}

	componentDidMount () {
		if(!this.props.videos.length) {
			this.props.fetchVideos(0);
		}
	}

	loadMoreVideos (e) {
		e.preventDefault();
		this.props.fetchVideos(
			this.props.videos.length
		)
	}

	render () {
		if(this.props.loading && !this.props.videos.length) {
			return <Loader />
		}
		return (
			<div>	
				<Helmet title="All videos" />
				<DateGroupedVideoList videos={this.props.videos} />
				{this.props.hasMore && !this.props.loading &&
					<BlockButton onButtonClicked={this.loadMoreVideos} className="more-btn">
						More videos
					</BlockButton>
				}
				{this.props.loading && <Loader />}
			</div>
		);
	}
}

export default connect (
	state => {
		const {allVideos} = state.videos;
		return {
			loading: allVideos.loading,
			videos: allVideos.results,
			hasMore: allVideos.has_more,
			total: allVideos.total
		}
	},
	dispatch => ({
		fetchVideos (start = 0) {
			dispatch(fetchAllVideos({start}));
		}
	})
)(AllVideos);