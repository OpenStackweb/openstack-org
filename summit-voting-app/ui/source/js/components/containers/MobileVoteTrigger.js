import React from 'react';
import { connect } from 'react-redux';
import { toggleVotingCard } from '../../action-creators';

const MobileVoteTrigger = ({
	trigger,
	active,
	shouldDisplay
}) => (
	shouldDisplay ? 
		<a className={`mobile-vote-trigger ${active ? 'active' : ''}`} onClick={trigger}>
			<i className="fa fa-check-square" /> Cast your vote
		</a> :
		null
	
);

export default connect (
	state => ({
		active: state.mobile.showVotingCard,
		shouldDisplay: !state.presentations.requestedPresentationID || 
			(state.presentations.requestedPresentationID === state.presentations.selectedPresentation.id)

	}),

	dispatch => ({
		trigger(e) {
			e.preventDefault();
			dispatch(toggleVotingCard());
		}
	})
)(MobileVoteTrigger);