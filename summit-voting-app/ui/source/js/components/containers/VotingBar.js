import React from 'react';
import HotKeyBar from '../ui/HotKeyBar';
import HotKeyOption from '../ui/HotKeyOption';
import { requestVote, navigatePresentations } from '../../action-creators';
import { connect } from 'react-redux';
import Config from '../../utils/Config';

const VotingBar = ({
	presentation,
	votePresentation
}) => (
	<HotKeyBar className="voting-rate-wrapper" onItemSelected={votePresentation} value={presentation.user_vote}>
		<HotKeyOption keyCodes={[51,99]} className="voting-rate-single" eventKey="3"><i className="fa fa-heart" /> Would Love To See!</HotKeyOption>
		<HotKeyOption keyCodes={[50,98]} className="voting-rate-single" eventKey="2"><i className="fa fa-thumbs-up" /> Would Try To See</HotKeyOption>
		<HotKeyOption keyCodes={[49,97]} className="voting-rate-single" eventKey="1"><i className="fa fa-minus" /> Might See</HotKeyOption>
		<HotKeyOption keyCodes={[48,96]} className="voting-rate-single" eventKey="0"><i className="fa fa-thumbs-down" />Would Not See</HotKeyOption>
	</HotKeyBar>
);

export default connect (
	(state, ownProps) => ({
		presentation: state.presentations.selectedPresentation
	}),
	null,
	(stateProps, dispatchProps) => {
		const { dispatch } = dispatchProps;
		return {
			...stateProps,
			votePresentation (vote) {
				dispatch(requestVote(stateProps.presentation.id, vote))
			}
		}
	}
)(VotingBar);