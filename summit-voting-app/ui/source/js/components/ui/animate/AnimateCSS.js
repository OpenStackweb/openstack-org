import React from 'react';
import Animate from 'react-addons-css-transition-group';
import AnimateCSSAnimation from './AnimateCSSAnimation';
import './slide-in-left.less';

const AnimateCSS = ({
	enterAnimation,
	leaveAnimation,
	appearAnimation,
	name,
	children
}) => {
	const transitionName = {};
	if(enterAnimation instanceof AnimateCSSAnimation) {
		transitionName.enter = `${enterAnimation.getName()}BeforeEnter`;
		transitionName.enterActive = `${enterAnimation.getName()}Entering`;
	}
	if(leaveAnimation instanceof AnimateCSSAnimation) {
		transitionName.leave = `${leaveAnimation.getName()}BeforeLeave`;
		transitionName.leaveActive = `${leaveAnimation.getName()}Leaving`;		
	}
	if(appearAnimation instanceof AnimateCSSAnimation) {
		transitionName.appear = `${appearAnimation.getName()}BeforeAppear`;
		transitionName.appearActive = `${appearAnimation.getName()}Appearing`;				
	}
	return (
		<Animate 
			transitionName={transitionName}
			transitionAppear={appearAnimation instanceof AnimateCSSAnimation}
			transitionLeave={leaveAnimation instanceof AnimateCSSAnimation}
			transitionEnter={enterAnimation instanceof AnimateCSSAnimation} 
			transitionAppearTimeout={1000} 
			transitionEnterTimeout={1000} 
			transitionLeaveTimeout={1000}
		>
			{children}
		</Animate>
	);
};

export default AnimateCSS;