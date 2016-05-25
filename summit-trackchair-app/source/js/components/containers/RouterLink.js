import React from 'react';
import { connect } from 'react-redux';
import { routeActions } from 'react-router-redux';
import LinkButton from '../ui/LinkButton';
import URL from '../../utils/url';

export default connect (
	(state, ownProps) => {		
		return {
			link: ownProps.link,
			onClick: ownProps.onClick,
			active: state.routing.locationBeforeTransitions.pathname === URL.create(ownProps.link)
		}
	},
	dispatch => ({
		onLinkClicked (link) {
			dispatch(routeActions.push(URL.create(link)));
		}
	})
)(LinkButton);