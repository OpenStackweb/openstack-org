import React from 'react';
import Animate from 'react-addons-css-transition-group';

const AnimateCSS = ({
	enterAnimation,
	leaveAnimation,
	appearAnimation,	
	children
}) => {
	const transitionName = {};
	if(enterAnimation) {
		transitionName.enter = `${enterAnimation}BeforeEnter`;
		transitionName.enterActive = `${enterAnimation}Entering`;
	}
	if(leaveAnimation) {
		transitionName.leave = `${leaveAnimation}BeforeLeave`;
		transitionName.leaveActive = `${leaveAnimation}Leaving`;		
	}
	if(appearAnimation) {
		transitionName.appear = `${appearAnimation}BeforeAppear`;
		transitionName.appearActive = `${appearAnimation}Appearing`;				
	}
	return (
		<Animate 
			transitionName={transitionName}
			transitionAppear={!!appearAnimation}
			transitionLeave={!!leaveAnimation}
			transitionEnter={!!enterAnimation} 
			transitionAppearTimeout={1000} 
			transitionEnterTimeout={1000} 
			transitionLeaveTimeout={1000}
		>
			{children}
		</Animate>
	);
};

export default AnimateCSS;