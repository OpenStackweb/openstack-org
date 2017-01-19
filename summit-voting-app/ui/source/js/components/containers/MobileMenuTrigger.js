import React from 'react';
import { connect } from 'react-redux';
import { togglePresentationList } from '../../action-creators';

const MobileMenuTrigger = ({
	trigger,
	active
}) => (
	<a className="voting-open-panel" onClick={trigger}>
		All Submissions <i className="fa fa-chevron-down" /> 
	</a>
);

export default connect (
	state => ({
		active: state.mobile.showPresentationList
	}),

	dispatch => ({
		trigger(e) {			
			e.preventDefault();
			dispatch(togglePresentationList());
		}
	})
)(MobileMenuTrigger);