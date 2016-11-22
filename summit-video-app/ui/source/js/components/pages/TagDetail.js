import React from 'react';
import { connect } from 'react-redux';
import { fetchTagVideos } from '../../actions';
import DateGroupedVideoList from '../views/DateGroupedVideoList';
import RouterLink from '../containers/RouterLink';
import Loader from '../ui/Loader';
import BlockButton from '../ui/BlockButton';
import Helmet from 'react-helmet';

class TagDetail extends React.Component {

	constructor (props) {
		super(props);
		this.loadMoreVideos = this.loadMoreVideos.bind(this);
	}

	componentDidMount () {
		const {tag} = this.props.videos;

		if(!tag || tag.tag != this.props.params.tag) {
			this.props.requestVideos(0);	
		}
	}

	componentWillReceiveProps(nextProps) {
		if(nextProps.params.tag !== this.props.params.tag) {
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
				{this.props.tag &&
					<Helmet title={this.props.tag.tag} />
				}
				{this.props.tag &&
				<div className="container">
					<div className="row">
						<div className="col-sm-12 video-breadcrumbs">
							<RouterLink link='videos'>All videos</RouterLink> > &nbsp; TAGS &nbsp; >
							<a href="#" className="active">{this.props.tag.tag}</a>
						</div>
					</div>
					<div className="row">
						<div className="col-sm-12">
							<h3>Tag: {this.props.tag.tag}</h3>
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
		const {tagVideos} = state.videos;
		return {
			tag: tagVideos.tag,
			videos: tagVideos.results,
			loading: tagVideos.loading,
			hasMore: tagVideos.has_more,
			total: tagVideos.total
		}
	},
	(dispatch, ownProps) => {
		return {
			requestVideos (start = 0) {
				dispatch(fetchTagVideos(ownProps.params.tag, start));
			}
		}
	}
)(TagDetail);