import React from 'react';
import { connect } from 'react-redux';
import MainNav from '../containers/MainNav';
import NotificationDropdown from '../containers/NotificationDropdown';

const NavigationBar = ({
	onLinkClicked,
	className,
	activeLink
}) => {

	return (
        <nav className="navbar navbar-static-top" role="navigation">
            <div className="navbar-header">
                <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" className="navbar-toggle collapsed" type="button">
                    <i className="fa fa-reorder"></i>
                </button>
                <a href="#" className="navbar-brand">Track Chairs App</a>
            </div>
            <div className="navbar-collapse collapse" id="navbar">
				<MainNav />
				<ul className="nav navbar-top-links navbar-right">
					<NotificationDropdown />
					<li>
						<a href="login.html">
							<i className="fa fa-sign-out"></i>
						</a>
					</li>
				</ul>
            </div>
        </nav>

	);
};

export default NavigationBar;