import React from 'react';
import { connect } from 'react-redux';
import { routeActions } from 'react-router-redux';
import LinkButton from '../ui/LinkButton';
import URL from '../../utils/url';

export default connect (
	(state, ownProps) => ({
		link: ownProps.link,
		onClick: ownProps.onClick
	}),
	dispatch => ({
		onLinkClicked (link) {
			dispatch(routeActions.push(URL.create(link)));
		}
	})
)(LinkButton);