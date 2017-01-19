import React from 'react';
import { TransitionMotion, spring, presets } from 'react-motion';
import PresentationDetail from './PresentationDetail';
import Loader from '../ui/Loader';
import { connect } from 'react-redux';
import animationStyle from '../../utils/animationStyle';

class AnimatedPresentationDetail extends React.Component {

	constructor(props) {
		super(props);
		this.willLeave = this.willLeave.bind(this);
		this.willEnter = this.willEnter.bind(this);
	}

	isLoading() {
		const {requestedPresentationID, presentationID} = this.props;
		return (requestedPresentationID && (presentationID !== requestedPresentationID))
	}

	willLeave() {
		if(this.props.dir === 'forward') {
			return {
				opacity: spring(0, presets.stiff),
				x: spring(-500, presets.stiff)
			}
		}

		return {
			opacity: spring(0, presets.stiff),
			x: spring(500, presets.stiff)			
		}
	}

	willEnter() {
		if(this.props.dir === 'forward') {
			return {
				opacity:0,
				x: 500
			}			
		}
		return {
			opacity: 0,
			x: -500
		}
	}

	getChildren() {
		return this.isLoading() ? ['loading'] : [this.props.requestedPresentationID];
	}

	render() {
		const { requestedPresentationID, presentationID, isMobile } = this.props;
		const dirProp = isMobile ? 'x' : 'y';
		const children = this.getChildren();

		return (
	      <TransitionMotion
	        willLeave={this.willLeave}
	        willEnter={this.willEnter}
	        defaultStyles={children.map(k => ({
	        	key: String(k),
	        	style: {opacity: 1, x: 0}
	        }))}
	        styles={children.map(k => ({
	        	key: String(k),
	        	style: {opacity: spring(1, presets.stiff), x: spring(0, presets.stiff)}
	        }))}>
	        {interpolatedStyles => 
	        	<div>
		          {interpolatedStyles.map(config => {
		          		const {x, opacity} = config.style;
		          		const s = animationStyle({panel: true, [dirProp]: x, opacity});

		          		return (
		          			<div key={config.key} style={s}>
		          				{config.key === 'loading' ?
		          					<Loader active={true} type='bounce' /> :
		          					<PresentationDetail />
		          				}
		          			</div>
		          		);
		          })}
	          	</div>
	        }
	      </TransitionMotion>	
	    );	
	}
}

export default connect(
	state => ({
		dir: state.presentations.navigationDirection,
		isMobile: state.mobile.isMobile,
		requestedPresentationID: state.presentations.requestedPresentationID,
		presentationID: state.presentations.selectedPresentation.id
	})
)(AnimatedPresentationDetail);