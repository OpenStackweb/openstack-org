import React from 'react';
import {connect} from 'react-redux';
import URL from '../../utils/url';
import LinkBar from '../ui/LinkBar';
import LinkButton from '../ui/LinkButton';
import {browserHistory} from 'react-router';

const MainNav = ({
	activeLink,
	onLinkClicked
}) => (
	<LinkBar activeLink={activeLink} onLinkClicked={onLinkClicked} className="nav navbar-nav" component="UL">                     
        <LinkButton link="">
        	<i className="fa fa-list"></i>
        	<span className="nav-label">Browse</span>
        </LinkButton>
        <LinkButton link="selections">
        	<i className="fa fa-check-square"></i>
        	<span className="nav-label">Selections</span>
        </LinkButton>
        <LinkButton link="directory">
        	<i className="fa fa-sort-alpha-asc"></i>
        	<span className="nav-label">Directory</span>
        </LinkButton>
        <LinkButton link="change-requests">
        	<i className="fa fa-edit"></i>
        	<span className="nav-label">Change Requests</span>
        </LinkButton>
        {/*
        <LinkButton link="help">
        	<i className="fa fa-question-circle"></i>
        	<span className="nav-label">Help</span>
        </LinkButton>
		*/}
    </LinkBar>
);

export default connect (
	(state, ownProps) => {
		const activeLink = URL.makeRelative(state.routing.locationBeforeTransitions.pathname);
		return {
			activeLink
		}
	},
	dispatch => ({
		onLinkClicked (link) {		
			browserHistory.push(URL.create(link || '/'));
		}
	})

)(MainNav);