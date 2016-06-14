import React from 'react';
import {connect} from 'react-redux';
import ButtonGroup from '../ui/ButtonGroup';
import ButtonOption from '../ui/ButtonOption';
import {activatePresentationFilter} from '../../actions';

const PresentationFilterButtons = ({
	activeFilter,
	onSelect,
	category
}) => (
	<ButtonGroup activeKey={activeFilter} onSelect={onSelect}>
		<ButtonOption eventKey='all'>All</ButtonOption>
		<ButtonOption eventKey='unseen'>Unseen</ButtonOption>
		<ButtonOption eventKey='seen'>Seen</ButtonOption>
		{category &&
			<ButtonOption eventKey='moved'>Moved here</ButtonOption>
		}
	</ButtonGroup>
);

export default connect(
	state => ({
		category: state.routing.locationBeforeTransitions.query.category,
		activeFilter: state.presentations.filter
	}),

	dispatch => ({
		onSelect(key) {			
			dispatch(activatePresentationFilter(key));
		}
	})
)(PresentationFilterButtons);