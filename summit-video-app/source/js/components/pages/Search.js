import React from 'react';
import URL from '../../utils/url';
import { connect } from 'react-redux';
import VideoItem from '../containers/VideoItem';
import VideoPanel from '../containers/VideoPanel';
import LinkButton from '../ui/LinkButton';
import SearchTabs from '../containers/SearchTabs';
import Loader from '../ui/Loader';
import { fetchSearchVideos, updateSearchText } from '../../actions';
import Helmet from 'react-helmet';

class Search extends React.Component {
	
	componentDidMount () {
		this.props.requestVideos(this.props.searchTerm);
		this.props.updateSearchText(this.props.searchTerm);
	}

	componentWillReceiveProps (nextProps) {
		if(nextProps.searchTerm !== this.props.searchTerm) {
			this.props.requestVideos(nextProps.searchTerm);
		}
	}

	render () {
		const term = this.props.searchTerm;
		const {activeTab} = this.props;

		return (
			<div>
				<Helmet title={`Search results for ${this.props.searchTerm}`} />`
				<div className="container">
					<div className="row">
						<div className="col-sm-12">
							<SearchTabs />
						</div>
					</div>
				</div>
				{() => {

					if(this.props.loading || !this.props.videos) {
						return <Loader />
					}

					const {
						titleMatches,
						speakerMatches,
						topicMatches
					} = this.props.videos;

					if(activeTab === 'titleMatches') {
						return (
							<VideoPanel 
								title={`Videos matching title "${term}"`}
								videos={titleMatches}
						 	/>					
						);
					}
					if(activeTab === 'speakerMatches') {
						return (
							<VideoPanel 
								title={`Videos matching speakers named "${term}"`} 
								videos={speakerMatches}
							 />
						);
					}
					if(activeTab === 'topicMatches') {
						return (
							<VideoPanel 
								title={`Videos matching topic "${term}"`}
								videos={topicMatches}
							 />
						);						
					}
				}()}
			</div>
		);
	}
}

export default connect (
	state => {
		const {searchVideos} = state.videos;
		return {
			loading: searchVideos.loading,
			videos: searchVideos.results,
			searchTerm: state.router.location.query.search,
			activeTab: searchVideos.activeTab
		}
	},
	(dispatch, ownProps) => ({
		requestVideos (term) {
			dispatch(fetchSearchVideos(term));
		},
		updateSearchText (term) {
			dispatch(updateSearchText(term));
		}
	})
)(Search);