import React from 'react';
import { connect } from 'react-redux';
import MainNav from '../containers/MainNav';
import NotificationDropdown from '../containers/NotificationDropdown';
import {toggleMobileMenu} from '../../actions';

const NavigationBar = ({
	onLinkClicked,
	className,
	activeLink,
	collapsed,
	toggleMobileMenu
}) => {
	let summitName = window.TrackChairAppConfig.summitName;
	let selectionLink = window.TrackChairAppConfig.selectionLink;
	return (
        <nav className="navbar navbar-static-top" role="navigation">
            <div className="navbar-header">
                <button onClick={toggleMobileMenu} className="navbar-toggle collapsed" type="button">
                    <i className="fa fa-reorder"></i>
                </button>
				<span className="navbar-brand"><a href={selectionLink}>Track Chairs App - {summitName}</a></span>
            </div>
            <div className={`navbar-collapse ${collapsed ? 'collapse' : ''}`} id="navbar">
				<MainNav />
				<ul className="nav navbar-top-links navbar-right">
					{/*<NotificationDropdown />*/}
				</ul>
            </div>
        </nav>

	);
};

export default connect(
	state => ({
		collapsed: !state.main.mobileMenu
	}),

	dispatch => ({
		toggleMobileMenu(e) {
			e.preventDefault();
			dispatch(toggleMobileMenu())
		}
	})
)(NavigationBar);