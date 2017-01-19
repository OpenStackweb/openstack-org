import React from 'react';
import VotingBar from './VotingBar';
import { TransitionMotion, Motion, spring, presets } from 'react-motion';
import animationStyle from '../../utils/animationStyle';
import HotKeyBar from '../ui/HotKeyBar';
import HotKeyOption from '../ui/HotKeyOption';
import { connect } from 'react-redux';
import { requestVote, toggleVotingCard, adjacentPresentation } from '../../action-creators';
import scrollToElement from '../../utils/scrollToElement';

const buttons = [
	{key: '3', data: {keyCodes: [51,99], text: 'Would love to see!', icon: 'heart'}},
	{key: '2', data: {keyCodes: [50,98], text: 'Would try to see', icon: 'thumbs-up'}},
	{key: '1', data: {keyCodes: [49,97], text: 'Might see', icon: 'minus'}},
	{key: '0', data: {keyCodes: [48,96], text: 'Would not see', icon: 'thumbs-down'}}
];

const actions = [
	{key: 'comment', data: {keyCodes: [], text: 'Leave comment', icon: 'comment'}},
	{key: 'next', data: {keyCodes: [], text: 'Next submission', icon: 'chevron-right'}}
];

class MobileVotingBar extends React.Component {

	constructor (props) {
		super(props);
		this.state = {
			items: []
		}
		this.doVote = this.doVote.bind(this);
	}

	staggeredUpdate(set) {
		set.forEach((data,i) => {
			setTimeout(() => {
				const { items } = this.state;
				this.setState({items: [...items, data]})
			}, i*200);
		})
	}

	componentWillReceiveProps(nextProps) {
		if(nextProps.active && !this.props.active) {
			this.staggeredUpdate(buttons);			
		} else if(!nextProps.active && this.props.active) {
			this.setState({items: []});
		}
	}

	willEnter() {
		return {
			opacity: 0,
			scale: 0.5
		}
	}


	doVote(vote) {
		if(vote === 'comment') {
			this.props.close();
			scrollToElement(
				document.querySelector('textarea'),
				document.querySelector('.outer-wrap')
			);
			document.querySelector('textarea').focus();
			return;
		} else if (vote === 'next') {					
			this.props.next();			
			this.props.close();
			return;
		}

		this.props.votePresentation(vote);
		this.setState({
			items: []			
		}, () => {
			this.staggeredUpdate(actions);	
		});
	}

	render() {
		const { votePresentation, presentation, close, active } = this.props;		
		const style = active ?
			{ alpha: spring(0.9), y: spring(0) } : 
			{ alpha: spring(0), y: spring(window.innerHeight) };
		const defaultStyle = {
 			alpha: 0,
			height: 0
		};
		
		return (
			<Motion style={style}>
			{interpolatedStyle => (
				<div style={animationStyle({
					panel: true,
					fixed: true,
					zIndex: 999,
					...interpolatedStyle,
					background: `rgba(60,60,60,${interpolatedStyle.alpha})`					
				})}>
					{active && <a className="voting-close-panel" onClick={close}>&times;</a>}
					<TransitionMotion
						styles={this.state.items.map((button, i) => ({
							key: button.key,
							style: { opacity: 1, scale: spring(1, {stiffness: 100, damping: 10})},
							data: button.data
						}))}
						willEnter={this.willEnter}
					>
					{interpolatedStyles => (
						<HotKeyBar className="mobile-voting-rate-wrapper" onItemSelected={this.doVote} value={presentation.user_vote}>
						{interpolatedStyles.map(config => {
							const { style, key, data } = config;
							return (							
								<HotKeyOption style={animationStyle(style)} key={key} keyCodes={data.keyCodes} eventKey={key} className="voting-rate-single">
									<i className={`fa fa-${data.icon}`} /> {data.text}
								</HotKeyOption>								
							);
						})}
						</HotKeyBar>						
					)}
					</TransitionMotion>
				</div>
			)}
			</Motion>
		);
	}
}

export default connect (
	(state, ownProps) => ({
		presentation: state.presentations.selectedPresentation,
		active: state.mobile.showVotingCard
	}),
	null,
	(stateProps, dispatchProps) => {
		const { dispatch } = dispatchProps;
		return {
			...stateProps,
			votePresentation (vote) {
				dispatch(requestVote(stateProps.presentation.id, vote));
			},
			close () {
				dispatch(toggleVotingCard());
			},
			next () {
				dispatch(adjacentPresentation(1));
			}

		}
	}
)(MobileVotingBar);