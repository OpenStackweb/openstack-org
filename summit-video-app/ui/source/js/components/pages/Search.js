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

		return (
			<div>
				<Helmet title={`Search results for ${this.props.searchTerm}`} />`
				{(() => {

					if(this.props.loading || !this.props.videos) {
						return <Loader />
					}

                    return (
                        <VideoPanel
                            title={`Videos matching title "${term}"`}
                            videos={this.props.videos}
                        />
                    );

				})()}
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