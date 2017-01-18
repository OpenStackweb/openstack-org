import React from 'react';
import { findDOMNode } from 'react-dom';
import TweenMax from 'gsap';

const animate = (Component, dir) => {
	return class Animated extends React.Component {

		componentWillEnter(callback) {
			const el = findDOMNode(this);
			if(dir === 'down') {
				TweenMax.from(el, 0.3, { 
					y: 500, 
					opacity: 0, 
					onComplete: callback, 
					delay: 0,
					ease: TweenMax.Back.easeOut
				});				

			} else {
				TweenMax.from(el, 0.3, { 
					y: -100, 
					opacity: 0, 
					onComplete: callback, 
					delay: 0,
					ease: TweenMax.Back.easeOut					
				});
			}			
		}

		componentWillLeave(callback) {
			const el = findDOMNode(this);
			if(dir === 'down') {
				TweenMax.to(el, 0.3, { 
					y: -100, 
					opacity: 0, 
					onComplete: callback,
					ease: TweenMax.Power4.easeIn
				});			
			} else {
				TweenMax.to(el, 0.3, { 
					y: 500, 
					opacity: 0, 
					onComplete: callback,
					ease: TweenMax.Power4.easeIn
				});
			}
		}

		render() {
			return <Component {...this.props} />
		}
	}
};

export default animate;