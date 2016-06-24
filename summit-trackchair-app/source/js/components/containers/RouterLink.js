import React from 'react';
import { connect } from 'react-redux';
import { browserHistory } from 'react-router';
import LinkButton from '../ui/LinkButton';
import URL from '../../utils/url';

export default connect (
	(state, ownProps) => {		
		const currentURL = URL.trim(state.routing.locationBeforeTransitions.pathname);
		const thisURL = URL.trim(URL.create(ownProps.link.split('?')[0]));

		return {
			link: ownProps.link,
			onClick: ownProps.onClick,
			active: currentURL === thisURL
		}
	},
	dispatch => ({
		onLinkClicked (link) {
			browserHistory.push(URL.create(link));
		}
	})
)(LinkButton);