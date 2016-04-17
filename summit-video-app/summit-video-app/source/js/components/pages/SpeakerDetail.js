import React from 'react';
import { connect } from 'react-redux';
import { fetchSpeakerVideos } from '../../actions';
import DateGroupedVideoList from '../views/DateGroupedVideoList';
import Loader from '../ui/Loader';
import BlockButton from '../ui/BlockButton';
import RouterLink from '../containers/RouterLink';
import Helmet from 'react-helmet';

class SpeakerDetail extends React.Component {

	constructor (props) {
		super(props);
		this.loadMoreVideos = this.loadMoreVideos.bind(this);
	}

	componentDidMount () {
		const {speaker} = this.props.videos;

		if(!speaker || speaker.id != this.props.params.id) {
			this.props.fetchVideos(0);	
		}
	}

	componentWillReceiveProps(nextProps) {
		if(nextProps.params.id !== this.props.params.id) {
			this.props.fetchVideos(0);
		}
	}

	loadMoreVideos (e) {
		e.preventDefault();
		this.props.fetchVideos(
			this.props.videos.length
		);
	}	

	render () {
		if(this.props.loading && !this.props.videos.length) {
			return <Loader />
		}
		return (
			<div>				
				{this.props.speaker &&
					<Helmet title={this.props.speaker.name} />
				}
				{this.props.speaker &&
				<div className="container">					
					<div className="row">
						<div className="col-sm-12 video-breadcrumbs">
							<RouterLink link='speakers'>All speakers</RouterLink> > 
							<a href="#" className="active">{this.props.speaker.name}</a>
						</div>
					</div>
					<div className="row">
						<div className="col-sm-12">
							<h3>{this.props.speaker.name}</h3>
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
		const {speakerVideos} = state.videos;	
		return {
			speaker: speakerVideos.speaker,
			videos: speakerVideos.results,
			loading: speakerVideos.loading,
			hasMore: speakerVideos.has_more,
			total: speakerVideos.total
		}
	},
	(dispatch, ownProps) => {
		return {
			fetchVideos (start = 0) {
				dispatch(fetchSpeakerVideos(
					ownProps.params.id,
					start
				));
			}
		}
	}
)(SpeakerDetail);