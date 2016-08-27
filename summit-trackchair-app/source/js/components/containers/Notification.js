import React from 'react';
import { connect } from 'react-redux';
import { clearNotification } from '../../actions';
import VerticalFade from '../ui/FadeAnimation';
import RouterLink from './RouterLink';

class Notification extends React.Component {

	constructor (props) {
		super(props);
		this._timeout = null;
		this.clearNotificationTimeout = this.clearNotificationTimeout.bind(this);
	}
	
	clearNotificationTimeout () {
		if(this._timeout) {
			window.clearTimeout(this._timeout);
		}
		this._timeout = window.setTimeout(this.props.clearNotification, 5000);
	}

	render () {
		const {notification} = this.props;
		return (
			<VerticalFade>
			{notification && 
				<div className="video-notification" ref={this.clearNotificationTimeout}> 
					<RouterLink onClick={this.props.clearNotification} link={notification.link}>
						{notification.content}
					</RouterLink>
					<span onClick={this.props.clearNotification}>&times;</span>
				</div>
			}
			</VerticalFade>		
		);
	}

}

export default connect (
	state => ({
		notification: state.main.notification
	}),

	dispatch => ({
		clearNotification () {
			dispatch(clearNotification());
		}
	})
)(Notification);