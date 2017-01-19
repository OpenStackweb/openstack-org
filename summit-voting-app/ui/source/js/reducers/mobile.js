export default (state = {
	showPresentationList: false,
	showVotingCard: false,
	isMobile: (window.innerWidth > 0) ? window.innerWidth < 768 : screen.width < 768
}, action) => {
	switch(action.type) {
		case 'TOGGLE_PRESENTATION_LIST':
			return {
				...state,
				showPresentationList: !state.showPresentationList				
			};

		case 'TOGGLE_VOTING_CARD':
			return {
				...state,
				showVotingCard: !state.showVotingCard				
			};

		default:
			return state;
	}
};