import React from 'react';
import Animate from 'react-addons-css-transition-group';

export default ({
	children
}) => (
	<Animate 
		transitionName="vertical-slide" 
		transitionAppear 
		transitionAppearTimeout={1000} 
		transitionEnterTimeout={1000} 
		transitionLeaveTimeout={1000}
	>
		{children}
	</Animate>
);