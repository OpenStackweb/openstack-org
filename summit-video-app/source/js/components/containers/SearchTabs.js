import React from 'react';
import LinkButton from '../ui/LinkButton';
import { connect } from 'react-redux';
import { updateSearchTab } from '../../actions';

const SearchTabs = ({
	changeTab,
	titleMatches,
	speakerMatches,
	topicMatches,
	selected
}) => (
	<ul className="search-tabs">
		{titleMatches.length > 0 && 
			<li className={selected === 'titleMatches' ? 'active' : ''}>
				<LinkButton link='#' eventKey='titleMatches' onLinkClicked={changeTab}>
					Videos <span className="count">{titleMatches.length}</span>
				</LinkButton>
			</li>
		}
		{speakerMatches.length > 0 &&
			<li className={selected === 'speakerMatches' ? 'active' : ''}>
				<LinkButton link='#' eventKey='speakerMatches' onLinkClicked={changeTab}>
					Speakers <span className="count">{speakerMatches.length}</span>
				</LinkButton>
			</li>
		}
		{topicMatches.length > 0 &&
			<li className={selected === 'topicMatches' ? 'active' : ''}>
				<LinkButton link='#' eventKey='topicMatches' onLinkClicked={changeTab}>
					Topics <span className="count">{topicMatches.length}</span>
				</LinkButton>
			</li>
		}
	</ul>
)

export default connect (
	state => {
		const {searchVideos} = state.videos;
		const {results} = searchVideos;

		return {
			titleMatches: results ? results.titleMatches : [],
			speakerMatches: results ? results.speakerMatches : [],
			topicMatches: results ? results.topicMatches : [],
			selected: searchVideos.activeTab	
		}
	},
	dispatch => ({
		changeTab (key) {
			dispatch(updateSearchTab(key));
		}
	})
)(SearchTabs);

