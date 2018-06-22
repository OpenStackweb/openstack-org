import React from 'react';
import HotKeyBar from '../ui/HotKeyBar';
import HotKeyOption from '../ui/HotKeyOption';
import { requestVote, navigatePresentations } from '../../action-creators';
import { connect } from 'react-redux';
import Config from '../../utils/Config';

const VotingBar = ({
	presentation,
	votePresentation
}) => {
	let disabled = !Config.get('votingOpen');
	return (
		<HotKeyBar className="voting-rate-wrapper" onItemSelected={votePresentation} value={presentation.user_vote}>
			<HotKeyOption keyCodes={[51, 99]} disabled={disabled} className="voting-rate-single" eventKey="3">
				<i className="fa fa-heart"/> Would Love To See!
			</HotKeyOption>
			<HotKeyOption keyCodes={[50, 98]} disabled={disabled} className="voting-rate-single" eventKey="2">
				<i className="fa fa-thumbs-up"/>Would Try To See
			</HotKeyOption>
			<HotKeyOption keyCodes={[49, 97]} disabled={disabled} className="voting-rate-single" eventKey="1">
				<i className="fa fa-minus"/> MightSee
			</HotKeyOption>
			<HotKeyOption keyCodes={[48, 96]} disabled={disabled} className="voting-rate-single" eventKey="0">
				<i className="fa fa-thumbs-down"/>WouldNot See
			</HotKeyOption>
		</HotKeyBar>
	);
};

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