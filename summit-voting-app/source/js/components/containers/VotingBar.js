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
		<HotKeyOption keyCodes={[51,99]} className="voting-rate-single" eventKey="3">Would Love To See!</HotKeyOption>
		<HotKeyOption keyCodes={[50,98]} className="voting-rate-single" eventKey="2">Would Try To See</HotKeyOption>
		<HotKeyOption keyCodes={[49,97]} className="voting-rate-single" eventKey="1">Might See</HotKeyOption>
		<HotKeyOption keyCodes={[48,96]} className="voting-rate-single" eventKey="0">Would Not See</HotKeyOption>
	</HotKeyBar>
);

export default connect (
	(state, ownProps) => ({
	}),
	(dispatch, ownProps) => {
		return {
			votePresentation: (vote) => {
				dispatch(requestVote(ownProps.presentation.id, vote))
				if(Config.get('isMobile')) {
					dispatch(navigatePresentations(1));
				}
			}
		}
	}
)(VotingBar);