import React from 'react';
import { connect } from 'react-redux';
import { fetchSummitVideos } from '../../actions';
import DateGroupedVideoList from '../views/DateGroupedVideoList';
import RouterLink from '../containers/RouterLink';
import Loader from '../ui/Loader';
import BlockButton from '../ui/BlockButton';
import Helmet from 'react-helmet';

class SummitDetail extends React.Component {

	constructor (props) {
		super(props);
		this.loadMoreVideos = this.loadMoreVideos.bind(this);
	}

	componentDidMount () {
		const {summit} = this.props.videos;

		if(!summit || summit.id != this.props.params.id) {
			this.props.requestVideos(0);	
		}
	}

	componentWillReceiveProps(nextProps) {
		if(nextProps.params.id !== this.props.params.id) {
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
				{this.props.summit &&
					<Helmet title={this.props.summit.title} />
				}
				{this.props.summit &&
				<div className="container">
					<div className="row">
						<div className="col-sm-12 video-breadcrumbs">
							<RouterLink link='summits'>All summits</RouterLink> > 
							<a href="#" className="active">{this.props.summit.title}</a>
						</div>
					</div>
					<div className="row">
						<div className="col-sm-12">
							<h3>{this.props.summit.title}</h3>
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
		const {summitVideos} = state.videos;
		return {
			summit: summitVideos.summit,
			videos: summitVideos.results,
			loading: summitVideos.loading,
			hasMore: summitVideos.has_more,
			total: summitVideos.total
		}
	},
	(dispatch, ownProps) => {
		return {
			requestVideos (start = 0) {
				dispatch(fetchSummitVideos(ownProps.params.id, start));
			}
		}
	}
)(SummitDetail);