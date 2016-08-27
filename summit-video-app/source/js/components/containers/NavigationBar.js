import React from 'react';
import { connect } from 'react-redux';
import { routeActions } from 'react-router-redux';
import LinkBar from '../ui/LinkBar';
import LinkButton from '../ui/LinkButton';
import URL from '../../utils/url';

const NavigationBar = ({
	newVideos,
	onLinkClicked,
	className,
	activeLink
}) => {
	const notificationBadge = newVideos.length ? <span className="badge">{newVideos.length}</span> : null;
	
	return (
		<LinkBar activeLink={activeLink} className={className} onLinkClicked={onLinkClicked}>
			<LinkButton link='featured'>Featured & Popular</LinkButton>
			<LinkButton link=''>All Videos {notificationBadge}</LinkButton>
			<LinkButton link='summits'>Summits</LinkButton>
			<LinkButton link='speakers'>Speakers</LinkButton>
		</LinkBar>
	);
};
export default connect (
	(state, ownProps) => {
		const activeLink = URL.makeRelative(state.router.location.pathname);
		return {
			className: ownProps.className,
			newVideos: state.videos.allVideos.results.filter(v => v.isNew),
			activeLink
		}
	},
	dispatch => ({
		onLinkClicked (link) {			
			dispatch(routeActions.push(URL.create(link || '/')));
		}
	})
)(NavigationBar);