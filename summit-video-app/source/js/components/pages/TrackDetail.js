import React from 'react';
import { connect } from 'react-redux';
import { fetchTrackVideos } from '../../actions';
import DateGroupedVideoList from '../views/DateGroupedVideoList';
import RouterLink from '../containers/RouterLink';
import Loader from '../ui/Loader';
import BlockButton from '../ui/BlockButton';
import Helmet from 'react-helmet';

class TrackDetail extends React.Component {

	constructor (props) {
		super(props);
		this.loadMoreVideos = this.loadMoreVideos.bind(this);
	}

	componentDidMount () {
		const {track} = this.props.videos;

		if(!track || track.slug != this.props.params.slug) {
			this.props.requestVideos(0);	
		}
	}

	componentWillReceiveProps(nextProps) {
		if(nextProps.params.slug !== this.props.params.slug) {
			this.props.requestVideos(0);
		}
	}

	loadMoreVideos (e) {
		this.props.requestVideos(
			this.props.videos.length
		)
	}

	render () {
		if(this.props.loading && !this.props.videos.length) {
			return <Loader />;
		}
		return (
			<div>
				{this.props.track &&
					<Helmet title={this.props.track.title} />
				}
				{this.props.track &&
				<div className="container">
					<div className="row">
						<div className="col-sm-12 video-breadcrumbs">
							<RouterLink link='videos'>All videos</RouterLink> > &nbsp; TRACKS &nbsp; >
							<a href="#" className="active">{this.props.track.title}</a>
						</div>
					</div>
					<div className="row">
						<div className="col-sm-12">
							<h3>Track: {this.props.track.title}</h3>
						</div>
					</div>
				</div>
				}
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
	(state, ownProps) => {
		const {trackVideos} = state.videos;
		return {
			track: trackVideos.track,
			videos: trackVideos.results,
			loading: trackVideos.loading,
			hasMore: trackVideos.has_more,
			total: trackVideos.total
		}
	},
	(dispatch, ownProps) => {
		return {
			requestVideos (start = 0) {
				dispatch(fetchTrackVideos(ownProps.params.summit, ownProps.params.slug, start));
			}
		}
	}
)(TrackDetail);